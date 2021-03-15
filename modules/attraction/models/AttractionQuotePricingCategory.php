<?php

namespace modules\attraction\models;

/**
 * This is the model class for table "attraction_quote_pricing_category".
 *
 * @property int $atqpc_id
 * @property int $atqpc_attraction_quote_id
 * @property string|null $atqpc_category_id
 * @property string|null $atqpc_label
 * @property int|null $atqpc_min_age
 * @property int|null $atqpc_max_age
 * @property int|null $atqpc_min_participants
 * @property int|null $atqpc_max_participants
 * @property int|null $atqpc_quantity
 * @property float|null $atqpc_price
 * @property string|null $atqpc_currency
 *
 * @property AttractionQuote $atqpcAttractionQuote
 */
class AttractionQuotePricingCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attraction_quote_pricing_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atqpc_attraction_quote_id'], 'required'],
            [['atqpc_attraction_quote_id', 'atqpc_min_age', 'atqpc_max_age', 'atqpc_min_participants', 'atqpc_max_participants', 'atqpc_quantity'], 'integer'],
            [['atqpc_price'], 'number'],
            [['atqpc_category_id', 'atqpc_label'], 'string', 'max' => 40],
            [['atqpc_currency'], 'string', 'max' => 3],
            [['atqpc_attraction_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => AttractionQuote::class, 'targetAttribute' => ['atqpc_attraction_quote_id' => 'atnq_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'atqpc_id' => 'ID',
            'atqpc_attraction_quote_id' => 'Attraction Quote ID',
            'atqpc_category_id' => 'Category ID',
            'atqpc_label' => 'Label',
            'atqpc_min_age' => 'Min Age',
            'atqpc_max_age' => 'Max Age',
            'atqpc_min_participants' => 'Min Participants',
            'atqpc_max_participants' => 'Max Participants',
            'atqpc_quantity' => 'Quantity',
            'atqpc_price' => 'Price',
            'atqpc_currency' => 'Currency',
        ];
    }

    /**
     * Gets query for [[AtqpcAttractionQuote]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtqpcAttractionQuote()
    {
        return $this->hasOne(AttractionQuote::class, ['atnq_id' => 'atqpc_attraction_quote_id']);
    }
}
