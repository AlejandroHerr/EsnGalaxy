<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\Authentication\Token;

class EsnGalaxyToken extends CasToken
{
    public function getCredentials()
    {
        return array(
            'roles' => $this->getAttribute('roles'),
            'section' => $this->getAttribute('sc'),
        );
    }
}
