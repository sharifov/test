<?php
namespace webapi\src\repositories\communication;

use common\models\Call;
use common\models\CallUserGroup;
use common\models\ClientPhone;
use sales\repositories\Repository;
use webapi\src\services\communication\RequestDataDTO;
use yii\helpers\VarDumper;

class CommunicationRepository extends Repository
{
	/**
	 * @param string $callSid
	 * @param string|null $parentCallSid
	 * @param RequestDataDTO $requestDataDTO
	 * @param int|null $call_project_id
	 * @param int|null $call_dep_id
	 * @return Call
	 */
	public function findOrCreateCall(string $callSid, ?string $parentCallSid, RequestDataDTO $requestDataDTO, ?int $call_project_id, ?int $call_dep_id): Call
	{
		$call = null;
		$parentCall = null;
		$clientPhone = null;

		if (!empty($requestDataDTO->From)) {
			$clientPhone = ClientPhone::find()->where(['phone' => $requestDataDTO->From])->orderBy(['id' => SORT_DESC])->limit(1)->one();
		}

		if ($callSid) {
			$call = Call::find()->where(['c_call_sid' => $callSid])->limit(1)->one();
		}

		if ($parentCallSid) {
			$parentCall = Call::find()->where(['c_call_sid' => $parentCallSid])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
		}

		if (!$call) {
			$call = Call::createByIncoming($requestDataDTO, $call_project_id, $call_dep_id, $clientPhone->id ?? null);
			if ($parentCall) {
				$call->assignParentCall($parentCall->c_id, $parentCall->c_project_id, $parentCall->c_dep_id, $parentCall->c_source_type_id);

				if ($parentCall->callUserGroups && !$call->callUserGroups) {
					foreach ($parentCall->callUserGroups as $cugItem) {
						$cug = new CallUserGroup();
						$cug->cug_ug_id = $cugItem->cug_ug_id;
						$cug->cug_c_id = $call->c_id;
						if (!$cug->save()) {
							\Yii::error(VarDumper::dumpAsString($cug->errors), 'API:CommunicationController:findOrCreateCall:CallUserGroup:save');
						}
					}
				}
			}
			$this->save($call);
		}
		return $call;
	}

	public function save(Call $call): Call
	{
		if (!$call->save()) {
			\Yii::error(VarDumper::dumpAsString($call->errors), 'API:CommunicationRepository:findOrCreateCall:Call:save');
			throw new \RuntimeException('findOrCreateCall: Can not save call in db', 1);
		}
		return $call;
	}

}