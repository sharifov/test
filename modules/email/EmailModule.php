<?php

namespace modules\email;

use Yii;
use yii\base\Module;

class EmailModule extends Module
{
    public $controllerNamespace = 'modules\email\controllers\frontend';

    public function init(): void
    {
        parent::init();

        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'modules\email\controllers\console';
        }

        $this->setViewPath('@modules/email/views');
    }

    public static function t($category, $message, $params = [], $language = null): string
    {
        return Yii::t('modules/email/' . $category, $message, $params, $language);
    }
}
