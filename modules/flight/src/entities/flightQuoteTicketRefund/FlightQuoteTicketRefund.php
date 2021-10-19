<?php

namespace modules\flight\src\entities\flightQuoteTicketRefund;

use modules\flight\models\FlightQuoteBooking;
use modules\flight\models\query\FlightQuoteBookingQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "flight_quote_ticket_refund".
 *
 * @property int $fqtr_id
 * @property string $fqtr_ticket_number
 * @property string|null $fqtr_created_dt
 * @property int|null $fqtr_fqb_id
 *
 * @property FlightQuoteBooking $flightQuoteBooking
 */
class FlightQuoteTicketRefund extends \yii\db\ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fqtr_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote_ticket_refund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fqtr_ticket_number'], 'required'],
            [['fqtr_created_dt'], 'safe'],
            [['fqtr_fqb_id'], 'integer'],
            [['fqtr_ticket_number'], 'string', 'max' => 50],
            [['fqtr_fqb_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteBooking::class, 'targetAttribute' => ['fqtr_fqb_id' => 'fqb_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fqtr_id' => 'ID',
            'fqtr_ticket_number' => 'Ticket Number',
            'fqtr_created_dt' => 'Created Dt',
            'fqtr_fqb_id' => 'Fqb ID',
        ];
    }

    /**
     * Gets query for [[FqtrFqb]].
     *
     * @return \yii\db\ActiveQuery|FlightQuoteBookingQuery
     */
    public function getFlightQuoteBooking()
    {
        return $this->hasOne(FlightQuoteBooking::class, ['fqb_id' => 'fqtr_fqb_id']);
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
