<?php
namespace frontend\widgets;

use common\models\Call;
use common\models\Employee;
use common\models\UserCallStatus;
use common\models\UserProfile;
use common\models\UserProjectParams;
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

		$userPhoneProject = $this->getUserProjectParams($this->userId);

		if (!$useNewWebPhoneWidget) {
			return '';
		}

		return $this->render('web_phone_new', [
            'formattedPhoneProject' => json_encode($this->formatDataForSelectList($userPhoneProject)),
            'userPhones' => array_keys($this->getUserPhones()),
            'userEmails' => array_keys($this->getUserEmails()),
			'userCallStatus' => UserCallStatus::find()->where(['us_user_id' => $this->userId])->orderBy(['us_id' => SORT_DESC])->limit(1)->one(),
            'countMissedCalls' => Call::find()->byCreatedUser($this->userId)->missed()->count(),
		]);
	}

	private function getUserPhones(): array
    {
        return Employee::getPhoneList($this->userId);
    }

	private function getUserEmails(): array
    {
        return Employee::getEmailList($this->userId);
    }

    private function getUserProjectParams(int $userId)
	{
		return UserProjectParams::find()
			->select(['upp_project_id', 'pl_phone_number', 'p.name as project_name'])
			->byUserId($userId)
			->withExistingPhoneInPhoneList()
			->withProject()
			->asArray()->all();
	}

	private function formatDataForSelectList(array $userProjectPhones): array
	{
		$result = [
			'selected' => [],
			'options' => []
		];

		foreach ($userProjectPhones as $phone) {
			$result['options'][] = [
				'value' => $phone['pl_phone_number'],
				'project' => $phone['project_name'],
				'projectId' => $phone['upp_project_id']
			];
		}

		if (!isset($userProjectPhones[0])) {
            $result['selected']['value'] = '';
            $result['selected']['project'] = 'no number';
            $result['selected']['projectId'] = '';
        } else {
            $result['selected']['value'] = $userProjectPhones[0]['pl_phone_number'];
            $result['selected']['project'] = $userProjectPhones[0]['project_name'];
            $result['selected']['projectId'] = $userProjectPhones[0]['upp_project_id'];
        }

		return $result;
	}
}
