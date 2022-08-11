<?php

namespace modules\taskList;

use Yii;
use yii\base\Module;

class TaskListModule extends Module
{
    public $controllerNamespace = 'modules\taskList\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/taskList/views');
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
        return Yii::t('modules/taskList/' . $category, $message, $params, $language);
    }
}
