<?php

namespace modules\hotel\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "hotel_room_pax".
 *
 * @property int $hrp_id
 * @property int $hrp_hotel_room_id
 * @property int $hrp_type_id
 * @property int|null $hrp_age
 * @property string|null $hrp_first_name
 * @property string|null $hrp_last_name
 * @property string|null $hrp_dob
 *
 * @property HotelRoom $hrpHotelRoom
 */
class HotelRoomPax extends \yii\db\ActiveRecord
{

    public const PAX_TYPE_ADL = 1;
    public const PAX_TYPE_CHD = 2;

    public const PAX_TYPE_LIST = [
        self::PAX_TYPE_ADL => 'Adult',
        self::PAX_TYPE_CHD => 'Child',
    ];

    public const PAX_AGE_RANGE = [
    	self::PAX_TYPE_CHD => [
    		'min' => 1,
			'max' => 12
		],
		self::PAX_TYPE_ADL => [
			'min' => 13
		]
	];

        /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hotel_room_pax';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hrp_hotel_room_id', 'hrp_type_id'], 'required'],
            [['hrp_hotel_room_id', 'hrp_type_id', 'hrp_age'], 'integer'],
            [['hrp_dob'], 'filter', 'filter' => static function ($value) {
				return date('Y-m-d', strtotime($value));
            }, 'skipOnEmpty' => true],
            //[['hrp_dob'], 'safe'],
            [['hrp_first_name', 'hrp_last_name'], 'string', 'max' => 40],
            [['hrp_hotel_room_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelRoom::class, 'targetAttribute' => ['hrp_hotel_room_id' => 'hr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hrp_id' => 'ID',
            'hrp_hotel_room_id' => 'Hotel Room ID',
            'hrp_type_id' => 'Type ID',
            'hrp_age' => 'Age',
            'hrp_first_name' => 'First Name',
            'hrp_last_name' => 'Last Name',
            'hrp_dob' => 'DOB',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHrpHotelRoom()
    {
        return $this->hasOne(HotelRoom::class, ['hr_id' => 'hrp_hotel_room_id']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\hotel\models\query\HotelRoomPaxQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\hotel\models\query\HotelRoomPaxQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getPaxTypeList(): array
    {
        return self::PAX_TYPE_LIST;
    }

    /**
     * @return string
     */
    public function getPaxTypeName(): string
    {
        return self::PAX_TYPE_LIST[$this->hrp_type_id] ?? '';
    }

    /**
     * @return bool
     */
    public function isAdult(): bool
    {
        return (int) $this->hrp_type_id === self::PAX_TYPE_ADL;
    }

    /**
     * @return bool
     */
    public function isChild(): bool
    {
        return (int) $this->hrp_type_id === self::PAX_TYPE_CHD;
    }

	/**
	 * @param int $paxId
	 * @return array|null
	 */
    public function getPaxAgeRangeByPaxId(int $paxId): ?array
	{
		return self::PAX_AGE_RANGE[$paxId] ?? null;
	}

    /**
     * @param int $roomId
     * @return array|bool
     */
    public function getChildrenAgesByRoom(int $roomId)
    {
        $childrenAges = (new Query())
            ->select(['GROUP_CONCAT(hrp_age ORDER BY hrp_age ASC SEPARATOR ",") AS childrenAges'])
            ->from('hotel_room_pax')
            ->where([
                'hrp_hotel_room_id' => $roomId,
                'hrp_type_id' => 2,
            ])
            ->one();

        return $childrenAges ? $childrenAges : ['childrenAges' => ''];
    }

    /**
     * @param int $roomId
     * @return array|bool
     */
    public function getQtyByRoom(int $roomId)
    {
        return (new Query())
            ->select([
                'SUM(
                    CASE 
                        WHEN hotel_room_pax.hrp_type_id = 1
                        THEN 1 
                        ELSE 0  
                    END
                ) AS adults',
                'SUM(
                    CASE 
                        WHEN hotel_room_pax.hrp_type_id = 2
                        THEN 1 
                        ELSE 0  
                    END
                ) AS children',
            ])
            ->from('hotel_room_pax')
            ->where(['hotel_room_pax.hrp_hotel_room_id' => $roomId])
            ->one();
    }

    /**
     * @param int $roomId
     * @return array
     */
    public function getSummaryByRoom(int $roomId)
    {
        return (ArrayHelper::merge($this->getQtyByRoom($roomId), $this->getChildrenAgesByRoom($roomId)));
	}
}
