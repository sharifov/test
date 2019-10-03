<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * UserInfoWidget widget
 *
 * @author Alexandr <alex.connor@techork.com>
 */
class UserInfoWidget extends \yii\bootstrap\Widget
{

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $user = Yii::$app->user->identity;
        $userGroups = [];

        if($user->ugsGroups) {
            $userGroups = ArrayHelper::map($user->ugsGroups, 'ug_id', 'ug_name');
        }

        $departments = $user->getUserDepartmentList();

        return $this->render('user_info', ['user' => $user, 'userGroups' => $userGroups, 'departments' => $departments]);
    }
}
