<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "airport".
 *
 * @property string $ai_iata_code
 * @property string $ai_icao_code
 * @property string $ai_local_code
 * @property string $ai_country_iso_code
 * @property string $ai_region_iso_code
 * @property string $ai_name
 * @property string $ai_municipality
 * @property string $ai_latitude_deg
 * @property string $ai_longitude_deg
 * @property int $ai_elevation_ft
 * @property string $ai_gps_code
 * @property int $ai_type_id
 * @property int $ai_id
 *
 * @property Country $aiCountryIsoCode
 * @property Region $aiRegionIsoCode
 */
class AirportList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'airport';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ai_iata_code', 'ai_region_iso_code', 'ai_name'], 'required'],
            [['ai_latitude_deg', 'ai_longitude_deg'], 'number'],
            [['ai_elevation_ft', 'ai_type_id', 'ai_id'], 'default', 'value' => null],
            [['ai_elevation_ft', 'ai_type_id', 'ai_id'], 'integer'],
            [['ai_iata_code'], 'string', 'max' => 3],
            [['ai_icao_code'], 'string', 'max' => 8],
            [['ai_local_code', 'ai_gps_code'], 'string', 'max' => 4],
            [['ai_country_iso_code'], 'string', 'max' => 2],
            [['ai_region_iso_code'], 'string', 'max' => 7],
            [['ai_name'], 'string', 'max' => 80],
            [['ai_municipality'], 'string', 'max' => 50],
            [['ai_icao_code'], 'unique'],
            [['ai_iata_code'], 'unique'],
            [['ai_country_iso_code'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['ai_country_iso_code' => 'c_iso_code']],
            [['ai_region_iso_code'], 'exist', 'skipOnError' => true, 'targetClass' => Region::class, 'targetAttribute' => ['ai_region_iso_code' => 'r_iso_code']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ai_iata_code' => 'Iata Code',
            'ai_icao_code' => 'Icao Code',
            'ai_local_code' => 'Local Code',
            'ai_country_iso_code' => 'Country Iso Code',
            'ai_region_iso_code' => 'Region Iso Code',
            'ai_name' => 'Name',
            'ai_municipality' => 'Municipality',
            'ai_latitude_deg' => 'Latitude Deg',
            'ai_longitude_deg' => 'Longitude Deg',
            'ai_elevation_ft' => 'Elevation Ft',
            'ai_gps_code' => 'GPS Code',
            'ai_type_id' => 'Type ID',
            'ai_id' => 'ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAiCountryIsoCode()
    {
        return $this->hasOne(Country::class, ['c_iso_code' => 'ai_country_iso_code']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAiRegionIsoCode()
    {
        return $this->hasOne(Region::class, ['r_iso_code' => 'ai_region_iso_code']);
    }

    /**
     * @inheritdoc
     * @return AirportListQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AirportListQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function getSelectName()
    {
        return '('.$this->ai_iata_code.') - '.$this->ai_municipality .', '. $this->aiRegionIsoCode->r_name . ', '. $this->aiCountryIsoCode->c_name;
    }
}
