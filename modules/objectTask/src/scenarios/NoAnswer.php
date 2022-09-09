<?php

namespace modules\objectTask\src\scenarios;

use common\models\Employee;
use common\models\Lead;
use common\models\query\LeadFlowQuery;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\entities\ObjectTaskScenario;
use modules\objectTask\src\entities\repositories\ObjectTaskRepository;
use modules\objectTask\src\jobs\CommandExecutorJob;
use modules\objectTask\src\scenarios\statements\NoAnswerDto;
use modules\objectTask\src\scenarios\statements\NoAnswerObject;
use modules\objectTask\src\services\NoAnswerProtocolService;
use modules\objectTask\src\services\ObjectTaskService;
use modules\objectTask\src\services\ObjectTaskStatusLogService;
use src\helpers\app\AppHelper;
use src\helpers\DateHelper;
use src\helpers\lead\LeadHelper;
use src\repositories\lead\LeadRepository;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use yii\helpers\VarDumper;

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

    public const PARAMETER_ANSWER_NOTIFICATION = 'answerNotification';
    public const PARAMETER_ANSWER_NOTIFICATION_ROLES = 'roles';
    public const PARAMETER_ANSWER_NOTIFICATION_TITLE = 'title';
    public const PARAMETER_ANSWER_NOTIFICATION_DESCRIPTION = 'description';

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

    public static function getParametersDescription(): array
    {
        $data = [];

        $data[self::PARAMETER_ANSWER_NOTIFICATION] = [
            'description' => 'Client response notification (<span class="text-danger">applies only to the roles included in the lead project</span>)',
            'type' => ['object'],
            'data' => [
                'title' => [
                    'description' => 'Notification title. Can use templates of type {{id}} from the Lead object',
                    'type' => ['text'],
                ],
                'description' => [
                    'description' => 'Notification text. Can use templates of type {{id}} from the Lead object',
                    'type' => ['text'],
                ],
                'roles' => [
                    'description' => 'Roles that should receive notifications',
                    'type' => ['array'],
                    'data' => ['supervision', 'sale_manager'],
                ],
            ],
        ];

        $data['allowedTime'] = [
            'description' => 'Time to send an email to the lead (<span class="text-danger">applies only to the Days interval</span>).',
            'type' => ['object'],
            'data' => [
                'hour' => [
                    'description' => 'Hours, from 0 to 23',
                    'type' => ['integer']
                ],
                'minute' => [
                    'description' => 'Minutes from 0 to 59',
                    'type' => ['integer']
                ],
            ],
        ];

        foreach (self::INTERVAL_TYPE_LIST as $interval) {
            $data[$interval] = [
                'description' => "Object containing the {$interval} on which the email should be sent. The object key is the ordinal number of the day.",
                'type' => ['object'],
                'data' => [
                    3 => [
                        'description' => 'Index number',
                        'type' => ['array'],
                        'data' => [
                            [
                                'command' => 'name',
                                'config' => [
                                    'parameter' => 'value'
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $data;
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

    public static function getVirtualAgentByProjectKey(string $key): ?Employee
    {
        $virtualAgentList = \Yii::$app->params['settings']['virtual_agent_list'] ?? [];

        if (isset($virtualAgentList[$key]) && !empty($virtualAgentList[$key])) {
            return Employee::find()
                ->where([
                    'username' => $virtualAgentList[$key],
                ])
                ->limit(1)
                ->one();
        }

        return null;
    }

    public function canProcess(): bool
    {
        if (parent::canProcess() === true) {
            $lead = $this->getObject();

            /** @fflag FFlag::FF_KEY_NO_ANSWER_PROTOCOL_CHECK_EMAIL_IN_UNSUBSCRIBE_LIST, No Answer check email in unsubscribe list */
            if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_NO_ANSWER_PROTOCOL_CHECK_EMAIL_IN_UNSUBSCRIBE_LIST) === true) {
                $clientEmail = LeadHelper::getFirstEmailNotInUnsubscribeList($lead);

                if ($clientEmail === null) {
                    Yii::warning(VarDumper::dumpAsString([
                        'leadId' => $lead->id,
                        'message' => "Lead email does not exist or is in the unsubscribed list",
                    ]), 'NoAnswer:canProcess');

                    return false;
                }
            }

            return NoAnswer::leadIsAvailableForProcess($lead);
        }

        return false;
    }

    public static function clientResponseLogicInit(Lead $lead): void
    {
        if ($lead->status !== Lead::STATUS_FOLLOW_UP || NoAnswerProtocolService::leadWasInNoAnswer($lead) === false) {
            return;
        }

        try {
            $scenarioIds = ObjectTaskService::cancelJobs(
                self::KEY,
                ObjectTaskService::OBJECT_LEAD,
                $lead->id,
                'Client has answered'
            );

            if ($scenarioIds) {
                foreach ($scenarioIds as $scenarioId) {
                    $objectTaskScenario = ObjectTaskScenario::find()
                        ->where([
                            'ots_id' => $scenarioId,
                        ])
                        ->limit(1)
                        ->one();

                    NoAnswerProtocolService::notifyAboutClientAnswer(
                        $objectTaskScenario,
                        $lead
                    );
                }
            }

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
