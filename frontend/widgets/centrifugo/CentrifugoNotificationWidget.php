<?php

namespace frontend\widgets\centrifugo;

use yii\bootstrap\Widget;
use phpcent\Client;
use Yii;
use yii\base\InvalidConfigException;

/**
 * @author vincent.barnes
 * @since 2.20
 */

class CentrifugoNotificationWidget extends Widget
{
    public $userId;
    public $userAllowedChannels;
    private $centrifugoUrl;
    private $tokenHmacSecretKey;

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
        $this->registerAssets();
        $client = new Client($this->centrifugoUrl);
        $token = $client->setSecret($this->tokenHmacSecretKey)->generateConnectionToken($this->userId,  '');

        return $this->render('index',[
            'channels' => $this->userAllowedChannels,
            'centrifugoUrl' => $this->centrifugoUrl,
            'token' => $token
        ]);
    }

    /**
     * Register assets.
     */
    protected function registerAssets()
    {
        $view = $this->getView(); // получаем объект вида, в который рендерится виджет
        CentrifugoNotificationAssets::register($view);// регестрируем файл с классом наборов css, js.
    }
}