<?php
namespace AlejandroHerr\Silex\EsnGalaxy\Security\Http\Firewall;

use AlejandroHerr\Silex\EsnGalaxy\Security\Core\Authentication\Token\EsnGalaxyToken;

class EsnGalaxyAuthenticationListener extends AbstractCasAuthenticationListener
{
    protected function generatePreAuthToken($tokenData)
    {
        return new EsnGalaxyToken(
            $tokenData['username'],
            $tokenData['attributes']
        );
    }
}
