<?php

namespace frontend\controllers;

use common\components\CommunicationService;
use common\models\Call;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Notifications;
use common\models\Project;
use common\models\UserProfile;
use common\models\UserProjectParams;
use const Grpc\CALL_ERROR;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\filters\VerbFilter;
use Yii;


class PhoneController extends FController
{

    public function actionIndex()
    {
        $this->layout = false;

        $user = \Yii::$app->user->identity;
        /*$params = UserProjectParams::find(['upp_user_id' => $user->id])->all();
        $tw_number = '';
        if(count($params)) {
            foreach ($params AS $param) {
                if(strlen($param->upp_tw_phone_number) > 7) {
                    $tw_number = $param->upp_tw_phone_number;
                    break;
                }
            }
        }*/

        $tw_number = '+15596489977';
        $client = 'seller'.$user->id;
        return $this->render('index', [
            'client' => $client,
            'fromAgentPhone' => $tw_number,
        ]);
    }


    public function actionTest()
    {
        //$this->layout = false;
        return $this->render('test', [
        ]);
    }


    public function actionAjaxPhoneDial()
    {
        $phone_number = Yii::$app->request->post('phone_number');
        $project_id = Yii::$app->request->post('project_id');
        $lead_id = Yii::$app->request->post('lead_id');
        $case_id = Yii::$app->request->post('case_id');


        $selectProjectPhone = null;

        $project = Project::findOne($project_id);

        $userId = \Yii::$app->user->id; //identity;
        $userParams = UserProjectParams::find()->where(['upp_user_id' => $userId])->all();

        $fromPhoneNumbers = [];
        if($userParams) {
            foreach ($userParams as $param) {
                if(!$param->upp_tw_phone_number) {
                    continue;
                }
                $fromPhoneNumbers[$param->upp_tw_phone_number] = $param->uppProject->name . ' (' . $param->upp_tw_phone_number . ')';

                if($project_id  && $project_id == $param->upp_project_id) {
                    $selectProjectPhone = $param->upp_tw_phone_number;
                }
            }
        }


        $model = null;

        $userPhone = ClientPhone::find()->where(['phone' => $phone_number])->orderBy(['id' => SORT_DESC])->limit(1)->one();
        if($userPhone) {
            $model = $userPhone->client;
        }


        if(Yii::$app->user->identity->canRole('agent')) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        /*$searchModel = new LeadSearch();
        $params = Yii::$app->request->queryParams;
        $params['LeadSearch']['client_id'] = $model->id;
        if($isAgent) {
            $dataProvider = $searchModel->searchAgent($params);
        } else {
            $dataProvider = $searchModel->search($params);
        }

        $dataProvider->sort = false;*/

        $currentCall = Call::find()->where(['c_created_user_id' => Yii::$app->user->id, 'c_call_status' => [Call::CALL_STATUS_RINGING, Call::CALL_STATUS_QUEUE, Call::CALL_STATUS_IN_PROGRESS]])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
        //$currentCall = Call::find()->orderBy(['c_id' => SORT_DESC])->limit(1)->one();


        return $this->renderPartial('ajax-phone-dial', [
            'phone_number' => $phone_number,
            'project' => $project,
            'model' => $model,
            'lead_id' => $lead_id,
            'case_id' => $case_id,
            //'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
            'fromPhoneNumbers' => $fromPhoneNumbers,
            'selectProjectPhone' => $selectProjectPhone,
            'currentCall' => $currentCall
        ]);
    }

    public function actionGetToken()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $username = 'seller'. \Yii::$app->user->identity->id;
        //VarDumper::dump($username, 10, true); exit;
        $data = Yii::$app->communication->getJwtTokenCache($username, true);
        return $data;
    }

    /**
     * @return array
     */
    public function actionAjaxSaveCall(): array
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['error' => '', 'data' => []];

        // update call status when agent reject call
        if (Yii::$app->request->getIsGet()) {
            //$get_sid = Yii::$app->request->get('sid');
            $userId = (int) Yii::$app->request->get('user_id');

            $call = Call::find()->where(['c_created_user_id' => $userId])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
            if ($call) {
                $call->c_call_status = Call::CALL_STATUS_NO_ANSWER;
                if (!$call->save()) {
                    $out['error'] = VarDumper::dumpAsString($call->errors);
                    Yii::error($out['error'], 'PhoneController:actionAjaxSaveCall:Call:save_1');
                } else {
                    $out['data'] = $call->attributes;
                }
            }
            return $out;
        }

        $call_sid = Yii::$app->request->post('call_sid');
        $call_acc_sid = Yii::$app->request->post('call_acc_sid');

        $call_from = Yii::$app->request->post('call_from');
        $call_to = Yii::$app->request->post('call_to');
        $call_status = Yii::$app->request->post('call_status', Call::CALL_STATUS_RINGING);

        $lead_id = Yii::$app->request->post('lead_id');
        $case_id = Yii::$app->request->post('case_id');
        $project_id = Yii::$app->request->post('project_id');

        if($call_sid && $call_from && $call_to) {
            $call = Call::find()->where(['c_call_sid' => $call_sid])->limit(1)->one();
            if(!$call) {
                $call = new Call();
                $call->c_call_sid = $call_sid;
                $call->c_from = $call_from;
                $call->c_to = $call_to;
                $call->c_created_dt = date('Y-m-d H:i:s');
                $call->c_created_user_id = Yii::$app->user->id;
                $call->c_call_type_id = Call::CALL_TYPE_OUT;
                $call->c_call_status = Call::CALL_STATUS_RINGING;

                if($call_acc_sid) {
                    $call->c_account_sid = $call_acc_sid;
                }
            }

            if(!$call->c_lead_id && $lead_id) {
                $call->c_lead_id = (int) $lead_id;
            }

            if(!$call->c_case_id && $case_id) {
                $call->c_case_id = (int) $case_id;
            }

            if(!$call->c_project_id && $project_id) {
                $call->c_project_id = (int) $project_id;
            }

            $call->c_call_status = $call_status;
            $call->c_updated_dt = date('Y-m-d H:i:s');

            if(!$call->save()) {
                $out['error'] = VarDumper::dumpAsString($call->errors);
                Yii::error($out['error'], 'PhoneController:actionAjaxSaveCall:Call:save');
            } else {
                $out['data'] = $call->attributes;
            }

            //Notifications::create(Yii::$app->user->id, 'Outgoing Call from '.$call_from, 'Outgoing Call from ' . $call_from .' to '.$call_to, Notifications::TYPE_WARNING, true);
            //Notifications::socket(Yii::$app->user->id, null, 'getNewNotification', [], true);
            //Notifications::socket(Yii::$app->user->id, null, 'callUpdate', ['status' => Call::CALL_STATUS_RINGING, 'duration' => 0, 'snr' => 0], true);

        }

        return $out;
    }

    public function actionAjaxCallRedirect()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        sleep(1);
        try {
            $sid = Yii::$app->request->post('sid');
            $type = Yii::$app->request->post('type');
            $from = Yii::$app->request->post('from', '');
            $to = Yii::$app->request->post('to');
            $to_id = (int)Yii::$app->request->post('to_id');
            $projectid = (int)Yii::$app->request->post('project_id');
            $lead_id = (int)Yii::$app->request->post('lead_id');
            $case_id = (int)Yii::$app->request->post('case_id');
            $check_user = Yii::$app->request->get('check_user');
            $call = null;

            if($to_id && $check_user) {
                $is_ready = true;
                $userRedirect = Employee::findOne($to_id);
                if($userRedirect) {
                    //if(!$userRedirect->isOnline() || !$userRedirect->isCallStatusReady() || !$userRedirect->isCallFree()) {
                    if(!$userRedirect->isOnline() || !$userRedirect->isCallFree()) {
                        $is_ready = false;
                    }
                } else {
                    $is_ready = false;
                }
                return [
                    'is_ready' => $is_ready,
                ];
            }


            /**
             * @var CommunicationService $communication
             */
            $communication = \Yii::$app->communication;
            $result = $communication->callRedirect($sid, $type, $from, $to);

            if ($to_id > 0) {

                $call = Call::findOne(['c_id' => $sid]);
                if (!$call && $result && isset($result['data'], $result['data']['result'], $result['data']['result']['sid'])) {


                    $dataCall = $result['data']['result'];

                    $call = Call::findOne(['c_call_sid' => $result['data']['result']['sid'], 'c_created_user_id' => $to_id]);
                    if (!$call) {
                        $call = new Call();
                    }
                    $call->c_call_sid = $result['data']['result']['sid'];
                    $call->c_account_sid = $dataCall['accountSid'] ?? null;
                    $call->c_call_type_id = Call::CALL_TYPE_IN;
                    $call->c_call_status = Call::CALL_STATUS_RINGING;
                    $call->c_com_call_id = null;
                    $call->c_direction = $dataCall['direction'] ?? null;
                    $call->c_parent_call_sid = $result['data']['result']['sid']; // $call_parent->c_parent_call_sid;
                    $call->c_project_id = $projectid;
                    $call->c_is_new = true;
                    $call->c_api_version = $dataCall['apiVersion'] ?? null;
                    $call->c_created_dt = date('Y-m-d H:i:s');
                    $call->c_from = $from;
                    $call->c_sip = null;
                    $call->c_to = $result['data']['result']['forwardedFrom'] ?? null;
                    $call->c_created_user_id = $to_id;
                    $call->c_lead_id = ($lead_id > 0) ? $lead_id : null;
                    $call->c_case_id = ($case_id > 0) ? $case_id : null;
                    $call->save();

                }

                /*if($call) {
                    Notifications::socket(null, $call->c_lead_id, 'incomingCall', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
                }*/
            }

            \Yii::info(VarDumper::dumpAsString([$result, \Yii::$app->request->post()]), 'PhoneController:actionAjaxCallRedirect:$result');
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage() . '   File/Line: ' . $e->getFile() . ':' . $e->getLine(),
            ];
        }
        return $result;
    }

    public function actionAjaxCallGetAgents()
    {
        try {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $call = null;

            $sid = Yii::$app->request->post('sid');
            $type = Yii::$app->request->post('type');
            $from = Yii::$app->request->post('from');
            $to = Yii::$app->request->post('to');
            $agent_id = (int)Yii::$app->request->post('agent_id');

            $callAgents = [];
            $html = '<div>';

            $call = Call::findOne(['c_call_sid' => $sid]);
            if(!$call) {
                $call = Call::find()->where(['c_created_user_id' => $agent_id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
            }

            if(!$call) {
                throw new \Exception('Call not found by callId: ' . $sid);
            }

            $project_id =  $call->c_project_id;
            if(!$project_id) {
                throw new \Exception('Project id not found in call by callId: ' . $sid);
            }

            //$agents_for_call = Employee::getAgentsForCall($agent_id, $project_id);
            $agents_for_call = Employee::getAgentsForGeneralLineCall($project_id, '', 50000);
            $lead_id = $call->c_lead_id ?: 0;
            $case_id = $call->c_case_id ?: 0;

            if($agents_for_call) {
                $html .= '<div id="redirect-agent-info" style="display: none;"><h3></h3></div>';
                $html .= '<table class="table" style="margin: 0" id="redirect-agent-table"><tr><th>Username</th><th>Roles</th><th>Action</th></tr>';
                foreach ($agents_for_call AS $agentForCall) {
                    $agentId = (int)$agentForCall['tbl_user_id'];
                    if($agentId === $agent_id) {
                        continue;
                    }
                    $agentObject = Employee::findOne($agentId);
                    if(!$agentObject || !$agentObject->userProfile) {
                        continue;
                    }
                    if( $agentObject->userProfile && $agentObject->userProfile->up_call_type_id !== UserProfile::CALL_TYPE_WEB ) {
                        continue;
                    }
                    $agents_ids[] = $agentObject->id . ' : '. $agentObject->username . ' - '. print_r($agentObject->getRolesRaw(), true);
                    $roles = $agentObject->getRolesRaw();
                    if(array_key_exists('agent', $roles) || array_key_exists('supervision', $roles)) {
                        $callAgents[] = [
                            'id' => $agentObject->id,
                            'name' => $agentObject->username,
                            'roles' => '('.implode(",", $roles).')',
                        ];

                        $html .= '<tr>';
                        $html .= '<td>'.$agentObject->username.'</td><td>'.implode(",", $roles).'</td>';
                        $html .= '<td><button class="btn btn-sm btn-primary redirect-agent-data" 
                            data-agentid="'.$agentObject->id.'" data-called="'.$from.'" data-agent="seller'.$agentObject->id.'" 
                            data-projectid="'.$project_id.'" data-leadid="'.$lead_id.'" data-caseid="'.$case_id.'" id="redirect-agent-id">Redirect</button></td>';
                        $html .= '</tr>';
                    }
                }
                $html .= '</table>';
            }
            $html .= '</div>';
            $result = [
                'status' => 'ok',
                'sid' => $sid,
                'project_id' => $project_id,
                'user_id' => $agent_id,
                'to' => $to,
                'from' => $from,
                'items' => $callAgents,
                'html' => $html,
                'call' => $call ? $call->c_id : 0,
                'agents_for_call' => $agents_for_call,
            ];
        } catch (\Throwable $e) {
            $result = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }

}
