<?php

namespace modules\flight\models;

use common\models\Airline;
use common\models\Client;
use common\models\Lead;
use common\models\Project;
use modules\flight\models\query\FlightQuoteBookingQuery;
use modules\flight\src\entities\flightQuoteBooking\serializer\FlightQuoteBookingSerializer;
use modules\order\src\entities\order\Order;
use modules\product\src\interfaces\ProductDataInterface;
use src\entities\serializer\Serializable;
use src\services\caseSale\PnrPreparingService;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "flight_quote_booking".
 *
 * @property int $fqb_id
 * @property int $fqb_fqf_id
 * @property string|null $fqb_booking_id
 * @property string|null $fqb_pnr
 * @property string|null $fqb_gds
 * @property string|null $fqb_gds_pcc
 * @property string|null $fqb_validating_carrier
 * @property string|null $fqb_created_dt
 * @property string|null $fqb_updated_dt
 *
 * @property FlightQuoteBookingAirline[] $flightQuoteBookingAirlines
 * @property FlightQuoteTicket[] $flightQuoteTickets
 * @property FlightQuoteFlight $fqbFqf
 * @property FlightPax[] $fqtPaxess
 */
class FlightQuoteBooking extends ActiveRecord implements Serializable, ProductDataInterface
{
    public function rules(): array
    {
        return [
            ['fqb_fqf_id', 'required'],
            ['fqb_fqf_id', 'integer'],
            ['fqb_fqf_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteFlight::class, 'targetAttribute' => ['fqb_fqf_id' => 'fqf_id']],

            ['fqb_booking_id', 'string', 'max' => 10],
            ['fqb_gds', 'string', 'max' => 1],
            ['fqb_gds_pcc', 'string', 'max' => 255],

            ['fqb_validating_carrier', 'string', 'max' => 2],

            [['fqb_created_dt', 'fqb_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            [['fqb_pnr'], 'string', 'max' => 70],
            [['fqb_pnr'], 'filter', 'filter' => static function ($value) {
                return (new PnrPreparingService($value))->getPnr();
            }],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fqb_created_dt', 'fqb_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['fqb_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getFlightQuoteBookingAirlines(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightQuoteBookingAirline::class, ['fqba_fqb_id' => 'fqb_id']);
    }

    public function getFlightQuoteTickets(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightQuoteTicket::class, ['fqt_fqb_id' => 'fqb_id']);
    }

    public function getFqbFqf(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FlightQuoteFlight::class, ['fqf_id' => 'fqb_fqf_id']);
    }

    public function getFqtPaxes(): \yii\db\ActiveQuery
    {
        return $this->hasMany(FlightPax::class, ['fp_id' => 'fqt_pax_id'])->viaTable('flight_quote_ticket', ['fqt_fqb_id' => 'fqb_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fqb_id' => 'ID',
            'fqb_fqf_id' => 'FlightQuoteFlight',
            'fqb_booking_id' => 'Booking ID',
            'fqb_pnr' => 'Pnr',
            'fqb_gds' => 'Gds',
            'fqb_gds_pcc' => 'Gds Pcc',
            'fqb_validating_carrier' => 'Validating Carrier',
            'fqb_created_dt' => 'Created Dt',
            'fqb_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): FlightQuoteBookingQuery
    {
        return new FlightQuoteBookingQuery(static::class);
    }

    public static function tableName(): string
    {
        return 'flight_quote_booking';
    }

    public static function create(
        int $flightQuoteFlightId,
        ?string $bookingId,
        ?string $pnr,
        ?string $gds,
        ?string $gdsPss,
        ?string $validatingCarrier
    ): FlightQuoteBooking {
        $model = new self();
        $model->fqb_fqf_id = $flightQuoteFlightId;
        $model->fqb_booking_id = $bookingId;
        $model->fqb_pnr = $pnr;
        $model->fqb_gds = $gds;
        $model->fqb_gds_pcc = $gdsPss;
        $model->fqb_validating_carrier = $validatingCarrier;
        return $model;
    }

    public function serialize(): array
    {
        return (new FlightQuoteBookingSerializer($this))->getData();
    }

    public function getProject(): ?Project
    {
        return $this->fqbFqf->getProject();
    }

    public function getLead(): ?Lead
    {
        return $this->fqbFqf->getLead();
    }

    public function getClient(): ?Client
    {
        return $this->fqbFqf->getClient();
    }

    public function getOrder(): ?Order
    {
        return $this->fqbFqf->getOrder();
    }

    public function getId(): int
    {
        return $this->fqb_id;
    }

    public function fields(): array
    {
        $fields = [
            'fqb_booking_id',
            'fqb_pnr',
            'fqb_gds',
            'fqb_validating_carrier',
        ];
        $fields['paxes'] = function () {
            $paxes = [];
            if ($this->fqtPaxes) {
                foreach ($this->fqtPaxes as $keyPax => $flightPax) {
                    $paxes[$keyPax] = $flightPax->toArray();
                    $paxes[$keyPax]['ticketNumber'] = $flightPax->flightQuoteTicket->fqt_ticket_number ?? '';
                }
            }
            return $paxes;
        };
        $fields['airlines'] = function () {
            $airlines = [];
            if ($this->flightQuoteBookingAirlines) {
                foreach ($this->flightQuoteBookingAirlines as $keyAirline => $bookingAirline) {
                    $airlines[$keyAirline]['record_locator'] = $bookingAirline->fqba_record_locator;
                    $airlines[$keyAirline]['airline_code'] = $bookingAirline->fqba_airline_code;
                    $airlines[$keyAirline]['airline'] = '';
                    if ($airline = Airline::findIdentity($bookingAirline->fqba_airline_code)) {
                        $airlines[$keyAirline]['airline'] = $airline->name;
                    }
                }
            }
            return $airlines;
        };
        return $fields;
    }
}
