<?php

namespace AlejandroHerr\Silex\EsnGalaxy\Security\Core\Encoder;

interface DigestEncoderInterface
{
    public function encode($password, $nonce, $created);
}
