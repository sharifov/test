<?php

namespace modules\flight\models;

use modules\flight\src\useCases\flightQuote\create\FlightPaxDTO;
use Yii;

/**
 * This is the model class for table "flight_pax".
 *
 * @property int $fp_id
 * @property int $fp_flight_id
 * @property int|null $fp_pax_id
 * @property string|null $fp_pax_type
 * @property string|null $fp_first_name
 * @property string|null $fp_last_name
 * @property string|null $fp_middle_name
 * @property string|null $fp_dob
 *
 * @property Flight $fpFlight
 * @property FlightQuoteSegmentPaxBaggageCharge[] $flightQuoteSegmentPaxBaggageCharges
 */
class FlightPax extends \yii\db\ActiveRecord
{
	public const PAX_ADULT = 'ADT';
	public const PAX_CHILD = 'CHD';
	public const PAX_INFANT = 'INF';

	public const PAX_LIST_ID = [
		self::PAX_ADULT => 1,
		self::PAX_CHILD => 2,
		self::PAX_INFANT => 3
	];

	public const PAX_LIST = [
		self::PAX_LIST_ID[self::PAX_ADULT] => self::PAX_ADULT,
		self::PAX_LIST_ID[self::PAX_CHILD] => self::PAX_CHILD,
		self::PAX_LIST_ID[self::PAX_INFANT] => self::PAX_INFANT,
	];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_pax';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fp_flight_id'], 'required'],
            [['fp_flight_id', 'fp_pax_id'], 'integer'],
            [['fp_dob'], 'safe'],
            [['fp_pax_type'], 'string', 'max' => 3],
            [['fp_first_name', 'fp_last_name', 'fp_middle_name'], 'string', 'max' => 40],
            [['fp_flight_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flight::class, 'targetAttribute' => ['fp_flight_id' => 'fl_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fp_id' => 'Fp ID',
            'fp_flight_id' => 'Fp Flight ID',
            'fp_pax_id' => 'Fp Pax ID',
            'fp_pax_type' => 'Fp Pax Type',
            'fp_first_name' => 'Fp First Name',
            'fp_last_name' => 'Fp Last Name',
            'fp_middle_name' => 'Fp Middle Name',
            'fp_dob' => 'Fp Dob',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFpFlight()
    {
        return $this->hasOne(Flight::class, ['fl_id' => 'fp_flight_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightPaxQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightPaxQuery(static::class);
    }

	/**
	 * @param FlightPaxDTO $dto
	 * @return FlightPax
	 */
    public static function create(FlightPaxDTO $dto): self
	{
		$flightPax = new self();

		$flightPax->fp_flight_id = $dto->flightId;
		$flightPax->fp_pax_id = $dto->paxId;
		$flightPax->fp_pax_type = $dto->paxType;
		$flightPax->fp_first_name = $dto->firstName;
		$flightPax->fp_last_name = $dto->lastName;
		$flightPax->fp_middle_name = $dto->middleName;
		$flightPax->fp_dob = $dto->dob;

		return $flightPax;
	}

	/**
	 * @return array
	 */
	public static function getPaxListId(): array
	{
		return self::PAX_LIST_ID;
	}

	/**
	 * @param string $type
	 * @return int|null
	 */
	public static function getPaxId(string $type): ?int
	{
		return self::getPaxListId()[$type] ?? null;
	}

	/**
	 * @param int $id
	 * @return string|null
	 */
	public static function getPaxTypeById(int $id):? string
	{
		return self::PAX_LIST[$id] ?? null;
	}
}
