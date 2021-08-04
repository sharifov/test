<?php

namespace modules\flight\models;

use common\components\validators\CheckJsonValidator;
use modules\flight\models\query\FlightRequestQuery;
use sales\behaviors\StringToJsonBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
 * @property string $fr_booking_id
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
            ['fr_booking_id', 'required'],
            ['fr_booking_id', 'string', 'max' => 10],

            ['fr_created_api_user_id', 'integer'],

            ['fr_created_dt', 'required'],
            ['fr_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

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

            ['fr_updated_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['fr_year', 'required'],
            ['fr_year', 'integer'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => false,
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['fr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'stringToJson' => [
                'class' => StringToJsonBehavior::class,
                'jsonColumn' => 'fr_data_json',
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function attributeLabels(): array
    {
        return [
            'fr_id' => 'ID',
            'fr_booking_id' => 'Booking ID',
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
        return self::STATUS_LIST[$this->fr_status_id] ?? null;
    }

    /**
     * @param string $booking_id
     * @param int $type_id
     * @param array $data_json
     * @param int|null $created_api_user_id
     * @return FlightRequest
     */
    public static function create(
        string $booking_id,
        int $type_id,
        $data_json,
        ?int $created_api_user_id
    ): FlightRequest {

        $model = new self();
        $model->fr_booking_id = $booking_id;
        $model->fr_type_id = $type_id;
        $model->fr_status_id = self::STATUS_NEW;
        $model->fr_data_json = $data_json;
        $model->fr_created_api_user_id = $created_api_user_id;

        $model->fr_hash = self::generateHashFromDataJson($data_json);

        $model->fr_created_dt = date('Y-m-d H:i:s');
        $createdDt = strtotime($model->fr_created_dt);
        $model->fr_year = date('Y', $createdDt);
        $model->fr_month = date('m', $createdDt);

        return $model;
    }

    public static function generateHashFromDataJson(array $dataJson): string
    {
        return md5(serialize($dataJson));
    }

    public function statusToPending(): FlightRequest
    {
        $this->fr_status_id = self::STATUS_PENDING;
        return $this;
    }

    public function statusToError(): FlightRequest
    {
        $this->fr_status_id = self::STATUS_ERROR;
        return $this;
    }

    public function statusToDone(): FlightRequest
    {
        $this->fr_status_id = self::STATUS_DONE;
        return $this;
    }
}
