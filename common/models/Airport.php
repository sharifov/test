<?php

namespace common\models;


use Yii;
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

    public static function getAirportListByIata($iata = [])
    {
        $data = [];
        $airports = self::find()->where(['iata' => $iata])->all();
        foreach ($airports as $airport) {
            $data[$airport['iata']] = ['name' => $airport['name'], 'city' => $airport['city'], 'country' => $airport['country']];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getSelection(): string
    {
        return '(' . $this->iata . ') ' . $this->city;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return '(' . $this->iata . ') ' . $this->name . ', ' . $this->city  . ', ' . $this->country;
    }

    /**
     * @return string
     */
    public function getCityName(): string
    {
        return $this->city;
    }

    /**
     * @param int $duration
     * @return array
     */
    public static function getIataList(int $duration = 5 * 60): array
    {
        return Yii::$app->cacheFile->getOrSet(__FUNCTION__, static function () {
            return ArrayHelper::map(
                self::find()->select(['iata'])->distinct()->orderBy(['iata' => SORT_ASC])->all(),
                'iata', 'iata'
            );
        }, $duration);
    }

    /**
     * @param int $duration
     * @return array
     */
    public static function getCountryList(int $duration = 5 * 60): array
    {
        return Yii::$app->cacheFile->getOrSet(__FUNCTION__, static function () {
            return ArrayHelper::map(
                self::find()->select(['country'])->distinct()->orderBy(['country' => SORT_ASC])->all(),
                'country', 'country'
            );
        }, $duration);
    }

}
