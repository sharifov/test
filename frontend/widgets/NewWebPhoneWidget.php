<?php
namespace frontend\widgets;

use common\models\UserProfile;
use common\models\UserProjectParams;
use Yii;
use yii\bootstrap\Widget;

class NewWebPhoneWidget extends Widget
{
	public function init()
	{
		parent::init();
	}

	public function run()
	{
		$user_id = Yii::$app->user->id;

		$userProfile = UserProfile::find()->where(['up_user_id' => $user_id])->limit(1)->one();
		if(!$userProfile || (int) $userProfile->up_call_type_id !== UserProfile::CALL_TYPE_WEB) {
			return '';
		}

		$useNewWebPhoneWidget = Yii::$app->params['settings']['use_new_web_phone_widget'] ?? false;

		$phoneUserProject = UserProjectParams::find()->select(['upp_project_id', 'pl_phone_number'])->withExistedPhoneInPhoneList()->asArray()->one();

		$phoneFrom = $phoneUserProject['pl_phone_number'] ?? null;
		$userProjectId = $phoneUserProject['upp_project_id'] ?? null;

		if (!$useNewWebPhoneWidget || !$phoneFrom) {
			return '';
		}

		return $this->render('web_phone_new', [
			'phoneFrom' => $phoneFrom,
			'projectId' => $userProjectId
		]);
	}
}