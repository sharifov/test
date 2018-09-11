<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property string $c_iso_code
 * @property string $c_name
 * @property string $c_continent_code
 * @property string $c_local_name
 * @property int $c_id
 *
 * @property Airport[] $airports
 * @property Region[] $regions
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['c_iso_code', 'c_name'], 'required'],
            [['c_id'], 'default', 'value' => null],
            [['c_id'], 'integer'],
            [['c_iso_code', 'c_continent_code'], 'string', 'max' => 2],
            [['c_name', 'c_local_name'], 'string', 'max' => 100],
            [['c_name'], 'unique'],
            [['c_iso_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'c_iso_code' => 'ISO Code',
            'c_name' => 'Name',
            'c_continent_code' => 'Continent Code',
            'c_local_name' => 'Local Name',
            'c_id' => 'ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirports()
    {
        return $this->hasMany(Airport::class, ['ai_country_iso_code' => 'c_iso_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegions()
    {
        return $this->hasMany(Region::class, ['r_country_iso_code' => 'c_iso_code']);
    }

    /**
     * @inheritdoc
     * @return CountryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CountryQuery(get_called_class());
    }
}
