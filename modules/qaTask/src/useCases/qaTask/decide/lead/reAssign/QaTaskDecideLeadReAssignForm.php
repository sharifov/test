<?php

namespace modules\qaTask\src\useCases\qaTask\decide\lead\reAssign;

use common\models\Employee;
use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\useCases\qaTask\QaTaskActionForm;
use sales\access\ListsAccess;

/**
 * Class QaTaskDecideLeadReAssignForm
 *
 * @property int $assignUserId
 * @property array $users
 */
class QaTaskDecideLeadReAssignForm extends QaTaskActionForm
{
    public $assignUserId;

    private $users;

    public function __construct(QaTask $task, Employee $user, $config = [])
    {
        parent::__construct($task, $user, $config);
        $this->users = (new ListsAccess($this->getUserId()))->getEmployees();
    }

    public function rules(): array
    {
        return [
            ['assignUserId', 'required'],
            ['assignUserId', 'integer'],
            ['assignUserId', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['assignUserId', 'in', 'range' => array_keys($this->getUserList())],
        ];
    }

    public function getUserList(): array
    {
        return $this->users;
    }

    public function attributeLabels(): array
    {
        return [
            'assignUserId' => 'Employee',
        ];
    }
}
