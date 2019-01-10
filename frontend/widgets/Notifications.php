<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;

/**
 * Alert widget renders a message from
 *
 * @author Alexandr <chalpet@gmail.com>
 */
class Notifications extends \yii\bootstrap\Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $user_id = \Yii::$app->user->id;
        $newCount = \common\models\Notifications::findNewCount($user_id);
        $model = \common\models\Notifications::findNew($user_id);


        return $this->render('notifications', ['model' => $model, 'newCount' => $newCount]);
    }
}
