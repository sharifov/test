<?php

namespace webapi\src\behaviors;

use Yii;
use common\models\ApiUser;
use yii\web\NotAcceptableHttpException;

class HttpBasicAuth extends \yii\filters\auth\HttpBasicAuth
{
    public function init(): void
    {
        parent::init();
        $this->auth = [$this, 'verify'];
    }

    public function verify($username, $password): ?ApiUser
    {
        if (!$user = ApiUser::findOne(['au_api_username' => $username])) {
            Yii::warning('API not found username: ' . $username, 'API:HttpBasicAuth:ApiUser');
            return null;
        }

        if (!$user->validatePassword($password)) {
            Yii::warning('API invalid password: ' . $password . ', username: ' . $username . ' ', 'API:HttpBasicAuth:ApiUser');
            return null;
        }

        if ($user->isDisabled()) {
            throw new NotAcceptableHttpException('ApiUser is disabled', 10);
        }

        return $user;
    }
}
