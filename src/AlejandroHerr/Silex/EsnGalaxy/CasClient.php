<?php
namespace AlejandroHerr\Silex\EsnGalaxy;

use AlejandroHerr\Silex\EsnGalaxy\Exception\CasCurlException;
use AlejandroHerr\Silex\EsnGalaxy\Security\Core\Exception\CasAuthenticationException;
use Curl\Curl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler\Crawler;

class CasClient
{
    protected $baseUrl;
    protected $context;
    protected $port;
    protected $serverUrl;
    protected $validationUrl;
    protected $response;

    public function __construct($options)
    {
        $this->baseUrl = $options['baseUri'];
        $this->context = isset($options['context']) ? $options['context'] : 'cas';
        $this->port = isset($options['port']) ? $options['port'] : 442;

        $this->buildServerUrl();
    }

    public function getLoginUrl(Request $request)
    {
        $url = $this->serverUrl . '/login';

        return $this->buildUrl(
            $url,
            array(
                "service" => $this->getServiceUrl($request)
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

        $responseParser = new ResponseParser\EsnGalaxyResponseParser();

        return $responseParser->getTokenData($this->response);
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
        if(!count($authenticationSuccess)) return false;
        $this->response = $authenticationSuccess;

        return true;
    }

    protected function buildServerUrl()
    {
        $this->serverUrl = 'https://'.$this->baseUrl.':' . $this->port . '/'.$this->context;
    }
    protected function getValidationUrl(Request $request)
    {
        $url = $this->serverUrl . '/serviceValidate';

        return $this->buildUrl(
            $url,
            array(
                "ticket" =>  $request->get('ticket'),
                "service" => $this->getServiceUrl($request)
            )
        );
    }

    protected function getServiceUrl(Request $request)
    {
        return $request->getSchemeAndHttpHost() . $request->getBaseUrl() . '/validation';
    }

    private function buildUrl($url, $data = array())
    {
        return $url . (empty($data) ? '' : '?' . http_build_query($data));
    }

    /**********/
    /* Errors */
    /**********/

    protected function getValidationError()
    {
        $this->response = $this->response->filterXPath('//cas:authenticationFailure');

        return sprintf('%s: %s',$this->getValidationErrorCode(),$this->getValidationErrorMsg());

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
