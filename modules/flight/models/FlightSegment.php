<?php

namespace modules\flight\models;

use Yii;

/**
 * This is the model class for table "flight_segment".
 *
 * @property int $fs_id
 * @property int $fs_flight_id
 * @property int $fs_origin_iata
 * @property string $fs_destination_iata
 * @property string $fs_departure_date
 * @property int|null $fs_flex_type_id
 * @property int|null $fs_flex_days
 *
 * @property Flight $fsFlight
 */
class FlightSegment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_segment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fs_flight_id', 'fs_origin_iata', 'fs_destination_iata', 'fs_departure_date'], 'required'],
            [['fs_flight_id', 'fs_origin_iata', 'fs_flex_type_id', 'fs_flex_days'], 'integer'],
            [['fs_departure_date'], 'safe'],
            [['fs_destination_iata'], 'string', 'max' => 3],
            [['fs_flight_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flight::class, 'targetAttribute' => ['fs_flight_id' => 'fl_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fs_id' => 'Fs ID',
            'fs_flight_id' => 'Fs Flight ID',
            'fs_origin_iata' => 'Fs Origin Iata',
            'fs_destination_iata' => 'Fs Destination Iata',
            'fs_departure_date' => 'Fs Departure Date',
            'fs_flex_type_id' => 'Fs Flex Type ID',
            'fs_flex_days' => 'Fs Flex Days',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFsFlight()
    {
        return $this->hasOne(Flight::class, ['fl_id' => 'fs_flight_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightSegmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightSegmentQuery(static::class);
    }
}
