<?php

namespace sales\model\clientChatUserAccess\entity;

/**
 * @see ClientChatUserAccess
 */
class Scopes extends \yii\db\ActiveQuery
{
	public function byUserId(int $userId): self
	{
		return $this->andWhere(['ccua_user_id' => $userId]);
	}

	public function pending(): self
	{
		return $this->andWhere(['ccua_status_id' => ClientChatUserAccess::STATUS_PENDING]);
	}

	public function exceptUser(int $userId): self
	{
		return $this->andWhere(['<>', 'ccua_user_id', $userId]);
	}

	public function byChatId(int $id): self
	{
		return $this->andWhere(['ccua_cch_id' => $id]);
	}

	public function accepted(): self
	{
		return $this->andWhere(['ccua_status_id' => ClientChatUserAccess::STATUS_ACCEPT]);
	}

	public function byClientChat(int $cchId): self
	{
		return $this->andWhere(['ccua_cch_id' => $cchId]);
	}
}
