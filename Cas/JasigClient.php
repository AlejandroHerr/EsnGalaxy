<?php
namespace AlejandroHerr\Silex\EsnGalaxy\Cas;

use AlejandroHerr\Silex\EsnGalaxy\Cas\Exception\CasCurlException;
use AlejandroHerr\Silex\EsnGalaxy\Cas\ResponseParser\CasResponseParserInterface;
use AlejandroHerr\Silex\EsnGalaxy\Security\Core\Exception\CasAuthenticationException;
use Curl\Curl;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;

class JasigClient implements CasClientInterface
{
    protected $responseParser;
    protected $httpUtils;
    protected $serverUrl;
    protected $response;
    protected $options;

    public function __construct(CasResponseParserInterface $responseParser, HttpUtils $httpUtils, array $options = array())
    {
        $this->responseParser = $responseParser;
        $this->httpUtils = $httpUtils;

        // cas_server MUST be set, since contains base_url
        $options['cas_server'] = array_merge([
            'context' => 'cas',
            'port' => '443',
            'login_path' => '/login',
            'validation_path' => '/serviceValidate',
        ], $options['cas_server']);
        $this->options = array_merge([
            'check_path' => '/validation',
        ], $options);

        $this->serverUrl = 'https://'.$this->options['cas_server']['base_url'].':'.$this->options['cas_server']['port'].'/'.$this->options['cas_server']['context'];
    }

    public function getLoginUrl(Request $request)
    {
        return $this->getCasServerRequest(
            $this->options['cas_server']['login_path'],
            ["service" => $this->httpUtils->generateUri($request, $this->options['check_path'])]
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
        return $this->httpUtils->checkRequestPath($request, $this->options['check_path']) && $request->query->has('ticket');
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

    protected function getValidationUrl(Request $request)
    {
        return $this->getCasServerRequest(
            $this->options['cas_server']['validation_path'],
            [
                "ticket" =>  $request->get('ticket'),
                "service" => $this->httpUtils->generateUri($request, $this->options['check_path'])
            ]
        );
    }

    private function getCasServerRequest($path, array $parameters = array())
    {
        if ('/' !== $path[0]) {
            $path = '/'.$path;
        }
        $request = Request::create($this->serverUrl.$path, 'GET', $parameters);

        return $request->getUri();
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
