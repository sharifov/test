<?php
namespace frontend\widgets;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\Employee;
use common\models\UserCallStatus;
use common\models\UserProfile;
use common\models\UserProjectParams;
use sales\auth\Auth;
use Yii;
use yii\bootstrap\Widget;
use yii\helpers\VarDumper;

/**
 * Class CallWidget
 *
 * @property int $userId
 */
class CallWidget extends Widget
{
    public $userId;

    public function run(): string
	{
//		$userProfile = UserProfile::find()->where(['up_user_id' => $this->userId])->limit(1)->one();
//		if(!$userProfile || (int) $userProfile->up_call_type_id !== UserProfile::CALL_TYPE_WEB) {
//			return '';
//		}
//
//		$useNewWebPhoneWidget = Yii::$app->params['settings']['use_new_web_phone_widget'] ?? false;
//
//		$userPhoneProject = $this->getUserProjectParams($this->userId);
//
//		if (!$useNewWebPhoneWidget || empty($userPhoneProject)) {
//			return '';
//		}



		return $this->render('call_widget', [

		]);
	}

}
