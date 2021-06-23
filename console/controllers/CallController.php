<?php

namespace console\controllers;

use common\components\Metrics;
use common\models\Call;
use common\models\Employee;
use common\models\ProjectEmployeeAccess;
use common\models\Sources;
use common\models\UserProfile;
use console\helpers\OutputHelper;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\helpers\UserCallIdentity;
use sales\model\callTerminateLog\entity\CallTerminateLog;
use sales\model\callTerminateLog\repository\CallTerminateLogRepository;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class CallController
 * @property bool $terminatorEnable
 * @property array $terminatorParams
 * @property string $shortClassName
 * @property int $defaultRingingMinutes
 * @property int $defaultQueueMinutes
 * @property int $defaultInProgressMinutes
 * @property int $defaultIvrMinutes
 * @property OutputHelper $outputHelper
 */
class CallController extends Controller
{
    public $defaultRingingMinutes = 5;
    public $defaultQueueMinutes = 60;
    public $defaultInProgressMinutes = 90;
    public $defaultIvrMinutes = 3;

    private $shortClassName;
    private $terminatorEnable = false;
    private $terminatorParams;
    private $outputHelper;

    /**
     * @param $id
     * @param $module
     * @param OutputHelper $outputHelper
     * @param array $config
     */
    public function __construct($id, $module, OutputHelper $outputHelper, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->outputHelper = $outputHelper;
        $this->setSettings();
    }

    /**
     * @param int $days
     */
    public function actionCleaner(int $days = 10): void
    {
        /* TODO:: remove this action */
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $processed = 0;
        $timeStart = microtime(true);
        $dtOlder = (new \DateTime('now'))->modify('-' . $days . ' days')->format('Y-m-d H:i:s');

        try {
            $processed = Call::deleteAll(['<=', 'c_created_dt', $dtOlder]);
        } catch (\Throwable $throwable) {
            Yii::error(
                AppHelper::throwableFormatter($throwable),
                'CallController:actionCleaner'
            );
            echo Console::renderColoredString('%r --- Error : ' . $throwable->getMessage() . ' %n'), PHP_EOL;
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g Processed: %w[' . $processed . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        Yii::info(VarDumper::dumpAsString([
            'Processed' => $processed,
            'Days' => $days,
            'Execute Time' => $time . ' sec',
            'End Time' => date('Y-m-d H:i:s'),
        ]), 'info\CallController:actionCleaner:result');
    }

    /**
     * @param int|null $ringingMinutes
     * @param int|null $queueMinutes
     * @param int|null $inProgressMinutes
     * @param int|null $delayMinutes
     */
    public function actionTerminator(?int $ringingMinutes = null, ?int $queueMinutes = null, ?int $inProgressMinutes = null, ?int $delayMinutes = null): void
    {
        $timeStart = microtime(true);
        $ringingMinutes = $ringingMinutes ?? $this->terminatorParams['ringing_minutes'];
        $queueMinutes = $queueMinutes ?? $this->terminatorParams['queue_minutes'];
        $inProgressMinutes = $inProgressMinutes ?? $this->terminatorParams['in_progress_minutes'];
        $delayMinutesDefault = $this->terminatorParams['in_progress_minutes'] ?? 120;
        $delayMinutes = $delayMinutes ?? $delayMinutesDefault;
        $inIvrMinutes = $this->terminatorParams['ivr_minutes'];

        $point = $this->shortClassName . ':' . $this->action->id;

        if (!$this->terminatorEnable) {
            $this->outputHelper->printInfo('Call terminator is disable. ', $point, Console::FG_RED);
            return;
        }

        $this->outputHelper->printInfo('Start. ', $point);

        $dtRinging = (new \DateTime('now'))->modify('-' . $ringingMinutes . ' minutes')->format('Y-m-d H:i:s');
        $dtQueue = (new \DateTime('now'))->modify('-' . $queueMinutes . ' minutes')->format('Y-m-d H:i:s');
        $dtInProgress = (new \DateTime('now'))->modify('-' . $inProgressMinutes . ' minutes')->format('Y-m-d H:i:s');
        $dtDelay = (new \DateTime('now'))->modify('-' . $delayMinutes . ' minutes')->format('Y-m-d H:i:s');
        $dtIvr = (new \DateTime('now'))->modify('-' . $inIvrMinutes . ' minutes')->format('Y-m-d H:i:s');

        $itemsQuery = Call::find()
            ->where(['OR',
                ['AND',
                    ['c_status_id' => Call::STATUS_QUEUE],
                    ['<=', 'c_created_dt', $dtQueue],
                ],
                ['AND',
                    ['c_status_id' => Call::STATUS_RINGING],
                    ['<=', 'c_created_dt', $dtRinging],
                    ['IS NOT', 'c_parent_id', null],
                ],
                ['AND',
                    ['c_status_id' =>  Call::STATUS_IN_PROGRESS],
                    ['<=', 'c_created_dt', $dtInProgress],
                    ['IS NOT', 'c_parent_id', null],
                ],
                ['AND',
                    ['c_status_id' =>  Call::STATUS_DELAY],
                    ['<=', 'c_created_dt', $dtDelay],
                ],
            ]);

        if ($inIvrMinutes > 0) {
            $itemsQuery->orWhere(
                ['AND',
                    ['c_status_id' => Call::STATUS_IVR],
                    ['<=', 'c_created_dt', $dtIvr],
                ]
            );
        }
        $items = $itemsQuery->orderBy(['c_id' => SORT_ASC])
            ->indexBy('c_id')
            ->all();

        $this->outputHelper->printInfo('Query execution time: ' .
            number_format(round(microtime(true) - $timeStart, 2), 2), 'Query:end');

        $out = [];
        $errors = [];

        if ($items) {
            $metrics = \Yii::$container->get(Metrics::class);
            $this->outputHelper->printInfo('Find ' . count($items) . ' items for update', 'Count');

            /** @var Call $call */
            foreach ($items as $call) {
                $old_status = $call->getStatusName();
                $oldStatusId = $call->c_status_id;
                if ($call->isStatusDelay()) {
                    $call->setStatusCompleted();
                } else {
                    $call->setStatusFailed();
                }

                if ($call->save()) {
                    $out[] = ['c_id' => $call->c_id,
                        'old_status' => $old_status,
                        'new_status' => $call->getStatusName(),
                    ];
                    try {
                        if (SettingHelper::getCallTerminateBlackListByKey('enable_write_log')) {
                            $callTerminateLog = CallTerminateLog::create($call->c_from, $oldStatusId, $call->c_project_id);
                            (new CallTerminateLogRepository())->save($callTerminateLog);
                        }

                        $result = Yii::$app->communication->hangUp($call->c_call_sid);
                        if ($result['error']) {
                            Yii::error(VarDumper::dumpAsString([
                                'result' => $result,
                                'call' => $call->getAttributes(),
                            ]), 'CallController:actionTerminator:HangUpResult');
                        } else {
                            $metrics->serviceCounter('call_terminator', ['status' => $old_status]);
                            Yii::info(VarDumper::dumpAsString(['callId' => $call->c_id]), 'info\CallTerminatorCompleteCall');
                        }
                    } catch (\Throwable $e) {
                        Yii::error(VarDumper::dumpAsString([
                            'error' => AppHelper::throwableFormatter($e),
                            'call' => $call->getAttributes()
                        ]), 'CallController:actionTerminator:HangUp');
                    }
                } else {
                    $errors[] = $call->errors;
                }
            }
            unset($metrics);
        } else {
            $this->outputHelper->printInfo('No items to update', 'Count:noItems');
        }

        $resultInfo = 'Processed: ' . count($out) . ' Total execution time: ' .
            number_format(round(microtime(true) - $timeStart, 2), 2);
        $this->outputHelper->printInfo($resultInfo, $point);
    }

    public function actionUsers()
    {
        $users = Employee::find()->orderBy(['id' => SORT_ASC])->all();
        //VarDumper::dump(count($users)); exit;
        $items = [];
        $dtNow = new \DateTime('now');
        $dateFormatNow = $dtNow->format("Y-m-d H:i:s");
        if (count($users)) {
            foreach ($users as $user) {
                $upps = $user->userProjectParams;
                if (count($upps)) {
                    foreach ($upps as $upp) {
                        if (!isset($items[$user->id]) && $upp->upp_tw_sip_id && (strlen($upp->upp_tw_sip_id) > 2)) {
                            $items[$user->id] = $upp->upp_tw_sip_id;
                            if (!$user->userProfile) {
                                $userProfile = new UserProfile();
                                $userProfile->up_call_type_id = 2;
                                $userProfile->up_user_id = $user->id;
                                $userProfile->save();
                                $user = Employee::findOne($user->id);
                            }
                            $user->userProfile->up_sip = $upp->upp_tw_sip_id;
                            $user->userProfile->up_updated_dt = $dateFormatNow;
                            $user->userProfile->save();
                        }
                    }
                }
            }
        }
        VarDumper::dump($items);
        exit;
    }

    public function actionCallFromHold()
    {
        try {
            $results = [];

            /**
             * @var \common\components\CommunicationService::class $communicationService
             */
            $communicationService = \Yii::$app->communication;
            //echo VarDumper::dumpAsString($communicationService, 10, false) . PHP_EOL; exit;

            $dateNowString = (new \DateTime('now'))->modify('-3 minutes')->format('Y-m-d H:i:s');

            //echo VarDumper::dumpAsString($dateNowString, 10, false) . PHP_EOL;
            // get calls with status queued
            $itemsInHold = Call::find()->where(['>', 'c_created_dt', $dateNowString])
                            ->andWhere(['c_status_id', Call::STATUS_QUEUE])
                            ->orderBy(['c_id' => SORT_ASC])
                            ->all();

            if ($itemsInHold && is_array($itemsInHold)) {
                foreach ($itemsInHold as $call) {
                    if (!$call->c_to) {
                        continue;
                    }

                    if (!$call->c_from) {
                        continue;
                    }

                    $agent_phone_number = $call->c_to;
                    $source = Sources::findOne(['phone_number' => $agent_phone_number]);
                    if ($source && $source->project) {
                        $project = $source->project;
                        $project_employee_access = ProjectEmployeeAccess::find()->where(['project_id' => $project->id])->all();
                        if ($project_employee_access && is_array($project_employee_access) && count($project_employee_access)) {
                            foreach ($project_employee_access as $projectEmployer) {
                                $projectUser = Employee::findOne($projectEmployer->employee_id);
                                if ($projectUser && $projectUser->userProfile && $projectUser->userProfile->up_call_type_id === UserProfile::CALL_TYPE_WEB) {
                                    $user = $projectUser;
                                    if ($user->isOnline() && $user->isCallStatusReady() && $user->isCallFree()) {
                                        $agent = UserCallIdentity::getId($user->id);
                                        echo 'Find agent:' . $agent . PHP_EOL;
                                        $res = $communicationService->callRedirect($call->c_call_sid, 'client', $call->c_from, $agent);
                                        if ($res && isset($res['error']) && $res['error'] === false) {
                                            $results[] = $res;
                                            break;
                                        } else {
                                            echo "Bad response: " . PHP_EOL .  VarDumper::dumpAsString($res, 10, false) . PHP_EOL;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            echo VarDumper::dumpAsString($e->getMessage(), 10, false) . PHP_EOL;
        }
        echo "Results redirects for hold calls: " . PHP_EOL .  VarDumper::dumpAsString($results, 10, false) . PHP_EOL;
        return 0;
    }

    /**
     * @return CallController
     */
    private function setSettings(): CallController
    {
        $this->shortClassName = OutputHelper::getShortClassName(self::class);

        $settings = Yii::$app->params['settings'];
        $this->terminatorEnable = $settings['console_call_terminator_enable'] ?? false;

        try {
            $this->terminatorParams = [
                'ringing_minutes' => (int) $settings['console_call_terminator_params']['ringing_minutes'],
                'queue_minutes' => (int) $settings['console_call_terminator_params']['queue_minutes'],
                'in_progress_minutes' => (int) $settings['console_call_terminator_params']['in_progress_minutes'],
                'ivr_minutes' => (int) $settings['console_call_terminator_params']['ivr_minutes'],
            ];
        } catch (\Throwable $throwable) {
            $this->terminatorParams = [
                'ringing_minutes' => $this->defaultRingingMinutes,
                'queue_minutes' => $this->defaultQueueMinutes,
                'in_progress_minutes' => $this->defaultInProgressMinutes,
                'ivr_minutes' => $this->defaultIvrMinutes,
            ];
            Yii::warning(AppHelper::throwableFormatter($throwable), 'CallController:setSettings:Throwable');
        }
        return $this;
    }
}
