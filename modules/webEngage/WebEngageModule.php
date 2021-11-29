<?php

namespace modules\webEngage;

use Yii;
use yii\base\Module;

/**
 * Class WebEngageModules
 */
class WebEngageModule extends Module
{
    public $controllerNamespace = 'modules\webEngage\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/webEngage/views');
    }
}
