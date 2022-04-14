<?php

/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-08-13
 */

namespace common\components\jobs;

use common\components\Metrics;
use common\models\Call;
use common\models\Client;
use common\models\Employee;
use common\models\Lead;
use common\models\Notifications;
use common\models\Project;
use common\models\ProjectEmployeeAccess;
use common\models\query\SourcesQuery;
use common\models\Sources;
use common\models\UserGroupAssign;
use src\forms\lead\PhoneCreateForm;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\contactPhoneList\service\ContactPhoneListService;
use src\model\leadData\services\LeadDataCreateService;
use src\repositories\cases\CasesRepository;
use src\repositories\lead\LeadRepository;
use src\services\cases\CasesCreateService;
use src\services\cases\CasesSaleService;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use src\services\lead\LeadManageService;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the Job class for Call Queue.
 *
 * @property int $call_id
 * @property int $delay
 * @property int|null $source_id
 *
 * @property CasesCreateService $casesCreateService
 * @property ClientManageService $clientManageService
 * @property float|int $ttr
 * @property CasesRepository $casesRepository
 * @property CasesSaleService $casesSaleService
 */

class CallQueueJob extends BaseJob implements JobInterface
{
    public int $call_id;
    public ?int $source_id = null;
    public int $delay;

    private CasesCreateService $casesCreateService;
    private CasesRepository $casesRepository;
    private ClientManageService $clientManageService;
    private CasesSaleService $casesSaleService;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->waitingTimeRegister();
        $metrics = \Yii::$container->get(Metrics::class);
        $timeStart = microtime(true);

        try {
            $this->casesCreateService = Yii::createObject(CasesCreateService::class);
            $this->clientManageService = Yii::createObject(ClientManageService::class);
            $this->casesRepository = Yii::createObject(CasesRepository::class);
            $this->casesSaleService = Yii::createObject(CasesSaleService::class);

            // Yii::info('CallQueueJob - CallId: ' . $this->call_id ,'info\CallQueueJob');

            if ($this->delay) {
                sleep($this->delay);
            }

            if ($this->call_id) {
                $originalAgentId = null;

                $call = Call::find()->where(['c_id' => $this->call_id])->limit(1)->one();

                if (!$call) {
                    throw new Exception('CallQueueJob: Not found CallId: ' . $this->call_id, 5);
                }

                $originalAgentId = $call->c_created_user_id;

                if ($call->isStatusIvr()) {
                    $call->setStatusQueue();
                }

                if ($call->c_dep_id && ($departmentParams = $call->cDep->getParams()) && $project = Project::findOne($call->c_project_id)) {
                    $projectParams = $project->getParams();
                    if ($departmentParams->object->type->isLead()) {
                        if ($call->c_from && !ContactPhoneListService::isInvalid($call->c_from)) {
                            try {
                                $lead = Lead::findLastLeadByClientPhone($call->c_from, $call->c_project_id);

                                if (
                                    !$lead &&
                                    $departmentParams->object->lead->createOnCall &&
                                    $projectParams->object->lead->allow_auto_lead_create &&
                                    !ContactPhoneListService::isAutoCreateLeadOff($call->c_from)
                                ) {
                                    if ($call->isDirect() || $call->isRedirectCall()) {
                                        if ($source = SourcesQuery::getByCidOrDefaultByProject($projectParams->object->lead->default_cid_on_direct_call, $call->c_project_id)) {
                                            $this->source_id = $source->id;
                                        } else if ($source = SourcesQuery::getFirstSourceByProjectId($call->c_project_id)) {
                                            $this->source_id = $source->id;
                                            Yii::warning([
                                                'message' => 'Not found source by CID and not found default by project for Direct Call',
                                                'callId' => $call->c_id,
                                                'sourceCidFromSettings' => $projectParams->object->lead->default_cid_on_direct_call,
                                                'projectId' => $call->c_project_id,
                                                'currentCid' => $source->cid
                                            ], 'CallQueueJob:defaultSourceCidDetecting');
                                        }
                                    }

                                    $lead = (Yii::createObject(LeadManageService::class))
                                        ->createByIncomingCall(
                                            $call->c_from,
                                            $call->c_project_id,
                                            $this->source_id,
                                            $call->c_dep_id,
                                            $call->c_offset_gmt,
                                            $call->c_id
                                        );
                                }

                                $call->c_lead_id = $lead->id ?? null;

                                if (
                                    !$departmentParams->object->lead->createOnCall &&
                                    !$projectParams->object->lead->allow_auto_lead_create
                                ) {
                                    $clientForm = ClientCreateForm::createWidthDefaultName();
                                    $clientForm->projectId = $call->c_project_id;
                                    $clientForm->typeCreate = Client::TYPE_CREATE_CALL;
                                    $client = $this->clientManageService->getOrCreateByPhones([
                                        new PhoneCreateForm([
                                            'phone' => $call->c_from,
                                            'comments' => 'incoming'
                                        ])
                                    ], $clientForm);
                                    $call->c_client_id = $client->id;
                                }

                                if (!$call->c_client_id) {
                                    $call->c_client_id = $lead->client_id ?? null;
                                }

                                if ($lead) {
                                    $call->c_client_id = $lead->client_id;
                                }

                                if (($lead && !$lead->isCallReady()) && $call->isEnded()) {
                                    $leadRepository = Yii::createObject(LeadRepository::class);
                                    $lead->callReady();
                                    $leadRepository->save($lead);
                                }
                                if (!$originalAgentId && $lead && $lead->employee_id) {
                                    $originalAgentId = $lead->employee_id;
                                }
                            } catch (\Throwable $e) {
                                Yii::error($e, 'CallQueueJob:execute:Call');
                            }
                        }
                    } elseif ($departmentParams->object->type->isCase()) {
                        try {
                            $allowAutoCreateByProject = $projectParams->object->case->allow_auto_case_create;
                            $createCaseOnIncoming = (
                                $allowAutoCreateByProject &&
                                $departmentParams->object->case->createOnCall &&
                                !ContactPhoneListService::isAutoCreateCaseOff($call->c_from)
                            );

                            $case = $this->casesCreateService->getOrCreateByCall(
                                [new PhoneCreateForm(['phone' => $call->c_from])],
                                $call->c_id,
                                $call->c_project_id,
                                (int)$call->c_dep_id,
                                $createCaseOnIncoming,
                                $departmentParams->object->case->trashActiveDaysLimit
                            );
                            $call->c_case_id = $case->cs_id ?? null;

                            if (!$departmentParams->object->case->createOnCall && !$allowAutoCreateByProject) {
                                $clientForm = ClientCreateForm::createWidthDefaultName();
                                $clientForm->projectId = $call->c_project_id;
                                $clientForm->typeCreate = Client::TYPE_CREATE_CALL;
                                $client = $this->clientManageService->getOrCreateByPhones(
                                    [new PhoneCreateForm(['phone' => $call->c_from])],
                                    $clientForm
                                );
                                $call->c_client_id = $client->id;
                            }

                            if (!$call->c_client_id) {
                                $call->c_client_id = $case->cs_client_id ?? null;
                            }

                            if (!$originalAgentId && $case && $case->cs_user_id) {
                                $originalAgentId = $case->cs_user_id;
                            }
                            if ($case) {
                                $call->c_client_id = $case->cs_client_id;

                                try {
                                    $job = new CreateSaleFromBOJob();
                                    $job->case_id = $case->cs_id;
                                    $job->phone = $call->c_from;
                                    Yii::$app->queue_job->priority(100)->push($job);
                                } catch (\Throwable $throwable) {
                                    Yii::error(
                                        AppHelper::throwableFormatter($throwable),
                                        'CallQueueJob:addToJobFailed'
                                    );
                                }
                            }
                        } catch (\Throwable $exception) {
                            Yii::error(VarDumper::dumpAsString($exception), 'CallQueueJob:createClient:catch');
                        }
                    }
                }

                if ($call->update() === false) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'CallQueueJob:execute:Call:update');
                }

                if ($call->isStatusQueue() || $call->isStatusIvr()) {
                    if ($call->checkCancelCall()) {
                        $seconds = round(microtime(true) - $timeStart, 1);
                        $metrics->jobHistogram(substr(strrchr(get_class($this), '\\'), 1) . '_seconds', $seconds, ['type' => 'cancel']);
                        unset($metrics);
                        return true;
                    }

                    // Yii::info('CallQueueJob - CallId: ' . $this->call_id . ', c_call_status: ' . $call->c_call_status . ', ' . VarDumper::dumpAsString($call->attributes),'info\CallQueueJob-call');

                    $isCalled = false;

                    if ($originalAgentId) {
                        $user = Employee::findOne($originalAgentId);

                        if ($user && $user->isOnline() /*&& $user->isCallStatusReady() && $user->isCallFree()*/) {
//                            $depListIds = array_keys($user->getUserDepartmentList());
//                            if (in_array($call->c_dep_id, $depListIds, true)) {

                            $isProjectAccess = ProjectEmployeeAccess::find()->andWhere(['employee_id' => $user->id, 'project_id' => $call->c_project_id])->exists();
                            if ($isProjectAccess) {
                                $isGroupAccess = true;
                                if ($call->cugUgs) {
                                    $callGroups = ArrayHelper::map($call->cugUgs, 'ug_id', 'ug_id');
                                    if ($callGroups) {
                                        $userGroups = UserGroupAssign::find()
                                            ->select(['ugs_group_id'])
                                            ->andWhere(['ugs_user_id' => $user->id])
                                            ->indexBy('ugs_group_id')->column();
                                        $isGroupAccess = array_intersect_key($callGroups, $userGroups) ? true : false;
                                    }
                                }

                                if ($isGroupAccess) {
                                    $isCalled = Call::applyCallToAgentAccess($call, $user->id);

                                    // Yii::info('Accept one user ('. ($isCalled ? 'isCalled' : 'NotIsCalled' ) .') - CallId: ' . $this->call_id . ', c_call_status: ' . $call->c_call_status . ', ' . VarDumper::dumpAsString($call->attributes),'info\CallQueueJob-Accept-one');


                                    if ((int)$call->c_source_type_id === Call::SOURCE_GENERAL_LINE) {
                                        $timeStartCallUserAccess = SettingHelper::getTimeStartCallUserAccessGeneral($call->cDep, $call->c_to);
                                    } else {
                                        $timeStartCallUserAccess = (int)(Yii::$app->params['settings']['time_start_call_user_access_direct'] ?? 0);
                                    }

                                    if ($timeStartCallUserAccess) {
                                        $job = new CallUserAccessJob();
                                        $job->call_id = $call->c_id;
                                        $job->delayJob = $timeStartCallUserAccess;
                                        $jobId = Yii::$app->queue_job->delay($timeStartCallUserAccess)->priority(100)->push($job);
                                    }
                                }
                            }
//                            }
                        }
                    }

                    if (!$isCalled) {
                        // Yii::info('Accept multiple users - CallId: ' . $call->c_id . ', c_call_status: ' . $call->c_call_status . ', ' . VarDumper::dumpAsString($call->attributes),'info\CallQueueJob-Accept-multi');

                        $last_hours = (int)(Yii::$app->params['settings']['general_line_last_hours'] ?? 1);

                        $limitCallUsers = SettingHelper::getGeneralLineUserLimit($call->cDep, $call->c_to);

                        $users = Employee::getUsersForCallQueue($call, $limitCallUsers, $last_hours);
                        if ($users) {
                            foreach ($users as $userItem) {
                                $user_id = (int) $userItem['tbl_user_id'];
                                Call::applyCallToAgentAccess($call, $user_id);
                            }
                        }


                        $timeStartCallUserAccess = SettingHelper::getTimeStartCallUserAccessGeneral($call->cDep, $call->c_to);

                        if ($timeStartCallUserAccess) {
                            $job = new CallUserAccessJob();
                            $job->call_id = $call->c_id;
                            $job->delayJob = $timeStartCallUserAccess;
                            $jobId = Yii::$app->queue_job->delay($timeStartCallUserAccess)->priority(100)->push($job);
                        }
                    }

                    Notifications::pingUserMap();
                } else {
//                    Yii::info('Call not in status Queue or IVR, CallId: ' . $this->call_id . ' (' . $call->c_call_sid . '), Status: ' . $call->getStatusName(),
//                        'info\CallQueueJob:notRun');
                }
            }
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'CallQueueJob:execute:catch');
        }
        $seconds = round(microtime(true) - $timeStart, 1);
        $metrics->jobHistogram(substr(strrchr(get_class($this), '\\'), 1) . '_seconds', $seconds, ['type' => 'execute']);
        unset($metrics);

        return false;
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 5;
    }

    /*public function canRetry($attempt, $error)
    {
        return ($attempt < 2) && ($error instanceof \Throwable);
    }*/
}
