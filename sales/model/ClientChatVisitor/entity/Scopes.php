<?php

namespace sales\model\ClientChatVisitor\entity;

use yii\db\ActiveQuery;

/**
 * @see ClientChatVisitor
 */
class Scopes extends ActiveQuery
{
	public function byVisitorRcId(string $id): ActiveQuery
	{
		return $this->andWhere(['ccv_visitor_rc_id' => $id]);
	}
}
