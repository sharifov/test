<?php

namespace modules\flight\models;

use Yii;

/**
 * This is the model class for table "flight_quote_trip".
 *
 * @property int $fqt_id
 * @property string|null $fqt_key
 * @property int $fqt_flight_quote_id
 * @property int|null $fqt_duration
 *
 * @property FlightQuoteSegment[] $flightQuoteSegments
 * @property FlightQuote $fqtFlightQuote
 */
class FlightQuoteTrip extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote_trip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fqt_flight_quote_id'], 'required'],
            [['fqt_flight_quote_id', 'fqt_duration'], 'integer'],
            [['fqt_key'], 'string', 'max' => 255],
            [['fqt_flight_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['fqt_flight_quote_id' => 'fq_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fqt_id' => 'Fqt ID',
            'fqt_key' => 'Fqt Key',
            'fqt_flight_quote_id' => 'Fqt Flight Quote ID',
            'fqt_duration' => 'Fqt Duration',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlightQuoteSegments()
    {
        return $this->hasMany(FlightQuoteSegment::class, ['fqs_flight_quote_trip_id' => 'fqt_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFqtFlightQuote()
    {
        return $this->hasOne(FlightQuote::class, ['fq_id' => 'fqt_flight_quote_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightQuoteTripQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightQuoteTripQuery(static::class);
    }

	/**
	 * @param FlightQuote $flightQuote
	 * @param string $duration
	 * @return FlightQuoteTrip
	 */
    public static function create(FlightQuote $flightQuote, string $duration): self
	{
		$trip = new self();

		$trip->fqt_flight_quote_id = $flightQuote->fq_id;
		$trip->fqt_duration = $duration;

		return $trip;
	}

}
