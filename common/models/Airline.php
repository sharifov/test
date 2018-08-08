<?php
namespace common\models;


use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Airline model
 *
 * @property integer $id
 * @property string $name
 * @property string $iata
 * @property string $code
 * @property string $iaco
 * @property string $countryCode
 * @property string $country
 */
class Airline extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%airlines}}';
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
            [['code', 'iaco', 'countryCode', 'country'], 'safe']
        ];
    }

    public static function getAirlinesMapping($fullName = false)
    {
        return ($fullName)
            ? ArrayHelper::map(self::find()->asArray()->all(), 'iata', 'name')
            : ArrayHelper::map(self::find()->asArray()->all(), 'iata', 'iata');
    }
}
