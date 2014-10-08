<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\User;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SpawnedUserProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        throw new UsernameNotFoundException('They\'re spawned users, the don\exist!');
    }
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SpawnedUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $user;
    }
    public function createUser(TokenInterface $token)
    {
        $username = $token->getUser();
        $attributes = $token->getAttributes();
        if(is_null($token->getRoles())){
            $roles = isset($attributes['roles']) ? $attributes['roles'] : null;
            unset($attributes['roles']);
        }else{
            $roles = $token->getRoles();
        }
        
        return new SpawnedUser($username, $attributes, $roles);
    }
    public function supportsClass($class)
    {
        return $class === 'AlejandroHerr\\CasSecurity\\Security\\Core\\User\\SpawnedUser';
    }
}
