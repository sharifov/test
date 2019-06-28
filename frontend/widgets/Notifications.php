<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;

use common\models\Call;
use common\models\Email;
use common\models\Sms;
use yii\caching\DbDependency;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * Alert widget renders a message from
 *
 * @author Alexandr <chalpet@gmail.com>
 */
class Notifications extends \yii\bootstrap\Widget
{
    private static $instance;

    /**
     * Returns *Notifications* instance of this class.
     *
     * @return Notifications The *Notifications* instance.
     */
    public static function getInstance(): Notifications
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $user_id = \Yii::$app->user->id;

        $cache = \Yii::$app->cache;

        //$newCount = \common\models\Notifications::findNewCount($user_id);

        $sql = \common\models\Notifications::find()->select('COUNT(*)')->where(['n_user_id' => $user_id])->createCommand()->rawSql;
        //echo $sql; exit;


        //$db = \Yii::$app->db;
        $duration = 600;
        $dependency = new DbDependency();
        $dependency->sql = $sql;

        //$dependency = ...;  // optional dependency


        /*$newCount = $db->cache(function ($db) use ($user_id) {
            return \common\models\Notifications::findNewCount($user_id);
        }, $duration, $dependency);

        $model = $db->cache(function ($db) use ($user_id) {
            return \common\models\Notifications::findNew($user_id);
        }, $duration, $dependency);


        $model =  $cache->getOrSet(['top-n-products', 'n' => $count], function ($cache) use ($count) {
            return Products::find()->mostPopular()->limit(10)->all();
        }, 1000);*/


        //$model = null;
        //$newCount = 0;

        $model = \common\models\Notifications::findNew($user_id);
        $newCount = \common\models\Notifications::findNewCount($user_id);

        $newCallCount = 0;
        $newSmsCount = 0;
        $newEmailCount = 0;

        $key = 'notify_' . $user_id;
        //$cache->delete($key);

        $data = $cache->get($key);
        /*if ($data === false) {


            $data['newCallCount'] = Call::find()->where(['c_created_user_id' => $user_id, 'c_call_type_id' => Call::CALL_TYPE_IN, 'c_is_new' => true])->count();
            $data['newSmsCount'] = Sms::find()->where(['s_created_user_id' => $user_id, 's_type_id' => Sms::TYPE_INBOX, 's_is_new' => true])->count();
            $data['newEmailCount'] = Email::find()->where(['e_created_user_id' => $user_id, 'e_type_id' => Email::TYPE_INBOX, 'e_is_new' => true])->count();

            $cache->set($key, $data, $duration, $dependency);
        } else {
            //$model = $data['model'];
            //$newCount = $data['newCount'];
            $newCallCount = $data['newCallCount'];
            $newSmsCount = $data['newSmsCount'];
            $newEmailCount = $data['newEmailCount'];
        }*/

        //$model = \common\models\Notifications::findNew($user_id);
        /*$newCallCount = Call::find()->where(['c_created_user_id' => $user_id, 'c_call_type_id' => Call::CALL_TYPE_IN, 'c_is_new' => true])->count();
        $newSmsCount = Sms::find()->where(['s_created_user_id' => $user_id, 's_type_id' => Sms::TYPE_INBOX, 's_is_new' => true])->count();
        $newEmailCount = Email::find()->where(['e_created_user_id' => $user_id, 'e_type_id' => Email::TYPE_INBOX, 'e_is_new' => true])->count();*/

        //if($newEmailCount > 0) {

        /*$this->view->registerJs("$('#call-inbox-queue').text(" . ($newCallCount ?: '') . ');', \yii\web\View::POS_READY);
        $this->view->registerJs("$('#sms-inbox-queue').text(" . ($newSmsCount ?: '') . ');', \yii\web\View::POS_READY);
        $this->view->registerJs("$('#email-inbox-queue').text(" . ($newEmailCount ?: '') . ');', \yii\web\View::POS_READY);*/


        /*} else {

        }*/

        return $this->render('notifications', ['model' => $model, 'newCount' => $newCount]);
    }
}
