<?php

namespace modules\taskList\src\forms;

use modules\taskList\src\entities\userTask\UserTask;
use yii\base\Model;

class UserTaskNoteForm extends Model
{
    public $userTaskId;
    public $note;

    private $userTask;


    public function __construct(int $userTaskId, $config = [])
    {
        $this->userTask = UserTask::findOne($userTaskId);
        $this->note = $this->userTask ? $this->userTask->ut_description : '';
        $this->userTaskId = $userTaskId;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['userTaskId'], 'required'],
            [['userTaskId'], 'integer'],
            [['userTaskId'], 'exist', 'skipOnError' => true, 'targetClass' => UserTask::class, 'targetAttribute' => 'ut_id'],

            [['note'], 'required'],
            [['note'], 'string', 'max' => 255]
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'userTaskId' => 'User Task Id',
            'note' => 'Note',
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function getUserTask(): ?UserTask
    {
        return $this->userTask;
    }
}
