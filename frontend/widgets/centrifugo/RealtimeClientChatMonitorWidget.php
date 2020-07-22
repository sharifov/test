<?php


namespace frontend\widgets\centrifugo;

use phpcent\Client;
use yii\base\InvalidConfigException;
use yii\bootstrap\Widget;
use yii;

class RealtimeClientChatMonitorWidget extends Widget
{
    public int $userId;
    public array $userAllowedChannels;
    private string $centrifugoUrl;
    private string $tokenHmacSecretKey;

    public function init()
    {
        parent::init();
        $this->centrifugoUrl = Yii::$app->params['centrifugo']['jsClientUrl'];
        $this->tokenHmacSecretKey = Yii::$app->params['centrifugo']['tokenHmacSecretKey'];

        if ($this->userId === null) {
            throw new InvalidConfigException('The "userId" property must be set.');
        }

        if ($this->centrifugoUrl === null || $this->centrifugoUrl === '') {
            throw new InvalidConfigException('The "jsClientUrl" property must be set in system params.');
        }

        if ($this->tokenHmacSecretKey === null || $this->tokenHmacSecretKey === '') {
            throw new InvalidConfigException('The "tokenHmacSecretKey" property must be set in system params.');
        }
    }

    public function run()
    {
        $client = new Client($this->centrifugoUrl);
        $token = $client->setSecret($this->tokenHmacSecretKey)->generateConnectionToken($this->userId,  '');

        return $this->render('monitor',[
            'channels' => $this->userAllowedChannels,
            'centrifugoUrl' => $this->centrifugoUrl,
            'token' => $token
        ]);
    }
}