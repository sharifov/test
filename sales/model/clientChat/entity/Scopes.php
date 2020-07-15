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

	public function byRid(string $rid): self
	{
		return $this->andWhere(['cch_rid' => $rid]);
	}

	public function notClosed(): self
	{
		return $this->andWhere(['<>', 'cch_status_id', ClientChat::STATUS_CLOSED]);
	}

	public function active(): self
	{
		return $this->notClosed();
	}

	public function archive(): self
	{
		return $this->andWhere(['cch_status_id' => ClientChat::STATUS_CLOSED]);
	}
}
