<?php

namespace sales\model\ClientChatVisitorData\entity;

use yii\db\ActiveQuery;

/**
 * @see ClientChatVisitorData
 */
class Scopes extends ActiveQuery
{
	public function byVisitorId(string $id): ActiveQuery
	{
		return $this->andWhere(['cvd_visitor_rc_id' => $id]);
	}
}
