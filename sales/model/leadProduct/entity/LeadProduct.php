<?php

namespace sales\model\leadProduct\entity;

use common\models\Lead;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * This is the model class for table "lead_product".
 *
 * @property int $lp_lead_id
 * @property int $lp_product_id
 * @property int $lp_quote_id
 *
 * @property Lead $lead
 * @property Product $product
 * @property ProductQuote $quote
 */
class LeadProduct extends \yii\db\ActiveRecord
{
    public static function create(int $leadId, int $productId, int $quoteId): self
    {
        $leadProduct = new self();
        $leadProduct->lp_lead_id = $leadId;
        $leadProduct->lp_product_id = $productId;
        $leadProduct->lp_quote_id = $quoteId;
        return $leadProduct;
    }

    public function rules(): array
    {
        return [
            ['lp_lead_id', 'required'],
            ['lp_lead_id', 'integer'],
            ['lp_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lp_lead_id' => 'id']],

            ['lp_product_id', 'required'],
            ['lp_product_id', 'integer'],
            ['lp_product_id', 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['lp_product_id' => 'pr_id']],

            [['lp_lead_id', 'lp_product_id'], 'unique'],

            ['lp_quote_id', 'required'],
            ['lp_quote_id', 'integer'],
            ['lp_quote_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['lp_quote_id' => 'pq_id']],
        ];
    }

    public function getLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'lp_lead_id']);
    }

    public function getProduct(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'lp_product_id']);
    }

    public function getProductQuote(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'lp_quote_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'lp_lead_id' => 'Lead ID',
            'lp_product_id' => 'Product ID',
            'lp_quote_id' => 'Quote ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%lead_product}}';
    }
}
