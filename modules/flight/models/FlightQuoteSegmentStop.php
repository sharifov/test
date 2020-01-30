<?php

namespace modules\flight\models;

use common\models\Airport;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentStopDTO;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "flight_quote_segment_stop".
 *
 * @property int $qss_id
 * @property int $qss_quote_segment_id
 * @property string|null $qss_location_iata
 * @property string|null $qss_equipment
 * @property int|null $qss_elapsed_time
 * @property int|null $qss_duration
 * @property string|null $qss_departure_dt
 * @property string|null $qss_arrival_dt
 *
 * @property FlightQuoteSegment $qssQuoteSegment
 * @property Airport $locationAirport
 */
class FlightQuoteSegmentStop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote_segment_stop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qss_quote_segment_id'], 'required'],
            [['qss_quote_segment_id', 'qss_elapsed_time', 'qss_duration'], 'integer'],
            [['qss_departure_dt', 'qss_arrival_dt'], 'safe'],
            [['qss_location_iata'], 'string', 'max' => 3],
            [['qss_equipment'], 'string', 'max' => 5],
            [['qss_quote_segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteSegment::class, 'targetAttribute' => ['qss_quote_segment_id' => 'fqs_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qss_id' => 'Qss ID',
            'qss_quote_segment_id' => 'Qss Quote Segment ID',
            'qss_location_iata' => 'Qss Location Iata',
            'qss_equipment' => 'Qss Equipment',
            'qss_elapsed_time' => 'Qss Elapsed Time',
            'qss_duration' => 'Qss Duration',
            'qss_departure_dt' => 'Qss Departure Dt',
            'qss_arrival_dt' => 'Qss Arrival Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQssQuoteSegment()
    {
        return $this->hasOne(FlightQuoteSegment::class, ['fqs_id' => 'qss_quote_segment_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightQuoteSegmentStopQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightQuoteSegmentStopQuery(static::class);
    }

	/**
	 * @param FlightQuoteSegmentStopDTO $dto
	 * @return FlightQuoteSegmentStop
	 */
    public static function create(FlightQuoteSegmentStopDTO $dto): self
	{
		$stop = new self();

		$stop->qss_quote_segment_id = $dto->quoteSegmentId;
		$stop->qss_location_iata = $dto->locationIata;
		$stop->qss_elapsed_time = $dto->elapsedTime;
		$stop->qss_duration = $dto->duration;
		$stop->qss_departure_dt = $dto->departureDt;
		$stop->qss_arrival_dt = $dto->arrivalDt;

		return $stop;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getLocationAirport(): ActiveQuery
	{
		return $this->hasOne(Airport::class, ['iata' => 'qss_location_iata']);
	}
}
