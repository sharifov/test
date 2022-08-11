<?php

namespace modules\product\src\entities\productQuoteOrigin;

use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;
use Yii;

/**
 * This is the model class for table "product_quote_origin".
 *
 * @property int $pqo_product_id
 * @property int $pqo_quote_id
 *
 * @property Product $product
 * @property ProductQuote $quote
 */
class ProductQuoteOrigin extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['pqo_product_id', 'pqo_quote_id'], 'unique', 'targetAttribute' => ['pqo_product_id', 'pqo_quote_id']],

            ['pqo_product_id', 'required'],
            ['pqo_product_id', 'integer'],
            ['pqo_product_id', 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['pqo_product_id' => 'pr_id']],

            ['pqo_quote_id', 'required'],
            ['pqo_quote_id', 'integer'],
            ['pqo_quote_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqo_quote_id' => 'pq_id']],
        ];
    }

    public function getProduct(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'pqo_product_id']);
    }

    public function getQuote(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqo_quote_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'pqo_product_id' => 'Pqo Product ID',
            'pqo_quote_id' => 'Pqo Quote ID',
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

    public static function create(int $productId, int $quoteId): self
    {
        $self = new self();
        $self->pqo_product_id = $productId;
        $self->pqo_quote_id = $quoteId;
        return $self;
    }
}
