<?php

namespace modules\product\src\entities\productQuoteChangeRelation;

use frontend\assets\Select2Asset;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use Yii;

/**
 * This is the model class for table "product_quote_change_relation".
 *
 * @property int $pqcr_pqc_id
 * @property int $pqcr_pq_id
 *
 * @property ProductQuote $pqcrPq
 * @property ProductQuoteChange $pqcrPqc
 * @property-read  ProductQuote|null $newProductQuote
 */
class ProductQuoteChangeRelation extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['pqcr_pqc_id', 'pqcr_pq_id'], 'unique', 'targetAttribute' => ['pqcr_pqc_id', 'pqcr_pq_id']],

            ['pqcr_pq_id', 'required'],
            ['pqcr_pq_id', 'integer'],
            ['pqcr_pq_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqcr_pq_id' => 'pq_id']],

            ['pqcr_pqc_id', 'required'],
            ['pqcr_pqc_id', 'integer'],
            ['pqcr_pqc_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuoteChange::class, 'targetAttribute' => ['pqcr_pqc_id' => 'pqc_id']],
        ];
    }

    public function getPqcrPq(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqcr_pq_id']);
    }

    /**
     * Returns product quote in status NEW
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewProductQuote(): \yii\db\ActiveQuery
    {
        return $this->getPqcrPq()->where(['pq_status_id' => ProductQuoteStatus::NEW]);
    }

    public function getPqcrPqc(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuoteChange::class, ['pqc_id' => 'pqcr_pqc_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'pqcr_pqc_id' => 'ProductQuoteChange ID',
            'pqcr_pq_id' => 'ProductQuote ID',
        ];
    }

    public static function find(): ProductQuoteChangeRelationQueryScopes
    {
        return new ProductQuoteChangeRelationQueryScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'product_quote_change_relation';
    }

    public static function create(int $productQuoteChangeId, int $productQuoteId): ProductQuoteChangeRelation
    {
        $model = new self();
        $model->pqcr_pqc_id = $productQuoteChangeId;
        $model->pqcr_pq_id = $productQuoteId;
        return $model;
    }
}
