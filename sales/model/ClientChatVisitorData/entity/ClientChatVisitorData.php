<?php

namespace sales\model\ClientChatVisitorData\entity;

use sales\model\clientChat\entity\ClientChat;
use sales\model\ClientChatVisitor\entity\ClientChatVisitor;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "client_chat_visitor_data".
 *
 * @property int $cvd_id
 * @property string|null $cvd_visitor_rc_id
 * @property string|null $cvd_country
 * @property string|null $cvd_region
 * @property string|null $cvd_city
 * @property float|null $cvd_latitude
 * @property float|null $cvd_longitude
 * @property string|null $cvd_url
 * @property string|null $cvd_title
 * @property string|null $cvd_referrer
 * @property string|null $cvd_timezone
 * @property string|null $cvd_local_time
 * @property string|null $cvd_data
 * @property string|null $cvd_created_dt
 * @property string|null $cvd_updated_dt
 *
 * @property ClientChat[] $ccvCches
 * @property ClientChatVisitor[] $clientChatVisitors
 */
class ClientChatVisitorData extends \yii\db\ActiveRecord
{
	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['cvd_created_dt', 'cvd_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['cvd_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
			],
		];
	}

    public function rules(): array
    {
        return [

            ['cvd_city', 'string', 'max' => 50],

            ['cvd_country', 'string', 'max' => 50],

            ['cvd_created_dt', 'safe'],

            ['cvd_data', 'safe'],

            ['cvd_latitude', 'number'],

            ['cvd_local_time', 'string', 'max' => 10],

            ['cvd_longitude', 'number'],

            ['cvd_referrer', 'string', 'max' => 255],

            ['cvd_region', 'string', 'max' => 5],

            ['cvd_timezone', 'string', 'max' => 50],

            ['cvd_title', 'string', 'max' => 50],

            ['cvd_updated_dt', 'safe'],

            ['cvd_url', 'string', 'max' => 255],

			['cvd_visitor_rc_id', 'string', 'max' => 50],
			['cvd_visitor_rc_id', 'unique'],
		];
    }

    public function getCcvCches(): \yii\db\ActiveQuery
    {
		return $this->hasMany(ClientChat::class, ['cch_id' => 'ccv_cch_id'])->viaTable('client_chat_visitor', ['ccv_cvd_id' => 'cvd_id']);
    }

	public function getClientChatVisitors(): \yii\db\ActiveQuery
	{
		return $this->hasMany(ClientChatVisitor::class, ['ccv_cvd_id' => 'cvd_id']);
	}

    public function attributeLabels(): array
    {
        return [
            'cvd_id' => 'ID',
            'cvd_ccv_id' => 'Ccv ID',
            'cvd_country' => 'Country',
            'cvd_region' => 'Region',
            'cvd_city' => 'City',
            'cvd_latitude' => 'Latitude',
            'cvd_longitude' => 'Longitude',
            'cvd_url' => 'Url',
            'cvd_title' => 'Title',
            'cvd_referrer' => 'Referrer',
            'cvd_timezone' => 'Timezone',
            'cvd_local_time' => 'Local Time',
            'cvd_data' => 'Data',
            'cvd_created_dt' => 'Created Dt',
            'cvd_updated_dt' => 'Updated Dt',
			'cvd_visitor_rc_id' => 'Cvd Visitor Rc ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_visitor_data';
    }

	public static function createByClientChatRequest(string $visitorRcId, array $data): self
	{
		$_self = new self();
		$_self->cvd_visitor_rc_id = $visitorRcId;
		self::fillInData($_self, $data);

		return $_self;
	}

	public function updateByClientChatRequest(array $data): void
	{
		self::fillInData($this, $data);
	}

	/**
	 * @param ClientChatVisitorData $_self
	 * @param array $data
	 */
	private static function fillInData(self $_self, array $data): void
	{
		$_self->cvd_country = $data['geo']['country'] ?? '';
		$_self->cvd_region = $data['geo']['region'] ?? '';
		$_self->cvd_city = $data['geo']['city'] ?? '';
		$_self->cvd_latitude = (float)($data['geo']['latitude'] ?? 0);
		$_self->cvd_longitude = (float)($data['geo']['longitude'] ?? 0);
		$_self->cvd_url = $data['page']['url'] ?? '';
		$_self->cvd_title = $data['page']['title'] ?? '';
		$_self->cvd_referrer = $data['page']['referer'] ?? '';
		$_self->cvd_timezone = $data['page']['timezone'] ?? '';
		$_self->cvd_local_time = $data['page']['local_time'] ?? '';
		$_self->cvd_data = Json::encode($data);
	}
}
