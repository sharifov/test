<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\widgets;

use common\models\Call;
use common\models\UserCallStatus;
use common\models\UserProfile;
use yii\helpers\VarDumper;

/**
 * Alert widget renders a message from
 *
 * @author Alexandr <chalpet@gmail.com>
 *
 *
 */
class WebPhone extends \yii\bootstrap\Widget
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $user_id = \Yii::$app->user->id;



        $userProfile = UserProfile::find()->where(['up_user_id' => $user_id])->limit(1)->one();

        //$sipExist = \common\models\UserProjectParams::find()->where(['upp_user_id' => $user_id])->andWhere(['AND', ['IS NOT', 'upp_tw_sip_id', null], ['!=', 'upp_tw_sip_id', '']])->one();



        if(!$userProfile || (int) $userProfile->up_call_type_id !== UserProfile::CALL_TYPE_WEB) {
            return '';
        }


        /*if (\Yii::$app->controller->uniqueId != 'phone') {
            return '';
        }*/

        //VarDumper::dump($userProfile, 10, true);        exit;

        $clientId = 'seller'.$user_id;
        $tokenData = \Yii::$app->communication->getJwtTokenCache($clientId, true);


        if($tokenData && isset($tokenData['data']['token'])) {
            $token = $tokenData['data']['token'];
        } else {
            $token = false;
        }

        return $this->render('web_phone', ['clientId' => $clientId, 'token' => $token]);
    }
}
