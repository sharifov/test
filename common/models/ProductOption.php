<?php

namespace common\models;

use common\models\query\ProductOptionQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
 * @property string $priceTypeClassName
 * @property string $priceTypeLabel
 * @property string $priceTypeName
 * @property ProductQuoteOption[] $productQuoteOptions
 */
class ProductOption extends ActiveRecord
{

    public const PRICE_TYPE_AUTO        = 1;
    public const PRICE_TYPE_MANUAL      = 2;
    public const PRICE_TYPE_AUTO_MANUAL = 3;


    public const PRICE_TYPE_LIST        = [
        self::PRICE_TYPE_AUTO           => 'Auto',
        self::PRICE_TYPE_MANUAL         => 'Manual',
        self::PRICE_TYPE_AUTO_MANUAL    => 'Auto & Manual',
    ];

    public const PRICE_TYPE_CLASS_LIST        = [
        self::PRICE_TYPE_AUTO           => 'info',
        self::PRICE_TYPE_MANUAL         => 'warning',
        self::PRICE_TYPE_AUTO_MANUAL    => 'success',
    ];

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'product_option';
    }


    /**
     * @return array
     */
    public function rules()
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
    public function attributeLabels()
    {
        return [
            'po_id' => 'ID',
            'po_key' => 'Key',
            'po_product_type_id' => 'Product Type ID',
            'po_name' => 'Name',
            'po_description' => 'Description',
            'po_price_type_id' => 'Price Type ID',
            'po_max_price' => 'Max Price',
            'po_min_price' => 'Min Price',
            'po_price' => 'Price',
            'po_enabled' => 'Enabled',
            'po_created_user_id' => 'Created User ID',
            'po_updated_user_id' => 'Updated User ID',
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

    /**
     * {@inheritdoc}
     * @return ProductOptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductOptionQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getPriceTypeList(): array
    {
        return self::PRICE_TYPE_LIST;
    }

    /**
     * @return string
     */
    public function getPriceTypeName(): string
    {
        return self::PRICE_TYPE_LIST[$this->po_price_type_id] ?? '';
    }

    /**
     * @return string
     */
    public function getPriceTypeClassName(): string
    {
        return self::PRICE_TYPE_CLASS_LIST[$this->po_price_type_id] ?? '';
    }

    /**
     * @return string
     */
    public function getPriceTypeLabel(): string
    {
        return Html::tag('span', $this->getPriceTypeName(), ['class' => 'badge badge-' . $this->getPriceTypeClassName()]);
    }


    /**
     * @param bool $enabled
     * @param int|null $productTypeId
     * @return array
     */
    public static function getList(bool $enabled = true, ?int $productTypeId = null) : array
    {
        $query = self::find()->orderBy(['po_id' => SORT_ASC]);
        if ($enabled) {
            $query->andWhere(['po_enabled' => true]);
        }

        if ($productTypeId !== null) {
            $query->andWhere(['po_product_type_id' => $productTypeId]);
        }

        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'po_id', 'po_name');
    }
}
