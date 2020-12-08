<?php

namespace sales\model\clientChatUserChannel\entity;

use common\models\Employee;
use common\models\UserOnline;
use common\models\UserProfile;
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

    public function hasRcProfile(): Scopes
    {
        return $this->join(
            'INNER JOIN',
            UserProfile::tableName(),
            new Expression('ccuc_user_id = up_user_id and (up_rc_user_id <> \' \' or up_rc_user_id is not null)')
        );
    }

    public function onlineUsers(): Scopes
    {
        return $this->join('INNER JOIN', UserOnline::tableName(), 'ccuc_user_id = uo_user_id');
    }
}
