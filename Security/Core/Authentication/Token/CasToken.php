<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class CasToken extends AbstractToken
{
    protected $userIsNew;

    public function __construct($user, $attributes, array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($user);
        $this->setAttributes($attributes);
        $this->userIsNew = false;
    }

    public function getCredentials()
    {
        return $this->attributes;
    }

    public function setUserIsNew($bool)
    {
        $this->userIsNew = $bool;

        return $this;
    }
    public function isUserNew()
    {
        return $this->userIsNew;
    }
}
