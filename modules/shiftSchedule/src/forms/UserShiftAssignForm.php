<?php

namespace modules\shiftSchedule\src\forms;

use common\components\validators\IsArrayValidator;
use common\models\Employee;
use modules\shiftSchedule\src\entities\shift\Shift;
use yii\base\Model;

/**
 * Class UserShiftAssignForm
 */
class UserShiftAssignForm extends Model
{
    public $userId;
    public $shftIds;

    public function rules(): array
    {
        return [
            [['userId'], 'required'],
            [['userId'], 'integer'],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['userId' => 'id']],

            [['shftIds'], IsArrayValidator::class],
            [['shftIds'], 'each', 'rule' => ['filter', 'filter' => 'intval']],
            [['shftIds'], 'each', 'rule' => ['exist', 'targetClass' => Shift::class, 'targetAttribute' => 'sh_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'userId' => 'User',
            'shftIds' => 'Shifts',
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
