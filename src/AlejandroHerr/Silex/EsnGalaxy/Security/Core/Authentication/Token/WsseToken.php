<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class WsseToken extends AbstractToken
{
    private $digest;
    private $nonce;
    private $created;
    private $providerKey;
    
    public function __construct($user, $providerKey, array $roles = array())
    {
        parent::__construct($roles);
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->setUser($user);
        $this->providerKey = $providerKey;
        $this->digest = '';
        $this->nonce = '';
        $this->created = '';

        parent::setAuthenticated(count($roles) > 0);
    }
    /**
     * {@inheritdoc}
     * @return array Associative array containing the digest, the nonce and the created value
     */
    public function getCredentials()
    {
        return [
            'digest' => $this->getDigest(),
            'nonce' => $this->getNonce(),
            'created' => $this->getCreated()
        ];
    }

    /**
     * Gets the value of digest.
     *
     * @return string
     */
    public function getDigest()
    {
        return $this->digest;
    }

    /**
     * Sets the value of digest.
     *
     * @param string $digest the digest
     *
     * @return self
     */
    public function setDigest($digest)
    {
        $this->digest = $digest;

        return $this;
    }

    /**
     * Gets the value of nonce.
     *
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * Sets the value of nonce.
     *
     * @param string $nonce the nonce
     *
     * @return self
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     * Gets the value of created.
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Sets the value of created.
     *
     * @param string $created the created
     *
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }
}
