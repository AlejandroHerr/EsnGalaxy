<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Http\Firewall;

use AlejandroHerr\Silex\EsnGalaxy\CasClientInterface;
use AlejandroHerr\Silex\EsnGalaxy\Exception\CasCurlException;
use AlejandroHerr\Silex\EsnGalaxy\Security\Core\Exception\CasAuthenticationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

abstract class AbstractCasAuthenticationListener extends AbstractAuthenticationListener
{
    protected $cas;

    public function __construct(CasClientInterface $cas, SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler,  $options, $logger, $dispatcher);
        $this->cas = $cas;
    }

    protected function attemptAuthentication(Request $request)
    {
        try {
            $tokenData = $this->cas->validateTicket($request);
        } catch (CasCurlException $e) {
            throw new CasAuthenticationException($e->getMessage());
        }

        return $this->authenticationManager->authenticate(
            $this->generatePreAuthToken($tokenData)
        );
    }

    protected function requiresAuthentication(Request $request)
    {
        return $this->cas->isValidationRequest($request);
    }

    abstract protected function generatePreAuthToken($tokenData);
}
