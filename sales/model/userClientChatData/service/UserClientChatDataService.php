<?php

namespace sales\model\userClientChatData\service;

use common\models\Employee;
use sales\auth\Auth;
use sales\model\userClientChatData\entity\UserClientChatData;
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
}
