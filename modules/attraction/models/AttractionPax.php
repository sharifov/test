<?php

namespace modules\attraction\models;

use Yii;
use modules\attraction\src\entities\attractionPax\serializer\AttractionPaxSerializer;

/**
 * This is the model class for table "attraction_pax".
 *
 * @property int $atnp_id
 * @property int $atnp_atn_id
 * @property int $atnp_type_id
 * @property int|null $atnp_age
 * @property string|null $atnp_first_name
 * @property string|null $atnp_last_name
 * @property string|null $atnp_dob
 *
 * @property Attraction $atnpAtn
 */
class AttractionPax extends \yii\db\ActiveRecord
{
    public const PAX_ADULT = 'Adult';
    public const PAX_CHILD = 'Child';
    public const PAX_INFANT = 'Infant';

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

    public const PAX_AGE_RANGE = [
        self::PAX_LIST_ID[self::PAX_CHILD] => [
            'min' => 1,
            'max' => 12
        ],
        self::PAX_LIST_ID[self::PAX_ADULT] => [
            'min' => 13
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attraction_pax';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atnp_atn_id', 'atnp_type_id'], 'required'],
            [['atnp_atn_id', 'atnp_type_id', 'atnp_age'], 'integer'],
            [['atnp_dob'], 'safe'],
            [['atnp_first_name', 'atnp_last_name'], 'string', 'max' => 40],
            [['atnp_atn_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attraction::class, 'targetAttribute' => ['atnp_atn_id' => 'atn_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'atnp_id' => 'ID',
            'atnp_atn_id' => 'Attraction ID',
            'atnp_type_id' => 'Type',
            'atnp_age' => 'Age',
            'atnp_first_name' => 'First Name',
            'atnp_last_name' => 'Last Name',
            'atnp_dob' => 'Date of Birth',
        ];
    }

    /**
     * @return bool
     */
    public function isAdult(): bool
    {
        return (int) $this->atnp_type_id === self::PAX_LIST_ID[self::PAX_ADULT];
    }

    /**
     * @return bool
     */
    public function isChild(): bool
    {
        return (int) $this->atnp_type_id === self::PAX_LIST_ID[self::PAX_CHILD];
    }

    public function getPaxTypeName(): string
    {
        return self::PAX_LIST[$this->atnp_type_id] ?? '';
    }

    /**
     * @param int $paxId
     * @return array|null
     */
    public function getPaxAgeRangeByPaxId(int $paxId): ?array
    {
        return self::PAX_AGE_RANGE[$paxId] ?? null;
    }

    public function serialize(): array
    {
        return (new AttractionPaxSerializer($this))->getData();
    }

    /**
     * Gets query for [[AtnpAtn]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtnpAtn()
    {
        return $this->hasOne(Attraction::class, ['atn_id' => 'atnp_atn_id']);
    }
}
