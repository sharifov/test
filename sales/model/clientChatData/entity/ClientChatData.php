<?php

namespace sales\model\clientChatData\entity;

use sales\model\clientChat\entity\ClientChat;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_chat_data".
 *
 * @property int $ccd_cch_id
 * @property string|null $ccd_country
 * @property string|null $ccd_region
 * @property string|null $ccd_city
 * @property float|null $ccd_latitude
 * @property float|null $ccd_longitude
 * @property string|null $ccd_url
 * @property string|null $ccd_title
 * @property string|null $ccd_referrer
 * @property string|null $ccd_timezone
 * @property string|null $ccd_local_time
 * @property string|null $ccd_created_dt
 * @property string|null $ccd_updated_dt
 *
 * @property ClientChat $ccdCch
 */
class ClientChatData extends \yii\db\ActiveRecord
{
	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['ccd_created_dt', 'ccd_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['ccd_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
			],
		];
	}

	public function rules(): array
    {
        return [
            ['ccd_cch_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientChat::class, 'targetAttribute' => ['ccd_cch_id' => 'cch_id']],

            ['ccd_city', 'string', 'max' => 50],

            ['ccd_country', 'string', 'max' => 50],

            ['ccd_latitude', 'number'],

            ['ccd_local_time', 'string', 'max' => 10],

            ['ccd_longitude', 'number'],

            ['ccd_referrer', 'string', 'max' => 50],

            ['ccd_region', 'string', 'max' => 5],

            ['ccd_timezone', 'string', 'max' => 50],

            ['ccd_title', 'string', 'max' => 50],

            ['ccd_url', 'string', 'max' => 50],

            [['ccd_created_dt', 'ccd_updated_dt'], 'safe'],
        ];
    }

    public function getCcdCch(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ClientChat::class, ['cch_id' => 'ccd_cch_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ccd_cch_id' => 'Client Chat ID',
            'ccd_country' => 'Country',
            'ccd_region' => 'Region',
            'ccd_city' => 'City',
            'ccd_latitude' => 'Latitude',
            'ccd_longitude' => 'Longitude',
            'ccd_url' => 'Url',
            'ccd_title' => 'Title',
            'ccd_referrer' => 'Referrer',
            'ccd_timezone' => 'Timezone',
            'ccd_local_time' => 'Local Time',
            'ccd_created_dt' => 'Created Dt',
            'ccd_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_data';
    }

    public static function createByClientChatRequest(int $cchId, array $data): self
	{
		$_self = new self();
		$_self->ccd_cch_id = $cchId;
		$_self->ccd_country = $data['geo']['country'] ?? '';
		$_self->ccd_region = $data['geo']['region'] ?? '';
		$_self->ccd_city = $data['geo']['city'] ?? '';
		$_self->ccd_latitude = (float)($data['geo']['latitude'] ?? 0);
		$_self->ccd_longitude = (float)($data['geo']['longitude'] ?? 0);
		$_self->ccd_url = $data['page']['url'] ?? '';
		$_self->ccd_title = $data['page']['title'] ?? '';
		$_self->ccd_referrer = $data['page']['referer'] ?? '';
		$_self->ccd_timezone = $data['page']['timezone'] ?? '';
		$_self->ccd_local_time = $data['page']['local_time'] ?? '';

		return $_self;
	}

	public static function getCountryList(): array
    {
        return ArrayHelper::map(self::find()->orderBy(['ccd_country' => SORT_ASC])->distinct()->asArray()->all(),
        'ccd_country', 'ccd_country');
    }

    public static function getCityList(): array
    {
        return ArrayHelper::map(self::find()->orderBy(['ccd_city' => SORT_ASC])->distinct()->asArray()->all(),
        'ccd_city', 'ccd_city');
    }
}
