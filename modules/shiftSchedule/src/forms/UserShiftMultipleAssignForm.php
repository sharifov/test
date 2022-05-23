<?php

namespace modules\shiftSchedule\src\forms;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use modules\shiftSchedule\src\entities\shift\Shift;
use src\dictionary\ActionDictionary;
use yii\base\Model;

/**
 * Class UserShiftMultipleAssignForm
 */
class UserShiftMultipleAssignForm extends Model
{
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

            ['formAction', 'default', 'value' => ActionDictionary::ACTION_ADD],
            ['formAction', 'string'],
            ['formAction', 'in', 'range' => array_keys(ActionDictionary::BASE_ACTION_LIST)],
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
