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
            Yii::warning(['message' => 'API: not found username', 'endpoint' => Yii::$app->controller->action->uniqueId, 'username' => $username, 'RemoteIP' => Yii::$app->request->getRemoteIP(), 'UserIP' => Yii::$app->request->getUserIP()], 'API:HttpBasicAuth:ApiUser');
            return null;
        }

        if (!$user->validatePassword($password)) {
            Yii::warning(['message' => 'API: invalid password', 'endpoint' => Yii::$app->controller->action->uniqueId, 'username' => $username, 'password' => $password, 'RemoteIP' => Yii::$app->request->getRemoteIP(), 'UserIP' => Yii::$app->request->getUserIP()], 'API:HttpBasicAuth:ApiUser');
            return null;
        }

        if ($user->isDisabled()) {
            throw new NotAcceptableHttpException('ApiUser is disabled', 10);
        }

        return $user;
    }

    public function authenticate($user, $request, $response)
    {
        if (($identity = parent::authenticate($user, $request, $response)) && $identity instanceof ApiUser) {
            $this->owner->auth = $identity;
        }
        return $identity;
    }
}
