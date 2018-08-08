<?php
namespace common\models;


use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Airport model
 *
 * @property integer $id
 * @property string $name
 * @property string $city
 * @property string $country
 * @property string $countryId
 * @property string $state
 * @property string $iata
 * @property string $iaco
 * @property string $latitude
 * @property string $longitude
 * @property string $timezone
 * @property string $dst
 *
 */
class Airport extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%airports}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($iata)
    {
        return static::findOne(['iata' => $iata]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'iata'], 'required'],
            [['city', 'country', 'latitude', 'longitude', 'countryId', 'timezone', 'dst', 'state', 'iaco'], 'safe'],
        ];
    }

}
