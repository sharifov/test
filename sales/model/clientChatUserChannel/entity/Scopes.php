<?php

namespace sales\model\clientChatUserChannel\entity;

use common\models\Employee;
use common\models\UserOnline;
use common\models\UserProfile;
use sales\model\userClientChatData\entity\UserClientChatData;
use yii\db\Expression;

/**
 * @see ClientChatUserChannel
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byChannelId(int $id): Scopes
    {
        return $this->andWhere(['ccuc_channel_id' => $id]);
    }

    public function byUserId(int $id): Scopes
    {
        return $this->andWhere(['ccuc_user_id' => $id]);
    }

    public function joinUser(): Scopes
    {
        return $this->join('INNER JOIN', Employee::tableName(), 'ccuc_user_id = id');
    }

    public function joinRcProfile(): Scopes
    {
        return $this->join(
            'INNER JOIN',
            UserClientChatData::tableName(),
            new Expression('ccuc_user_id = uccd_employee_id AND (uccd_rc_user_id <> \' \' OR uccd_rc_user_id IS NOT NULL)')
        );
    }

    public function onlineUsers(): Scopes
    {
        return $this->join('INNER JOIN', UserOnline::tableName(), 'ccuc_user_id = uo_user_id');
    }
}
