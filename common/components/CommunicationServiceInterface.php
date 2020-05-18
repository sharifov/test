<?php
namespace common\components;

use yii\httpclient\Response;

interface CommunicationServiceInterface
{
	public function init(): void;

	public function smsPreview(int $project_id, ?string $template_type, string $phone_from, string $phone_to, array $sms_data = [], ?string $language = 'en-US') : array;

	public function smsSend(int $project_id, ?string $template_type, string $phone_from, string $phone_to, array $content_data = [], array $sms_data = [], ?string $language = 'en-US', ?int $delay = 0) : array;

	public function smsTypes() : array;

	public function smsGetMessages(array $filter = []) : array;

	public function callToPhone(int $project_id, string $phone_from, string $from_number, string $phone_to, string $from_name = '', array $options = []) : array;

	public function updateCall(string $sid, array $updateData = []) : array;

	public function redirectCall(string $sid, array $data = [], string $callBackUrl = '') : array;

	public function getJwtToken($username = '') : array;

	public function getJwtTokenCache($username = '', $deleteCache = false);

	public function callRedirect($cid, $type, $from, $to, $firstTransferToNumber = false);
}