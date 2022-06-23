<?php

namespace modules\taskList\src\forms;

use common\components\validators\IsArrayValidator;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentType;
use modules\taskList\src\entities\taskList\TaskList;
use yii\base\Model;

class TaskListAssignForm extends Model
{
    public $taskListId;
    public $objectTypeId;
    public $objectSegmentIds;

    public function rules(): array
    {
        return [
            [['taskListId'], 'required'],
            [['taskListId'], 'integer'],
            [['taskListId'], 'exist', 'skipOnError' => true, 'targetClass' => TaskList::class, 'targetAttribute' => 'tl_id'],

            [['objectTypeId'], 'required'],
            [['objectTypeId'], 'integer'],
            [['objectTypeId'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectSegmentType::class, 'targetAttribute' => 'ost_id'],

            [['objectSegmentIds'], IsArrayValidator::class],
            [['objectSegmentIds'], 'default', 'value' => []],
            [['objectSegmentIds'], 'each', 'rule' => ['filter', 'filter' => 'intval']],
            [['objectSegmentIds'], 'each', 'rule' => ['exist', 'targetClass' => ObjectSegmentList::class, 'targetAttribute' => 'osl_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'taskListId' => 'Task List Id',
            'objectSegmentIds' => 'Object Segments',
        ];
    }
}
