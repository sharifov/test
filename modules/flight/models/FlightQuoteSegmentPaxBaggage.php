<?php

namespace modules\flight\models;

use modules\flight\src\entities\flightQuoteSegmentPaxBaggage\serializer\FlightQuoteSegmentPaxBaggageSerializer;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentPaxBaggageDTO;
use Yii;

/**
 * This is the model class for table "flight_quote_segment_pax_baggage".
 *
 * @property int $qsb_id
 * @property int $qsb_flight_pax_code_id
 * @property int $qsb_flight_quote_segment_id
 * @property string|null $qsb_airline_code
 * @property int|null $qsb_allow_pieces
 * @property int|null $qsb_allow_weight
 * @property string|null $qsb_allow_unit
 * @property string|null $qsb_allow_max_weight
 * @property string|null $qsb_allow_max_size
 * @property bool $qsb_carry_one
 *
 * @property FlightQuoteSegment $qsbFlightQuoteSegment
 */
class FlightQuoteSegmentPaxBaggage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote_segment_pax_baggage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsb_flight_pax_code_id'], 'required', 'message' => 'FlightQuoteSegmentPaxBaggage - Flight Pax Code ID cannot be blank'],
            [['qsb_flight_quote_segment_id'], 'required'],
            [['qsb_flight_pax_code_id', 'qsb_flight_quote_segment_id', 'qsb_allow_pieces', 'qsb_allow_weight'], 'integer'],
            [['qsb_airline_code'], 'string', 'max' => 3],
            [['qsb_allow_unit'], 'string', 'max' => 4],
            [['qsb_allow_max_weight', 'qsb_allow_max_size'], 'string', 'max' => 100],
            [['qsb_flight_quote_segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteSegment::class, 'targetAttribute' => ['qsb_flight_quote_segment_id' => 'fqs_id']],
            [['qsb_carry_one'], 'boolean'],
            [['qsb_carry_one'], 'default', 'value' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qsb_id' => 'ID',
            'qsb_flight_pax_code_id' => 'Flight Pax Code ID',
            'qsb_flight_quote_segment_id' => 'Flight Quote Segment ID',
            'qsb_airline_code' => 'Airline Code',
            'qsb_allow_pieces' => 'Allow Pieces',
            'qsb_allow_weight' => 'Allow Weight',
            'qsb_allow_unit' => 'Allow Unit',
            'qsb_allow_max_weight' => 'Allow Max Weight',
            'qsb_allow_max_size' => 'Allow Max Size',
            'qsb_carry_one' => 'Carry one',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbFlightQuoteSegment()
    {
        return $this->hasOne(FlightQuoteSegment::class, ['fqs_id' => 'qsb_flight_quote_segment_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightQuoteSegmentPaxBaggageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightQuoteSegmentPaxBaggageQuery(static::class);
    }

    /**
     * @param FlightQuoteSegmentPaxBaggageDTO $dto
     * @return FlightQuoteSegmentPaxBaggage
     */
    public static function create(FlightQuoteSegmentPaxBaggageDTO $dto): self
    {
        $baggage = new self();

        $baggage->qsb_flight_pax_code_id = $dto->flightPaxCodeId;
        $baggage->qsb_flight_quote_segment_id = $dto->flightQuoteSegmentId;
        $baggage->qsb_airline_code = $dto->airlineCode;
        $baggage->qsb_allow_pieces = $dto->allowPieces;
        $baggage->qsb_allow_weight = $dto->allowWeight;
        $baggage->qsb_allow_unit = $dto->allowUnit;
        $baggage->qsb_allow_max_weight = $dto->allowMaxWeight;
        $baggage->qsb_allow_max_size = $dto->allowMaxSize;
        $baggage->qsb_carry_one = $dto->carryOn;

        return $baggage;
    }

    public static function clone(FlightQuoteSegmentPaxBaggage $baggage, int $segmentId): self
    {
        $clone = new self();

        $clone->attributes = $baggage->attributes;

        $clone->qsb_id = null;
        $clone->qsb_flight_quote_segment_id = $segmentId;

        return $clone;
    }

    public function serialize(): array
    {
        return (new FlightQuoteSegmentPaxBaggageSerializer($this))->getData();
    }

    public static function createByParams(
        int $flightPaxCodeId,
        int $flightQuoteSegmentId,
        ?bool $carryOn,
        ?string $airlineCode,
        ?int $allowPieces,
        ?int $allowWeight = null,
        ?string $allowUnit = null,
        ?string $allowMaxWeight = null,
        ?string $allowMaxSize = null
    ): FlightQuoteSegmentPaxBaggage {
        $baggage = new self();
        $baggage->qsb_flight_pax_code_id = $flightPaxCodeId;
        $baggage->qsb_flight_quote_segment_id = $flightQuoteSegmentId;
        $baggage->qsb_carry_one = $carryOn;
        $baggage->qsb_airline_code = $airlineCode;
        $baggage->qsb_allow_pieces = $allowPieces;
        $baggage->qsb_allow_weight = $allowWeight;
        $baggage->qsb_allow_unit = $allowUnit;
        $baggage->qsb_allow_max_weight = $allowMaxWeight;
        $baggage->qsb_allow_max_size = $allowMaxSize;
        return $baggage;
    }

    public function fields(): array
    {
        return [
            'qsb_flight_pax_code_id',
            'qsb_flight_quote_segment_id',
            'qsb_airline_code',
            'qsb_carry_one',
            'qsb_allow_pieces',
            'qsb_allow_weight',
            'qsb_allow_unit',
            'qsb_allow_max_weight',
            'qsb_allow_max_size',
        ];
    }
}
