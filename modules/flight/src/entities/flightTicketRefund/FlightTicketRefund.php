<?php

namespace modules\flight\src\entities\flightTicketRefund;

use Yii;

/**
 * This is the model class for table "flight_ticket_refund".
 *
 * @property int $ftr_id
 * @property string $ftr_ticket_number
 * @property string|null $ftr_booking_id
 * @property string|null $ftr_pnr
 * @property string|null $ftr_gds
 * @property string|null $ftr_gds_pcc
 */
class FlightTicketRefund extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_ticket_refund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ftr_ticket_number'], 'required'],
            [['ftr_ticket_number'], 'string', 'max' => 50],
            [['ftr_booking_id'], 'string', 'max' => 10],
            [['ftr_pnr'], 'string', 'max' => 6],
            [['ftr_gds'], 'string', 'max' => 1],
            [['ftr_gds_pcc'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ftr_id' => 'app',
            'ftr_ticket_number' => 'app', 'Ticket Number',
            'ftr_booking_id' => 'app', 'Booking ID',
            'ftr_pnr' => 'app', 'Pnr',
            'ftr_gds' => 'app', 'Gds',
            'ftr_gds_pcc' => 'app', 'Gds Pcc',
        ];
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }
}
