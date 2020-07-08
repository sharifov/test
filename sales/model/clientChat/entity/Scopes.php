<?php

namespace sales\model\clientChat\entity;

/**
 * @see ClientChat
 */
class Scopes extends \yii\db\ActiveQuery
{
	public function byChannel(int $id): self
	{
		return $this->andWhere(['cch_channel_id' => $id]);
	}

	public function byChannelIds(array $ids): self
	{
		return $this->andWhere(['IN', 'cch_channel_id', $ids]);
	}

	public function byIds(array $ids): self
	{
		return $this->andWhere(['IN', 'cch_id', $ids]);
	}

	public function byOwner(int $userId): self
	{
		return $this->andWhere(['cch_owner_user_id' => $userId]);
	}
}
