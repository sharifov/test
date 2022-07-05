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
    /**
     * @var string value with object, that will be passed into abac component for access checking
     */
    public $object;
    /**
     * @var string value with action, that will be passed into abac component for access checking
     */
    public $action;

    /**
     * Checks whether the Web user is allowed to perform the specified action (from point of abac).
     *
     * @param $user
     * @return bool
     */
    public function isAllow($user): bool
    {
        return \Yii::$app->abac->can(null, $this->object, $this->action, $user->identity);
    }
}
