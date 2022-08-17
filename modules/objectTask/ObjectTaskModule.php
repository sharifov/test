<?php

namespace modules\objectTask;

use Yii;

/**
 * ObjectTaskModule module definition class
 */
class ObjectTaskModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\objectTask\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/objectTask/views');
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
        return Yii::t('modules/objectTask/' . $category, $message, $params, $language);
    }
}
