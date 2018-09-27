<?php

namespace App\Utils;


class TokenCreator implements TokenCreatorInterface
{

    private const ENTROPY = 32;

    public function createToken()
    {
        return bin2hex(random_bytes(self::ENTROPY));
    }
}