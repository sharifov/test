<?php

namespace modules\flight\models;

use modules\flight\src\entities\flightQuoteTrip\serializer\FlightQuoteTripSerializer;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "flight_quote_trip".
 *
 * @property int $fqt_id
 * @property string $fqt_uid [varchar(20)]
 * @property string|null $fqt_key
 * @property int $fqt_flight_quote_id
 * @property int|null $fqt_duration
 * @property int|null $fqp_flight_id
 *
 * @property FlightQuoteSegment[] $flightQuoteSegments
 * @property FlightQuote $fqtFlightQuote
 * @property FlightQuoteFlight $flightQuoteFlight
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
            [['fqt_uid'], 'string', 'max' => 20],
            [['fqt_flight_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['fqt_flight_quote_id' => 'fq_id']],

            [['fqp_flight_id'], 'integer'],
            [['fqp_flight_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteFlight::class, 'targetAttribute' => ['fqp_flight_id' => 'fqf_id']],
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
            'fqp_flight_id' => 'Quote Flight',
        ];
    }

    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->fqt_uid = $this->generateUid();
        }
        return parent::beforeSave($insert);
    }

    public function getFlightQuoteFlight(): ActiveQuery
    {
        return $this->hasOne(FlightQuoteFlight::class, ['fqf_id' => 'fqp_flight_id']);
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

    public static function clone(FlightQuoteTrip $trip, int $quoteId): self
    {
        $clone = new self();

        $clone->attributes = $trip->attributes;

        $clone->fqt_id = null;
        $clone->fqt_flight_quote_id = $quoteId;

        return $clone;
    }

    public function serialize(): array
    {
        return (new FlightQuoteTripSerializer($this))->getData();
    }

    /**
     * @return string
     */
    public function generateUid(): string
    {
        return uniqid('fqt');
    }
}
