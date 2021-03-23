<?php

namespace sales\model\userClientChatData\service;

use common\models\Employee;
use sales\auth\Auth;
use sales\model\userClientChatData\entity\UserClientChatData;
use sales\services\clientChat\ClientChatRequesterService;
use yii\db\Expression;

/**
 * Class UserClientChatDataService
 */
class UserClientChatDataService
{
    public static function getCurrentRcUserId(): ?string
    {
        if (!$userClientChatData = self::getCurrentUserChatData()) {
            return null;
        }
        return $userClientChatData->uccd_rc_user_id;
    }

    public static function getCurrentAuthToken(): ?string
    {
        if (!$userClientChatData = self::getCurrentUserChatData()) {
            return null;
        }
        return $userClientChatData->uccd_auth_token;
    }

    public static function getUserChatData(?Employee $user): ?UserClientChatData
    {
        if (!$user) {
            return null;
        }
        return $user->userClientChatData ?? null;
    }

    public static function getCurrentUserChatData(): ?UserClientChatData
    {
        return self::getUserChatData(Auth::user());
    }

    public static function getUserList(): array
    {
        return Employee::find()
            ->select([
                new Expression('CONCAT_WS(\' - \', username, email) AS employee'),
                'id'
            ])
            ->leftJoin(UserClientChatData::tableName(), 'id = uccd_employee_id')
            ->andWhere(['IS', 'uccd_id', null])
            ->orderBy(['username' => SORT_ASC])
            ->indexBy('id')
            ->asArray()
            ->column();
    }

    public function refreshRocketChatUserToken(UserClientChatData $userClientChatData): void
    {
        if (!$rocketUsername = $userClientChatData->uccd_username) {
            throw new \RuntimeException('Not found Username for this user(' . $userClientChatData->uccd_employee_id . ')');
        }
        if (!$rocketPassword = $userClientChatData->uccd_password) {
            throw new \RuntimeException('Not found Rocket Chat Auth Password for this user(' . $userClientChatData->uccd_employee_id . ')');
        }

        $authToken = ClientChatRequesterService::refreshToken($rocketUsername, $rocketPassword);
        $userClientChatData->uccd_auth_token = $authToken;
        $userClientChatData->uccd_token_expired = \Yii::$app->rchat::generateTokenExpired();

        if (!$userClientChatData->save(false)) {
            throw new \RuntimeException($userClientChatData->getErrorSummary(false)[0]);
        }
    }
}
