<?php

namespace modules\objectTask\src\services;

use common\models\Lead;
use modules\objectTask\src\commands\SendEmailWithQuotes;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\entities\ObjectTaskScenario;
use modules\objectTask\src\scenarios\NoAnswer;
use Yii;

class ObjectTaskService
{
    public const SCENARIO_NO_ANSWER = NoAnswer::KEY;

    public const SCENARIO_LIST = [
        self::SCENARIO_NO_ANSWER => 'No answer',
    ];

    public const COMMAND_LIST = [
        SendEmailWithQuotes::COMMAND => 'Send email with quotes',
    ];

    public const COMMAND_CLASS_LIST = [
        SendEmailWithQuotes::COMMAND => SendEmailWithQuotes::class,
    ];

    public const OBJECT_LEAD = 'lead';

    public const OBJECT_MODEL_LIST = [
        self::OBJECT_LEAD => Lead::class,
    ];

    public static function cancelJobs(string $scenarioKey, string $object, int $objectId): void
    {
        $objectTaskScenario = ObjectTaskScenario::find()
            ->where([
                'ots_key' => $scenarioKey,
            ])
            ->limit(1)
            ->one();

        $pendingObjectTaskList = ObjectTask::find()
            ->where([
                'ot_ots_id' => $objectTaskScenario->ots_id,
                'ot_object' => $object,
                'ot_object_id' => $objectId,
                'ot_status' => ObjectTask::STATUS_PENDING
            ])
            ->all();

        if (!empty($pendingObjectTaskList)) {
            foreach ($pendingObjectTaskList as $objectTask) {
                Yii::$app->queue_db->remove(
                    $objectTask->ot_q_id
                );

                $objectTask->ot_q_id = null;
                $objectTask->setCanceledStatus();
                if ($objectTask->save() === false) {
                    \Yii::error(
                        \yii\helpers\VarDumper::dumpAsString([
                            'objectTaskUuid' => $objectTask->ot_uuid,
                            $objectTask->getErrors()
                        ]),
                        'ObjectTaskService::cancelJobs::objectTask::save'
                    );
                }
            }
        }
    }
}
