<?php

namespace modules\flight\models;

use Yii;

/**
 * This is the model class for table "flight_quote_segment_pax_baggage".
 *
 * @property int $qsb_id
 * @property int $qsb_flight_pax_code_id
 * @property int $qsb_flight_quote_segment_id
 * @property string|null $qsb_airline_code
 * @property int|null $qsb_allow_pieces
 * @property int|null $qsb_allow_weight
 * @property string|null $qsb_allow_unit
 * @property string|null $qsb_allow_max_weight
 * @property string|null $qsb_allow_max_size
 *
 * @property FlightQuoteSegment $qsbFlightQuoteSegment
 */
class FlightQuoteSegmentPaxBaggage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote_segment_pax_baggage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsb_flight_pax_code_id', 'qsb_flight_quote_segment_id'], 'required'],
            [['qsb_flight_pax_code_id', 'qsb_flight_quote_segment_id', 'qsb_allow_pieces', 'qsb_allow_weight'], 'integer'],
            [['qsb_airline_code'], 'string', 'max' => 3],
            [['qsb_allow_unit'], 'string', 'max' => 4],
            [['qsb_allow_max_weight', 'qsb_allow_max_size'], 'string', 'max' => 100],
            [['qsb_flight_quote_segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteSegment::class, 'targetAttribute' => ['qsb_flight_quote_segment_id' => 'fqs_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qsb_id' => 'Qsb ID',
            'qsb_flight_pax_code_id' => 'Qsb Flight Pax Code ID',
            'qsb_flight_quote_segment_id' => 'Qsb Flight Quote Segment ID',
            'qsb_airline_code' => 'Qsb Airline Code',
            'qsb_allow_pieces' => 'Qsb Allow Pieces',
            'qsb_allow_weight' => 'Qsb Allow Weight',
            'qsb_allow_unit' => 'Qsb Allow Unit',
            'qsb_allow_max_weight' => 'Qsb Allow Max Weight',
            'qsb_allow_max_size' => 'Qsb Allow Max Size',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbFlightQuoteSegment()
    {
        return $this->hasOne(FlightQuoteSegment::class, ['fqs_id' => 'qsb_flight_quote_segment_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightQuoteSegmentPaxBaggageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightQuoteSegmentPaxBaggageQuery(static::class);
    }
}
