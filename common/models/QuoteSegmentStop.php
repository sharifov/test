<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "quote_segment_stop".
 *
 * @property int $qss_id
 * @property string $qss_location_code
 * @property string $qss_departure_dt
 * @property string $qss_arrival_dt
 * @property int $qss_duration
 * @property int $qss_elapsed_time
 * @property string $qss_equipment
 * @property int $qss_segment_id
 *
 * @property QuoteSegment $qssSegment
 * @property Airports $locationAirport
 */
class QuoteSegmentStop extends \yii\db\ActiveRecord
{
    /**
     * @param array $attributes
     * @param int $qsId
     * @return static
     */
    public static function clone(array $attributes, int $qsId): self
    {
        $stop = new self();
        $stop->attributes = $attributes;
        $stop->qss_segment_id = $qsId;
        return $stop;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_segment_stop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['qss_departure_dt', 'safe'],
            ['qss_arrival_dt', 'safe'],
            [['qss_departure_dt', 'qss_arrival_dt'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            [['qss_duration', 'qss_elapsed_time', 'qss_segment_id'], 'integer'],
            [['qss_location_code'], 'string', 'max' => 3],
            [['qss_equipment'], 'string', 'max' => 5],
            [['qss_segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteSegment::class, 'targetAttribute' => ['qss_segment_id' => 'qs_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qss_id' => 'ID',
            'qss_location_code' => 'Location Code',
            'qss_departure_dt' => 'Departure Dt',
            'qss_arrival_dt' => 'Arrival Dt',
            'qss_duration' => 'Duration',
            'qss_elapsed_time' => 'Elapsed Time',
            'qss_equipment' => 'Equipment',
            'qss_segment_id' => 'Segment ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQssSegment()
    {
        return $this->hasOne(QuoteSegment::class, ['qs_id' => 'qss_segment_id']);
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        $data = [
            'locationCode' => $this->qss_location_code,
            'departureDateTime' => $this->qss_departure_dt,
            'arrivalDateTime' => $this->qss_arrival_dt,
        ];

        if (!empty($this->qss_duration)) {
            $data['duration'] = $this->qss_duration;
        }
        if (!empty($this->qss_elapsed_time)) {
            $data['elapsedTime'] = $this->qss_elapsed_time;
        }
        if (!empty($this->qss_equipment)) {
            $data['equipment'] = $this->qss_equipment;
        }

        return $data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocationAirport()
    {
        return $this->hasOne(Airports::class, ['iata' => 'qss_location_code']);
    }

    public static function createFromSearch(array $stopEntry): QuoteSegmentStop
    {
        $stop = new self();
        $stop->qss_location_code = $stopEntry['locationCode'] ?? null;
        $stop->qss_departure_dt = $stopEntry['departureDateTime'] ?? null;
        $stop->qss_arrival_dt = $stopEntry['arrivalDateTime'] ?? null;
        $stop->qss_duration = $stopEntry['duration'] ?? null;
        $stop->qss_elapsed_time = $stopEntry['elapsedTime'] ?? null;
        $stop->qss_equipment = $stopEntry['equipment'] ?? null;
        return $stop;
    }
}
