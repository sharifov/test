<?php

namespace modules\flight\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "flight_quote_booking_airline".
 *
 * @property int $fqba_id
 * @property int $fqba_fqb_id
 * @property string|null $fqba_record_locator
 * @property string|null $fqba_airline_code
 * @property string|null $fqba_created_dt
 * @property string|null $fqba_updated_dt
 *
 * @property FlightQuoteBooking $fqbaFqb
 */
class FlightQuoteBookingAirline extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['fqba_fqb_id', 'required'],
            ['fqba_fqb_id', 'integer'],
            ['fqba_fqb_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteBooking::class, 'targetAttribute' => ['fqba_fqb_id' => 'fqb_id']],

            ['fqba_record_locator', 'string', 'max' => 20],
            ['fqba_airline_code', 'string', 'max' => 2],

            [['fqba_created_dt', 'fqba_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fqba_created_dt', 'fqba_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['fqba_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getFqbaFqb(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FlightQuoteBooking::class, ['fqb_id' => 'fqba_fqb_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fqba_id' => 'ID',
            'fqba_fqb_id' => 'FlightQuoteBooking',
            'fqba_record_locator' => 'Record Locator',
            'fqba_airline_code' => 'Airline Code',
            'fqba_created_dt' => 'Created Dt',
            'fqba_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): \modules\flight\models\query\FlightQuoteBookingAirlineQuery
    {
        return new \modules\flight\models\query\FlightQuoteBookingAirlineQuery(static::class);
    }

    public static function tableName(): string
    {
        return 'flight_quote_booking_airline';
    }

    public static function create(
        int $flightQuoteBookingId,
        ?string $recordLocator,
        ?string $airlineCode
    ): FlightQuoteBookingAirline {
        $model = new self();
        $model->fqba_fqb_id = $flightQuoteBookingId;
        $model->fqba_record_locator = $recordLocator;
        $model->fqba_airline_code = $airlineCode;
        return $model;
    }
}
