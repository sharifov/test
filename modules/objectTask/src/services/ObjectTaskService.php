<?php

namespace modules\objectTask\src\services;

use common\models\Lead;
use modules\objectTask\src\commands\SendEmailWithQuotes;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\entities\ObjectTaskScenario;
use modules\objectTask\src\entities\repositories\ObjectTaskRepository;
use modules\objectTask\src\scenarios\BaseScenario;
use modules\objectTask\src\scenarios\NoAnswer;
use modules\objectTask\src\scenarios\statements\BaseObject;
use src\helpers\app\AppHelper;
use src\model\leadData\entity\LeadData;
use src\model\leadData\repository\LeadDataRepository;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use Yii;

class ObjectTaskService
{
    public const SCENARIO_NO_ANSWER = NoAnswer::KEY;

    public const SCENARIO_LIST = [
        self::SCENARIO_NO_ANSWER => 'No answer',
    ];

    public const SCENARIO_CLASS_LIST = [
        NoAnswer::KEY => NoAnswer::class,
    ];

    public const COMMAND_LIST = [
        SendEmailWithQuotes::COMMAND => 'Send email with quotes',
    ];

    public const COMMAND_CLASS_LIST = [
        SendEmailWithQuotes::COMMAND => SendEmailWithQuotes::class,
    ];

    public const OBJECT_LEAD = 'lead';

    public static function runScenario(string $key, mixed $object, ?int $scenarioId = null): void
    {
        $otsQuery = ObjectTaskScenario::find()
            ->where([
                'ots_key' => $key,
                'ots_enable' => 1,
            ]);

        if ($scenarioId !== null) {
            $otsQuery->andWhere([
                'ots_id' => $scenarioId,
            ]);
        }

        $objectTaskScenarios = $otsQuery->all();

        if (empty($objectTaskScenarios)) {
            return;
        }

        foreach ($objectTaskScenarios as $objectTaskScenario) {
                /** @var BaseScenario $scenario */
            $scenario = Yii::createObject(
                self::SCENARIO_CLASS_LIST[$key],
                [
                    'objectTaskScenario' => $objectTaskScenario,
                    'object' => $object,
                ]
            );

            if ($scenario->canProcess()) {
                if ($object instanceof Lead) {
                    try {
                        $leadDataRepository = \Yii::createObject(LeadDataRepository::class);
                        $leadDataFollowUp = LeadData::create($object->id, LeadDataKeyDictionary::KEY_AUTO_FOLLOW_UP, $key);

                        $leadDataRepository->save($leadDataFollowUp);
                    } catch (\Throwable $e) {
                        \Yii::error([
                            'message' => $e->getMessage(),
                            'leadId' => $object->id,
                            'scenarioName' => $key,
                            'scenarioId' => $objectTaskScenario->ots_id,
                        ], 'ObjectTaskService::runScenario');
                    }
                }

                try {
                    $scenario->process();
                } catch (\Throwable $exception) {
                    Yii::error(
                        AppHelper::throwableLog($exception),
                        'ObjectTaskService:runScenario'
                    );
                }
            }
        }
    }

    public static function cancelJobs(string $scenarioKey, string $object, int $objectId, ?string $description = null): void
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
                try {
                    $objectTask->setCanceledStatus(
                        $description
                    );

                    (new ObjectTaskRepository($objectTask))->save();
                } catch (\Throwable $e) {
                    \Yii::error(
                        AppHelper::throwableLog($e),
                        'ObjectTaskService::cancelJobs::objectTask::save'
                    );
                }
            }
        }
    }

    public static function getStatementObjectForScenario(string $key): ?BaseObject
    {
        /** @var BaseScenario $class */
        $class = self::SCENARIO_CLASS_LIST[$key] ?? null;

        if ($class !== null) {
            try {
                return $class::getStatementObject();
            } catch (\Throwable $e) {
                Yii::error(
                    AppHelper::throwableLog($e),
                    'ObjectTaskService:getStatementObjectForScenario'
                );
            }
        }

        return null;
    }

    public static function getTemplateForScenario(string $key): array
    {
        $data = [];
        /** @var BaseScenario $class */
        $class = self::SCENARIO_CLASS_LIST[$key] ?? null;

        if ($class !== null) {
            try {
                $data = $class::getTemplate();
            } catch (\Throwable $e) {
                Yii::error(
                    AppHelper::throwableLog($e),
                    'ObjectTaskService:getTemplateForScenario'
                );
            }
        }

        return $data;
    }

    public static function getParametersDescriptionForScenario(string $key): array
    {
        $data = [];
        /** @var BaseScenario $class */
        $class = self::SCENARIO_CLASS_LIST[$key] ?? null;

        if ($class !== null) {
            try {
                $data = $class::getParametersDescription();
            } catch (\Throwable $e) {
                Yii::error(
                    AppHelper::throwableLog($e),
                    'ObjectTaskService:getParametersDescriptionForScenario'
                );
            }
        }

        return $data;
    }
}
