<?php

namespace modules\shiftSchedule\src\forms;

use common\models\Employee;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\base\Model;
use yii\helpers\Json;

class UserShiftScheduleMultipleUpdateForm extends Model
{
    public array $shift_list = [];
    public ?string $shift_list_json = null;
    public ?string $uss_sst_id = null;
    public ?string $uss_start_utc_dt = null;
    public ?string $uss_end_utc_dt = null;
    public ?string $uss_type_id = null;
    public ?string $uss_status_id = null;
    public ?string $uss_shift_id = null;
    public ?string $uss_ssr_id = null;
    public ?string $uss_user_id = null;

    public function rules(): array
    {
        return [
            ['shift_list_json', 'required'],
            ['shift_list_json', 'safe'],
            ['shift_list_json', 'filter', 'filter' => function ($value) {
                try {
                    $data = Json::decode($value);

                    if (!is_array($data)) {
                        $this->addError('shift_list_json', 'Invalid JSON data for decode');
                        return null;
                    }

                    foreach ($data as $scheduleId) {
                        $model = UserShiftSchedule::findOne(['uss_id' => $scheduleId]);

                        if (!$model) {
                            $this->addError('shift_list_json', 'Not found Schedule ID: ' . $scheduleId);
                            return null;
                        }
                    }

                    $this->shift_list = $data;

                    return $value;
                } catch (\yii\base\Exception $e) {
                    $this->addError('shift_list_json', $e->getMessage());
                    return null;
                }
            }],

            [['uss_sst_id'], 'default', 'value' => null],
            [['uss_sst_id'], 'integer'],
            [['uss_sst_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftScheduleType::class, 'targetAttribute' => ['uss_sst_id' => 'sst_id']],

            ['uss_shift_id', 'default', 'value' => null],
            ['uss_shift_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Shift::class, 'targetAttribute' => ['uss_shift_id' => 'sh_id']],

            ['uss_ssr_id', 'default', 'value' => null],
            ['uss_ssr_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => ShiftScheduleRule::class, 'targetAttribute' => ['uss_ssr_id' => 'ssr_id']],

            ['uss_user_id', 'default', 'value' => null],
            ['uss_user_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uss_user_id' => 'id']],

            ['uss_start_utc_dt', 'safe'],
            ['uss_start_utc_dt', 'default', 'value' => null],

            ['uss_end_utc_dt', 'safe'],
            ['uss_end_utc_dt', 'default', 'value' => null],

            ['uss_type_id', 'default', 'value' => null],
            ['uss_type_id', 'in', 'range' => array_keys(UserShiftSchedule::getTypeList())],

            ['uss_status_id', 'default', 'value' => null],
            ['uss_status_id', 'in', 'range' => array_keys(UserShiftSchedule::getStatusList())],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'uss_sst_id' => 'Schedule Type',
            'uss_start_utc_dt' => 'Start DateTime (UTC)',
            'uss_end_utc_dt' => 'End DateTime (UTC)',
            'uss_type_id' => 'Type',
            'uss_status_id' => 'Status',
            'uss_shift_id' => 'Shift',
            'uss_ssr_id' => 'Schedule Rule',
            'uss_user_id' => 'User',
        ];
    }
}
