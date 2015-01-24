<?php
namespace AlejandroHerr\Silex\EsnGalaxy\Security\Http\Authentication;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class CasAuthenticationSuccesHandler extends DefaultAuthenticationSuccessHandler
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($token->isUserNew()) {
            return $this->httpUtils->createRedirectResponse($request, $this->options['new_user_path']);
        }

        return $this->httpUtils->createRedirectResponse($request, $this->determineTargetUrl($request));
    }
}
