<?php

/**
 * Created by PhpStorm.
 * User: shakarim
 * Date: 6/30/22
 * Time: 2:40 PM
 */

namespace src\model\callLog\abac;

use yii\base\Component;

/**
 * Class CallLogAccessRule
 * @package src\model\callLog\abac
 */
class CallLogAccessRule extends Component
{
    public $action;
    public $object;

    /**
     * @param $user
     * @return bool
     */
    public function isAllow($user): bool
    {
        return \Yii::$app->abac->can(null, $this->object, $this->action, $user->identity);
    }
}
