<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "region".
 *
 * @property string $r_iso_code
 * @property string $r_local_code
 * @property string $r_country_iso_code
 * @property string $r_name
 * @property string $r_local_name
 * @property int $r_id
 *
 * @property Airport[] $airports
 * @property Country $rCountryIsoCode
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['r_iso_code', 'r_local_code', 'r_country_iso_code', 'r_name'], 'required'],
            [['r_id'], 'default', 'value' => null],
            [['r_id'], 'integer'],
            [['r_iso_code'], 'string', 'max' => 7],
            [['r_local_code'], 'string', 'max' => 4],
            [['r_country_iso_code'], 'string', 'max' => 2],
            [['r_name', 'r_local_name'], 'string', 'max' => 100],
            [['r_iso_code'], 'unique'],
            [['r_country_iso_code'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['r_country_iso_code' => 'c_iso_code']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'r_iso_code' => 'ISO Code',
            'r_local_code' => 'Local Code',
            'r_country_iso_code' => 'Country ISO Code',
            'r_name' => 'Name',
            'r_local_name' => 'Local Name',
            'r_id' => 'ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAirports()
    {
        return $this->hasMany(Airport::className(), ['ai_region_iso_code' => 'r_iso_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRCountryIsoCode()
    {
        return $this->hasOne(Country::className(), ['c_iso_code' => 'r_country_iso_code']);
    }

    /**
     * @inheritdoc
     * @return RegionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RegionQuery(get_called_class());
    }
}
