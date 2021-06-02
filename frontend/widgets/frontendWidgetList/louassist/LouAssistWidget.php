<?php

namespace frontend\widgets\frontendWidgetList\louassist;

use common\models\UserConnection;
use sales\auth\Auth;
use sales\helpers\setting\SettingHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * LouAssistWidget
 *
 * @property bool|null $enabled
 * @property array|null $params
 * @property array|null $routes
 */
class LouAssistWidget extends \yii\bootstrap\Widget
{
    public $enabled;
    public $params;
    public $routes;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if (!$this->enabled) {
            return '';
        }
        if (!$this->checkRoute()) {
            return '';
        }
        if (!$identify = ArrayHelper::remove($this->params, 'identify')) {
            throw new \RuntimeException('"identify" is required in LouAssistWidget params');
        }

        return $this->render('view', [
            'params' => $this->params,
            'identify' => $identify,
            'userId' => Auth::id(),
        ]);
    }

    private function checkRoute(): bool
    {
        if (ArrayHelper::isIn('*', $this->routes)) {
            return true;
        }
        if (ArrayHelper::isIn(Yii::$app->controller->action->uniqueId, $this->routes)) {
            return true;
        }
        if (ArrayHelper::isIn(Yii::$app->controller->uniqueId . '/*', $this->routes)) {
            return true;
        }
        return false;
    }
}
