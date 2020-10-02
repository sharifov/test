<?php


namespace sales\model\clientChat\entity\actionReason;


use sales\model\clientChat\useCase\close\ReasonDto;

class ClientChatActionReasonQuery
{
	/**
	 * @param int $actionId
	 * @return ReasonDto[]
	 */
	public static function getReasons(int $actionId): array
	{
		$reasons = [];
		foreach (ClientChatActionReason::find()->list()->byActionId($actionId)->enabled()->asArray()->all() as $reason) {
			$reasons[$reason['ccar_id']] = new ReasonDto((int)$reason['ccar_id'], $reason['ccar_name'], (bool)$reason['ccar_comment_required']);
		}
		return $reasons;
	}
}