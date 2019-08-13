<?php
/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-08-13
 */

namespace common\components\jobs;

use common\components\SearchService;
use common\models\Call;
use common\models\Department;
use common\models\Employee;
use common\models\Lead;
use common\models\Lead2;
use common\models\UserProfile;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the Job class for Call Queue.
 *
 * @property int $call_id
 */

class CallQueueJob extends BaseObject implements JobInterface
{
    public $call_id;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {
        try {
            if($this->call_id) {

                $originalAgentId = null;

                $call = Call::findOne($this->call_id);
                if($call) {

                    if((int) $call->c_call_status === Call::CALL_STATUS_IVR) {
                        $call->c_call_status = Call::CALL_STATUS_QUEUE;
                        $call->update();
                    }

                    if((int) $call->c_dep_id === Department::DEPARTMENT_SALES) {
                        if ($call->c_from) {
                            $lead = Lead2::findLastLeadByClientPhone($call->c_from, $call->c_project_id);
                            if (!$lead) {
                                $lead = Lead2::createNewLeadByPhone($call->c_from, $call->c_project_id);
                            }
                            if($lead) {
                                $call->c_lead_id = $lead->id;
                                if(!$call->update()) {
                                    Yii::error(VarDumper::dumpAsString($call->errors), 'CallQueueJob:execute:Call:update');
                                }
                            }

                            if($lead && $lead->employee_id) {
                                $originalAgentId = $lead->employee_id;
                            }
                        }

                    } /*elseif((int) $call->c_dep_id === Department::DEPARTMENT_EXCHANGE || (int) $call->c_dep_id === Department::DEPARTMENT_SUPPORT) {

                    }*/

                    $isCalled = false;

                    if($originalAgentId) {
                        $user = Employee::findOne($originalAgentId);
                        if($user && $user->isOnline() && $user->isCallStatusReady() && $user->isCallFree()) {
                            $isCalled = Call::applyCallToAgent($call, $user->id);
                        }
                    }

                    if(!$isCalled) {
                        $last_hours = (int)(Yii::$app->params['settings']['general_line_last_hours'] ?? 1);
                        $users = Employee::getUsersForCallQueue($call->c_project_id, $call->c_dep_id, 1, $last_hours);
                        if ($users) {
                            foreach ($users as $userItem) {
                                $user_id = (int)$userItem['tbl_user_id'];
                                Call::applyCallToAgent($call, $user_id);
                            }
                        }
                    }
                }
            }

        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'CallQueueJob:execute:catch');
        }
        return false;
    }

    public function getTtr()
    {
        return 1 * 15;
    }

    /*public function canRetry($attempt, $error)
    {
        return ($attempt < 5) && ($error instanceof TemporaryException);
    }*/
}