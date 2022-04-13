<?php

namespace src\model\shiftSchedule\entity\shift;

use common\models\Employee;
use src\model\shiftSchedule\entity\shiftCategory\ShiftCategory;
use src\model\shiftSchedule\entity\shiftScheduleRule\ShiftScheduleRule;
use src\model\shiftSchedule\entity\userShiftAssign\UserShiftAssign;
use src\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
 * @property string $sh_title [varchar(255)]
 * @property int $sh_category_id [int]
 * @property ShiftCategory $category
 * @property UserShiftAssign[] $userShiftAssigns
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
            ['sh_title', 'string', 'max' => 255],

            ['sh_sort_order', 'integer', 'max' => self::MAX_VALUE_INT],

            ['sh_created_dt', 'safe'],
            ['sh_updated_dt', 'safe'],

            ['sh_created_user_id', 'integer', 'max' => self::MAX_VALUE_INT],
            ['sh_updated_user_id', 'integer', 'max' => self::MAX_VALUE_INT],

            ['sh_category_id', 'integer', 'max' => self::MAX_VALUE_INT],
            [['sh_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftCategory::class, 'targetAttribute' => ['sh_category_id' => 'sc_id']],
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

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(ShiftCategory::class, ['sc_id' => 'sh_category_id']);
    }

    public function getUserShiftAssigns(): \yii\db\ActiveQuery
    {
        return $this->hasMany(UserShiftAssign::class, ['usa_sh_id' => 'sh_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'sh_id' => 'ID',
            'sh_name' => 'Name',
            'sh_enabled' => 'Enabled',
            'sh_title' => 'Title',
            'sh_category_id' => 'Category',
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

    /**
     * @param bool $enabled
     * @return array
     */
    public static function getList(?bool $enabled = null): array
    {
        $query = self::find()->orderBy(['sh_sort_order' => SORT_ASC]);
        if ($enabled !== null) {
            $query->andWhere(['sh_enabled' => true]);
        }
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'sh_id', 'sh_title');
    }
}
