<?php

namespace src\useCase\login\twoFactorAuth\service;

class OtpEmailService
{
    public function generateSecretCode(): int
    {
        return random_int(100000, 999999);
    }

    public function hashKey(string $key): string
    {
        return md5($key);
    }
}
