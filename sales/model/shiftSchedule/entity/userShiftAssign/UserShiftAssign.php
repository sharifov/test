<?php

namespace sales\model\shiftSchedule\entity\userShiftAssign;

use common\models\Employee;
use sales\model\shiftSchedule\entity\shiftScheduleRule\ShiftScheduleRule;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_shift_assign".
 *
 * @property int $usa_user_id
 * @property int $usa_ssr_id
 * @property string|null $usa_created_dt
 * @property int|null $usa_created_user_id
 *
 * @property ShiftScheduleRule $shiftScheduleRule
 * @property Employee $user
 * @property Employee $createdUser
 */
class UserShiftAssign extends \yii\db\ActiveRecord
{
    private const MAX_VALUE_INT = 2147483647;

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['usa_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['usa_created_user_id'],
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['usa_user_id', 'usa_ssr_id'], 'unique', 'targetAttribute' => ['usa_user_id', 'usa_ssr_id']],

            ['usa_ssr_id', 'required'],
            ['usa_ssr_id', 'integer'],
            ['usa_ssr_id', 'exist', 'skipOnError' => true, 'targetClass' => ShiftScheduleRule::class, 'targetAttribute' => ['usa_ssr_id' => 'ssr_id']],

            ['usa_user_id', 'required'],
            ['usa_user_id', 'integer'],
            ['usa_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['usa_user_id' => 'id']],

            ['usa_created_dt', 'safe'],
            ['usa_created_user_id', 'integer', 'max' => self::MAX_VALUE_INT],
        ];
    }

    public function getShiftScheduleRule(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ShiftScheduleRule::class, ['ssr_id' => 'usa_ssr_id']);
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'usa_user_id']);
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'usa_created_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'usa_user_id' => 'User ID',
            'usa_ssr_id' => 'Ssr ID',
            'usa_created_dt' => 'Created Dt',
            'usa_created_user_id' => 'Created User ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'user_shift_assign';
    }
}
