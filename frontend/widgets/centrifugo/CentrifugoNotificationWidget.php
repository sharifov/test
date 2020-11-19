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
    public string $widgetView;
    public $userAllowedChannels;
    private $centrifugoUrl;

    public function init()
    {
        parent::init();
        $this->centrifugoUrl = Yii::$app->params['centrifugo']['wsConnectionUrl'];

        if ($this->userId === null) {
            throw new InvalidConfigException('The "userId" property must be set.');
        }

        if ($this->centrifugoUrl === null || $this->centrifugoUrl === '') {
            throw new InvalidConfigException('The "wsConnectionUrl" property must be set in system params.');
        }

        if ($this->widgetView === null || $this->widgetView === '') {
            throw new InvalidConfigException('The "widgetView" property must be set in widget params.');
        }
    }

    public function run()
    {
        $this->registerAssets();

        return $this->render($this->widgetView, [
            'channels' => $this->userAllowedChannels,
            'centrifugoUrl' => $this->centrifugoUrl,
            'token' => Yii::$app->centrifugo->generateConnectionToken($this->userId)
        ]);
    }

    /**
     * Register assets.
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        CentrifugoNotificationAssets::register($view);
    }
}
