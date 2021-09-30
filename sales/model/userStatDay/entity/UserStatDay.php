<?php

namespace sales\model\userStatDay\entity;

use common\models\Employee;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_stat_day".
 *
 * @property int $usd_id
 * @property int $usd_key
 * @property float|null $usd_value
 * @property int $usd_user_id
 * @property int|null $usd_day
 * @property int $usd_month
 * @property int $usd_year
 * @property string|null $usd_created_dt
 *
 * @property Employee $user
 */
class UserStatDay extends \yii\db\ActiveRecord
{

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['usd_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_stat_day';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usd_key', 'usd_user_id', 'usd_month', 'usd_year'], 'required'],
            [['usd_key', 'usd_user_id', 'usd_day', 'usd_month', 'usd_year'], 'integer'],
            [['usd_value'], 'number'],
            [['usd_created_dt'], 'safe'],

            ['usd_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['usd_user_id' => 'id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'usd_id' => 'ID',
            'usd_key' => 'Key',
            'usd_value' => 'Value',
            'usd_user_id' => 'User ID',
            'usd_day' => 'Day',
            'usd_month' => 'Month',
            'usd_year' => 'Year',
            'usd_created_dt' => 'Created Dt',
        ];
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }


    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'usd_user_id']);
    }

    public function create(float $value, int $userId, int $day, int $month, int $year): self
    {
        $self = new self();
        $self->usd_value = $value;
        $self->usd_user_id = $userId;
        $self->usd_day = $day;
        $self->usd_month = $month;
        $self->usd_year = $year;
        return $self;
    }

    public function setGrossProfit(): void
    {
        $this->usd_key = UserStatDayKey::GROSS_PROFIT;
    }
}
