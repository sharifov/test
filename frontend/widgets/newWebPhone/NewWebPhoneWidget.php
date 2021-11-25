<?php

namespace frontend\widgets\newWebPhone;

use common\models\Call;
use common\models\Employee;
use common\models\UserCallStatus;
use common\models\UserProfile;
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

        if (!$userProfile || !$userProfile->canWebCall()) {
            return '';
        }

        $availablePhones = new AvailablePhones($this->userId);

        return $this->render('web_phone_new', [
            'formattedPhoneProject' => json_encode($availablePhones->formatPhonesForSelectList()),
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
}
