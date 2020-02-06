<?php

namespace modules\flight\models;

use modules\flight\src\dto\flightSegment\SegmentDTO;
use modules\flight\src\events\FlightRequestUpdateEvent;
use sales\entities\EventTrait;
use Yii;

/**
 * This is the model class for table "flight_segment".
 *
 * @property int $fs_id
 * @property int $fs_flight_id
 * @property string $fs_origin_iata
 * @property string $fs_destination_iata
 * @property string $fs_origin_iata_label
 * @property string $fs_destination_iata_label
 * @property string $fs_departure_date
 * @property int|null $fs_flex_type_id
 * @property int|null $fs_flex_days
 *
 * @property Flight $fsFlight
 */
class FlightSegment extends \yii\db\ActiveRecord
{
	use EventTrait;

	public CONST FLEX_TYPE_MINUS = 1;
	public CONST FLEX_TYPE_PLUS = 2;
	public CONST FLEX_TYPE_PLUS_MINUS = 3;

	public CONST FLEX_TYPE_LIST = [
		self::FLEX_TYPE_MINUS => '-',
		self::FLEX_TYPE_PLUS => '+',
		self::FLEX_TYPE_PLUS_MINUS => '+/-',
	];

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
            [['fs_destination_iata', 'fs_destination_iata'], 'string', 'max' => 3],
            [['fs_origin_iata_label', 'fs_destination_iata_label'], 'string', 'max' => 255],
            [['fs_flight_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flight::class, 'targetAttribute' => ['fs_flight_id' => 'fl_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fs_id' => 'ID',
            'fs_flight_id' => 'Flight ID',
            'fs_origin_iata' => 'Origin',
            'fs_destination_iata' => 'Destination',
            'fs_departure_date' => 'Departure Date',
            'fs_flex_type_id' => 'Flex (+/-)',
            'fs_flex_days' => 'Flex (Days)',
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
	 * @param SegmentDTO $segmentDTO
	 * @return static
	 */
	public static function create(SegmentDTO $segmentDTO): self
	{
		$segment = new static();
		$segment->fs_flight_id = $segmentDTO->flightId;
		$segment->fs_origin_iata = $segmentDTO->origin;
		$segment->fs_destination_iata = $segmentDTO->destination;
		$segment->fs_departure_date = $segmentDTO->departure;
		$segment->fs_flex_days = $segmentDTO->flexDays;
		$segment->fs_flex_type_id = $segmentDTO->flexTypeId;
		$segment->fs_origin_iata_label = $segmentDTO->originLabel;
		$segment->fs_destination_iata_label = $segmentDTO->destinationLabel;
		return $segment;
	}

	/**
	 * @param SegmentDTO $segmentDTO
	 */
	public function edit(SegmentDTO $segmentDTO): void
	{
		if ($this->fs_origin_iata !== $segmentDTO->origin ||
			$this->fs_destination_iata !== $segmentDTO->destination ||
			$this->fs_departure_date !== $segmentDTO->departure) {
			$this->recordEvent(new FlightRequestUpdateEvent($this->fsFlight), FlightRequestUpdateEvent::EVENT_KEY);
		}
		$this->fs_origin_iata = $segmentDTO->origin;
		$this->fs_destination_iata = $segmentDTO->destination;
		$this->fs_departure_date = $segmentDTO->departure;
		$this->fs_flex_days = $segmentDTO->flexDays;
		$this->fs_flex_type_id = $segmentDTO->flexTypeId;
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
