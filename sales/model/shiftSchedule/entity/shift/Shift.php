<?php

namespace sales\model\shiftSchedule\entity\shift;

use common\models\Employee;
use sales\model\shiftSchedule\entity\shiftScheduleRule\ShiftScheduleRule;
use sales\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shift".
 *
 * @property int $sh_id
 * @property string $sh_name
 * @property int $sh_enabled
 * @property string|null $sh_color
 * @property int|null $sh_sort_order
 * @property string|null $sh_created_dt
 * @property string|null $sh_updated_dt
 * @property int|null $sh_created_user_id
 * @property int|null $sh_updated_user_id
 *
 * @property ShiftScheduleRule[] $shiftScheduleRules
 * @property UserShiftSchedule[] $userShiftSchedules
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class Shift extends ActiveRecord
{
    private const MAX_VALUE_INT = 2147483647;

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['sh_created_dt', 'sh_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['sh_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['sh_created_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['sh_updated_user_id'],
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['sh_color', 'string', 'max' => 15, 'isEmpty' => null],
            ['sh_color', 'default', 'value' => null],

            ['sh_enabled', 'required'],
            ['sh_enabled', 'integer', 'max' => 1, 'min' => 0],

            ['sh_name', 'required'],
            ['sh_name', 'string', 'max' => 100],

            ['sh_sort_order', 'integer', 'max' => self::MAX_VALUE_INT],

            ['sh_created_dt', 'safe'],
            ['sh_updated_dt', 'safe'],

            ['sh_created_user_id', 'integer', 'max' => self::MAX_VALUE_INT],
            ['sh_updated_user_id', 'integer', 'max' => self::MAX_VALUE_INT],
        ];
    }

    public function getShiftScheduleRules(): ActiveQuery
    {
        return $this->hasMany(ShiftScheduleRule::class, ['ssr_shift_id' => 'sh_id']);
    }

    public function getUserShiftSchedules(): ActiveQuery
    {
        return $this->hasMany(UserShiftSchedule::class, ['uss_shift_id' => 'sh_id']);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'sh_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'sh_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'sh_id' => 'ID',
            'sh_name' => 'Name',
            'sh_enabled' => 'Enabled',
            'sh_color' => 'Color',
            'sh_sort_order' => 'Sort Order',
            'sh_created_dt' => 'Created Dt',
            'sh_updated_dt' => 'Updated Dt',
            'sh_created_user_id' => 'Created User ID',
            'sh_updated_user_id' => 'Updated User ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'shift';
    }
}
