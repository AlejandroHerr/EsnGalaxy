<?php
namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\Authentication\Provider;

use AlejandroHerr\Silex\EsnGalaxy\Security\Core\Authentication\Token\CasToken;
use AlejandroHerr\Silex\EsnGalaxy\Security\Core\Authentication\Token\EsnGalaxyToken;
use Symfony\Component\Security\Core\Authentication\Token\Token;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Role\Role;

class EsnGalaxyAuthenticationProvider implements AuthenticationProviderInterface
{
    protected $userProvider;
    protected $options;

    public function __construct(UserProviderInterface $userProvider, array $options = array())
    {
        $this->options = $options;
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return;
        }
        if (empty($token->getUser()) ||
            empty($token->getCredentials())) {
            throw new BadCredentialsException('No pre-authenticated principal found in request.');
        }
        $token = $this->checkAuthentication($token);
        try {
            $user = $this->userProvider->loadUserByUsername($token->getUsername());
            $user = $this->userProvider->updateUser($user, $token);
        } catch (UsernameNotFoundException $e) {
            $user = $this->userProvider->createUser($token);
        }
        $token->setUser($user);

        return $token;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof EsnGalaxyToken;
    }

    protected function checkAuthentication(CasToken $token)
    {
        $credentials = $token->getCredentials();
        $validCredentials = $this->options;
        $finalRoles = [];

        foreach ($validCredentials as $section => $roles) {
            if ($credentials['section'] != $section && '*' != $section) {
                continue;
            }
            foreach ($roles as $role => $givenRole) {
                if (in_array($role, $credentials['roles'])) {
                    $finalRoles[] = new Role($givenRole);
                }
            }
        }

        return new EsnGalaxyToken($token->getUser(), $token->getAttributes(), $finalRoles);
    }
}
