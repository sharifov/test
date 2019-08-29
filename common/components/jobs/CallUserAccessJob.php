<?php
/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-08-29
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
 *
 * @property CasesCreateService $casesCreateService
 * @property ClientManageService $clientManageService
 * @property CasesRepository $casesRepository
 */

class CallUserAccessJob extends BaseObject implements JobInterface
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

            Yii::info('CallUserAccessJob - CallId: ' . $this->call_id ,'info\CallUserAccessJob');

            if($this->delay) {
                sleep($this->delay);
            }

            if($this->call_id) {

                $originalAgentId = null;

                $call = Call::find()->where(['c_id' => $this->call_id])->limit(1)->one();

                if($call && $call->c_call_status === Call::CALL_STATUS_QUEUE) {

                    //$originalAgentId = $call->c_created_user_id;

                    Yii::info('CallUserAccessJob - CallId: ' . $this->call_id . ', c_call_status: ' . $call->c_call_status . ', ' . VarDumper::dumpAsString($call->attributes),'info\CallUserAccessJob-call');


                    $last_hours = (int)(Yii::$app->params['settings']['general_line_last_hours'] ?? 1);
                    $limitCallUsers = (int)(Yii::$app->params['settings']['general_line_user_limit'] ?? 1);

                    $exceptUserIds = ArrayHelper::map($call->callUserAccesses, 'cua_user_id', 'cua_user_id');

                    $users = Employee::getUsersForCallQueue($call->c_project_id, $call->c_dep_id, $limitCallUsers, $last_hours, $exceptUserIds);

                    if ($users) {
                        foreach ($users as $userItem) {
                            $user_id = (int) $userItem['tbl_user_id'];
                            Call::applyCallToAgentAccess($call, $user_id);
                        }

                        Notifications::pingUserMap();
                    }

                    $timeStartCallUserAccess = (int) Yii::$app->params['settings']['time_repeat_call_user_access'] ?? 0;

                    if($timeStartCallUserAccess) {
                        $job = new CallUserAccessJob();
                        $job->call_id = $call->c_id;
                        $job->delay = 0;
                        $jobId = Yii::$app->queue_job->delay($timeStartCallUserAccess)->priority(100)->push($job);
                    }

                }
            }

        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'CallUserAccessJob:execute:catch');
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