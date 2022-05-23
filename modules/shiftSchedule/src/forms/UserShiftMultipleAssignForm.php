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
    public const ACTION_ADD = 'add';
    public const ACTION_REPLACE   = 'replace';
    public const ACTION_REMOVE = 'remove';

    public const ACTION_LIST = [
        self::ACTION_ADD => 'Add',
        self::ACTION_REPLACE => 'Replace',
        self::ACTION_REMOVE => 'Remove'
    ];

    public $userIds;
    public $shftIds;
    public $formAction;

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

            ['formAction', 'default', 'value' => self::ACTION_ADD],
            ['formAction', 'string'],
            ['formAction', 'in', 'range' => array_keys(self::ACTION_LIST)],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'userIds' => 'Users',
            'shftIds' => 'Shifts',
            'formAction' => 'Action',
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
