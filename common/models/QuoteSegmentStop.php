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
 */
class QuoteSegmentStop extends \yii\db\ActiveRecord
{
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
            [['qss_departure_dt', 'qss_arrival_dt'], 'safe'],
            [['qss_duration', 'qss_elapsed_time', 'qss_segment_id'], 'integer'],
            [['qss_location_code'], 'string', 'max' => 3],
            [['qss_equipment'], 'string', 'max' => 5],
            [['qss_segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteSegment::className(), 'targetAttribute' => ['qss_segment_id' => 'qs_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qss_id' => 'Qss ID',
            'qss_location_code' => 'Qss Location Code',
            'qss_departure_dt' => 'Qss Departure Dt',
            'qss_arrival_dt' => 'Qss Arrival Dt',
            'qss_duration' => 'Qss Duration',
            'qss_elapsed_time' => 'Qss Elapsed Time',
            'qss_equipment' => 'Qss Equipment',
            'qss_segment_id' => 'Qss Segment ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQssSegment()
    {
        return $this->hasOne(QuoteSegment::className(), ['qs_id' => 'qss_segment_id']);
    }

    public function getInfo()
    {
        $data = [
            'locationCode' => $this->qss_location_code,
            'departureDateTime' => $this->qss_departure_dt,
            'arrivalDateTime' => $this->qss_arrival_dt,
        ];

        if(!empty($this->qss_duration)){
            $data['duration'] = $this->qss_duration;
        }
        if(!empty($this->qss_elapsed_time)){
            $data['elapsedTime'] = $this->qss_elapsed_time;
        }
        if(!empty($this->qss_equipment)){
            $data['equipment'] = $this->qss_equipment;
        }

        return $data;
    }
}
