<?php

namespace modules\product\src\entities\productQuoteLead;

use common\models\Lead;
use modules\product\src\entities\productQuote\ProductQuote;
use Yii;

/**
 * This is the model class for table "product_quote_lead".
 *
 * @property int $pql_product_quote_id
 * @property int $pql_lead_id
 *
 * @property Lead $pqlLead
 * @property ProductQuote $pqlProductQuote
 */
class ProductQuoteLead extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['pql_product_quote_id', 'pql_lead_id'], 'unique', 'targetAttribute' => ['pql_product_quote_id', 'pql_lead_id']],

            ['pql_lead_id', 'required'],
            ['pql_lead_id', 'integer'],
            ['pql_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['pql_lead_id' => 'id']],

            ['pql_product_quote_id', 'required'],
            ['pql_product_quote_id', 'integer'],
            ['pql_product_quote_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pql_product_quote_id' => 'pq_id']],
        ];
    }

    public function getPqlLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'pql_lead_id']);
    }

    public function getPqlProductQuote(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pql_product_quote_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'pql_product_quote_id' => 'Product Quote ID',
            'pql_lead_id' => 'Lead ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'product_quote_lead';
    }
}
