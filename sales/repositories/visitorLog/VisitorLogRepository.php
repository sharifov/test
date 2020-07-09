<?php
namespace sales\repositories\visitorLog;

use common\models\VisitorLog;
use sales\repositories\Repository;
use yii\helpers\VarDumper;

class VisitorLogRepository extends Repository
{
	public function createByClientChatRequest($clientChat, array $data): void
	{
		$visitorLog = VisitorLog::createByClientChatRequest($clientChat, $data);
		if (!$visitorLog->validate()) {
			foreach ($visitorLog->errors as $attribute => $error) {
				$visitorLog->{$attribute} = null;
			}
			\Yii::error('VisitorLog validation failed: ' . VarDumper::dumpAsString($visitorLog->errors), 'ClientChatRequestService::guestConnected::visitorLog::validation');
		}

		try {
			$this->save($visitorLog);
		} catch (\RuntimeException $e) {
			\Yii::error('VisitorLog save failed: ' . VarDumper::dumpAsString($visitorLog->errors), 'ClientChatRequestService::guestConnected::visitorLog::save');
		}
	}

	public function save(VisitorLog $visitorLog): VisitorLog
	{
		if (!$visitorLog->save(false)) {
			throw new \RuntimeException('Visitor log saving failed');
		}
		return $visitorLog;
	}

}