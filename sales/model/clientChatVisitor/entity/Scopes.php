<?php

namespace sales\model\clientChatVisitor\entity;

use yii\db\ActiveQuery;

/**
 * @see ClientChatVisitor
 */
class Scopes extends ActiveQuery
{
	public function byUniqueFields(int $cchId, int $cvdId): self
	{
		return $this->andWhere(['ccv_cch_id' => $cchId, 'ccv_cvd_id' => $cvdId]);
	}
}
