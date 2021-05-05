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
 *
 * @property Lead $lead
 * @property Product $product
 */
class LeadProduct extends \yii\db\ActiveRecord
{
    public static function create(int $leadId, int $productId): self
    {
        $leadProduct = new self();
        $leadProduct->lp_lead_id = $leadId;
        $leadProduct->lp_product_id = $productId;
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

    public function attributeLabels(): array
    {
        return [
            'lp_lead_id' => 'Lead ID',
            'lp_product_id' => 'Product ID'
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
