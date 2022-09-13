<?php

namespace modules\quoteAward\src\entities;

use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "quote_flight_program".
 *
 * @property int $gfp_id
 * @property string $gfp_name
 * @property string $gfp_airline_iata
 * @property float|null $gfp_ppm
 * @property string|null $gfp_created_dt
 * @property string|null $gfp_updated_dt
 * @property int|null $gfp_updated_user_id
 *
 * @property Employee $updatedUser
 */
class QuoteFlightProgram extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_flight_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gfp_name', 'gfp_airline_iata'], 'required'],
            [['gfp_ppm'], 'number'],
            [['gfp_created_dt', 'gfp_updated_dt'], 'safe'],
            [['gfp_updated_user_id'], 'integer'],
            [['gfp_name', 'gfp_airline_iata'], 'string', 'max' => 255],
            [['gfp_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['gfp_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'gfp_id' => 'Gfp ID',
            'gfp_name' => 'Gfp Name',
            'gfp_airline_iata' => 'Gfp Airline Iata',
            'gfp_ppm' => 'Gfp Ppm',
            'gfp_created_dt' => 'Gfp Created Dt',
            'gfp_updated_dt' => 'Gfp Updated Dt',
            'gfp_updated_user_id' => 'Gfp Updated User ID',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['gfp_created_dt', 'gfp_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['gfp_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['gfp_updated_user_id'],
                ]
            ],
        ];
    }

    /**
     * Gets query for [[updatedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::className(), ['id' => 'gfp_updated_user_id']);
    }

    public static function getList(): array
    {
        return ArrayHelper::map(self::find()->orderBy('gfp_id ASC')->asArray()->all(), 'gfp_id', 'gfp_name');
    }
}
