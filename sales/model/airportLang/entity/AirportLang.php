<?php

namespace sales\model\airportLang\entity;

use common\models\Airports;
use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "airport_lang".
 *
 * @property string $ail_iata
 * @property string $ail_lang
 * @property string|null $ail_name
 * @property string|null $ail_city
 * @property string|null $ail_country
 * @property int|null $ail_created_user_id
 * @property int|null $ail_updated_user_id
 * @property string|null $ail_created_dt
 * @property string|null $ail_updated_dt
 *
 * @property Airports $airportsIata
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class AirportLang extends ActiveRecord
{
    public function rules(): array
    {
        return [
            [['ail_iata', 'ail_lang'], 'unique', 'targetAttribute' => ['ail_iata', 'ail_lang']],

            ['ail_city', 'string', 'max' => 40],

            ['ail_country', 'string', 'max' => 160],

            ['ail_created_dt', 'safe'],
            ['ail_updated_dt', 'safe'],

            ['ail_created_user_id', 'integer'],
            ['ail_updated_user_id', 'integer'],

            [['ail_iata', 'ail_lang'], 'filter', 'filter' => 'trim'],
            [['ail_iata', 'ail_lang'], 'filter', 'filter' => 'strtoupper'],

            ['ail_iata', 'required'],
            ['ail_iata', 'string', 'max' => 3],
            ['ail_iata', 'exist', 'skipOnError' => true, 'targetClass' => Airports::class, 'targetAttribute' => ['ail_iata' => 'iata']],

            ['ail_lang', 'required'],
            ['ail_lang', 'string', 'max' => 2],

            ['ail_name', 'string', 'max' => 255],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ail_created_dt', 'ail_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ail_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ail_created_user_id',
                'updatedByAttribute' => 'ail_updated_user_id',
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getAirportsIata(): ActiveQuery
    {
        return $this->hasOne(Airports::class, ['iata' => 'ail_iata']);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ail_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ail_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ail_iata' => 'Iata',
            'ail_lang' => 'Lang',
            'ail_name' => 'Name',
            'ail_city' => 'City',
            'ail_country' => 'Country',
            'ail_created_user_id' => 'Created User',
            'ail_updated_user_id' => 'Updated User',
            'ail_created_dt' => 'Created Dt',
            'ail_updated_dt' => 'Updated Dt',
        ];
    }

    public static function tableName(): string
    {
        return '{{%airport_lang}}';
    }

    public static function find(): AirportLangScopes
    {
        return new AirportLangScopes(static::class);
    }
}
