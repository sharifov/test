<?php

namespace frontend\widgets\frontendWidgetList\louassist;

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
 * @property string|int|null $scriptId
 */
class LouAssistWidget extends \yii\bootstrap\Widget
{
    public $enabled;
    public $params;
    public $routes;
    public $scriptId;

    public function init()
    {
        parent::init();
        $this->fillSettings();
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
            'scriptId' => $this->scriptId,
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

    private function fillSettings(): void
    {
        $settings = SettingHelper::getFrontendWidgetByKey('louassist');

        if (($this->scriptId === null) && !$this->scriptId = ArrayHelper::getValue($settings, 'id')) {
            throw new \RuntimeException('"id" is required in LouAssistWidget settings');
        }
        if ($this->enabled === null) {
            $this->enabled = ArrayHelper::getValue($settings, 'enabled', false);
        }
        if ($this->params === null) {
            $this->params = ArrayHelper::getValue($settings, 'params', []);
        }
        if ($this->routes === null) {
            $this->routes = ArrayHelper::getValue($settings, 'routes', []);
        }
    }
}
