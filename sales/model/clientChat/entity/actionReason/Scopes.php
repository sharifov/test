<?php

namespace sales\model\clientChat\entity\actionReason;

/**
 * @see ClientChatActionReason
 */
class Scopes extends \yii\db\ActiveQuery
{
	public function list(): self
	{
		return $this->select(['ccar_id', 'ccar_action_id', 'ccar_key', 'ccar_name', 'ccar_comment_required']);
	}

	public function byActionId(int $id): self
	{
		return $this->andWhere(['ccar_action_id' => $id]);
	}

	public function enabled(): self
	{
		return $this->andWhere(['ccar_enabled' => 1]);
	}
}
