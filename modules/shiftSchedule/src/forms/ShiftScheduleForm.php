<?php

namespace modules\shiftSchedule\forms;

use common\models\Employee;
use Cron\CronExpression;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\base\Model;
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
 *
 */
class ShiftScheduleForm extends Model
{
    private const MAX_VALUE_INT = 2147483647;
    private const CRON_EXPRESSION_MINUTES = '*';
    private const CRON_EXPRESSION_HOURS = '*';

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

            ['ssr_start_time_utc', 'required'],
            [['ssr_start_time_utc', 'ssr_end_time_utc'], 'safe'],

            ['ssr_timezone', 'string', 'max' => 100],

            ['ssr_title', 'string', 'max' => 255],

            [['ssr_title', 'ssr_timezone', 'ssr_cron_expression', 'ssr_cron_expression_exclude'],
                'default', 'value' => null],


            [['ssr_cron_expression', 'ssr_cron_expression_exclude'], 'validateCronExpression'],
            [['ssr_sst_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftScheduleType::class,
                'targetAttribute' => ['ssr_sst_id' => 'sst_id']],
        ];
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
            'ssr_sst_id' => 'Schedule Type',
        ];
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
}
