<?php

namespace modules\order\src\entities\orderRequest;

use modules\order\src\entities\order\OrderSourceType;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "order_request".
 *
 * @property int $orr_id
 * @property string|null $orr_request_data_json
 * @property string|null $orr_response_data_json
 * @property int|null $orr_source_type_id
 * @property int|null $orr_response_type_id
 * @property string|null $orr_created_dt
 */
class OrderRequest extends \yii\db\ActiveRecord
{
    public const RESPONSE_TYPE_SUCCESS = 1;
    public const RESPONSE_TYPE_ERROR = 2;

    public const RESPONSE_TYPE_LIST = [
        self::RESPONSE_TYPE_SUCCESS => 'Success',
        self::RESPONSE_TYPE_ERROR => 'Error'
    ];

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['orr_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'creditCard' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['orr_request_data_json'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['orr_request_data_json'],
                ],
                'value' => static function ($event) {
                    $requestData = Json::decode($event->sender->orr_request_data_json);
                    $filter = new CreditCardFilter();
                    return Json::encode($filter->filterData($requestData));
                }
            ]
        ];
    }

    public function rules(): array
    {
        return [
            ['orr_created_dt', 'safe'],

            ['orr_request_data_json', 'safe'],

            ['orr_response_data_json', 'safe'],

            ['orr_response_type_id', 'integer'],

            ['orr_source_type_id', 'integer'],

            ['orr_source_type_id', 'in', 'range' => array_keys(OrderSourceType::LIST)],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'orr_id' => 'Orr ID',
            'orr_request_data_json' => 'Orr Request Data Json',
            'orr_response_data_json' => 'Orr Response Data Json',
            'orr_source_type_id' => 'Orr Source Type ID',
            'orr_response_type_id' => 'Orr Response Type ID',
            'orr_created_dt' => 'Orr Created Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'order_request';
    }

    public static function create(array $requestData, int $sourceType): self
    {
        $request = new self();
        $request->orr_request_data_json = Json::encode($requestData);
        $request->orr_source_type_id = $sourceType;
        return $request;
    }

    public function successResponse(array $data): void
    {
        $this->orr_response_type_id = self::RESPONSE_TYPE_SUCCESS;
        $this->orr_response_data_json = Json::encode($data);
    }

    public function errorResponse(array $data): void
    {
        $this->orr_response_type_id = self::RESPONSE_TYPE_ERROR;
        $this->orr_response_data_json = Json::encode($data);
    }

    public function getSourceName(): string
    {
        return OrderSourceType::LIST[$this->orr_source_type_id] ?? 'Unknown';
    }

    public function getResponseType(): string
    {
        return self::RESPONSE_TYPE_LIST[$this->orr_response_type_id] ?? 'Unknown';
    }
}
