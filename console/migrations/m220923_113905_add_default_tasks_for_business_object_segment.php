<?php

use modules\taskList\src\entities\taskList\repository\TaskListRepository;
use modules\objectSegment\src\entities\ObjectSegmentListQuery;
use modules\objectSegment\src\entities\ObjectSegmentTask;
use modules\taskList\src\entities\taskList\TaskList;
use src\helpers\ErrorsToStringHelper;
use yii\db\Migration;

/**
 * Class m220923_113905_add_default_tasks_for_business_object_segment
 */
class m220923_113905_add_default_tasks_for_business_object_segment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tasksIds = [];
        $tasks = $this->getTasksList();
        $recipientSegment = ObjectSegmentListQuery::getByKey('lead_business_type');

        if (empty($recipientSegment)) {
            \Yii::error(
                'Couldn`t find object segment by key - lead_business_type',
                'm220921_175617_add_default_tasks_for_business_object_segment:safeUp'
            );
            return false;
        }

        // Save tasks
        if (!empty($tasks)) {
            foreach ($tasks as $taskData) {
                try {
                    $taskListEntity = new TaskList();
                    $taskListEntity->load($taskData, '');

                    if (!$taskListEntity->validate()) {
                        throw new \Exception(ErrorsToStringHelper::extractFromModel($taskListEntity, ' '));
                    }

                    (new TaskListRepository($taskListEntity))->save();
                    $tasksIds[] = $taskListEntity->tl_id;
                } catch (\Throwable $e) {
                    \Yii::error([
                        'message' => $e->getMessage(),
                        'task' => $taskListEntity,
                    ], 'm220921_175617_add_default_tasks_for_business_object_segment:safeUp:Throwable');
                }
            }
        }

        // Save object segment tasks
        if (!empty($tasksIds)) {
            try {
                ObjectSegmentTask::deleteOrAddTasks($recipientSegment->osl_id, $tasksIds);
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => $e->getMessage(),
                    'tasksIds' => $tasksIds,
                    'osl_id' => $recipientSegment->osl_id,
                ], 'm220921_175617_add_default_tasks_for_business_object_segment:safeUp:Throwable:2');
            }
        }

        return true;
    }

    /**
     * @return array[]
     */
    protected function getTasksList(): array
    {
        return [
            [
                'tl_title' => 'Business Default Task: One Shift. EMAIL',
                'tl_object' => 'email',
                'tl_target_object_id' => 1,
                'tl_condition' => '(email.is_offer_template == true)',
                'tl_condition_json' => [
                    "rules" => [
                        [
                            "id" => "email/email.is_offer_template",
                            "type" => "boolean",
                            "field" => "email.is_offer_template",
                            "input" => "radio",
                            "value" => true,
                            "operator" => "==",
                        ],
                    ],
                    "valid" => true,
                    "condition" => "OR",
                ],
                'tl_params_json' => ["delayHours" => 0, "delayShift" => 0],
                'tl_duration_min' => 480,
                'tl_enable_type' => 0,
                'tl_cron_expression' => '* * * * *',
                'tl_sort_order' => 0,
            ],
            [
                'tl_title' => 'Business Default Task: One Shift. SMS',
                'tl_object' => 'sms',
                'tl_target_object_id' => 1,
                'tl_condition' => '(sms.is_offer_template == true)',
                'tl_condition_json' => [
                    "rules" => [
                        [
                            "id" => "sms/sms.is_offer_template",
                            "type" => "boolean",
                            "field" => "sms.is_offer_template",
                            "input" => "radio",
                            "value" => true,
                            "operator" => "==",
                        ],
                    ],
                    "valid" => true,
                    "condition" => "AND",
                ],
                'tl_params_json' => ["delayHours" => 0, "delayShift" => 0],
                'tl_duration_min' => 480,
                'tl_enable_type' => 0,
                'tl_cron_expression' => '* * * * *',
                'tl_sort_order' => 0,
            ],
            [
                'tl_title' => 'Business Default Task: One Shift. CALL',
                'tl_object' => 'call',
                'tl_target_object_id' => 1,
                'tl_condition' => '(call.target_object_call_attempts >= 2) || (call.target_object_call_completed >= 1)',
                'tl_condition_json' => [
                    "rules" => [
                        [
                            "id" => "call.target_object_call_attempts",
                            "type" => "integer",
                            "field" => "call.target_object_call_attempts",
                            "input" => "number",
                            "value" => 2,
                            "operator" => ">=",
                        ],
                        [
                            "id" => "call.target_object_call_completed",
                            "type" => "integer",
                            "field" => "call.target_object_call_completed",
                            "input" => "number",
                            "value" => 1,
                            "operator" => ">=",
                        ],
                    ],
                    "valid" => true,
                    "condition" => "OR",
                ],
                'tl_params_json' => ["delayHours" => 0, "delayShift" => 0],
                'tl_duration_min' => 480,
                'tl_enable_type' => 0,
                'tl_cron_expression' => '* * * * *',
                'tl_sort_order' => 0,
            ],

            [
                'tl_title' => 'Business Default Task: Two Shift. EMAIL',
                'tl_object' => 'email',
                'tl_target_object_id' => 1,
                'tl_condition' => '(email.is_offer_template == true)',
                'tl_condition_json' => [
                    "rules" => [
                        [
                            "id" => "email/email.is_offer_template",
                            "type" => "boolean",
                            "field" => "email.is_offer_template",
                            "input" => "radio",
                            "value" => true,
                            "operator" => "==",
                        ],
                    ],
                    "valid" => true,
                    "condition" => "AND",
                ],
                'tl_params_json' => ["delayHours" => 0, "delayShift" => 1],
                'tl_duration_min' => 480,
                'tl_enable_type' => 0,
                'tl_cron_expression' => '* * * * *',
                'tl_sort_order' => 0,
            ],
            [
                'tl_title' => 'Business Default Task: Two Shift. SMS',
                'tl_object' => 'sms',
                'tl_target_object_id' => 1,
                'tl_condition' => '(sms.is_offer_template == true)',
                'tl_condition_json' => [
                    "rules" => [
                        [
                            "id" => "sms/sms.is_offer_template",
                            "type" => "boolean",
                            "field" => "sms.is_offer_template",
                            "input" => "radio",
                            "value" => true,
                            "operator" => "==",
                        ],
                    ],
                    "valid" => true,
                    "condition" => "AND",
                ],
                'tl_params_json' => ["delayHours" => 0, "delayShift" => 1],
                'tl_duration_min' => 480,
                'tl_enable_type' => 0,
                'tl_cron_expression' => '* * * * *',
                'tl_sort_order' => 0,
            ],
            [
                'tl_title' => 'Business Default Task: Two Shift. CALL',
                'tl_object' => 'call',
                'tl_target_object_id' => 1,
                'tl_condition' => '(call.target_object_call_attempts >= 2) || (call.target_object_call_completed >= 1)',
                'tl_condition_json' => [
                    "rules" => [
                        [
                            "id" => "call.target_object_call_attempts",
                            "type" => "integer",
                            "field" => "call.target_object_call_attempts",
                            "input" => "number",
                            "value" => 2,
                            "operator" => ">=",
                        ],
                        [
                            "id" => "call.target_object_call_completed",
                            "type" => "integer",
                            "field" => "call.target_object_call_completed",
                            "input" => "number",
                            "value" => 1,
                            "operator" => ">=",
                        ],
                    ],
                    "valid" => true,
                    "condition" => "OR",
                ],
                'tl_params_json' => ["delayHours" => 0, "delayShift" => 1],
                'tl_duration_min' => 480,
                'tl_enable_type' => 0,
                'tl_cron_expression' => '* * * * *',
                'tl_sort_order' => 0,
            ],

            [
                'tl_title' => 'Business Default Task: Three Shift. EMAIL',
                'tl_object' => 'email',
                'tl_target_object_id' => 1,
                'tl_condition' => '(email.is_offer_template == true)',
                'tl_condition_json' => [
                    "rules" => [
                        [
                            "id" => "email/email.is_offer_template",
                            "type" => "boolean",
                            "field" => "email.is_offer_template",
                            "input" => "radio",
                            "value" => true,
                            "operator" => "==",
                        ],
                    ],
                    "valid" => true,
                    "condition" => "AND",
                ],
                'tl_params_json' => ["delayHours" => 0, "delayShift" => 2],
                'tl_duration_min' => 480,
                'tl_enable_type' => 0,
                'tl_cron_expression' => '* * * * *',
                'tl_sort_order' => 0,
            ],
            [
                'tl_title' => 'Business Default Task: Three Shift. SMS',
                'tl_object' => 'sms',
                'tl_target_object_id' => 1,
                'tl_condition' => '(sms.is_offer_template == true)',
                'tl_condition_json' => [
                    "rules" => [
                        [
                            "id" => "sms/sms.is_offer_template",
                            "type" => "boolean",
                            "field" => "sms.is_offer_template",
                            "input" => "radio",
                            "value" => true,
                            "operator" => "==",
                        ],
                    ],
                    "valid" => true,
                    "condition" => "AND",
                ],
                'tl_params_json' => ["delayHours" => 0, "delayShift" => 2],
                'tl_duration_min' => 480,
                'tl_enable_type' => 0,
                'tl_cron_expression' => '* * * * *',
                'tl_sort_order' => 0,
            ],
            [
                'tl_title' => 'Business Default Task: Three Shift. CALL',
                'tl_object' => 'call',
                'tl_target_object_id' => 1,
                'tl_condition' => '(call.target_object_call_attempts >= 2) || (call.target_object_call_completed >= 1)',
                'tl_condition_json' => [
                    "rules" => [
                        [
                            "id" => "call.target_object_call_attempts",
                            "type" => "integer",
                            "field" => "call.target_object_call_attempts",
                            "input" => "number",
                            "value" => 2,
                            "operator" => ">=",
                        ],
                        [
                            "id" => "call.target_object_call_completed",
                            "type" => "integer",
                            "field" => "call.target_object_call_completed",
                            "input" => "number",
                            "value" => 1,
                            "operator" => ">=",
                        ],
                    ],
                    "valid" => true,
                    "condition" => "OR",
                ],
                'tl_params_json' => ["delayHours" => 0, "delayShift" => 2],
                'tl_duration_min' => 480,
                'tl_enable_type' => 0,
                'tl_cron_expression' => '* * * * *',
                'tl_sort_order' => 0,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220923_113905_add_default_tasks_for_business_object_segment cannot be reverted.\n";
        return false;
    }
}
