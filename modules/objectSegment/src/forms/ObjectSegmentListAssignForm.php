<?php

namespace modules\objectSegment\src\forms;

use common\components\validators\IsArrayValidator;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentType;
use modules\taskList\src\entities\taskList\TaskList;
use yii\base\Model;

class ObjectSegmentListAssignForm extends Model
{
    public $objectSegmentId;
    public $objectTypeId;
    public $taskIds;

    public function rules(): array
    {
        return [
            [['objectSegmentId'], 'required'],
            [['objectSegmentId'], 'integer'],
            [['objectSegmentId'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectSegmentList::class, 'targetAttribute' => 'osl_id'],

            [['objectTypeId'], 'required'],
            [['objectTypeId'], 'integer'],
            [['objectTypeId'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectSegmentType::class, 'targetAttribute' => 'ost_id'],

            [['taskIds'], IsArrayValidator::class],
            [['taskIds'], 'default', 'value' => []],
            [['taskIds'], 'each', 'rule' => ['filter', 'filter' => 'intval']],
            [['taskIds'], 'each', 'rule' => ['exist', 'targetClass' => TaskList::class, 'targetAttribute' => 'tl_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'objectSegmentId' => 'Object Segment',
            'taskIds' => 'Tasks',
        ];
    }
}
