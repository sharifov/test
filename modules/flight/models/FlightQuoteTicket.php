<?php

namespace modules\flight\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "flight_quote_ticket".
 *
 * @property int $fqt_pax_id
 * @property int $fqt_flight_id
 * @property string|null $fqt_ticket_number
 * @property string|null $fqt_created_dt
 * @property string|null $fqt_updated_dt
 *
 * @property FlightQuoteFlight $fqtFlight
 * @property FlightPax $fqtPax
 */
class FlightQuoteTicket extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['fqt_pax_id', 'fqt_flight_id'], 'unique', 'targetAttribute' => ['fqt_pax_id', 'fqt_flight_id']],

            ['fqt_flight_id', 'required'],
            ['fqt_flight_id', 'integer'],
            ['fqt_flight_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteFlight::class, 'targetAttribute' => ['fqt_flight_id' => 'fqf_id']],

            ['fqt_pax_id', 'required'],
            ['fqt_pax_id', 'integer'],
            ['fqt_pax_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightPax::class, 'targetAttribute' => ['fqt_pax_id' => 'fp_id']],

            ['fqt_ticket_number', 'string', 'max' => 50],

            [['fqt_created_dt', 'fqt_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fqt_created_dt', 'fqt_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['fqt_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getFqtFlight(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FlightQuoteFlight::class, ['fqf_id' => 'fqt_flight_id']);
    }

    public function getFqtPax(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FlightPax::class, ['fp_id' => 'fqt_pax_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fqt_pax_id' => 'Pax ID',
            'fqt_flight_id' => 'Flight ID',
            'fqt_ticket_number' => 'Ticket Number',
            'fqt_created_dt' => 'Created Dt',
            'fqt_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): \modules\flight\models\query\FlightQuoteTicketQuery
    {
        return new \modules\flight\models\query\FlightQuoteTicketQuery(static::class);
    }

    public static function tableName(): string
    {
        return 'flight_quote_ticket';
    }
}
