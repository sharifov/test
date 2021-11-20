<?php

namespace frontend\widgets\webPhone;

use common\models\UserProfile;
use yii\base\Widget;

/**
 * Class WebPhoneWidget
 *
 * @property $userId
 */
class WebPhoneWidget extends Widget
{
    public $userId;

    public function run()
    {
        $userProfile = UserProfile::find()->where(['up_user_id' => $this->userId])->limit(1)->one();

        if (!$userProfile || !$userProfile->canWebCall()) {
            return '';
        }

        return $this->render('web_phone_widget', [
            'userId' => $this->userId,
        ]);
    }
}
