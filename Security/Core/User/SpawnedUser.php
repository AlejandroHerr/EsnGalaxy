<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\User;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class SpawnedUser implements AdvancedUserInterface
{
    protected $username;
    protected $enabled;
    protected $accountNonExpired;
    protected $credentialsNonExpired;
    protected $accountNonLocked;
    protected $roles;
    protected $attributes;

    public function __construct($username, array $attributes = array(), array $roles = array(), $enabled = true, $userNonExpired = true, $credentialsNonExpired = true, $userNonLocked = true)
    {
        if (empty($username)) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->username = $username;
        $this->attributes = $attributes;
        $this->enabled = $enabled;
        $this->accountNonExpired = $userNonExpired;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->accountNonLocked = $userNonLocked;
        $this->roles = $roles;
    }

    /**
     * Gets the user email.
     *
     * @return string
     */
    public function getEmail()
    {
        if (isset($this->attributes['email'])) {
            return $this->attributes['email'];
        }
        if (isset($this->attributes['mail'])) {
            return $this->attributes['mail'];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    public function getAttributes()
    {
        return $this->attributes;
    }
}
