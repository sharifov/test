<?php

namespace frontend\widgets\centrifugo;

use yii\base\InvalidConfigException;
use yii\bootstrap\Widget;
use yii;

class CentrifugoWidget extends Widget
{
    public int $userId;
    public array $userAllowedChannels;
    public string $widgetView;
    private string $wsUrl;
    private bool $switch;

    public function init()
    {
        parent::init();
        $this->switch = Yii::$app->params['centrifugo']['enabled'];
        $this->wsUrl = Yii::$app->params['centrifugo']['wsConnectionUrl'];

        if ($this->userId === null) {
            throw new InvalidConfigException('The "userId" property must be set.');
        }

        if ($this->wsUrl === null || $this->wsUrl === '') {
            throw new InvalidConfigException('The "wsConnectionUrl" property must be set in system params.');
        }

        if ($this->widgetView === null || $this->widgetView === '') {
            throw new InvalidConfigException('The "widgetView" property must be set in widget params.');
        }
    }

    public function beforeRun()
    {
        if (!parent::beforeRun()) {
            return false;
        }

        if ($this->switch) {
            return true;
        }

        return false; // or false to not run the widget
    }

    public function run()
    {
        return $this->render($this->widgetView, [
            'channels' => $this->userAllowedChannels,
            'centrifugoUrl' => $this->wsUrl,
            'token' => Yii::$app->centrifugo->generateConnectionToken($this->userId)
        ]);
    }
}
