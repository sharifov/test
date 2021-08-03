<?php

namespace modules\flight\models;

use common\components\validators\CheckJsonValidator;
use modules\flight\models\query\FlightRequestQuery;
use Yii;

/**
 * This is the model class for table "flight_request".
 *
 * @property int $fr_id
 * @property string|null $fr_hash
 * @property int $fr_type_id
 * @property string|null $fr_data_json
 * @property int|null $fr_created_api_user_id
 * @property int|null $fr_status_id
 * @property int|null $fr_job_id
 * @property string $fr_created_dt
 * @property string|null $fr_updated_dt
 * @property int $fr_year
 * @property int $fr_month
 */
class FlightRequest extends \yii\db\ActiveRecord
{
    public const TYPE_REPRODUCTION_CREATE = 1;

    public const TYPE_LIST = [
        self::TYPE_REPRODUCTION_CREATE => 'reprotection/create',
    ];

    public const STATUS_NEW = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_ERROR = 3;
    public const STATUS_DONE = 4;

    public const STATUS_LIST = [
        self::STATUS_NEW => 'new',
        self::STATUS_PENDING => 'pending',
        self::STATUS_ERROR => 'error',
        self::STATUS_DONE => 'done',
    ];


    public function rules(): array
    {
        return [
            ['fr_created_api_user_id', 'integer'],

            ['fr_created_dt', 'required'],
            ['fr_created_dt', 'safe'],

            ['fr_data_json', CheckJsonValidator::class],

            ['fr_hash', 'string', 'max' => 32],

            ['fr_id', 'required'],
            ['fr_id', 'integer'],

            ['fr_job_id', 'integer'],

            ['fr_month', 'required'],
            ['fr_month', 'integer'],

            ['fr_status_id', 'integer'],
            ['fr_status_id', 'in', 'range' => array_keys(self::STATUS_LIST)],

            ['fr_type_id', 'required'],
            ['fr_type_id', 'integer'],
            ['fr_type_id', 'in', 'range' => array_keys(self::TYPE_LIST)],

            ['fr_updated_dt', 'safe'],

            ['fr_year', 'required'],
            ['fr_year', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'fr_id' => 'ID',
            'fr_hash' => 'Hash',
            'fr_type_id' => 'Type',
            'fr_data_json' => 'Data Json',
            'fr_created_api_user_id' => 'Created Api User',
            'fr_status_id' => 'Status',
            'fr_job_id' => 'Job ID',
            'fr_created_dt' => 'Created Dt',
            'fr_updated_dt' => 'Updated Dt',
            'fr_year' => 'Year',
            'fr_month' => 'Month',
        ];
    }

    public static function find(): FlightRequestQuery
    {
        return new FlightRequestQuery(static::class);
    }

    public static function tableName(): string
    {
        return 'flight_request';
    }

    public function getTypeName(): ?string
    {
        return self::TYPE_LIST[$this->fr_type_id] ?? null;
    }

    public function getStatusName(): ?string
    {
        return self::TYPE_LIST[$this->fr_status_id] ?? null;
    }
}
