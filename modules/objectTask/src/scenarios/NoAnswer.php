<?php

namespace modules\objectTask\src\scenarios;

use common\models\Lead;
use common\models\query\LeadFlowQuery;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\entities\repositories\ObjectTaskRepository;
use modules\objectTask\src\jobs\CommandExecutorJob;
use modules\objectTask\src\services\ObjectTaskService;
use src\helpers\app\AppHelper;
use src\helpers\DateHelper;
use src\repositories\lead\LeadRepository;
use thamtech\uuid\helpers\UuidHelper;
use Yii;
use yii\helpers\Json;

class NoAnswer extends BaseScenario
{
    public const KEY = 'noAnswer';

    private Lead $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;

        parent::__construct();
    }

    public function process(): void
    {
        if ($this->isEnable() === false || NoAnswer::leadIsAvailableForProcess($this->lead) === false) {
            return;
        }

        $daysList = $this->getConfigParameter('days');
        $firstDay = array_key_first(
            $daysList
        );
        $daysLeft = $this->getDaysIntervalForDistribution();

        if ($daysLeft > $firstDay) {
            $leadCurrentDt = $this->lead->clientTime2;

            foreach ($daysList as $day => $objects) {
                if ($day >= $daysLeft) {
                    break;
                }

                $leadDt = clone $leadCurrentDt;
                $nextEmailDateByLeadTime = $leadDt->modify("+{$day} days")
                    ->setTime(
                        $this->getConfigParameter('allowedTime.hour', 10),
                        $this->getConfigParameter('allowedTime.minute', 0)
                    );
                $utcDatetime = $nextEmailDateByLeadTime->setTimezone(new \DateTimeZone('UTC'))
                    ->format('Y-m-d H:i:s');
                $delaySeconds = DateHelper::getDifferentInSecondsByDatesUTC(
                    date('Y-m-d H:i:s'),
                    $utcDatetime
                );

                if (!empty($objects)) {
                    $groupHash = md5(time() . $this->lead->id);

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
                                $this->lead->id,
                                $object['command'],
                                $utcDatetime,
                                $groupHash
                            );

                            (new ObjectTaskRepository($objectTask))->save();
                        } catch (\Exception $exception) {
                            Yii::$app->queue_db->remove($queueID);

                            throw $exception;
                        }
                    }
                }
            }
        }
    }

    public static function getTemplate(): string
    {
        return Json::encode([
            'allowedTime' => [
                'hour' => 12,
                'minute' => 0,
            ],
            'days' => [
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
            ],
        ]);
    }

    private function getDaysIntervalForDistribution(): int
    {
        $days = 0;

        if ($this->lead->firstFlightSegment !== null) {
            /** @var \common\models\LeadFlightSegment $segment */
            $segment = $this->lead->firstFlightSegment;
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

        return ($project !== null && isset($virtualAgentList[$project->project_key]));
    }

    public static function clientResponseLogicInit(Lead $lead): void
    {
        if (NoAnswer::leadIsAvailableForProcess($lead) === false || $lead->status !== Lead::STATUS_FOLLOW_UP) {
            return;
        }

        try {
            ObjectTaskService::cancelJobs(
                self::KEY,
                ObjectTaskService::OBJECT_LEAD,
                $lead->id
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
}
