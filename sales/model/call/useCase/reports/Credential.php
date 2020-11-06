<?php

namespace sales\model\call\useCase\reports;

/**
 * Class Credential
 *
 * @property $user
 * @property $password
 * @property $url
 * @property $port
 * @property $path
 */
class Credential
{
    public $user;
    public $password;
    public $url;
    public $port;
    public $path;

    public function __construct(
        string $user,
        string $password,
        string $url,
        int $port,
        string $path
    ) {
        $this->user = $user;
        $this->password = $password;
        $this->url = $url;
        $this->port = $port;
        $this->path = $path;
    }
}
