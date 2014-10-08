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

class CasAuthenticationProvider implements AuthenticationProviderInterface
{
    protected $userProvider;
    protected $options;
    protected $logger;
    //todo remove the checker, add options field, thanks!!
    public function __construct($options, UserProviderInterface $userProvider, $logger)
    {
        $this->options = $options;
        $this->userProvider = $userProvider;
        $this->logger = $logger;
    }
    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }
        if (empty($token->getUser()) ||
            empty($token->getCredentials())){
            throw new BadCredentialsException('No pre-authenticated principal found in request.');
        }

        $this->checkAuthentication($token);

        try {
            $user = $this->userProvider->loadUserByUsername($token->getUser());
        } catch (UsernameNotFoundException $e) {
            $user = $this->userProvider->createUser($token);
        }

        $validatedToken = new EsnGalaxyToken($user, $user->getAttributes(), $user->getRoles());

        return $validatedToken;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof CasToken;
    }
    protected function checkAuthentication(CasToken $token)
    {
        $credentials = $token->getCredentials();
        if (!($credentials['section'] == $this->options['section'])) {
            throw new BadCredentialsException('Your section ain\'t authorized');
        }
    }
}
