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

	public function findByCchId(int $cchId): ?VisitorLog
	{
		return VisitorLog::find()->where(['vl_cch_id' => $cchId])->orderBy(['vl_id' => SORT_DESC])->one();
	}

	public function clone(VisitorLog $visitorLog): VisitorLog
	{
		$_visitorLog = new VisitorLog();
		$_visitorLog->vl_project_id = $visitorLog->vl_project_id;
		$_visitorLog->vl_source_cid = $visitorLog->vl_source_cid;
		$_visitorLog->vl_ga_client_id = $visitorLog->vl_ga_client_id;
		$_visitorLog->vl_ga_user_id = $visitorLog->vl_ga_user_id;
		$_visitorLog->vl_customer_id = $visitorLog->vl_customer_id;
		$_visitorLog->vl_client_id = $visitorLog->vl_client_id;
		$_visitorLog->vl_lead_id = $visitorLog->vl_lead_id;
		$_visitorLog->vl_gclid = $visitorLog->vl_gclid;
		$_visitorLog->vl_dclid = $visitorLog->vl_dclid;
		$_visitorLog->vl_utm_source = $visitorLog->vl_utm_source;
		$_visitorLog->vl_utm_medium = $visitorLog->vl_utm_medium;
		$_visitorLog->vl_utm_campaign = $visitorLog->vl_utm_campaign;
		$_visitorLog->vl_utm_term = $visitorLog->vl_utm_term;
		$_visitorLog->vl_utm_content = $visitorLog->vl_utm_content;
		$_visitorLog->vl_referral_url = $visitorLog->vl_referral_url;
		$_visitorLog->vl_location_url = $visitorLog->vl_location_url;
		$_visitorLog->vl_user_agent = $visitorLog->vl_user_agent;
		$_visitorLog->vl_ip_address = $visitorLog->vl_ip_address;
		$_visitorLog->vl_visit_dt = $visitorLog->vl_visit_dt;
		$_visitorLog->vl_cch_id = $visitorLog->vl_cch_id;
		return $_visitorLog;
	}

}