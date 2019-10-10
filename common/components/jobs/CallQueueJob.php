<?php
/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-08-13
 */

namespace common\components\jobs;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use common\models\Lead2;
use common\models\Notifications;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesCreateService;
use sales\services\client\ClientManageService;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the Job class for Call Queue.
 *
 * @property int $call_id
 * @property int $delay
 * @property int $source_id
 *
 * @property CasesCreateService $casesCreateService
 * @property ClientManageService $clientManageService
 * @property float|int $ttr
 * @property CasesRepository $casesRepository
 */

class CallQueueJob extends BaseObject implements JobInterface
{
    public $call_id;
    public $source_id = 0;
    public $delay;

    private $casesCreateService;
    private $casesRepository;
    private $clientManageService;

    /*public function __construct(CasesCreateService $casesCreateService, ClientManageService $clientManageService, $config = [])
    {
        parent::__construct($config);
        $this->casesCreateService = Yii::createObject(CasesCreateService::class);
        $this->clientManageService = Yii::createObject(ClientManageService::class);
    }*/


    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {

        try {

            $this->casesCreateService = Yii::createObject(CasesCreateService::class);
            $this->clientManageService = Yii::createObject(ClientManageService::class);
            $this->casesRepository = Yii::createObject(CasesRepository::class);

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
                    // Yii::info('CallId: ' . $this->call_id . ', STATUS_IVR' ,'info\CallQueueJob-STATUS_IVR');
                    // $call->c_call_status = Call::TW_STATUS_QUEUE;
                    $call->setStatusQueue();
                }


                if ((int) $call->c_dep_id === Department::DEPARTMENT_SALES) {
                    if ($call->c_from) {
                        $lead = Lead2::findLastLeadByClientPhone($call->c_from, $call->c_project_id);
                        if (!$lead) {
                            $lead = Lead2::createNewLeadByPhone($call->c_from, $call->c_project_id, $this->source_id);
                        }
                        if ($lead) {
                            $call->c_lead_id = $lead->id;

                            if ((int)$lead->l_call_status_id !== Lead::CALL_STATUS_READY && $call->isEnded()) {
                                $lead->l_call_status_id = Lead::CALL_STATUS_READY;
                                if (!$lead->update()) {
                                    Yii::error('CallId: ' . $this->call_id . ' ' . VarDumper::dumpAsString($lead->errors) ,'JOB:CallQueueJob:Lead:update');
                                }
                            }

//                            if(!$call->update()) {
//                                Yii::error(VarDumper::dumpAsString($call->errors), 'CallQueueJob:execute:Call:update2');
//                            }
                        }

                        if (!$originalAgentId && $lead && $lead->employee_id) {
                            $originalAgentId = $lead->employee_id;
                        }
                    }

                } elseif((int) $call->c_dep_id === Department::DEPARTMENT_EXCHANGE || (int) $call->c_dep_id === Department::DEPARTMENT_SUPPORT) {

                    try {
                        $case = $this->casesCreateService->getOrCreateByCall(
                            [new PhoneCreateForm(['phone' => $call->c_from])],
                            $call->c_id,
                            $call->c_project_id,
                            $call->c_dep_id
                        );
                        $call->c_case_id = $case->cs_id;
//                        if (!$call->update()) {
//                            Yii::error(VarDumper::dumpAsString($call->errors), 'CallQueueJob:execute:Call:update3');
//                        }

                        if (!$originalAgentId && $case && $case->cs_user_id) {
                            $originalAgentId = $case->cs_user_id;
                        }

                    } catch (\Throwable $exception) {
                        Yii::error(VarDumper::dumpAsString($exception), 'CallQueueJob:createClient:catch');
                    }
                }


                if (!$call->update()) {
                    Yii::error(VarDumper::dumpAsString($call->errors), 'CallQueueJob:execute:Call:update');
                }



                if ($call->isStatusQueue() || $call->isStatusIvr()) {

                    // Yii::info('CallQueueJob - CallId: ' . $this->call_id . ', c_call_status: ' . $call->c_call_status . ', ' . VarDumper::dumpAsString($call->attributes),'info\CallQueueJob-call');

                    $isCalled = false;

                    if ($originalAgentId) {

                        $user = Employee::findOne($originalAgentId);

                        if ($user && $user->isOnline() /*&& $user->isCallStatusReady() && $user->isCallFree()*/) {

                            $depListIds = array_keys($user->getUserDepartmentList());
                            if (in_array($call->c_dep_id, $depListIds, true)) {

                                $isCalled = Call::applyCallToAgentAccess($call, $user->id);

                                // Yii::info('Accept one user ('. ($isCalled ? 'isCalled' : 'NotIsCalled' ) .') - CallId: ' . $this->call_id . ', c_call_status: ' . $call->c_call_status . ', ' . VarDumper::dumpAsString($call->attributes),'info\CallQueueJob-Accept-one');


                                if ((int)$call->c_source_type_id === Call::SOURCE_GENERAL_LINE) {
                                    $timeStartCallUserAccess = (int)Yii::$app->params['settings']['time_start_call_user_access_general'] ?? 0;
                                } else {
                                    $timeStartCallUserAccess = (int)Yii::$app->params['settings']['time_start_call_user_access_direct'] ?? 0;
                                }

                                if ($timeStartCallUserAccess) {
                                    $job = new CallUserAccessJob();
                                    $job->call_id = $call->c_id;
                                    $jobId = Yii::$app->queue_job->delay($timeStartCallUserAccess)->priority(100)->push($job);
                                }
                            }
                        }
                    }

                    if (!$isCalled) {

                        // Yii::info('Accept multiple users - CallId: ' . $call->c_id . ', c_call_status: ' . $call->c_call_status . ', ' . VarDumper::dumpAsString($call->attributes),'info\CallQueueJob-Accept-multi');

                        $last_hours = (int)(Yii::$app->params['settings']['general_line_last_hours'] ?? 1);

                        $limitCallUsers = (int)(Yii::$app->params['settings']['general_line_user_limit'] ?? 1);

                        $users = Employee::getUsersForCallQueue($call, $limitCallUsers, $last_hours);
                        if ($users) {
                            foreach ($users as $userItem) {
                                $user_id = (int) $userItem['tbl_user_id'];
                                Call::applyCallToAgentAccess($call, $user_id);
                            }
                        }


                        $timeStartCallUserAccess = (int) Yii::$app->params['settings']['time_start_call_user_access_general'] ?? 0;

                        if ($timeStartCallUserAccess) {
                            $job = new CallUserAccessJob();
                            $job->call_id = $call->c_id;
                            $jobId = Yii::$app->queue_job->delay($timeStartCallUserAccess)->priority(100)->push($job);
                        }

                    }

                    Notifications::pingUserMap();
                }
            }

        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'CallQueueJob:execute:catch');
        }
        return false;
    }

    public function getTtr()
    {
        return 1 * 5;
    }

    /*public function canRetry($attempt, $error)
    {
        return ($attempt < 2) && ($error instanceof \Throwable);
    }*/
}