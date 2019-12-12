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
    public $stops = 0;

    /**
     * @param array $attributes
     * @param int $quoteId
     * @return static
     */
    public static function clone(array $attributes, int $quoteId): self
    {
        $trip = new self();
        $trip->attributes = $attributes;
        $trip->qt_quote_id = $quoteId;
        return $trip;
    }

    public function getStops()
    {

        if(!empty($this->quoteSegments) && count($this->quoteSegments) > 1){
            $this->stops = count($this->quoteSegments) - 1;
            foreach ($this->quoteSegments as $segment){
                if(isset($segment->qs_stop) && !empty($segment->qs_stop)){
                    $this->stops += $segment->qs_stop;
                }
            }
        }
        return $this->stops;
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->getStops();
    }

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
            [['qt_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::class, 'targetAttribute' => ['qt_quote_id' => 'id']],
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
        return $this->hasMany(QuoteSegment::class, ['qs_trip_id' => 'qt_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuote()
    {
        return $this->hasOne(Quote::class, ['id' => 'qt_quote_id']);
    }
}
