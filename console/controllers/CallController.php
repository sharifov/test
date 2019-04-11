<?php

namespace console\controllers;

use common\models\Call;
use common\models\Employee;
use common\models\ProjectEmployeeAccess;
use common\models\Source;
use common\models\UserProfile;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class CallController extends Controller
{
    /**
     *  Update Call status for old items
     *
     */
    public function actionUpdateStatus()
    {
        echo $this->ansiFormat('starting console script...' . PHP_EOL, Console::FG_GREEN);
        $dt = new \DateTime('now');
        $dt->modify('-1 hour');
        $items = Call::find()
            ->where(['c_call_status' => [Call::CALL_STATUS_QUEUE, Call::CALL_STATUS_RINGING, Call::CALL_STATUS_IN_PROGRESS]])
            ->andWhere(['<=', 'c_created_dt', $dt->format('Y-m-d H:i:s')])
            ->orderBy(['c_id' => SORT_ASC])
            ->all();

        $out = [];
        $errors = [];
        if ($count = count($items) > 0) {
            echo $this->ansiFormat('Find ' . $count . ' items for update' . PHP_EOL, Console::FG_GREEN);
            foreach ($items AS $call) {
                $old_status = $call->c_call_status;
                $call->c_call_status = Call::CALL_STATUS_COMPLETED;
                if ($call->save()) {
                    $out[] = ['c_id' => $call->c_id,
                        'old_status' => $old_status,
                        'new_status' => Call::CALL_STATUS_COMPLETED,
                    ];
                } else {
                    $errors[] = $call->errors;
                }
            }
        } else {
            echo $this->ansiFormat( 'No items to update ' . PHP_EOL, Console::FG_GREEN);
            return 0;
        }

        Yii::info(VarDumper::dumpAsString(['calls' => $out, 'errors' => $errors]), 'info\Console:CallController:actionUpdateStatus');
        echo $this->ansiFormat(PHP_EOL . 'Finish update' . PHP_EOL, Console::FG_GREEN);
        return 0;
    }


    public function actionUsers()
    {
        $users = Employee::find()->orderBy(['id' => SORT_ASC])->all();
        //VarDumper::dump(count($users)); exit;
        $items = [];
        $dtNow = new \DateTime('now');
        $dateFormatNow = $dtNow->format("Y-m-d H:i:s");
        if(count($users)) {
            foreach ($users AS $user) {
                $upps = $user->userProjectParams;
                if(count($upps)) {
                    foreach ($upps AS $upp) {
                        if(!isset($items[$user->id]) && $upp->upp_tw_sip_id && (strlen($upp->upp_tw_sip_id) > 2)) {
                            $items[$user->id] = $upp->upp_tw_sip_id;
                            if(!$user->userProfile) {
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
        VarDumper::dump($items); exit;
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
                            ->andWhere(['=', 'c_call_status', Call::CALL_STATUS_QUEUE])
                            ->orderBy(['c_id' => SORT_ASC])
                            ->all();

            if($itemsInHold && is_array($itemsInHold)) {
                foreach ($itemsInHold AS $call) {

                    if(!$call->c_to) {
                        continue;
                    }

                    if(!$call->c_from) {
                        continue;
                    }

                    $agent_phone_number = $call->c_to;
                    $source = Source::findOne(['phone_number' => $agent_phone_number]);
                    if($source && $source->project) {
                        $project = $source->project;
                        $project_employee_access = ProjectEmployeeAccess::find()->where(['project_id' => $project->id])->all();
                        if ($project_employee_access && is_array($project_employee_access) && count($project_employee_access)) {
                            foreach ($project_employee_access AS $projectEmployer) {
                                $projectUser = Employee::findOne($projectEmployer->employee_id);
                                if($projectUser && $projectUser->userProfile && $projectUser->userProfile->up_call_type_id === UserProfile::CALL_TYPE_WEB) {
                                        $user = $projectUser;
                                        if ($user->isOnline() && $user->isCallStatusReady() && $user->isCallFree()) {
                                            $agent = 'seller' . $user->id;
                                            echo 'Find agent:'. $agent . PHP_EOL;
                                            $res = $communicationService->callRedirect($call->c_call_sid, 'client', $call->c_from, $agent);
                                            if($res && isset($res['error']) && $res['error'] === false) {
                                                $results[] = $res;
                                                break;
                                            } else {
                                                echo "Bad response: " . PHP_EOL .  VarDumper::dumpAsString( $res, 10, false) . PHP_EOL;
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
        echo "Results redirects for hold calls: " . PHP_EOL .  VarDumper::dumpAsString( $results, 10, false) . PHP_EOL;
        return 0;
    }

}