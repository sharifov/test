<?php

namespace modules\flight\models;

use sales\entities\serializer\Serializable;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "flight_quote_ticket".
 *
 * @property int $fqt_pax_id
 * @property string|null $fqt_ticket_number
 * @property string|null $fqt_created_dt
 * @property string|null $fqt_updated_dt
 * @property int $fqt_fqb_id
 *
 * @property FlightQuoteBooking $fqtFqb
 * @property FlightPax $fqtPax
 */
class FlightQuoteTicket extends ActiveRecord
{
    public function rules(): array
    {
        return [
            [['fqt_pax_id', 'fqt_fqb_id'], 'unique', 'targetAttribute' => ['fqt_pax_id', 'fqt_fqb_id']],

            ['fqt_fqb_id', 'required'],
            ['fqt_fqb_id', 'integer'],
            ['fqt_fqb_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteBooking::class, 'targetAttribute' => ['fqt_fqb_id' => 'fqb_id']],

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

    public function getFqtFqb(): ActiveQuery
    {
        return $this->hasOne(FlightQuoteBooking::class, ['fqb_id' => 'fqt_fqb_id']);
    }

    public function getFqtPax(): ActiveQuery
    {
        return $this->hasOne(FlightPax::class, ['fp_id' => 'fqt_pax_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fqt_pax_id' => 'Pax',
            'fqt_fqb_id' => 'Flight Quote Booking',
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

    public static function create(int $paxId, int $flightQuoteBookingId, string $ticketNumber): FlightQuoteTicket
    {
        $model = new self();
        $model->fqt_pax_id = $paxId;
        $model->fqt_fqb_id = $flightQuoteBookingId;
        $model->fqt_ticket_number = $ticketNumber;
        return $model;
    }
}
