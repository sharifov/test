<?php

namespace modules\product\src\entities\productQuoteStatusLog;

use common\models\Employee;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuote\ProductQuoteStatusAction;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%product_quote_status_log}}".
 *
 * @property int $pqsl_id
 * @property int $pqsl_product_quote_id
 * @property int|null $pqsl_start_status_id
 * @property int $pqsl_end_status_id
 * @property string $pqsl_start_dt
 * @property string|null $pqsl_end_dt
 * @property int|null $pqsl_duration
 * @property string|null $pqsl_description
 * @property int|null $pqsl_owner_user_id
 * @property int|null $pqsl_created_user_id
 * @property int|null $pqsl_action_id
 *
 * @property Employee $createdUser
 * @property Employee $ownerUser
 * @property ProductQuote $productQuote
 */
class ProductQuoteStatusLog extends \yii\db\ActiveRecord
{
    public static function create(CreateDto $dto): self
    {
        $log = new static();
        $log->pqsl_product_quote_id = $dto->productQuoteId;
        $log->pqsl_start_status_id = $dto->startStatusId;
        $log->pqsl_end_status_id = $dto->endStatusId;
        $log->pqsl_start_dt = date('Y-m-d H:i:s');
        $log->pqsl_description = $dto->description;
        $log->pqsl_action_id = $dto->actionId;
        $log->pqsl_owner_user_id = $dto->ownerId;
        $log->pqsl_created_user_id = $dto->creatorId;
        return $log;
    }

    public function end(): void
    {
        $this->pqsl_end_dt = date('Y-m-d H:i:s');
        $this->pqsl_duration = (int) (strtotime($this->pqsl_end_dt) - strtotime($this->pqsl_start_dt));
    }

    public static function tableName(): string
    {
        return '{{%product_quote_status_log}}';
    }

    public function rules(): array
    {
        return [
            ['pqsl_product_quote_id', 'required'],
            ['pqsl_product_quote_id', 'integer'],
            ['pqsl_product_quote_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqsl_product_quote_id' => 'pq_id']],

            ['pqsl_start_status_id', 'integer'],
            ['pqsl_start_status_id', 'in', 'range' => array_keys(ProductQuoteStatus::getList())],

            ['pqsl_end_status_id', 'required'],
            ['pqsl_end_status_id', 'integer'],
            ['pqsl_end_status_id', 'in', 'range' => array_keys(ProductQuoteStatus::getList())],

            ['pqsl_start_dt', 'required'],
            ['pqsl_start_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['pqsl_end_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['pqsl_duration', 'integer'],

            ['pqsl_description', 'string', 'max' => 255],

            ['pqsl_action_id', 'integer'],
            ['pqsl_action_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['pqsl_action_id', 'in', 'range' => array_keys(ProductQuoteStatusAction::getList())],

            ['pqsl_owner_user_id', 'integer'],
            ['pqsl_owner_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqsl_owner_user_id' => 'id']],

            ['pqsl_created_user_id', 'integer'],
            ['pqsl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqsl_created_user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'pqsl_id' => 'ID',
            'pqsl_product_quote_id' => 'Product Quote ID',
            'productQuote' => 'Product Quote',
            'pqsl_start_status_id' => 'Start Status',
            'pqsl_end_status_id' => 'End Status',
            'pqsl_start_dt' => 'Start Dt',
            'pqsl_end_dt' => 'End Dt',
            'pqsl_duration' => 'Duration',
            'pqsl_description' => 'Description',
            'pqsl_action_id' => 'Action',
            'pqsl_owner_user_id' => 'Owner User',
            'pqsl_created_user_id' => 'Created User',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pqsl_created_user_id']);
    }

    public function getOwnerUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pqsl_owner_user_id']);
    }

    public function getProductQuote(): ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqsl_product_quote_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
