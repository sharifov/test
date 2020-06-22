<?php

namespace sales\model\clientChatChannel\entity;

use sales\auth\Auth;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use yii\db\Expression;

/**
 * @see ClientChatChannel
 */
class Scopes extends \yii\db\ActiveQuery
{
	public function byDepartment(?int $id): self
	{
		return $this->andWhere(['ccc_dep_id' => $id]);
	}

	public function byProject(?int $id): self
	{
		return $this->andWhere(['ccc_project_id' => $id]);
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
}
