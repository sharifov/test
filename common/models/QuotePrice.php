<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "quote_price".
 *
 * @property int $id
 * @property int $quote_id
 * @property string $passenger_type
 * @property double $selling
 * @property double $net
 * @property double $fare
 * @property double $taxes
 * @property double $mark_up
 * @property double $extra_mark_up
 * @property string $created
 * @property string $updated
 *
 * @property Quote $quote
 */
class QuotePrice extends \yii\db\ActiveRecord
{
    const
        PASSENGER_ADULT = 'ADT',
        PASSENGER_CHILD = 'CHD',
        PASSENGER_INFANT = 'INF';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quote_id'], 'integer'],
            [['selling', 'net', 'fare', 'taxes', 'mark_up', 'extra_mark_up'], 'number'],
            [['created', 'updated'], 'safe'],
            [['updated'], 'required'],
            [['passenger_type'], 'string', 'max' => 255],
            [['quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::className(), 'targetAttribute' => ['quote_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quote_id' => 'Quote ID',
            'passenger_type' => 'Passenger Type',
            'selling' => 'Selling',
            'net' => 'Net',
            'fare' => 'Fare',
            'taxes' => 'Taxes',
            'mark_up' => 'Mark Up',
            'extra_mark_up' => 'Extra Mark Up',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuote()
    {
        return $this->hasOne(Quote::className(), ['id' => 'quote_id']);
    }

    public function afterValidate()
    {
        $this->updated = date('Y-m-d H:i:s');

        parent::afterValidate();
    }
}
