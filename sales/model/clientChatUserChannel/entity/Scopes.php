<?php

namespace sales\model\clientChatUserChannel\entity;

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
}
