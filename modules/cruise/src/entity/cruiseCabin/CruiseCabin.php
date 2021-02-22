<?php

namespace modules\cruise\src\entity\cruiseCabin;

use modules\cruise\src\entity\cruise\Cruise;
use modules\cruise\src\entity\cruiseCabinPax\CruiseCabinPax;
use Yii;

/**
 * This is the model class for table "{{%cruise_cabin}}".
 *
 * @property int $crc_id
 * @property int $crc_cruise_id
 * @property string|null $crc_name
 *
 * @property Cruise $cruise
 * @property CruiseCabinPax[] $paxes
 */
class CruiseCabin extends \yii\db\ActiveRecord
{
    private $_adults    = null;
    private $_children  = null;

    public function rules(): array
    {
        return [
            ['crc_cruise_id', 'required'],
            ['crc_cruise_id', 'integer'],
            ['crc_cruise_id', 'exist', 'skipOnError' => true, 'targetClass' => Cruise::class, 'targetAttribute' => ['crc_cruise_id' => 'crs_id']],

            ['crc_name', 'string', 'max' => 200],
        ];
    }

    public function getCruise(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Cruise::class, ['crs_id' => 'crc_cruise_id']);
    }

    public function getPaxes(): \yii\db\ActiveQuery
    {
        return $this->hasMany(CruiseCabinPax::class, ['crp_cruise_cabin_id' => 'crc_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'crc_id' => 'ID',
            'crc_cruise_id' => 'Cruise ID',
            'crc_name' => 'Name',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%cruise_cabin}}';
    }

    public function getDataSearch(): array
    {
        $data = [];

        $adults = 0;
        $children = 0;
        $paxes = [];

        $data['rooms'] = 1;

        if ($this->paxes) {
            foreach ($this->paxes as $pax) {
                if ($pax->isAdult()) {
                    $adults++;
                } elseif ($pax->isChild()) {
                    $children++;
                    $paxes[] = ['paxType' => 1, 'age' => $pax->crp_age];
                }
            }
        }

        if ($adults) {
            $data['adults'] = $adults;
        }

        if ($children) {
            $data['children'] = $children;
        }

        if ($paxes) {
            $data['paxes'] = $paxes;
        }

        $this->_adults = $adults;
        $this->_children = $children;

//        ['rooms' => 1, 'adults' => 2, 'children' => 2, 'paxes' => [
//            ['paxType' => 1, 'age' => 6],
//            ['paxType' => 1, 'age' => 14],
//        ]];

        return $data;
    }

    public function getAdultCount(): int
    {
        if ($this->_adults === null) {
            $this->getDataSearch();
        }
        return $this->_adults ?: 0;
    }

    public function getChildrenCount(): int
    {
        if ($this->_children === null) {
            $this->getDataSearch();
        }
        return $this->_children ?: 0;
    }
}
