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
use common\models\Notifications;
use common\models\UserGroupAssign;
use src\forms\lead\PhoneCreateForm;
use src\helpers\setting\SettingHelper;
use src\repositories\cases\CasesRepository;
use src\services\cases\CasesCreateService;
use src\services\client\ClientManageService;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the Job class for Call Queue.
 *
 * @property int $user_id
 *
 * @property CasesCreateService $casesCreateService
 * @property ClientManageService $clientManageService
 * @property CasesRepository $casesRepository
 */

class AgentCallQueueJob extends BaseJob implements JobInterface
{
    public $user_id;

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
    public function execute($queue): bool
    {
        $this->waitingTimeRegister();
        try {
//            $this->casesCreateService = Yii::createObject(CasesCreateService::class);
//            $this->clientManageService = Yii::createObject(ClientManageService::class);
//            $this->casesRepository = Yii::createObject(CasesRepository::class);

            // Yii::info('AgentCallQueueJob - UserId: ' . $this->user_id ,'info\AgentCallQueueJob');

            //sleep(4);

            $last_hours = (int)(Yii::$app->params['settings']['general_line_last_hours'] ?? 1);

            $calls = Call::find()->where(['c_status_id' => Call::STATUS_QUEUE, 'c_source_type_id' => Call::SOURCE_GENERAL_LINE])->orderBy(['c_id' => SORT_ASC])->limit(10)->all();

            if ($calls) {
                foreach ($calls as $call) {
                    $originalAgentId = $call->c_created_user_id;
                    $isCalled = false;

                    if (!$originalAgentId && $call->c_lead_id && $call->cLead) {
                        $originalAgentId = $call->cLead->employee_id;
                    }

                    if (!$originalAgentId && $call->c_case_id && $call->cCase) {
                        $originalAgentId = $call->cCase->cs_user_id;
                    }

                    /*if(!$originalAgentId && $call->c_created_user_id) {
                        $originalAgentId = $call->c_created_user_id;
                    }*/

                    if ($originalAgentId) {
                        $user = Employee::findOne($originalAgentId);
                        if ($user && $user->isOnline() /*&& $user->isCallStatusReady() && $user->isCallFree()*/) {
                            $depListIds = array_keys($user->getUserDepartmentList());
                            if (in_array($call->c_dep_id, $depListIds, true)) {
                                if ($call->cugUgs) {
                                    $callGroups = ArrayHelper::map($call->cugUgs, 'ug_id', 'ug_id');
                                    if ($callGroups) {
                                        $userGroups = UserGroupAssign::find()
                                            ->select(['ugs_group_id'])
                                            ->andWhere(['ugs_user_id' => $user->id])
                                            ->indexBy('ugs_group_id')->column();
                                        $isGroupAccess = array_intersect_key($callGroups, $userGroups) ? true : false;
                                        if ($isGroupAccess) {
                                            $isCalled = Call::applyCallToAgentAccess($call, $user->id);
                                        }
                                    } else {
                                        $isCalled = Call::applyCallToAgentAccess($call, $user->id);
                                    }
                                } else {
                                    $isCalled = Call::applyCallToAgentAccess($call, $user->id);
                                }
                            }
                        }
                    }

                    if (!$isCalled) {
                        $limitCallUsers = SettingHelper::getGeneralLineUserLimit($call->cDep, $call->c_to);

                        $exceptUserIds = ArrayHelper::map($call->callUserAccesses, 'cua_user_id', 'cua_user_id');
                        $users = Employee::getUsersForCallQueue($call, $limitCallUsers, $last_hours, $exceptUserIds);
                        if ($users) {
                            foreach ($users as $userItem) {
                                $user_id = (int) $userItem['tbl_user_id'];
                                Call::applyCallToAgentAccess($call, $user_id);
                            }
                            // Yii::info('AgentCallQueueJob - UserId: ' . $this->user_id . ', Call Id: ' . $call->c_id . ', Users: '. VarDumper::dumpAsString($users),'info\AgentCallQueueJob:getUsersForCallQueue');
                        }
                    }
                }

                Notifications::pingUserMap();
            }
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'AgentCallQueueJob:execute:Throwable');
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
