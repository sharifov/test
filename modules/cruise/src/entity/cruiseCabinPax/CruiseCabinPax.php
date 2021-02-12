<?php

namespace modules\cruise\src\entity\cruiseCabinPax;

use modules\cruise\src\entity\cruiseCabin\CruiseCabin;
use Yii;

/**
 * This is the model class for table "{{%cruise_cabin_pax}}".
 *
 * @property int $crp_id
 * @property int $crp_cruise_cabin_id
 * @property int $crp_type_id
 * @property int|null $crp_age
 * @property string|null $crp_first_name
 * @property string|null $crp_last_name
 * @property string|null $crp_dob
 *
 * @property CruiseCabin $cabin
 */
class CruiseCabinPax extends \yii\db\ActiveRecord
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

    public static function getPaxTypeList(): array
    {
        return self::PAX_TYPE_LIST;
    }

    public function getPaxAgeRangeByPaxId(int $paxId): ?array
    {
        return self::PAX_AGE_RANGE[$paxId] ?? null;
    }

    public function getPaxTypeName(): string
    {
        return self::PAX_TYPE_LIST[$this->crp_type_id] ?? '';
    }

    public function isAdult(): bool
    {
        return (int) $this->crp_type_id === self::PAX_TYPE_ADL;
    }

    public function isChild(): bool
    {
        return (int) $this->crp_type_id === self::PAX_TYPE_CHD;
    }

    public function rules(): array
    {
        return [
            ['crp_age', 'integer'],

            ['crp_cruise_cabin_id', 'required'],
            ['crp_cruise_cabin_id', 'integer'],
            ['crp_cruise_cabin_id', 'exist', 'skipOnError' => true, 'targetClass' => CruiseCabin::class, 'targetAttribute' => ['crp_cruise_cabin_id' => 'crc_id']],

            ['crp_dob', 'safe'],

            ['crp_first_name', 'string', 'max' => 40],

            ['crp_last_name', 'string', 'max' => 40],

            ['crp_type_id', 'required'],
            ['crp_type_id', 'integer'],
        ];
    }

    public function getCabin(): \yii\db\ActiveQuery
    {
        return $this->hasOne(CruiseCabin::class, ['crc_id' => 'crp_cruise_cabin_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'crp_id' => 'ID',
            'crp_cruise_cabin_id' => 'Cruise Cabin ID',
            'crp_type_id' => 'Type ID',
            'crp_age' => 'Age',
            'crp_first_name' => 'First Name',
            'crp_last_name' => 'Last Name',
            'crp_dob' => 'Dob',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%cruise_cabin_pax}}';
    }
}
