<?php

namespace modules\product\src\entities\productQuoteData;

use modules\product\src\entities\productQuote\ProductQuote;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_quote_data".
 *
 * @property int $pqd_id
 * @property int $pqd_product_quote_id
 * @property int $pqd_key
 * @property string|null $pqd_value
 * @property string|null $pqd_created_dt
 * @property string|null $pqd_updated_dt
 *
 * @property ProductQuote $pqdProductQuote
 */
class ProductQuoteData extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pqd_created_dt', 'pqd_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pqd_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_quote_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pqd_product_quote_id', 'pqd_key'], 'required'],
            [['pqd_product_quote_id', 'pqd_key'], 'integer'],
            [['pqd_created_dt', 'pqd_updated_dt'], 'safe'],
            [['pqd_value'], 'string', 'max' => 50],
            [['pqd_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqd_product_quote_id' => 'pq_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pqd_id' => Yii::t('app', 'ID'),
            'pqd_product_quote_id' => Yii::t('app', 'Product Quote ID'),
            'pqd_key' => Yii::t('app', 'Key'),
            'pqd_value' => Yii::t('app', 'Value'),
            'pqd_created_dt' => Yii::t('app', 'Created Dt'),
            'pqd_updated_dt' => Yii::t('app', 'Updated Dt'),
        ];
    }

    /**
     * Gets query for [[PqdProductQuote]].
     *
     * @return ActiveQuery
     */
    public function getPqdProductQuote(): ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqd_product_quote_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function create(int $productQuoteId): self
    {
        $self = new self();
        $self->pqd_product_quote_id = $productQuoteId;
        return $self;
    }

    public static function createRecommended(int $productQuoteId): self
    {
        $self = self::create($productQuoteId);
        $self->setRecommended();
        return $self;
    }

    public function setRecommended(): void
    {
        $this->pqd_key = ProductQuoteDataKey::RECOMMENDED;
        $this->pqd_value = ProductQuoteDataKey::getValueByKey($this->pqd_key);
    }

    public function isRecommended(): bool
    {
        return $this->pqd_value === ProductQuoteDataKey::getValueByKey(ProductQuoteDataKey::RECOMMENDED);
    }

    public static function createConfirmed(int $productQuoteId): self
    {
        $self = self::create($productQuoteId);
        $self->setConfirmed();
        return $self;
    }

    public function setConfirmed(): void
    {
        $this->pqd_key = ProductQuoteDataKey::CONFIRMED;
        $this->pqd_value = ProductQuoteDataKey::getValueByKey($this->pqd_key);
    }

    public function isConfirmed(): bool
    {
        return $this->pqd_value === ProductQuoteDataKey::getValueByKey(ProductQuoteDataKey::CONFIRMED);
    }
}
