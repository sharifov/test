<?php

namespace modules\featureFlag;

use Yii;
use yii\base\Module;

class FeatureFlagModule extends Module
{
    public $controllerNamespace = 'modules\featureFlag\controllers';

    public function init(): void
    {
        parent::init();
        $this->setViewPath('@modules/featureFlag/views');
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
        return Yii::t('modules/featureFlag/' . $category, $message, $params, $language);
    }
}
