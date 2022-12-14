<?php

namespace src\model\clientChatChannel\entity;

use src\auth\Auth;
use src\model\clientChatUserChannel\entity\ClientChatUserChannel;
use yii\db\Expression;

/**
 * @see ClientChatChannel
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byDepartment(?int $id): self
    {
        if ($id) {
            return $this->andWhere(['ccc_dep_id' => $id]);
        }
        return $this->andWhere(new Expression('ccc_dep_id is NULL'));
    }

    public function byProject(?int $id): self
    {
        if ($id) {
            return $this->andWhere(['ccc_project_id' => $id]);
        }
        return $this->andWhere(new Expression('ccc_project_id is NULL'));
    }

    public function exceptDepartment(int $departmentId): Scopes
    {
        return $this->andWhere(['<>', 'ccc_dep_id', $departmentId]);
    }

    public function exceptChannels(array $channels): Scopes
    {
        return $this->andWhere(['not in', 'ccc_id', $channels]);
    }

    public function priority(int $priority)
    {
        return $this->andWhere(['ccc_priority' => $priority]);
    }

    public function joinWithCcuc(int $userId): self
    {
        return $this->join('join', ClientChatUserChannel::tableName(), new Expression('ccc_id = ccuc_channel_id and ccuc_user_id = :userId', ['userId' => $userId]));
    }

    public function byChannel(int $id): self
    {
        return $this->andWhere(['ccc_id' => $id]);
    }

    public function enabled(): self
    {
        return $this->andWhere(['ccc_disabled' => false]);
    }
}
