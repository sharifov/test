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
    const SCENARIO_CRUD = 'crud';

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

        if (!empty($this->quoteSegments) && count($this->quoteSegments) > 1) {
            $this->stops = count($this->quoteSegments) - 1;
            foreach ($this->quoteSegments as $segment) {
                if (isset($segment->qs_stop) && !empty($segment->qs_stop)) {
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
            [['qt_quote_id'], 'integer'],
            [['qt_quote_id', 'qt_duration', 'qt_key'], 'required', 'on' => [self::SCENARIO_CRUD]],
            [['qt_key'], 'string', 'max' => 255],
            [['qt_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::class, 'targetAttribute' => ['qt_quote_id' => 'id']],
            ['qt_duration', 'integer', 'min' => 0, 'message' => 'Can not add Quote with negative segment duration'],
            //['qt_duration', 'integer', 'min' => - 60 * 24, 'message' => 'Duration must be no less than ' . - 60 * 24 . ' min (24h)'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qt_id' => 'ID',
            'qt_duration' => 'Duration (Min)',
            'qt_key' => 'Key',
            'qt_quote_id' => 'Quote ID',
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
