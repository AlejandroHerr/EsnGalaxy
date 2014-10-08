<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\User;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class DbalEsnGalaxyUserProvider extends UserProviderInterface
{
    protected $connection;
    protected $table;

    public function _construct(Connection $conn, $table = 'users')
    {
        $this->connection = $conn;
        $this->table = $table;
    }
    public function createUser(array $user)
    {
        $this->conn->insert(
            $this->table,
            $user
        );

        return new EsnGalaxyUser($user);
    }
    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        if (!($user = $this->conn->fetchAssoc('SELECT * FROM '.$this->table.' WHERE username = ?', array($this->escape($username))))) {
            throw new UsernameNotFoundException();
        }

        return new EsnGalaxyUser($user);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof EsnGalaxyUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        $username = $user->getUsername();

        return $this->loadUserByUsername($user);
    }

    public function supportsClass($class)
    {
        return $class === 'AlejandroHerr\\CasSecurity\\Security\\Core\\User\\EsnGalaxyUser';
    }
}
