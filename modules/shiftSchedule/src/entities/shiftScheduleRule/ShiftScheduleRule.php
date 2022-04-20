<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleRule;

use common\models\Employee;
use Cron\CronExpression;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "shift_schedule_rule".
 *
 * @property int $ssr_id
 * @property int $ssr_shift_id
 * @property string|null $ssr_title
 * @property string|null $ssr_timezone
 * @property string $ssr_start_time_loc
 * @property string|null $ssr_end_time_loc
 * @property int|null $ssr_duration_time
 * @property string|null $ssr_cron_expression
 * @property string|null $ssr_cron_expression_exclude
 * @property int $ssr_enabled
 * @property int|null $ssr_sst_id
 * @property string $ssr_start_time_utc
 * @property string|null $ssr_end_time_utc
 * @property string|null $ssr_created_dt
 * @property string|null $ssr_updated_dt
 * @property int|null $ssr_created_user_id
 * @property int|null $ssr_updated_user_id
 *
 * @property Shift $shift
 * @property ShiftScheduleType $scheduleType
 * @property Employee $user
 * @property Employee $createdUser
 * @property Employee $updatedUser
 * @property UserShiftSchedule[] $userShiftSchedules
 */
class ShiftScheduleRule extends ActiveRecord
{
    private const MAX_VALUE_INT = 2147483647;

    private const CRON_EXPRESSION_MINUTES = '*';
    private const CRON_EXPRESSION_HOURS = '*';

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['ssr_created_dt', 'ssr_updated_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['ssr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['ssr_created_user_id'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['ssr_updated_user_id'],
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['ssr_cron_expression', 'string', 'max' => 100],
            ['ssr_cron_expression_exclude', 'string', 'max' => 100],

            ['ssr_duration_time', 'integer', 'max' => self::MAX_VALUE_INT],

            ['ssr_enabled', 'required'],
            ['ssr_enabled', 'integer', 'max' => 1, 'min' => 0],

            ['ssr_shift_id', 'required'],
            ['ssr_shift_id', 'integer'],
            ['ssr_shift_id', 'exist', 'skipOnError' => true,
                'targetClass' => Shift::class, 'targetAttribute' => ['ssr_shift_id' => 'sh_id']],

            ['ssr_start_time_loc', 'required'],
            [['ssr_start_time_loc', 'ssr_end_time_loc'], 'safe'],

            //['ssr_start_time_utc', 'required'],
            [['ssr_start_time_utc', 'ssr_end_time_utc'], 'safe'],

            ['ssr_timezone', 'string', 'max' => 100],

            ['ssr_title', 'string', 'max' => 255],

            [['ssr_title', 'ssr_timezone', 'ssr_cron_expression', 'ssr_cron_expression_exclude'],
                'default', 'value' => null],

            ['ssr_created_dt', 'safe'],
            ['ssr_updated_dt', 'safe'],

            ['ssr_created_user_id', 'integer'],
            ['ssr_updated_user_id', 'integer'],

            [['ssr_cron_expression', 'ssr_cron_expression_exclude'], 'validateCronExpression'],
            [['ssr_sst_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftScheduleType::class,
                'targetAttribute' => ['ssr_sst_id' => 'sst_id']],
        ];
    }

    public function getShift(): ActiveQuery
    {
        return $this->hasOne(Shift::class, ['sh_id' => 'ssr_shift_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'usa_user_id']);
    }

    public function getUserShiftSchedules(): ActiveQuery
    {
        return $this->hasMany(UserShiftSchedule::class, ['uss_ssr_id' => 'ssr_id']);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ssr_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ssr_updated_user_id']);
    }

    /**
     * Gets query for [[ScheduleType]].
     *
     * @return ActiveQuery|Scopes
     */
    public function getScheduleType(): ActiveQuery
    {
        return $this->hasOne(ShiftScheduleType::class, ['sst_id' => 'ssr_sst_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ssr_id' => 'ID',
            'ssr_shift_id' => 'Shift ID',
            'ssr_title' => 'Title',
            'ssr_timezone' => 'Timezone',
            'ssr_start_time_loc' => 'Start Time Loc',
            'ssr_end_time_loc' => 'End Time Loc',
            'ssr_duration_time' => 'Duration Time',
            'ssr_cron_expression' => 'Cron Expression',
            'ssr_cron_expression_exclude' => 'Cron Expression Exclude',
            'ssr_enabled' => 'Enabled',
            'ssr_start_time_utc' => 'Start Time Utc',
            'ssr_end_time_utc' => 'End Time Utc',
            'ssr_created_dt' => 'Created Dt',
            'ssr_updated_dt' => 'Updated Dt',
            'ssr_created_user_id' => 'Created User ID',
            'ssr_updated_user_id' => 'Updated User ID',
            'ssr_sst_id' => 'Schedule Type',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'shift_schedule_rule';
    }

    public function validateCronExpression($attribute, $params, $validator): bool
    {
        $expression = self::CRON_EXPRESSION_MINUTES . ' ' . self::CRON_EXPRESSION_HOURS . ' ' . $this->$attribute;
        $isValidCronExpression = CronExpression::isValidExpression($expression);

        if (!$isValidCronExpression) {
            $this->addError($attribute, 'Expression not valid');
            return false;
        }
        return true;
    }

    /**
     * @param bool $enabled
     * @return array
     */
    public static function getList(?bool $enabled = null): array
    {
        $query = self::find()->orderBy(['ssr_title' => SORT_ASC]);
        if ($enabled !== null) {
            $query->andWhere(['ssr_enabled' => true]);
        }
        $data = $query->asArray()->all();
        return ArrayHelper::map($data, 'ssr_id', 'ssr_title');
    }

    /**
     * @return string
     */
    public function getScheduleTypeTitle(): string
    {
        return $this->scheduleType ? $this->scheduleType->sst_title : '-';
    }

    /**
     * @return string|null
     */
    public function getCronExpression(): ?string
    {
        return empty($this->ssr_cron_expression) ? null :
            self::CRON_EXPRESSION_MINUTES . ' ' . self::CRON_EXPRESSION_HOURS . ' ' . $this->ssr_cron_expression;
    }

    /**
     * @return string|null
     */
    public function getCronExpressionExclude(): ?string
    {
        return empty($this->ssr_cron_expression_exclude) ? null :
            (self::CRON_EXPRESSION_MINUTES . ' ' . self::CRON_EXPRESSION_HOURS . ' '
            . $this->ssr_cron_expression_exclude);
    }
}
