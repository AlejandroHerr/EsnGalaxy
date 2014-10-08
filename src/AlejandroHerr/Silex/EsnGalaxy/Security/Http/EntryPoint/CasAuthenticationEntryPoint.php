<?php
namespace AlejandroHerr\Silex\EsnGalaxy\Security\Http\EntryPoint;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;

class CasAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    protected $httpUtils;
    protected $loginPath;

    public function __construct(HttpUtils $httpUtils, array $options = array())
    {
        $this->httpUtils = $httpUtils;
        $this->loginPath = isset($options['login_path']) ? $options['login_path'] : '/login';
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->httpUtils->createRedirectResponse($request, $this->loginPath);
    }
}
