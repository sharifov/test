<?php

namespace frontend\widgets\frontendWidgetList;

use frontend\helpers\JsonHelper;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class FrontendLauncherWidgetList
 *
 * @property array $settings;
 * @property string $content;
 */
class FrontendLauncherWidgetList
{
    private array $settings;
    private string $content = '';

    /**
     * @param array|null $settings
     */
    public function __construct(?array $settings = null)
    {
        $this->settings = $settings ?? JsonHelper::decode(SettingHelper::getFrontendWidgetList());

        $this->runWidgetList();
    }

    private function runWidgetList(): void
    {
        try {
            foreach ($this->settings as $widgetKey => $widgetParams) {
                $settings = SettingHelper::getFrontendWidgetByKey($widgetKey);
                if (!ArrayHelper::getValue($settings, 'enabled', false)) {
                    continue;
                }
                $routes = ArrayHelper::getValue($settings, 'routes', []);
                if (!$this->checkRoute($routes)) {
                    continue;
                }
                if (!$className = self::prepareCheckingClass($widgetKey, $widgetParams)) {
                    continue;
                }

                $params = ArrayHelper::getValue($settings, 'params', []);
                $widgetClass = new $className();

                $this->content .= $widgetClass::widget(['params' => $params]);
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'FrontendLauncherWidgetList:runWidgetList:Throwable');
        }
    }

    private function checkRoute(array $routes): bool
    {
        if (ArrayHelper::isIn('*', $routes)) {
            return true;
        }
        if (ArrayHelper::isIn(Yii::$app->controller->uniqueId . '/*', $routes)) {
            return true;
        }
        if (ArrayHelper::isIn(Yii::$app->controller->action->uniqueId, $routes)) {
            return true;
        }
        return false;
    }

    private static function prepareCheckingClass(string $widgetKey, array $widgetParams): ?string
    {
        try {
            if (!$className = ArrayHelper::getValue($widgetParams, 'className')) {
                throw new \DomainException('Widget (' . $widgetKey . ') could not be initialized. "className" is required');
            }
            if (!class_exists($className)) {
                throw new \DomainException('Widget (' . $widgetKey . ') class (' . $className . ') not exist');
            }
            if (!property_exists($className, 'params')) {
                throw new \DomainException('Class (' . $className . ') must contain "params" property');
            }
        } catch (\Throwable $throwable) {
            \Yii::warning($throwable->getMessage(), 'FrontendLauncherWidgetList:prepareChecking');
            return null;
        }
        return $className;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
