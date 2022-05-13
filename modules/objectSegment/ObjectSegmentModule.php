<?php

namespace modules\objectSegment;

use yii\base\Module;

class ObjectSegmentModule extends Module
{
    public $controllerNamespace = 'modules\objectSegment\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/objectSegment/views');
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
        return \Yii::t('modules/objectSegment/' . $category, $message, $params, $language);
    }
}
