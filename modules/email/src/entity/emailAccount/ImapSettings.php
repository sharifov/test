<?php

namespace modules\email\src\entity\emailAccount;

use yii\helpers\Json;

/**
 * Class ImapSettings
 *
 * @property $path
 * @property $login
 * @property $password
 */
class ImapSettings
{
    public $path;
    public $login;
    public $password;

    public function __construct(EmailAccount $account)
    {
        try {
            $settings = Json::decode($account->ea_imap_settings);
            $this->path = $settings['path'];
            $this->login = $settings['login'];
            $this->password = $settings['password'];
        } catch (\Throwable $e) {
            throw new \InvalidArgumentException('Imap settings error. ' . $e->getMessage());
        }
    }
}
