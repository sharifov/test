<?php

namespace modules\smartLeadDistribution;

use Yii;
use yii\base\Module;

class SmartLeadDistributionModule extends Module
{
    public $controllerNamespace = 'modules\smartLeadDistribution\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/smartLeadDistribution/views');
    }

    /**
     * @param string $category
     * @param string $message
     * @param array $params
     * @param string|null $language
     * @return string
     */
    public static function t(string $category, string $message, array $params = [], ?string $language = null): string
    {
        return Yii::t('modules/smartLeadDistribution/' . $category, $message, $params, $language);
    }
}
