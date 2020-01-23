<?php

namespace modules\flight\models;

use common\models\Currency;
use Yii;

/**
 * This is the model class for table "flight_quote_segment_pax_baggage_charge".
 *
 * @property int $qsbc_id
 * @property int $qsbc_flight_pax_id
 * @property int $qsbc_flight_quote_segment_id
 * @property int|null $qsbc_first_piece
 * @property int|null $qsbc_last_piece
 * @property float|null $qsbc_origin_price
 * @property string|null $qsbc_origin_currency
 * @property float|null $qsbc_price
 * @property float|null $qsbc_client_price
 * @property string|null $qsbc_client_currency
 * @property string|null $qsbc_max_weight
 * @property string|null $qsbc_max_size
 *
 * @property Currency $qsbcClientCurrency
 * @property FlightPax $qsbcFlightPax
 * @property Currency $qsbcOriginCurrency
 * @property FlightQuoteSegment $qsbcFlightQuoteSegment
 */
class FlightQuoteSegmentPaxBaggageCharge extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote_segment_pax_baggage_charge';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsbc_flight_pax_id', 'qsbc_flight_quote_segment_id'], 'required'],
            [['qsbc_flight_pax_id', 'qsbc_flight_quote_segment_id', 'qsbc_first_piece', 'qsbc_last_piece'], 'integer'],
            [['qsbc_origin_price', 'qsbc_price', 'qsbc_client_price'], 'number'],
            [['qsbc_origin_currency', 'qsbc_client_currency'], 'string', 'max' => 3],
            [['qsbc_max_weight', 'qsbc_max_size'], 'string', 'max' => 100],
            [['qsbc_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['qsbc_client_currency' => 'cur_code']],
            [['qsbc_flight_pax_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightPax::class, 'targetAttribute' => ['qsbc_flight_pax_id' => 'fp_id']],
            [['qsbc_origin_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['qsbc_origin_currency' => 'cur_code']],
            [['qsbc_flight_quote_segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteSegment::class, 'targetAttribute' => ['qsbc_flight_quote_segment_id' => 'fqs_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qsbc_id' => 'Qsbc ID',
            'qsbc_flight_pax_id' => 'Qsbc Flight Pax ID',
            'qsbc_flight_quote_segment_id' => 'Qsbc Flight Quote Segment ID',
            'qsbc_first_piece' => 'Qsbc First Piece',
            'qsbc_last_piece' => 'Qsbc Last Piece',
            'qsbc_origin_price' => 'Qsbc Origin Price',
            'qsbc_origin_currency' => 'Qsbc Origin Currency',
            'qsbc_price' => 'Qsbc Price',
            'qsbc_client_price' => 'Qsbc Client Price',
            'qsbc_client_currency' => 'Qsbc Client Currency',
            'qsbc_max_weight' => 'Qsbc Max Weight',
            'qsbc_max_size' => 'Qsbc Max Size',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbcClientCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'qsbc_client_currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbcFlightPax()
    {
        return $this->hasOne(FlightPax::class, ['fp_id' => 'qsbc_flight_pax_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbcOriginCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'qsbc_origin_currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbcFlightQuoteSegment()
    {
        return $this->hasOne(FlightQuoteSegment::class, ['fqs_id' => 'qsbc_flight_quote_segment_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightQuoteSegmentPaxBaggageChargeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightQuoteSegmentPaxBaggageChargeQuery(static::class);
    }
}
