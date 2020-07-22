<?php

namespace sales\model\ClientChatVisitor\entity;

use common\models\Client;
use sales\model\clientChat\entity\ClientChat;
use sales\model\ClientChatVisitorData\entity\ClientChatVisitorData;

/**
 * This is the model class for table "client_chat_visitor".
 *
 * @property int $ccv_id
 * @property int|null $ccv_cch_id
 * @property int|null $ccv_cvd_id
 * @property int|null $ccv_client_id
 *
 * @property ClientChat $ccvCch
 * @property Client $ccvClient
 * @property ClientChatVisitorData $ccvCvd
 */
class ClientChatVisitor extends \yii\db\ActiveRecord
{
	public function rules(): array
	{
		return [
			[['ccv_cch_id', 'ccv_cvd_id'], 'unique', 'targetAttribute' => ['ccv_cch_id', 'ccv_cvd_id']],

			['ccv_cch_id', 'integer'],
			['ccv_cch_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccv_cch_id' => 'cch_id']],
			['ccv_client_id', 'integer'],
			['ccv_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['ccv_client_id' => 'id']],
			['ccv_cvd_id', 'integer'],
			['ccv_cvd_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChatVisitorData::class, 'targetAttribute' => ['ccv_cvd_id' => 'cvd_id']],
		];
	}

	public function getCcvCch(): \yii\db\ActiveQuery
	{
		return $this->hasOne(ClientChat::class, ['cch_id' => 'ccv_cch_id']);
	}

	public function getCcvClient(): \yii\db\ActiveQuery
	{
		return $this->hasOne(Client::class, ['id' => 'ccv_client_id']);
	}

	public function getCcvCvd(): \yii\db\ActiveQuery
	{
		return $this->hasOne(ClientChatVisitorData::class, ['cvd_id' => 'ccv_cvd_id']);
	}

	public function attributeLabels(): array
	{
		return [
			'ccv_id' => 'ID',
			'ccv_cch_id' => 'Cch ID',
			'ccv_cvd_id' => 'Cvd ID',
			'ccv_client_id' => 'Client ID',
			'ccv_visitor_rc_id' => 'Visitor Rc ID',
		];
	}

	public static function tableName(): string
	{
		return 'client_chat_visitor';
	}

	public static function find(): Scopes
	{
		return new Scopes(static::class);
	}

	public static function create(int $cchId, int $cvdId, ?int $clientId): self
	{
		$_self = new self();
		$_self->ccv_cch_id = $cchId;
		$_self->ccv_cvd_id = $cvdId;
		$_self->ccv_client_id = $clientId;
		return $_self;
	}

//	public static function createByClientChatRequest(int $cchId, int $clientId, string $visitorRcId, array $data): self
//	{
//		$_self = new self();
//		$_self->ccv_cch_id = $cchId;
//		$_self->ccv_visitor_rc_id = $visitorRcId;
//		$_self->ccv_client_id = $clientId;
//		self::fillInData($_self, $data);
//
//		return $_self;
//	}

//	public function updateByClientChatRequest(array $data): void
//	{
//		self::fillInData($this, $data);
//	}

//	private static function fillInData(self $_self, array $data): void
//	{
//		$_self->ccv_country = $data['geo']['country'] ?? '';
//		$_self->ccv_region = $data['geo']['region'] ?? '';
//		$_self->ccv_city = $data['geo']['city'] ?? '';
//		$_self->ccv_latitude = (float)($data['geo']['latitude'] ?? 0);
//		$_self->ccv_longitude = (float)($data['geo']['longitude'] ?? 0);
//		$_self->ccv_url = $data['page']['url'] ?? '';
//		$_self->ccv_title = $data['page']['title'] ?? '';
//		$_self->ccv_referrer = $data['page']['referer'] ?? '';
//		$_self->ccv_timezone = $data['page']['timezone'] ?? '';
//		$_self->ccv_local_time = $data['page']['local_time'] ?? '';
//	}
}
