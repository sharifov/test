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
 *
 * JS Example: https://codepen.io/anon/pen/LqZYEo
 *
 */
class CallBox extends \yii\bootstrap\Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        //$user_id = \Yii::$app->user->id;
        $newCount = 0; //\common\models\Notifications::findNewCount($user_id);
        //$model = \common\models\Notifications::findNew($user_id);


        return $this->render('call_box', [/*'model' => $model,*/ 'newCount' => $newCount]);
    }
}
