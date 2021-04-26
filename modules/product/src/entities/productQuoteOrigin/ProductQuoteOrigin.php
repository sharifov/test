<?php

namespace modules\product\src\entities\productQuoteOrigin;

use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;
use Yii;

/**
 * This is the model class for table "product_quote_origin".
 *
 * @property int $pqa_product_id
 * @property int $pqa_quote_id
 *
 * @property Product $pqaProduct
 * @property ProductQuote $pqaQuote
 */
class ProductQuoteOrigin extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['pqa_product_id', 'pqa_quote_id'], 'unique', 'targetAttribute' => ['pqa_product_id', 'pqa_quote_id']],

            ['pqa_product_id', 'required'],
            ['pqa_product_id', 'integer'],
            ['pqa_product_id', 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['pqa_product_id' => 'pr_id']],

            ['pqa_quote_id', 'required'],
            ['pqa_quote_id', 'integer'],
            ['pqa_quote_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqa_quote_id' => 'pq_id']],
        ];
    }

    public function getPqaProduct(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'pqa_product_id']);
    }

    public function getPqaQuote(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqa_quote_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'pqa_product_id' => 'Product ID',
            'pqa_quote_id' => 'Quote ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'product_quote_origin';
    }

    public function create(int $productId, int $quoteId): self
    {
        $self = new self();
        $self->pqa_product_id = $productId;
        $self->pqa_quote_id = $quoteId;
        return $self;
    }
}
