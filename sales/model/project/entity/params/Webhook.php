<?php

namespace sales\model\project\entity\params;

/**
 * Class Webhook
 * @package sales\model\project\entity\params
 *
 * @property string $endpoint
 * @property string $username
 * @property string $password
 */
class Webhook
{
    public string $endpoint;
    public string $username;
    public string $password;

    public function __construct(array $params)
    {
        $this->endpoint = $params['endpoint'] ?? self::default()['endpoint'];
        $this->username = $params['username'] ?? self::default()['username'];
        $this->password = $params['password'] ?? self::default()['password'];
    }

    public static function default(): array
    {
        return [
            'endpoint' => '',
            'username' => '',
            'password' => ''
        ];
    }
}
