<?php

use frontend\helpers\JsonHelper;
use modules\objectSegment\src\contracts\ObjectSegmentListContract;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentTask;
use modules\objectSegment\src\repositories\ObjectSegmentTaskRepository;
use modules\taskList\src\entities\taskList\repository\TaskListRepository;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\TaskObject;
use modules\taskList\src\objects\TargetObjectList;
use src\helpers\app\AppHelper;
use yii\caching\TagDependency;
use yii\db\Migration;

/**
 * Class m220803_095315_add_default_task_list_for_simple_leads
 */
class m220803_095315_add_default_task_list_for_simple_leads extends Migration
{
    private string $prefixTitle = 'Default Task:';
    private array $iterations = [
        'One Shift.' => 0,
        'Two Shift.' => 24,
        'Three Shift.' => 48,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $objectSegmentListLeadSimple = ObjectSegmentList::findOne(['osl_key' => ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_LEAD_TYPE_SIMPLE]);
            if (!$objectSegmentListLeadSimple) {
                throw new \RuntimeException('ObjectSegmentList not found');
            }

            foreach ($this->iterations as $title => $delayHours) {
                $taskEmail = TaskList::create(
                    $this->prefixTitle . ' ' .  $title . ' ' . strtoupper(TaskObject::OBJ_EMAIL),
                    TaskObject::OBJ_EMAIL,
                    TargetObjectList::TARGET_OBJ_LEAD,
                    null,
                    JsonHelper::decode('{"rules": [{"id": "email/email.is_offer_template", "type": "boolean", "field": "email.is_offer_template", "input": "radio", "value": true, "operator": "=="}], "valid": true, "condition": "AND"}'),
                    JsonHelper::decode('{"delayHours": ' . $delayHours . '}'),
                    480,
                    0
                );
                if (!$taskEmail->validate()) {
                    throw new \RuntimeException(\src\helpers\ErrorsToStringHelper::extractFromModel($taskEmail, ' '));
                }
                (new TaskListRepository($taskEmail))->save();

                $objectSegmentTask = ObjectSegmentTask::create($objectSegmentListLeadSimple->osl_id, $taskEmail->tl_id);
                (new ObjectSegmentTaskRepository($objectSegmentTask))->save();

                $taskSms = TaskList::create(
                    $this->prefixTitle . ' ' .  $title . ' ' . strtoupper(TaskObject::OBJ_SMS),
                    TaskObject::OBJ_SMS,
                    TargetObjectList::TARGET_OBJ_LEAD,
                    null,
                    JsonHelper::decode('{"rules": [{"id": "sms/sms.is_offer_template", "type": "boolean", "field": "sms.is_offer_template", "input": "radio", "value": true, "operator": "=="}], "valid": true, "condition": "AND"}'),
                    JsonHelper::decode('{"delayHours": ' . $delayHours . '}'),
                    480,
                    0
                );
                if (!$taskSms->validate()) {
                    throw new \RuntimeException(\src\helpers\ErrorsToStringHelper::extractFromModel($taskSms, ' '));
                }
                (new TaskListRepository($taskSms))->save();

                $objectSegmentTask = ObjectSegmentTask::create($objectSegmentListLeadSimple->osl_id, $taskSms->tl_id);
                (new ObjectSegmentTaskRepository($objectSegmentTask))->save();

                $taskCall = TaskList::create(
                    $this->prefixTitle . ' ' .  $title . ' ' . strtoupper(TaskObject::OBJ_CALL),
                    TaskObject::OBJ_CALL,
                    TargetObjectList::TARGET_OBJ_LEAD,
                    null,
                    JsonHelper::decode('{"rules": [{"id": "call.target_object_call_attempts", "type": "integer", "field": "call.target_object_call_attempts", "input": "number", "value": 2, "operator": ">="}, {"id": "call.target_object_call_completed", "type": "integer", "field": "call.target_object_call_completed", "input": "number", "value": 1, "operator": ">="}], "valid": true, "condition": "OR"}'),
                    JsonHelper::decode('{"delayHours": ' . $delayHours . '}'),
                    480,
                    0
                );
                if (!$taskCall->validate()) {
                    throw new \RuntimeException(\src\helpers\ErrorsToStringHelper::extractFromModel($taskCall, ' '));
                }
                (new TaskListRepository($taskCall))->save();

                $objectSegmentTask = ObjectSegmentTask::create($objectSegmentListLeadSimple->osl_id, $taskCall->tl_id);
                (new ObjectSegmentTaskRepository($objectSegmentTask))->save();
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'm220803_095315_add_default_task_list_for_simple_leads:safeUp'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (TaskList::deleteAll(['LIKE', 'tl_title', $this->prefixTitle])) {
            TagDependency::invalidate(Yii::$app->cache, TaskList::CACHE_TAG);
        }
    }
}
