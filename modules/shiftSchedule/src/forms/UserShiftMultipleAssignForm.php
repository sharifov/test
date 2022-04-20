<?php

namespace modules\shiftSchedule\src\forms;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use modules\shiftSchedule\src\entities\shift\Shift;
use yii\base\Model;

/**
 * Class UserShiftMultipleAssignForm
 */
class UserShiftMultipleAssignForm extends Model
{
    public $userIds;
    public $shftIds;

    public function rules(): array
    {
        return [
            [['userIds'], 'required'],
            [['userIds'], IsArrayValidator::class],
            [['userIds'], 'each', 'rule' => ['filter', 'filter' => 'intval']],
            [['userIds'], 'each', 'rule' => ['exist', 'targetClass' => Employee::class, 'targetAttribute' => 'id']],

            [['shftIds'], IsArrayValidator::class],
            [['shftIds'], 'each', 'rule' => ['filter', 'filter' => 'intval']],
            [['shftIds'], 'each', 'rule' => ['exist', 'targetClass' => Shift::class, 'targetAttribute' => 'sh_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'userIds' => 'Users',
            'shftIds' => 'Shifts',
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
