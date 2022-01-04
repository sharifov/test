<?php

namespace modules\flight\models;

use common\models\Airline;
use common\models\Airports;
use modules\flight\src\dto\flightSegment\FlightQuoteSegmentApiBoDto;
use modules\flight\src\entities\flightQuoteSegment\serializer\FlightQuoteSegmentSerializer;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentDTOInterface;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "flight_quote_segment".
 *
 * @property int $fqs_id
 * @property int $fqs_flight_quote_id
 * @property int|null $fqs_flight_quote_trip_id
 * @property string $fqs_uid [varchar(15)]
 * @property string $fqs_departure_dt
 * @property string $fqs_arrival_dt
 * @property int|null $fqs_stop
 * @property int|null $fqs_flight_number
 * @property string|null $fqs_booking_class
 * @property int|null $fqs_duration
 * @property string $fqs_departure_airport_iata
 * @property string|null $fqs_departure_airport_terminal
 * @property string $fqs_arrival_airport_iata
 * @property string|null $fqs_arrival_airport_terminal
 * @property string|null $fqs_operating_airline
 * @property string|null $fqs_marketing_airline
 * @property string|null $fqs_air_equip_type
 * @property string|null $fqs_marriage_group
 * @property string|null $fqs_cabin_class
 * @property bool $fqs_cabin_class_basic [tinyint(1)]
 * @property string|null $fqs_meal
 * @property string|null $fqs_fare_code
 * @property string|null $fqs_key
 * @property int|null $fqs_ticket_id
 * @property int|null $fqs_recheck_baggage
 * @property int|null $fqs_mileage
 * @property int|null $fqs_flight_id
 *
 * @property FlightQuote $fqsFlightQuote
 * @property FlightQuoteTrip $fqsFlightQuoteTrip
 * @property FlightQuoteSegmentPaxBaggage[] $flightQuoteSegmentPaxBaggages
 * @property FlightQuoteSegmentPaxBaggageCharge[] $flightQuoteSegmentPaxBaggageCharges
 * @property FlightQuoteSegmentStop[] $flightQuoteSegmentStops
 * @property Airline $marketingAirline
 * @property Airline $operatingAirline
 * @property Airports $departureAirport
 * @property Airports $arrivalAirport
 * @property FlightQuoteFlight $flightQuoteFlight
 */
class FlightQuoteSegment extends \yii\db\ActiveRecord
{
    public const TICKET_COLOR_LIST = [
        0   => '#FFFFFF',
        1   => '#fbe5e1',
        2   => '#fafbe1',
        3   => '#e1fbec',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote_segment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fqs_flight_quote_id', 'fqs_departure_dt', 'fqs_arrival_dt', 'fqs_departure_airport_iata', 'fqs_arrival_airport_iata'], 'required'],
            [['fqs_flight_quote_id', 'fqs_flight_quote_trip_id', 'fqs_stop', 'fqs_flight_number', 'fqs_duration', 'fqs_ticket_id', 'fqs_recheck_baggage', 'fqs_mileage', 'fqs_cabin_class_basic'], 'integer'],
            [['fqs_departure_dt', 'fqs_arrival_dt', 'fqs_uid'], 'safe'],
            [['fqs_booking_class'], 'string', 'max' => 1],
            [
                [
                    'fqs_departure_airport_iata', 'fqs_departure_airport_terminal', 'fqs_arrival_airport_iata',
                    'fqs_arrival_airport_terminal', 'fqs_marriage_group'
                ],
                'string', 'max' => 3
            ],
            [['fqs_operating_airline', 'fqs_marketing_airline', 'fqs_cabin_class', 'fqs_meal'], 'string', 'max' => 2],
            [['fqs_air_equip_type'], 'string', 'max' => 30],
            [['fqs_uid'], 'string', 'max' => 20],
            [['fqs_fare_code'], 'string', 'max' => 50],
            [['fqs_key'], 'string', 'max' => 40],
            [['fqs_flight_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['fqs_flight_quote_id' => 'fq_id']],
            [['fqs_flight_quote_trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteTrip::class, 'targetAttribute' => ['fqs_flight_quote_trip_id' => 'fqt_id']],

            [['fqs_flight_id'], 'integer'],
            [['fqs_flight_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteFlight::class, 'targetAttribute' => ['fqs_flight_id' => 'fqf_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fqs_id' => 'ID',
            'fqs_flight_quote_id' => 'Flight Quote ID',
            'fqs_flight_quote_trip_id' => 'Flight Quote Trip ID',
            'fqs_departure_dt' => 'Departure Dt',
            'fqs_arrival_dt' => 'Arrival Dt',
            'fqs_stop' => 'Stop',
            'fqs_flight_number' => 'Flight Number',
            'fqs_booking_class' => 'Booking Class',
            'fqs_duration' => 'Duration',
            'fqs_departure_airport_iata' => 'Departure Airport Iata',
            'fqs_departure_airport_terminal' => 'Departure Airport Terminal',
            'fqs_arrival_airport_iata' => 'Arrival Airport Iata',
            'fqs_arrival_airport_terminal' => 'Arrival Airport Terminal',
            'fqs_operating_airline' => 'Operating Airline',
            'fqs_marketing_airline' => 'Marketing Airline',
            'fqs_air_equip_type' => 'Air Equip Type',
            'fqs_marriage_group' => 'Marriage Group',
            'fqs_cabin_class' => 'Cabin Class',
            'fqs_meal' => 'Meal',
            'fqs_fare_code' => 'Fare Code',
            'fqs_key' => 'Key',
            'fqs_ticket_id' => 'Ticket ID',
            'fqs_recheck_baggage' => 'Recheck Baggage',
            'fqs_mileage' => 'Mileage',
            'fqs_flight_id' => 'Quote Flight',
        ];
    }

    public function beforeSave($insert): bool
    {
        if ($insert) {
            $this->fqs_uid = $this->generateUid();
        }
        return parent::beforeSave($insert);
    }

    public function getFlightQuoteFlight(): ActiveQuery
    {
        return $this->hasOne(FlightQuoteFlight::class, ['fqf_id' => 'fqs_flight_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFqsFlightQuote()
    {
        return $this->hasOne(FlightQuote::class, ['fq_id' => 'fqs_flight_quote_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFqsFlightQuoteTrip()
    {
        return $this->hasOne(FlightQuoteTrip::class, ['fqt_id' => 'fqs_flight_quote_trip_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMarketingAirline()
    {
        return $this->hasOne(Airline::class, ['iata' => 'fqs_marketing_airline']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOperatingAirline()
    {
        return $this->hasOne(Airline::class, ['iata' => 'fqs_operating_airline']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDepartureAirport()
    {
        return $this->hasOne(Airports::class, ['iata' => 'fqs_departure_airport_iata']);
    }

    /**
     * @return ActiveQuery
     */
    public function getArrivalAirport()
    {
        return $this->hasOne(Airports::class, ['iata' => 'fqs_arrival_airport_iata']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightQuoteSegmentPaxBaggages()
    {
        return $this->hasMany(FlightQuoteSegmentPaxBaggage::class, ['qsb_flight_quote_segment_id' => 'fqs_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightQuoteSegmentPaxBaggageCharges()
    {
        return $this->hasMany(FlightQuoteSegmentPaxBaggageCharge::class, ['qsbc_flight_quote_segment_id' => 'fqs_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlightQuoteSegmentStops()
    {
        return $this->hasMany(FlightQuoteSegmentStop::class, ['qss_quote_segment_id' => 'fqs_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightQuoteSegmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightQuoteSegmentQuery(static::class);
    }

    /**
     * @param FlightQuoteSegmentDTO $dto
     * @return FlightQuoteSegment
     */
    public static function create(FlightQuoteSegmentDTOInterface $dto): self
    {
        $segment = new self();

        $segment->fqs_flight_quote_id = $dto->flightQuoteId;
        $segment->fqs_flight_quote_trip_id = $dto->flightQuoteTripId;
        $segment->fqs_departure_dt = $dto->departureDt;
        $segment->fqs_arrival_dt = $dto->arrivalDt;
        $segment->fqs_stop = $dto->stop;
        $segment->fqs_flight_number = $dto->flightNumber;
        $segment->fqs_booking_class = $dto->bookingClass;
        $segment->fqs_duration = $dto->duration;
        $segment->fqs_departure_airport_iata = $dto->departureAirportIata;
        $segment->fqs_departure_airport_terminal = $dto->departureAirportTerminal;
        $segment->fqs_arrival_airport_iata = $dto->arrivalAirportIata;
        $segment->fqs_arrival_airport_terminal = $dto->arrivalAirportTerminal;
        $segment->fqs_operating_airline = $dto->operatingAirline;
        $segment->fqs_marketing_airline = $dto->marketingAirline;
        $segment->fqs_air_equip_type = $dto->airEquipType;
        $segment->fqs_marriage_group = $dto->marriageGroup;
        $segment->fqs_cabin_class = $dto->cabinClass;
        $segment->fqs_cabin_class_basic = (int)$dto->cabinClassBasic;
        $segment->fqs_meal = $dto->meal;
        $segment->fqs_fare_code = $dto->fareCode;
        $segment->fqs_key = $dto->key;
        $segment->fqs_ticket_id = $dto->ticketId;
        $segment->fqs_recheck_baggage = $dto->recheckBaggage;
        $segment->fqs_mileage = $dto->mileage;

        return $segment;
    }

    public static function clone(FlightQuoteSegment $segment, int $quoteId, ?int $tripId): self
    {
        $clone = new self();

        $clone->attributes = $segment->attributes;

        $clone->fqs_id = null;
        $clone->fqs_flight_quote_id = $quoteId;
        $clone->fqs_flight_quote_trip_id = $tripId;

        return $clone;
    }

    /**
     * @return string
     */
    public function getTicketColor(): string
    {
        return self::TICKET_COLOR_LIST[$this->fqs_ticket_id] ?? '#FFFFFF';
    }

    public function serialize(): array
    {
        return (new FlightQuoteSegmentSerializer($this))->getData();
    }

    /**
     * @return string
     */
    public function generateUid(): string
    {
        return uniqid('fqs');
    }

    public static function createFromBo(FlightQuoteSegmentApiBoDto $dto): FlightQuoteSegment
    {
        $segment = new self();
        $segment->fqs_flight_quote_id = $dto->flightQuoteId;
        $segment->fqs_flight_quote_trip_id = $dto->flightQuoteTripId;
        $segment->fqs_departure_dt = $dto->departureDt;
        $segment->fqs_arrival_dt = $dto->arrivalDt;
        $segment->fqs_stop = $dto->stop;
        $segment->fqs_flight_number = $dto->flightNumber;
        $segment->fqs_booking_class = $dto->bookingClass;
        $segment->fqs_duration = $dto->duration;
        $segment->fqs_departure_airport_iata = $dto->departureAirportIata;
        $segment->fqs_departure_airport_terminal = $dto->departureAirportTerminal;
        $segment->fqs_arrival_airport_iata = $dto->arrivalAirportIata;
        $segment->fqs_arrival_airport_terminal = $dto->arrivalAirportTerminal;
        $segment->fqs_operating_airline = $dto->operatingAirline;
        $segment->fqs_marketing_airline = $dto->marketingAirline;
        $segment->fqs_air_equip_type = $dto->airEquipType;
        $segment->fqs_marriage_group = $dto->marriageGroup;
        $segment->fqs_cabin_class = $dto->cabinClass;
        $segment->fqs_cabin_class_basic = (int)$dto->cabinClassBasic;
        $segment->fqs_meal = $dto->meal;
        $segment->fqs_fare_code = $dto->fareCode;
        $segment->fqs_key = $dto->key;
        $segment->fqs_ticket_id = $dto->ticketId;
        $segment->fqs_recheck_baggage = $dto->recheckBaggage;
        $segment->fqs_mileage = $dto->mileage;
        return $segment;
    }

    public function fields(): array
    {
        $fields = [
            'uid' => 'fqs_uid',
            'departureTime' => function () {
                return date('Y-m-d H:i', strtotime($this->fqs_departure_dt));
            },
            'arrivalTime' => function () {
                return date('Y-m-d H:i', strtotime($this->fqs_arrival_dt));
            },
//            'fqs_stop',
            'flightNumber' => 'fqs_flight_number',
            'bookingClass' => 'fqs_booking_class',
            'duration' => 'fqs_duration',
            'departureAirportCode' => 'fqs_departure_airport_iata',
            'departureAirportTerminal' => 'fqs_departure_airport_terminal',
            'arrivalAirportCode' => 'fqs_arrival_airport_iata',
            'arrivalAirportTerminal' => 'fqs_arrival_airport_terminal',
            'operatingAirline' => 'fqs_operating_airline',
            'marketingAirline' => 'fqs_marketing_airline',
            'airEquipType' => 'fqs_air_equip_type',
            'marriageGroup' => 'fqs_marriage_group',
            'cabin' => 'fqs_cabin_class',
            'meal' => 'fqs_meal',
            'fareCode' => 'fqs_fare_code',
//            'fqs_ticket_id',
//            'fqs_recheck_baggage',
            'mileage' => 'fqs_mileage',
        ];
        $fields['departureLocation'] = function () {
            return Airports::getCityByIata($this->fqs_departure_airport_iata);
        };
        $fields['arrivalLocation'] = function () {
            return Airports::getCityByIata($this->fqs_arrival_airport_iata);
        };
//        $fields['cabin'] = function () {
//            return \common\components\SearchService::getCabin($this->fqs_cabin_class);
//        };
//        $fields['operatingAirline'] = function () {
//            $operatingAirline = '';
//            if ($this->fqs_operating_airline) {
//                $airLine = Airline::find()->andWhere(['iata' => $this->fqs_operating_airline])->asArray()->one();
//                if ($airLine) {
//                    $operatingAirline = $airLine['name'];
//                }
//            }
//            return $operatingAirline;
//        };
//        $fields['marketingAirline'] = function () {
//            $marketingAirline = '';
//            if ($this->fqs_marketing_airline) {
//                $airLine = Airline::find()->andWhere(['iata' => $this->fqs_marketing_airline])->asArray()->one();
//                if ($airLine) {
//                    $marketingAirline = $airLine['name'];
//                }
//            }
//            return $marketingAirline;
//        };
        $fields['stop'] = function () {
            return count($this->flightQuoteSegmentStops);
        };
        $fields['stops'] = function () {
            $stops = [];
            foreach ($this->flightQuoteSegmentStops as $stop) {
                $stops[] = $stop->toArray();
            }
            return $stops;
        };

        $fields['baggage'] = function () {
            $baggages = [];
            foreach ($this->flightQuoteSegmentPaxBaggages as $baggage) {
                $baggages[] = $baggage->toArray();
            }
            return $baggages;
        };

        if ($this->flightQuoteSegmentPaxBaggageCharges) {
            $fields['baggage_charges'] = function () {
                $baggageCharges = [];
                foreach ($this->flightQuoteSegmentPaxBaggageCharges as $charge) {
                    $baggageCharges[] = $charge->toArray();
                }
                return $baggageCharges;
            };
        }
        return $fields;
    }

    /**
     * @return string
     */
    public function getArrivalAirportName(): string
    {
        return $this->arrivalAirport ? $this->arrivalAirport->name : '-';
    }

    /**
     * @return string
     */
    public function getArrivalAirportCity(): string
    {
        return $this->arrivalAirport ? $this->arrivalAirport->city : '-';
    }

    /**
     * @return string
     */
    public function getDepartureAirportName(): string
    {
        return $this->departureAirport ? $this->departureAirport->name : '-';
    }

    /**
     * @return string
     */
    public function getDepartureAirportCity(): string
    {
        return $this->departureAirport ? $this->departureAirport->city : '-';
    }

    /**
     * @param int $width
     * @return string
     */
    public function getAirlineLogoImg(int $width = 70): string
    {
        return $this->fqs_marketing_airline ? '//www.gstatic.com/flights/airline_logos/' . $width . 'px/' .
            $this->fqs_marketing_airline . '.png' : '';
    }

    /**
     * @return string
     */
    public function getMarketingAirlineName(): string
    {
        return ($this->fqs_marketing_airline && $this->marketingAirline) ? $this->marketingAirline->name : '';
    }

    /**
     * @return string
     */
    public function getOperatingAirlineName(): string
    {
        return ($this->fqs_operating_airline && $this->operatingAirline) ? $this->operatingAirline->name : '';
    }

    public function setCabin(?string $cabin): void
    {
        $this->fqs_cabin_class = $cabin;
    }
}
