<?php

namespace modules\product\src\entities\productOption;

use common\models\Employee;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productType\ProductType;
use sales\entities\EventTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_option".
 *
 * @property int $po_id
 * @property string $po_key
 * @property int $po_product_type_id
 * @property string $po_name
 * @property string|null $po_description
 * @property int|null $po_price_type_id
 * @property float|null $po_max_price
 * @property float|null $po_min_price
 * @property float|null $po_price
 * @property bool|null $po_enabled
 * @property int|null $po_created_user_id
 * @property int|null $po_updated_user_id
 * @property string|null $po_created_dt
 * @property string|null $po_updated_dt
 *
 * @property Employee $poCreatedUser
 * @property ProductType $poProductType
 * @property Employee $poUpdatedUser
 * @property ProductQuoteOption[] $productQuoteOptions
 */
class ProductOption extends ActiveRecord
{
    use EventTrait;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'product_option';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['po_key', 'po_product_type_id', 'po_name'], 'required'],
            [['po_product_type_id', 'po_price_type_id', 'po_enabled', 'po_created_user_id', 'po_updated_user_id'], 'integer'],
            [['po_description'], 'string'],
            [['po_max_price', 'po_min_price', 'po_price'], 'number'],
            [['po_created_dt', 'po_updated_dt'], 'safe'],
            [['po_key'], 'string', 'max' => 30],
            [['po_name'], 'string', 'max' => 50],
            [['po_key'], 'unique'],
            [['po_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['po_created_user_id' => 'id']],
            [['po_product_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['po_product_type_id' => 'pt_id']],
            [['po_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['po_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'po_id' => 'ID',
            'po_key' => 'Key',
            'po_product_type_id' => 'Product Type',
            'po_name' => 'Name',
            'po_description' => 'Description',
            'po_price_type_id' => 'Price Type',
            'po_max_price' => 'Max Price',
            'po_min_price' => 'Min Price',
            'po_price' => 'Price',
            'po_enabled' => 'Enabled',
            'po_created_user_id' => 'Created User',
            'po_updated_user_id' => 'Updated User',
            'poCreatedUser' => 'Created User',
            'poUpdatedUser' => 'Updated User',
            'po_created_dt' => 'Created Dt',
            'po_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['po_created_dt', 'po_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['po_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'po_created_user_id',
                'updatedByAttribute' => 'po_updated_user_id',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPoCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'po_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPoProductType(): ActiveQuery
    {
        return $this->hasOne(ProductType::class, ['pt_id' => 'po_product_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPoUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'po_updated_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductQuoteOptions(): ActiveQuery
    {
        return $this->hasMany(ProductQuoteOption::class, ['pqo_product_option_id' => 'po_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
