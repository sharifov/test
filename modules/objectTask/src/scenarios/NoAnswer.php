<?php

namespace modules\objectTask\src\scenarios;

use common\models\Lead;
use common\models\query\LeadFlowQuery;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\entities\repositories\ObjectTaskRepository;
use modules\objectTask\src\jobs\CommandExecutorJob;
use modules\objectTask\src\scenarios\statements\NoAnswerDto;
use modules\objectTask\src\scenarios\statements\NoAnswerObject;
use modules\objectTask\src\services\ObjectTaskService;
use modules\objectTask\src\services\ObjectTaskStatusLogService;
use src\helpers\app\AppHelper;
use src\helpers\DateHelper;
use src\repositories\lead\LeadRepository;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use yii\helpers\Json;

class NoAnswer extends BaseScenario
{
    public const KEY = 'noAnswer';
    public const OBJECT = ObjectTaskService::OBJECT_LEAD;

    public const INTERVAL_TYPE_DAYS = 'days';
    public const INTERVAL_TYPE_HOURS = 'hours';
    public const INTERVAL_TYPE_MINUTES = 'minutes';
    public const INTERVAL_TYPE_SECONDS = 'seconds';

    public const INTERVAL_TYPE_LIST = [
        self::INTERVAL_TYPE_DAYS,
        self::INTERVAL_TYPE_HOURS,
        self::INTERVAL_TYPE_MINUTES,
        self::INTERVAL_TYPE_SECONDS,
    ];

    public function process(): void
    {
        $lead = $this->getObject();

        $groupHash = md5(time() . $lead->id);

        foreach (self::INTERVAL_TYPE_LIST as $intervalType) {
            $intervalData = $this->getConfigParameter($intervalType, []);

            if (empty($intervalData)) {
                continue;
            }

            $leadCurrentDt = $lead->clientTime2;

            if ($intervalType === self::INTERVAL_TYPE_DAYS) {
                $firstDay = array_key_first(
                    $intervalData
                );
                $daysLeft = $this->getDaysIntervalForDistribution();

                if ($daysLeft <= $firstDay) {
                    continue;
                }
            }

            foreach ($intervalData as $interval => $objects) {
                $leadDt = clone $leadCurrentDt;
                $nextEmailDateByLeadTime = $leadDt->modify("+{$interval} {$intervalType}");

                if ($intervalType === self::INTERVAL_TYPE_DAYS) {
                    if ($interval >= $daysLeft) {
                        break;
                    }

                    $nextEmailDateByLeadTime->setTime(
                        $this->getConfigParameter('allowedTime.hour', 10),
                        $this->getConfigParameter('allowedTime.minute', 0)
                    );
                }

                $utcDatetime = $nextEmailDateByLeadTime->setTimezone(new \DateTimeZone('UTC'))
                    ->format('Y-m-d H:i:s');
                $delaySeconds = DateHelper::getDifferentInSecondsByDatesUTC(
                    date('Y-m-d H:i:s'),
                    $utcDatetime
                );

                if (!empty($objects)) {
                    foreach ($objects as $object) {
                        $uuid = UuidHelper::uuid();
                        $job = new CommandExecutorJob(
                            $object['config'],
                            $uuid
                        );

                        $queueID = Yii::$app->queue_db
                            ->delay($delaySeconds)
                            ->push(
                                $job
                            );

                        try {
                            $objectTask = ObjectTask::create(
                                $uuid,
                                $queueID,
                                $this->objectTaskScenario->ots_id,
                                ObjectTaskService::OBJECT_LEAD,
                                $lead->id,
                                $object['command'],
                                $utcDatetime,
                                $groupHash
                            );

                            $objectTask->setPendingStatus();

                            (new ObjectTaskRepository($objectTask))->save();

                            /** @fflag FFlag::FF_KEY_OBJECT_TASK_STATUS_LOG_ENABLE, Object Task status log enable */
                            if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_OBJECT_TASK_STATUS_LOG_ENABLE) === true) {
                                ObjectTaskStatusLogService::createLog(
                                    $uuid,
                                    ObjectTask::STATUS_PENDING,
                                    null,
                                    'Created by noAnswer protocol'
                                );
                            }
                        } catch (\Exception $exception) {
                            Yii::error(
                                AppHelper::throwableLog($exception),
                                'NoAnswer:process'
                            );

                            Yii::$app->queue_db->remove($queueID);

                            throw $exception;
                        }
                    }
                }
            }
        }
    }

    public function getStatementDTO(): NoAnswerDto
    {
        return new NoAnswerDto(
            self::getObject()
        );
    }

    public static function getStatementObject(): NoAnswerObject
    {
        return new NoAnswerObject();
    }

    public static function getTemplate(): array
    {
        $template = [
            'allowedTime' => [
                'hour' => 12,
                'minute' => 0,
            ],
        ];

        foreach (self::INTERVAL_TYPE_LIST as $interval) {
            $template[$interval] = [
                3 => [
                    [
                        'command' => 'name',
                        'config' => [
                            'parameter' => 'value'
                        ],
                    ],
                    [
                        'command' => 'second command name',
                        'config' => [
                            'parameter' => 'value'
                        ],
                    ],
                ]
            ];
        }

        return $template;
    }

    private function getDaysIntervalForDistribution(): int
    {
        $days = 0;
        $lead = $this->getObject();

        if ($lead->firstFlightSegment !== null) {
            /** @var \common\models\LeadFlightSegment $segment */
            $segment = $lead->firstFlightSegment;
            $days = DateHelper::getDifferentInDaysByDatesUTC(
                date('Y-m-d H:i:s'),
                $segment->departure
            );
        }

        return $days;
    }

    public static function leadIsAvailableForProcess(Lead $lead): bool
    {
        $virtualAgentList = \Yii::$app->params['settings']['virtual_agent_list'] ?? [];
        $project = $lead->project;

        return ($project !== null && isset($virtualAgentList[$project->project_key]) && !empty($virtualAgentList[$project->project_key]));
    }

    public function canProcess(): bool
    {
        if (parent::canProcess() === true) {
            return NoAnswer::leadIsAvailableForProcess($this->getObject());
        }

        return false;
    }

    public static function clientResponseLogicInit(Lead $lead): void
    {
        if ($lead->status !== Lead::STATUS_FOLLOW_UP) {
            return;
        }

        try {
            ObjectTaskService::cancelJobs(
                self::KEY,
                ObjectTaskService::OBJECT_LEAD,
                $lead->id,
                'Client has answered'
            );

            if ($lead->status !== Lead::CALL_STATUS_PROCESS) {
                $leadFlow = LeadFlowQuery::getLastOwnerOfLead($lead->id);
                $ownerId = $leadFlow->lf_owner_id ?? null;

                $leadRepository = Yii::createObject(LeadRepository::class);
                $lead->processing($ownerId);
                $leadRepository->save($lead);
            }
        } catch (\Throwable $e) {
            Yii::error(
                AppHelper::throwableLog($e),
                'NoAnswer:removeJobsForLead'
            );
        }
    }

    public function getObject(): Lead
    {
        return $this->object;
    }
}
