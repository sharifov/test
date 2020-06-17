<?php

namespace sales\model\clientChatRequest\entity;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_chat_request".
 *
 * @property int $ccr_id
 * @property string|null $ccr_event
 * @property string|null $ccr_json_data
 * @property string|null $ccr_created_dt
 */
class ClientChatRequest extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['ccr_created_dt', 'safe'],

            ['ccr_event', 'string', 'max' => 50],

            ['ccr_json_data', 'string'],
        ];
    }

	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['ccr_created_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
			],
		];
	}

    public function attributeLabels(): array
    {
        return [
            'ccr_id' => 'ID',
            'ccr_event' => 'Event',
            'ccr_json_data' => 'Json Data',
            'ccr_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_request';
    }
}
