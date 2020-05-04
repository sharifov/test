<?php
namespace frontend\widgets;

use common\models\Employee;
use common\models\UserProfile;
use common\models\UserProjectParams;
use sales\auth\Auth;
use Yii;
use yii\bootstrap\Widget;

/**
 * Class NewWebPhoneWidget
 *
 * @property int $userId
 */
class NewWebPhoneWidget extends Widget
{
    public $userId;

    public function run(): string
	{
		$userProfile = UserProfile::find()->where(['up_user_id' => $this->userId])->limit(1)->one();
		if(!$userProfile || (int) $userProfile->up_call_type_id !== UserProfile::CALL_TYPE_WEB) {
			return '';
		}

		$useNewWebPhoneWidget = Yii::$app->params['settings']['use_new_web_phone_widget'] ?? false;

		$phoneUserProject = UserProjectParams::find()->select(['upp_project_id', 'pl_phone_number'])->byUserId(Auth::id())->withExistingPhoneInPhoneList()->asArray()->one();

		$phoneFrom = $phoneUserProject['pl_phone_number'] ?? null;
		$userProjectId = $phoneUserProject['upp_project_id'] ?? null;

		if (!$useNewWebPhoneWidget || !$phoneFrom) {
			return '';
		}

		return $this->render('web_phone_new', [
			'phoneFrom' => $phoneFrom,
			'projectId' => $userProjectId,
            'userPhones' => array_keys($this->getUserPhones()),
		]);
	}

	private function getUserPhones(): array
    {
        return Employee::getPhoneList($this->userId);
    }
}