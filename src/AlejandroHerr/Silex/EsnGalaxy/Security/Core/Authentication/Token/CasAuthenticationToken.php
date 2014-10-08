<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class CasAuthenticationToken extends AbstractToken
{
    protected $casAttributes;
    public function __construct($user, array $attributes = array(), array $roles = array())
    {
        parent::__construct($attributes['roles']);
        $this->setUser($user);
        $this->casAttributes = $attributes;
        parent::setAuthenticated(true);
    }
    public function getCredentials()
    {
        return '';
    }
    public function getCasAttributes()
    {
        return $this->casAttributes;
    }
    public function getCasAttribute($key)
    {
        return $this->casAttributes[$key];
    }
}
