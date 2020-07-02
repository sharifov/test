<?php

namespace sales\model\clientChatData\entity;

/**
 * @see ClientChatData
 */
class Scopes extends \yii\db\ActiveQuery
{
	public function byCchId(int $id): self
	{
		return $this->andWhere(['ccd_cch_id' => $id]);
	}
}
