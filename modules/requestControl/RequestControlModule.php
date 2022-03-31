<?php
/**
 * User: shakarim
 * Date: 3/31/22
 * Time: 6:51 PM
 */

namespace modules\requestControl;

use yii\base\Module;

/**
 * Class RequestControlModule
 * @package modules\requestControl\controllers
 *
 * Entry point of module.
 */
class RequestControlModule extends Module
{
    /**
     * @var string namespace of this module controllers
     *
     * More: https://www.yiiframework.com/doc/guide/2.0/ru/structure-modules
     */
    public $controllerNamespace = 'modules\requestControl\controllers';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        $this->setViewPath('@modules/requestControl/views');
    }
}