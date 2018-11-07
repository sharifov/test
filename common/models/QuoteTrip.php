<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "quote_trip".
 *
 * @property int $qt_id
 * @property int $qt_duration
 * @property string $qt_key
 * @property int $qt_quote_id
 *
 * @property QuoteSegment[] $quoteSegments
 * @property Quote $quote
 */
class QuoteTrip extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_trip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qt_duration', 'qt_quote_id'], 'integer'],
            [['qt_key'], 'string', 'max' => 255],
            [['qt_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::className(), 'targetAttribute' => ['qt_quote_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qt_id' => 'Qt ID',
            'qt_duration' => 'Qt Duration',
            'qt_key' => 'Qt Key',
            'qt_quote_id' => 'Qt Quote ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteSegments()
    {
        return $this->hasMany(QuoteSegment::className(), ['qs_trip_id' => 'qt_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuote()
    {
        return $this->hasOne(Quote::className(), ['id' => 'qt_quote_id']);
    }
}
