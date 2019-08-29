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
use common\models\Lead2;
use common\models\Notifications;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesCreateService;
use sales\services\client\ClientManageService;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the Job class for Call Queue.
 *
 * @property int $call_id
 * @property int $delay
 *
 * @property CasesCreateService $casesCreateService
 * @property ClientManageService $clientManageService
 * @property CasesRepository $casesRepository
 */

class CallQueueJob extends BaseObject implements JobInterface
{
    public $call_id;
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

            Yii::info('CallId: ' . $this->call_id ,'info\CallQueueJob');

            if($this->delay) {
                sleep($this->delay);
            }

            if($this->call_id) {

                $originalAgentId = null;

                $call = Call::find()->where(['c_id' => $this->call_id])->limit(1)->one();

                if($call) {

                    $originalAgentId = $call->c_created_user_id;

                    Yii::info('CallId: ' . $this->call_id . ', c_call_status: ' . $call->c_call_status . ', ' . VarDumper::dumpAsString($call->attributes),'info\CallQueueJob-call');

                    if($call->c_call_status === Call::CALL_STATUS_IVR) {
                        Yii::info('CallId: ' . $this->call_id . ', CALL_STATUS_IVR' ,'info\CallQueueJob-CALL_STATUS_IVR');
                        $call->c_call_status = Call::CALL_STATUS_QUEUE;
                        if(!$call->update()) {
                            Yii::error(VarDumper::dumpAsString($call->errors), 'CallQueueJob:execute:Call:update');
                        }
                    }

                    Yii::info('CallId: ' . $this->call_id . ', c_call_status: ' . $call->c_call_status . ', ' . VarDumper::dumpAsString($call->attributes),'info\CallQueueJob-call2');

                    if((int) $call->c_dep_id === Department::DEPARTMENT_SALES) {
                        if ($call->c_from) {
                            $lead = Lead2::findLastLeadByClientPhone($call->c_from, $call->c_project_id);
                            if (!$lead) {
                                $lead = Lead2::createNewLeadByPhone($call->c_from, $call->c_project_id);
                            }
                            if($lead) {
                                $call->c_lead_id = $lead->id;
                                if(!$call->update()) {
                                    Yii::error(VarDumper::dumpAsString($call->errors), 'CallQueueJob:execute:Call:update2');
                                }
                            }

                            if(!$originalAgentId && $lead && $lead->employee_id) {
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
                            if(!$call->update()) {
                                Yii::error(VarDumper::dumpAsString($call->errors), 'CallQueueJob:execute:Call:update3');
                            }

                            if(!$originalAgentId && $case && $case->cs_user_id) {
                                $originalAgentId = $case->cs_user_id;
                            }

                        } catch (\Throwable $exception) {
                            Yii::error(VarDumper::dumpAsString($exception), 'CallQueueJob:createClient:catch');
                        }
                    }

                    $isCalled = false;

                    if($originalAgentId) {
                        $user = Employee::findOne($originalAgentId);
                        if($user && $user->isOnline() /*&& $user->isCallStatusReady() && $user->isCallFree()*/) {
                            $isCalled = Call::applyCallToAgentAccess($call, $user->id);



                            $timeStartCallUserAccess = (int) Yii::$app->params['settings']['time_start_call_user_access'] ?? 0;

                            if($timeStartCallUserAccess) {
                                $job = new CallUserAccessJob();
                                $job->call_id = $call->c_id;
                                $jobId = Yii::$app->queue_job->delay($timeStartCallUserAccess)->priority(100)->push($job);
                            }
                        }
                    }

                    if(!$isCalled) {
                        $last_hours = (int)(Yii::$app->params['settings']['general_line_last_hours'] ?? 1);

                        $limitCallUsers = (int)(Yii::$app->params['settings']['general_line_user_limit'] ?? 1);

                        $users = Employee::getUsersForCallQueue($call->c_project_id, $call->c_dep_id, $limitCallUsers, $last_hours);
                        if ($users) {
                            foreach ($users as $userItem) {
                                $user_id = (int) $userItem['tbl_user_id'];
                                Call::applyCallToAgentAccess($call, $user_id);
                            }
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