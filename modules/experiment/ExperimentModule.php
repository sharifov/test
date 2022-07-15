<?php

namespace modules\experiment;

use yii\helpers\ArrayHelper;

/**
 * Class ExperimentModule
 *
 */
class ExperimentModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\experiment\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->setViewPath('@modules/experiment/views');
    }

    /**
     * @param string $category
     * @param string $message
     * @param array $params
     * @param null|string $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null): string
    {
        return \Yii::t('modules/experiment/' . $category, $message, $params, $language);
    }

    /**
     * @param string $modulePath
     * @return array
     */
    public static function getListMenu(string $modulePath = 'experiment'): array
    {
        $items = [
            ['label' => 'Experiments target relations', 'url' => ['/' . $modulePath . '/experiment-target-crud/index']],
            ['label' => 'Experiments list', 'url' => ['/' . $modulePath . '/experiment-crud/index']],
        ];
        return $items;
    }
}
