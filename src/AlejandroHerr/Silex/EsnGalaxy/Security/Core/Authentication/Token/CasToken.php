<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class CasToken extends AbstractToken
{
    public function __construct($user, $attributes, array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($user);
        $this->setAttributes($attributes);
    }

    public function getCredentials()
    {
        return $attributes;
    }
}
