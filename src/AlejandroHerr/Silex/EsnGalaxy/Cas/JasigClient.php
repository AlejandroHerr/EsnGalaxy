<?php
namespace AlejandroHerr\Silex\EsnGalaxy\Cas;

use AlejandroHerr\Silex\EsnGalaxy\Cas\ResponseParser\CasResponseParserInterface;
use AlejandroHerr\Silex\EsnGalaxy\Exception\CasCurlException;
use AlejandroHerr\Silex\EsnGalaxy\Security\Core\Exception\CasAuthenticationException;
use Curl\Curl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;

class JasigClient implements CasClientInerface
{
    protected $responseParser;
    protected $serverUrl;
    protected $validationUrl;
    protected $response;
    protected $options;

    public function __construct(CasResponseParserInterface $responseParser, array $options = array())
    {
        $this->responseParser = $responseParser;
        // cas_server MUST be set, since contains base_url
        $options['cas_server'] = array_merge([
            'context' => 'cas',
            'port' => '443',
        ], $options['cas_server']);
        $this->options = array_merge([
            'check_path' => '/validation',
            'login_path' => '/login',
        ], $options);
        $this->buildServerUrl();
    }

    public function getLoginUrl(Request $request)
    {
        $url = $this->serverUrl.$this->options['login_path'];

        return $this->buildUrl(
            $url,
            array(
                "service" => $this->getServiceUrl($request),
            )
        );
    }
    /**
     * [validateTicket description]
     * @param  Request                    $request
     * @return Array
     * @throws CasAuthenticationException If authentication fails
     */
    public function validateTicket(Request $request)
    {
        $this->getValidation($request);
        if (!$this->ticketIsValid()) {
            throw new CasAuthenticationException($this->getValidationError());
        }

        return $this->responseParser->getTokenData($this->response);
    }
    public function isValidationRequest(Request $request)
    {
        return $request->query->has('ticket');
    }
    /**
     * [getValidation description]
     * @param  Request $request
     * @return [type]
     */
    protected function getValidation(Request $request)
    {
        $client = $this;

        $curl = new Curl();
        $curl->success(function ($instance) use ($client) {
            $client->response = new Crawler();
            $client->response->addXmlContent($instance->response);
        });
        $curl->error(function ($instance) {
            throw new CasCurlException(sprintf('Code #%s: %s', $instance->error_code, $instance->error_message));
        });
        $curl->get($this->getValidationUrl($request));
    }

    protected function ticketIsValid()
    {
        $authenticationSuccess = $this->response->filterXPath('//cas:authenticationSuccess');
        if (!count($authenticationSuccess)) {
            return false;
        }
        $this->response = $authenticationSuccess;

        return true;
    }

    protected function buildServerUrl()
    {
        $this->serverUrl = 'https://'.$this->options['cas_server']['base_url'].':'.$this->options['cas_server']['port'].'/'.$this->options['cas_server']['context'];
    }
    protected function getValidationUrl(Request $request)
    {
        $url = $this->serverUrl.'/serviceValidate';

        return $this->buildUrl(
            $url,
            array(
                "ticket" =>  $request->get('ticket'),
                "service" => $this->getServiceUrl($request),
            )
        );
    }

    protected function getServiceUrl(Request $request)
    {
        return $request->getSchemeAndHttpHost().$request->getBaseUrl().$this->options['check_path'];
    }

    private function buildUrl($url, $data = array())
    {
        return $url.(empty($data) ? '' : '?'.http_build_query($data));
    }

    /**********/
    /* Errors */
    /**********/

    protected function getValidationError()
    {
        $this->response = $this->response->filterXPath('//cas:authenticationFailure');

        return sprintf('%s: %s', $this->getValidationErrorCode(), $this->getValidationErrorMsg());
    }
    protected function getValidationErrorCode()
    {
        return $this->response->extract('code')[0];
    }

    protected function getValidationErrorMsg()
    {
        return trim($this->response->text());
    }
}
