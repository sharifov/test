<?php

namespace frontend\controllers;

use common\components\CommunicationService;
use common\models\Call;
use common\models\CallUserAccess;
use common\models\ClientPhone;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\Notifications;
use common\models\PhoneBlacklist;
use common\models\Project;
use common\models\UserProfile;
use common\models\UserProjectParams;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use sales\helpers\UserCallIdentity;
use sales\model\call\useCase\conference\create\CreateCallForm;
use sales\model\conference\useCase\PrepareCurrentCallsForNewCall;
use sales\model\user\entity\userStatus\UserStatus;
use yii\base\Exception;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;
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
        $client = UserCallIdentity::getId($user->id);
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
        $case = Cases::findOne(['cs_id' => $case_id]);

        $fromPhoneNumbers = [];
        if ($case && $case->isDepartmentSupport()) {
            $departmentPhones = DepartmentPhoneProject::find()->where(['dpp_project_id' => $project_id, 'dpp_dep_id' => $case->cs_dep_id, 'dpp_default' => DepartmentPhoneProject::DPP_DEFAULT_TRUE])->withPhoneList()->all();
            foreach ($departmentPhones as $departmentPhone) {
//				$fromPhoneNumbers[$departmentPhone->dpp_phone_number] = $departmentPhone->dppProject->name . ' (' . $departmentPhone->dpp_phone_number . ')';
                $fromPhoneNumbers[$departmentPhone->getPhone()] = $departmentPhone->dppProject->name . ' (' . $departmentPhone->getPhone() . ')';
            }
        } else if ($userParams = UserProjectParams::find()->where(['upp_user_id' => $userId])->withPhoneList()->all()) {
            foreach ($userParams as $param) {
//                if(!$param->upp_tw_phone_number) {
                if (!$param->getPhone()) {
                    continue;
                }
//                $fromPhoneNumbers[$param->upp_tw_phone_number] = $param->uppProject->name . ' (' . $param->upp_tw_phone_number . ')';
                $fromPhoneNumbers[$param->getPhone()] = $param->uppProject->name . ' (' . $param->getPhone() . ')';

                if ($project_id && $project_id == $param->upp_project_id) {
//                    $selectProjectPhone = $param->upp_tw_phone_number;
                    $selectProjectPhone = $param->getPhone();
                }
            }
        }


        $model = null;

        $userPhone = ClientPhone::find()->where(['phone' => $phone_number])->orderBy(['id' => SORT_DESC])->limit(1)->one();
        if ($userPhone) {
            $model = $userPhone->client;
        }


//        if(Auth::user()->isAgent()) {
//            $isAgent = true;
//        } else {
//            $isAgent = false;
//        }

        /*$searchModel = new LeadSearch();
        $params = Yii::$app->request->queryParams;
        $params['LeadSearch']['client_id'] = $model->id;
        if($isAgent) {
            $dataProvider = $searchModel->searchAgent($params);
        } else {
            $dataProvider = $searchModel->search($params);
        }

        $dataProvider->sort = false;*/

        $currentCall = Call::find()->where(['c_created_user_id' => Yii::$app->user->id, 'c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
        //$currentCall = Call::find()->orderBy(['c_id' => SORT_DESC])->limit(1)->one();


        return $this->renderPartial('ajax-phone-dial', [
            'phone_number' => $phone_number,
            'project' => $project,
            'model' => $model,
            'lead_id' => $lead_id,
            'case_id' => $case_id,
            //'dataProvider' => $dataProvider,
            'fromPhoneNumbers' => $fromPhoneNumbers,
            'selectProjectPhone' => $selectProjectPhone,
            'currentCall' => $currentCall
        ]);
    }

    public function actionGetToken()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $username = UserCallIdentity::getId(Auth::id());
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

        $userId = Auth::id();

        // update call status when agent reject call
        if (Yii::$app->request->getIsGet()) {
            //$get_sid = Yii::$app->request->get('sid');

            $call = Call::find()->where(['c_created_user_id' => $userId])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
            if ($call) {
                $call->c_status_id = Call::STATUS_NO_ANSWER;
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
        $call_status = Yii::$app->request->post('call_status', Call::TW_STATUS_RINGING);

        $lead_id = Yii::$app->request->post('lead_id');
        $case_id = Yii::$app->request->post('case_id');
        $project_id = Yii::$app->request->post('project_id');

        $depId = null;

        if ($call_from && $project_id) {
//            $upp = UserProjectParams::find()->where(['upp_tw_phone_number' => $call_from, 'upp_project_id' => $project_id])->limit(1)->one();
            $upp = UserProjectParams::find()->byPhone($call_from, false)->andWhere(['upp_project_id' => $project_id])->limit(1)->one();
            if ($upp && $upp->upp_dep_id) {
                $depId = $upp->upp_dep_id;
            }
        }


        if ($call_sid && $call_from && $call_to) {
            $call = Call::find()->where(['c_call_sid' => $call_sid])->limit(1)->one();
            if (!$call) {
                $call = new Call();
                $call->c_call_sid = $call_sid;
                $call->c_from = $call_from;
                $call->c_to = $call_to;
                $call->c_created_dt = date('Y-m-d H:i:s');
                $call->c_created_user_id = $userId;
                $call->setTypeOut();

                // $call->c_call_status = Call::CALL_STATUS_RINGING;

                if ($depId) {
                    $call->c_dep_id = $depId;
                }


            }

            if (!$call->c_lead_id && $lead_id) {
                $call->c_lead_id = (int)$lead_id;
            }

            if (!$call->c_case_id && $case_id) {
                $call->c_case_id = (int)$case_id;
            }

            if (!$call->c_project_id && $project_id) {
                $call->c_project_id = (int)$project_id;
            }

            $call->c_call_status = $call_status;
            $call->setStatusByTwilioStatus($call->c_call_status);

            if (!$call->save()) {
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

    /**
     * @return Response
     */
    public function actionCheckBlackPhone(): Response
    {
        if (!$phone = (string)Yii::$app->request->post('phone')) {
            return $this->asJson(['success' => false, 'message' => 'Phone number not found']);
        }
        if (PhoneBlacklist::find()->isExists($phone)) {
            return $this->asJson(['success' => false, 'message' => 'Declined Call. Reason: Blacklisted']);
        }
        $callTo = ClientPhone::findOne(['phone' => $phone]);
        $response = [
            'callToName' => '',
            'phone' => $phone,
            'success' => true
        ];
        if ($callTo) {
            $response['callToName'] = $callTo->client->first_name . ' ' . $callTo->client->last_name;
        }
        return $this->asJson($response);
    }

    /**
     * @return array
     */
    public function actionAjaxCheckUserForCall(): array
    {
        $result = [];
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //sleep(1);
        try {
            $userId = (int)Yii::$app->request->post('user_id');
            $isReady = false;

            if ($userId) {
                $user = Employee::findOne($userId);
                if ($user) {
                    //if(!$user->isOnline() || !$user->isCallStatusReady() || !$userRedirect->isCallFree()) {
                    if ($user->isOnline() && $user->isCallFree()) {
                        $isReady = true;
                    }
                }

            }

            $result['is_ready'] = $isReady;

        } catch (\Throwable $e) {

            $message = 'Error: ' . $e->getMessage() . ', Code: ' . $e->getCode() . ',   ' . $e->getFile() . ':' . $e->getLine();
            $result = [
                'error' => true,
                'message' => $message,
            ];

            Yii::error($message, 'PhoneController:actionAjaxCallRedirect:Throwable');
        }

        return $result;
    }


    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionAjaxCallRedirect(): array
    {

        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Not POST data', 1);
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $sid = Yii::$app->request->post('sid');
        $type = Yii::$app->request->post('type');
        $from = Yii::$app->request->post('from', '');
        $to = Yii::$app->request->post('to');

        try {

            if (!$sid) {
                throw new Exception('Error: Not found Call SID (actionAjaxCallRedirect)', 3);
            }

            if (!$type) {
                throw new Exception('Error: Not found Call type (actionAjaxCallRedirect)', 4);
            }

            if (!$to) {
                throw new Exception('Error: Not found Call To (actionAjaxCallRedirect)', 5);
            }

            if (!$originalCall = Call::find()->andWhere(['c_call_sid' => $sid])->one()) {
                throw new Exception('Error: Not found Call ' . $sid . '(actionAjaxCallRedirect)', 6);
            }

            if ($originalCall->isJoin()) {
                throw new Exception('Error: Cant redirect Join Call', 7);
            }

            if (!$originalCall->isOwner(Auth::id())) {
                throw new BadRequestHttpException('Is not your Call', 8);
            }

//            $to_id = (int)Yii::$app->request->post('to_id');
//            $projectId = (int)Yii::$app->request->post('project_id');
//            $lead_id = (int)Yii::$app->request->post('lead_id');
//            $case_id = (int)Yii::$app->request->post('case_id');
            //$call = null;

            $firstTransferToNumber = false;

            $originalCall->c_is_transfer = true;

            $lastChild = null;

            $createdUserId = null;

            if ($originalCall->isGeneralParent()) {
                if ($lastChild = Call::find()->lastChild($originalCall->c_id)->andWhere(['<>', 'c_call_type_id', Call::CALL_TYPE_JOIN])->one()) {
                    $createdUserId = $lastChild->c_created_user_id;
                    $lastChild->c_source_type_id = Call::SOURCE_TRANSFER_CALL;
                    $lastChild->c_created_user_id = null;
                    $lastChild->c_dep_id = null;
                    $lastChild->c_conference_id = null;
                    $lastChild->c_conference_sid = null;
                    $lastChild->c_is_transfer = true;
                    $sid = $lastChild->c_call_sid;
                    $firstTransferToNumber = true;
                }
            } else {
                if ($originalCall->isOut()) {
                    if (!$parent = Call::find()->firstChild($originalCall->c_parent_id)->one()) {
                        return [
                            'error' => true,
                            'message' => 'Not found Out Parent Call',
                        ];
                    }
                } else {
                    $parent = $originalCall->cParent;
                }
                $createdUserId = $parent->c_created_user_id;
                $parent->c_created_user_id = null;
                $parent->c_dep_id = null;
                $parent->c_conference_id = null;
                $parent->c_conference_sid = null;
                $parent->c_is_transfer = true;
                $parent->c_source_type_id = Call::SOURCE_TRANSFER_CALL;
                if (!$parent->c_group_id) {
                    $parent->c_group_id = $originalCall->c_id;
                }
                if (!$parent->save()) {
                    Yii::error('Can save parent call', 'PhoneController:actionAjaxCallRedirect');
                }
            }

            $groupId = null;
            if (!$originalCall->c_group_id) {
                if ($lastChild) {
                    $groupId = $originalCall->c_id;
                    $lastChild->c_group_id = $groupId;
                    $originalCall->c_group_id = $groupId;

                } else {
                    $groupId = $originalCall->c_id;
                    $originalCall->c_group_id = $groupId;
                }
            }

            if ($createdUserId) {
                UserStatus::updateIsOnnCall($createdUserId, $groupId);
            }

            if (!$originalCall->save()) {
                Yii::error(VarDumper::dumpAsString(['message' => 'Cant save original call', 'errors' => $originalCall->getErrors()]), 'PhoneController:actionAjaxCallRedirect');
            }

            if ($lastChild && !$lastChild->save()) {
                Yii::error(VarDumper::dumpAsString(['message' => 'Cant save last child call', 'errors' => $lastChild->getErrors()]), 'PhoneController:actionAjaxCallRedirect');
            }

            if (!$from) {
                $from = UserCallIdentity::getClientId(Auth::id());
            }

            $communication = \Yii::$app->communication;

//            Yii::error(VarDumper::dumpAsString([$sid, $type, $from, $to, $firstTransferToNumber]));

            if ($originalCall->isConferenceType()) {

                if ($originalCall->cParent) {
                    if ($originalCall->isOut()) {
                        if (!$firstChild = Call::find()->firstChild($originalCall->c_parent_id)->andWhere(['<>', 'c_call_type_id', Call::CALL_TYPE_JOIN])->one()) {
                            throw new Exception('API Error: PhoneController/actionAjaxCallRedirect: Not found first child out conference call ', 10);
                        }
                        $sid = $firstChild->c_call_sid;
                    } else {
                        $sid = $originalCall->cParent->c_call_sid;
                    }

                } else {
                    if (!$firstChild = Call::find()->firstChild($originalCall->c_id)->andWhere(['<>', 'c_call_type_id', Call::CALL_TYPE_JOIN])->one()) {
                        throw new Exception('API Error: PhoneController/actionAjaxCallRedirect: Not found first child conference call ', 10);
                    }
                    $sid = $firstChild->c_call_sid;
                }

                $resultApi = $communication->callForward($sid, $from, $to);
                if ($resultApi && isset($resultApi['result']['sid'])) {
                    $result = [
                        'error' => false,
                        'message' => 'ok',
                        'sid' => $resultApi['result']['sid']
                    ];
                } else {
                    throw new Exception('API Error: PhoneController/actionAjaxCallRedirect: Not found resultApi[result][sid] - ' . VarDumper::dumpAsString($resultApi), 10);
                }

            } else {
                $resultApi = $communication->callRedirect($sid, $type, $from, $to, $firstTransferToNumber);
                if ($resultApi && isset($resultApi['data']['result']['sid'])) {

                    $result = [
                        'error' => false,
                        'message' => 'ok',
                        'sid' => $resultApi['data']['result']['sid']
                    ];

                } else {
                    throw new Exception('API Error: PhoneController/actionAjaxCallRedirect: Not found resultApi[data][result][sid] - ' . VarDumper::dumpAsString($resultApi), 10);
                }
            }

//            if ($to_id > 0) {
//
//                $call = Call::findOne(['c_id' => $sid]);
//                if (!$call && $result && isset($result['data'], $result['data']['result'], $result['data']['result']['sid'])) {
//
//                    $dataCall = $result['data']['result'];
//
//                    $call = Call::findOne(['c_call_sid' => $result['data']['result']['sid'], 'c_created_user_id' => $to_id]);
//                    if (!$call) {
//                        $call = new Call();
//                    }
//                    $call->c_call_sid = $result['data']['result']['sid'];
//                    $call->c_call_type_id = Call::CALL_TYPE_IN;
//
//                    // $call->c_call_status = Call::TW_STATUS_RINGING;
//                    $call->c_status_id = Call::STATUS_RINGING;
//
//                    $call->c_com_call_id = null;
//                    $call->c_parent_call_sid = $result['data']['result']['sid']; // $call_parent->c_parent_call_sid;
//                    $call->c_project_id = $projectId;
//                    $call->c_is_new = true;
//                    $call->c_created_dt = date('Y-m-d H:i:s');
//                    $call->c_from = $from;
//                    $call->c_to = $result['data']['result']['forwardedFrom'] ?? null;
//                    $call->c_created_user_id = $to_id;
//                    $call->c_lead_id = ($lead_id > 0) ? $lead_id : null;
//                    $call->c_case_id = ($case_id > 0) ? $case_id : null;
//                    $call->save();
//
//                }
//
//                /*if($call) {
//                    Notifications::socket(null, $call->c_lead_id, 'incomingCall', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
//                }*/
//            }

            //\Yii::info(VarDumper::dumpAsString([$result, \Yii::$app->request->post()]), 'PhoneController:actionAjaxCallRedirect:$result');

        } catch (\Throwable $e) {

            $message = 'Error: ' . $e->getMessage() . ', Code: ' . $e->getCode() . ',   ' . $e->getFile() . ':' . $e->getLine();
            $result = [
                'error' => true,
                'message' => $message,
            ];
            Yii::error($message, 'PhoneController:actionAjaxCallRedirect:Throwable');
        }
        return $result;
    }


    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxCallGetAgents(): string
    {

        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Not POST data', 1);
        }

        $sid = Yii::$app->request->post('sid');
        // $userId = (int) Yii::$app->request->post('user_id');

        $userId = Auth::id();
        $users = [];
        $error = null;

        try {

            if (!$sid) {
                throw new \Exception('Error: CallSID is empty', 2);
            }

//            $call = Call::findOne(['c_call_sid' => $sid]);

//            if (!$call) {
                $call = Call::find()
                    ->bySid($sid)
                    ->byCreatedUser($userId)
                    //todo status
//                    ->andWhere(['c_status_id' => [Call::STATUS_IN_PROGRESS]])
                    ->limit(1)->one();
//            }

            if (!$call) {
                throw new \Exception('Call not found by callSID: ' . $sid, 5);
            }

            $project_id = $call->c_project_id;

            if (!$project_id) {
                throw new \Exception('Project id not found in call by callSID: ' . $sid);
            }

            $userList = Employee::getUsersForRedirectCall($call);

            if ($userList) {
                foreach ($userList as $userItem) {
                    $agentId = (int)$userItem['tbl_user_id'];
                    if ($agentId === $userId) {
                        continue;
                    }
                    $userModel = \common\models\Employee::findOne($agentId);
                    if ($userModel && ($userModel->isAgent() || $userModel->isSupAgent() || $userModel->isExAgent() || $userModel->isSupervision() || $userModel->isSupSuper() || $userModel->isExSuper())) {
                        $users[] = $userModel;
                    }
                }
            }

//            $lead_id = $call->c_lead_id ?: 0;
//            $case_id = $call->c_case_id ?: 0;

        } catch (\Throwable $e) {
            $call = null;
            $error = $e->getMessage();
        }


        $departments = [];
        if ($call) {
            $departments = DepartmentPhoneProject::find()->where(['dpp_project_id' => $call->c_project_id, 'dpp_enable' => true])->andWhere(['>', 'dpp_dep_id', 0])->withPhoneList()->orderBy(['dpp_dep_id' => SORT_ASC])->all();
        }
        $phones = \Yii::$app->params['settings']['support_phone_numbers'] ?? [];

        return $this->renderAjax('ajax_redirect_call', [
            'users' => $users,
            'phones' => $phones,
            'departments' => $departments,
            'call' => $call,
            'error' => $error
        ]);

    }

    /**
     * @return array
     */
    public function actionAjaxCallTransfer()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [];

        try {

            $sid = Yii::$app->request->post('sid');
            $type = Yii::$app->request->post('type');
            $id = (int)Yii::$app->request->post('id');

            //$userId = Yii::$app->request->post('user_id');

            if (!$sid) {
                throw new BadRequestHttpException('Not found Call SID in request', 2);
            }

            if (!$id) {
                throw new BadRequestHttpException('Not found Id in request', 3);
            }

            if (!$type) {
                throw new BadRequestHttpException('Not found Type in request', 4);
            }

            //$originCall = Call::find()->where(['c_created_user_id' => Yii::$app->user->id/*, 'c_call_status' => Call::CALL_STATUS_IN_PROGRESS*/])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

            $originCall = Call::find()
                ->bySid($sid)
                ->byCreatedUser(Auth::id())
                //todo status
//                ->andWhere(['c_status_id' => [Call::STATUS_IN_PROGRESS]])
                ->limit(1)->one();

            if (!$originCall) {
                throw new BadRequestHttpException('Not found Call', 5);
            }

            if ($originCall->isJoin()) {
                throw new Exception('Error: Cant redirect Join Call', 7);
            }

            if (!$originCall->isOwner(Auth::id())) {
                throw new BadRequestHttpException('Is not your Call', 8);
            }

            $data = [];

            if ($type === 'user') {
                $user = Employee::findOne($id);
                if (!$user) {
                    throw new BadRequestHttpException('Invalid User Id: ' . $id, 6);
                }
                if (!$user->isOnline()) {  // || !$userRedirect->isCallFree()
                    throw new NotAcceptableHttpException('This agent is not online (Id: ' . $id . ')', 7);
                }
                $data['id'] = $user->id;

            } elseif ($type === 'department') {
                $department = DepartmentPhoneProject::findOne($id);
                if (!$department) {
                    throw new BadRequestHttpException('Invalid Department Id: ' . $id, 8);
                }

                $data['id'] = $department->dpp_id;
            } else {
                throw new BadRequestHttpException('Invalid Type: ' . $type, 10);
            }


            $communication = \Yii::$app->communication;

            if (!$originCall->isConferenceType()) {
                $communication->updateRecordingStatus($sid, Call::TW_RECORDING_STATUS_PAUSED);
            }

            //$updateData = ['status' => 'completed'];
            /*$updateData = [
                'method'    =>  'POST',
                'url'       =>  Yii::$app->params['url_api_address'] . '/twilio/redirect-call-user?user_id='.$user->id
            ];*/


            $callbackUrl = Yii::$app->params['url_api_address'] . '/twilio/redirect-call?id=' . $id . '&type=' . $type;
            $data['type'] = $type;
            $data['isTransfer'] = true;

            $groupId = $originCall->c_group_id;
            $createdUserId = $originCall->c_created_user_id;

            if ($originCall->cParent) {

                if ($originCall->isOut()) {
                    $parent = Call::find()->firstChild($originCall->c_parent_id)->one();
                } else {
                    $parent = $originCall->cParent;
                }

                if ($parent) {
                    $originCall->c_is_transfer = true;
                    $parent->c_is_transfer = true;

                    if (!$originCall->c_group_id) {
                        $groupId = $originCall->c_id;
                        $originCall->c_group_id = $groupId;
                        $parent->c_group_id = $groupId;
                    }

                    $parent->c_created_user_id = null;
                    $parent->c_conference_id = null;
                    $parent->c_conference_sid = null;

                    if ($createdUserId) {
                        UserStatus::updateIsOnnCall($createdUserId, $groupId);
                    }

                    if (!$originCall->save()) {
                        Yii::error('Cant save original call', 'PhoneController:AjaxCallTransfer');
                    }
                    if (!$parent->save()) {
                        Yii::error('Cant save original->parent call', 'PhoneController:AjaxCallTransfer');
                    }

                    $callSid = $parent->c_call_sid;
                    $result = $communication->redirectCall($callSid, $data, $callbackUrl);
                } else {
                    $result['error'] = 'Not found originCall->cParent->firstChild, Origin CallSid: ' . $originCall->c_call_sid;
                }


            } else {
                $childCall = Call::find()
                    ->andWhere(['c_parent_id' => $originCall->c_id])
                    ->andWhere(['<>', 'c_call_type_id', Call::CALL_TYPE_JOIN])
                    ->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

                if ($childCall) {

                    $originCall->c_is_transfer = true;
                    $childCall->c_is_transfer = true;

                    if (!$originCall->c_group_id) {
                        $groupId = $originCall->c_id;
                        $originCall->c_group_id = $groupId;
                        $childCall->c_group_id = $groupId;
                    }

                    $childCall->c_created_user_id = null;
                    $childCall->c_conference_id = null;
                    $childCall->c_conference_sid = null;

                    if ($createdUserId) {
                        UserStatus::updateIsOnnCall($createdUserId, $groupId);
                    }

                    if (!$originCall->save()) {
                        Yii::error('Cant save original call. Point 2', 'PhoneController:AjaxCallTransfer');
                    }
                    if (!$childCall->save()) {
                        Yii::error('Cant save child call. Point 2', 'PhoneController:AjaxCallTransfer');
                    }

                    $callSid = $childCall->c_call_sid;
                    $result = $communication->redirectCall($callSid, $data, $callbackUrl);
                } else {
                    $result['error'] = 'Not found originCall->cParent, Origin CallSid: ' . $originCall->c_call_sid;
                }
            }


            /*if ($result['result'][])
            $call->c_call_sid = $callSid;
            $call->c_call_type_id = $callTypeId;
            $call->c_from = $from;
            $call->c_to = $to;
            $call->c_created_dt = $createdDt;
            $call->c_updated_dt = date('Y-m-d H:i:s');
            $call->c_recording_url = $recordingUrl;
            $call->c_recording_duration = $recordingDuration;
            $call->c_caller_name = $callerName;
            $call->c_project_id = $projectId;*/


            $call = null;

            if (!isset($result['error'])) {
                $result['error'] = false;
            }

            // \Yii::info(VarDumper::dumpAsString([$result, \Yii::$app->request->post()]), 'PhoneController:actionAjaxCallRedirectToAgent');


            //$call = Call::findOne(['c_id' => $sid]);
//                if ($result && isset($result['data'], $result['data']['call'], $result['data']['call']['sid'])) {
//
//                    $dataCall = $result['data']['call'];
//
//                    $call = Call::findOne(['c_call_sid' => $dataCall['sid']/*, 'c_created_user_id' => $userId*/]);
//
//                    if (!$call) {
//                        $call = new Call();
//                    }
//
//                    $call->c_call_sid = $dataCall['sid'];
//                    $call->c_call_type_id = Call::CALL_TYPE_IN;
//                    $call->c_call_status = Call::CALL_STATUS_IVR;
//                    $call->c_status_id = $call->setStatusByTwilioStatus($call->c_call_status);
//                    // $call->c_com_call_id = null;
//                    // $call->c_parent_call_sid = $result['data']['result']['sid']; // $call_parent->c_parent_call_sid;
//                    // $call->c_project_id = $projectid;
//
//                    $call->c_project_id = $originCall->c_project_id;
//                    $call->c_dep_id = $originCall->c_dep_id;
//
//                    $call->c_is_new = true;
//                    $call->c_created_dt = date('Y-m-d H:i:s');
//                    $call->c_from = $dataCall['from']; //$from;
//                    $call->c_to = UserCallIdentity::getClientId($userId);//$result['data']['result']['forwardedFrom'] ?? null;
//                    $call->c_created_user_id = $userId;
//                    // $call->c_lead_id = ($lead_id > 0) ? $lead_id : null;
//                    // $call->c_case_id = ($case_id > 0) ? $case_id : null;
//                    if (!$call->save()) {
//                        Yii::error(VarDumper::dumpAsString($call->errors), 'PhoneController:actionAjaxCallRedirectToAgent');
//                    }
//
//                }

            /*if($call) {
                Notifications::socket(null, $call->c_lead_id, 'incomingCall', ['status' => $call->c_call_status, 'duration' => $call->c_call_duration, 'snr' => $call->c_sequence_number], true);
            }*/


            // \Yii::info(VarDumper::dumpAsString(['call' => $call ? $call->attributes  : null, 'sid' => $sid, 'updateData' => $updateData, 'result' => $result, 'post' => \Yii::$app->request->post()]), 'info\PhoneController:actionAjaxCallRedirectToAgent');

        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage() . '   File/Line: ' . $e->getFile() . ':' . $e->getLine(),
            ];
        }
        return $result;
    }

    public function actionAjaxHangup(): Response
    {
        try {

            $sid = (string)Yii::$app->request->post('sid');

            if (!$sid) {
                $id = (int)Yii::$app->request->post('id');
                if (!$id) {
                    throw new BadRequestHttpException('Not found Call SID or ID in request');
                } else {
                    if (!$call = Call::findOne(['c_id' => $id])) {
                        throw new BadRequestHttpException('Not found Call. ID: ' . $id);
                    }
                }
            } else {
                if (!$call = Call::findOne(['c_call_sid' => $sid])) {
                    throw new BadRequestHttpException('Not found Call. Sid: ' . $sid);
                }
            }

            if (!$call->isOwner(Auth::id())) {
                throw new BadRequestHttpException('Is not your Call');
            }

            if (!($call->isStatusInProgress() || $call->isStatusRinging())) {
                throw new BadRequestHttpException('Call status is not correct');
            }

            $result = Yii::$app->communication->hangUp($call->c_call_sid);

        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return $this->asJson($result);
    }

    public function actionAjaxHoldConferenceCall(): Response
    {
        try {
            $sid = (string)Yii::$app->request->post('sid');
            $data = $this->getDataForHoldConferenceCall($sid, Auth::id());
            /** @var Call $call */
            $call = $data['call'];
            if (!$call->currentParticipant->isJoin()) {
                throw new \Exception('Invalid type of Participant');
            }
            $result = Yii::$app->communication->holdConferenceCall($data['conferenceSid'], $data['keeperSid']);
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return $this->asJson($result);
    }

    public function actionAjaxUnholdConferenceCall(): Response
    {
        try {
            $sid = (string)Yii::$app->request->post('sid');
            $data = $this->getDataForHoldConferenceCall($sid, Auth::id());
            /** @var Call $call */
            $call = $data['call'];
            if (!$call->currentParticipant->isHold()) {
                throw new \Exception('Invalid type of Participant');
            }
            $result = Yii::$app->communication->unholdConferenceCall($data['conferenceSid'], $data['keeperSid']);
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return $this->asJson($result);
    }

    public function actionAjaxJoinToConference(): Response
    {
        try {
            $isOnCall = UserStatus::findOne(['us_user_id' => Auth::id(), 'us_is_on_call' => true]);
            if ($isOnCall) {
                throw new BadRequestHttpException('Already exist active call');
            }

            $call_sid = (string)Yii::$app->request->post('call_sid');
            $source_type_id = (string)Yii::$app->request->post('source_type_id');

            $call = $this->getJoinCall($call_sid);

            $key = 'conference-call-join';

            $diff = time() - (int)(Yii::$app->session->get($key, 0));
            /** 15 sec diff between requests */
            if ($diff < 15) {
                throw new \Exception('Please wait ' . (15 - $diff) . ' seconds.');
            }

            $from = $call->cParent->c_to ?? $call->c_from;

            $result = Yii::$app->communication->joinToConference(
                $call->c_call_sid,
                $call->c_conference_sid,
                $call->c_project_id,
                $from,
                UserCallIdentity::getClientId(Auth::id()),
                $source_type_id
            );
            Yii::$app->session->set($key, time());
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return $this->asJson($result);
    }

    public function actionAjaxCreateCall()
    {
        try {
            $isOnCall = UserStatus::findOne(['us_user_id' => Auth::id(), 'us_is_on_call' => true]);
            if ($isOnCall) {
                throw new BadRequestHttpException('Already exist active call');
            }

            $form = new CreateCallForm();

            if (!$form->load(Yii::$app->request->post())) {
                throw new BadRequestHttpException('Cant load data');
            }

            $form->user_id = Auth::id();
            $form->caller = UserCallIdentity::getClientId(Auth::id());

            if (!$form->validate()) {
                throw new BadRequestHttpException('Request error: ' . VarDumper::dumpAsString($form->getErrors()));
            }

            $result = Yii::$app->communication->createCall($form);

        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return $this->asJson($result);
    }

    public function actionAjaxMuteParticipant(): Response
    {
        try {
            $sid = (string)Yii::$app->request->post('sid');
            $call = $this->getCallForMuteUnmuteParticipant($sid, Auth::id());
            if ($call->currentParticipant->isMute()) {
                throw new \Exception('Participant already is mute');
            }
            $result = Yii::$app->communication->muteParticipant($call->c_conference_sid, $call->c_call_sid);
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return $this->asJson($result);
    }

    public function actionAjaxUnmuteParticipant(): Response
    {
        try {
            $sid = (string)Yii::$app->request->post('sid');
            $call = $this->getCallForMuteUnmuteParticipant($sid, Auth::id());
            if ($call->currentParticipant->isUnMute()) {
                throw new \Exception('Participant already is unMute');
            }
            $result = Yii::$app->communication->unmuteParticipant($call->c_conference_sid, $call->c_call_sid);
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return $this->asJson($result);
    }

    private function getJoinCall(string $sid): Call
    {
        if (!$sid) {
            throw new BadRequestHttpException('Not found Call SID in request');
        }

        if (!$call = Call::findOne(['c_call_sid' => $sid])) {
            throw new BadRequestHttpException('Not found Call. Sid: ' . $sid);
        }

        if (!$call->isStatusInProgress()) {
            throw new BadRequestHttpException('Invalid Call status. Sid: ' . $sid);
        }

        if (!($call->isIn() || $call->isOut())) {
            throw new BadRequestHttpException('Invalid Call Type. Sid: ' . $sid);
        }

        if (!$call->isConferenceType()) {
            throw new BadRequestHttpException('Call is not conference Call. Sid: ' . $sid);
        }

        if (!$call->c_conference_id) {
            throw new BadRequestHttpException('Call not updated. Please wait some seconds.');
        }

        if (!$participant = $call->currentParticipant) {
            throw new BadRequestHttpException('Not found Participant. Sid: ' . $sid);
        }

        if (!$participant->isAgent()) {
            throw new BadRequestHttpException('Invalid Participant type. Sid: ' . $sid);
        }

        if ($participant->isHold()) {
            throw new BadRequestHttpException('Participant status is Hold. Must be is Join');
        }

        if (!$participant->isJoin()) {
            throw new BadRequestHttpException('Participant status is not valid');
        }

        return $call;
    }

    private function getDataForHoldConferenceCall(string $sid, int $userId): array
    {
        if (!$sid) {
            throw new BadRequestHttpException('Not found Call SID in request');
        }

        if (!$call = Call::findOne(['c_call_sid' => $sid])) {
            throw new BadRequestHttpException('Not found Call. Sid: ' . $sid);
        }

        if (!$call->isOwner($userId)) {
            throw new BadRequestHttpException('Is not your Call');
        }

        if (!$participant = $call->currentParticipant) {
            throw new BadRequestHttpException('Not found Participant');
        }

        if (!($participant->isAgent() || $participant->isUser())) {
            throw new BadRequestHttpException('Invalid type of Participant');
        }

        if (!($call->isIn() || $call->isOut())) {
            throw new BadRequestHttpException('Invalid Call type');
        }

        if (!$call->isStatusInProgress()) {
            throw new BadRequestHttpException('Invalid Call Status');
        }

        if (!$call->isConferenceType()) {
            throw new BadRequestHttpException('Call is not conference Call. Sid: ' . $sid);
        }

        if (!$call->c_conference_id) {
            throw new BadRequestHttpException('Call not updated. Please wait some seconds.');
        }

        if (!$conference = Conference::findOne(['cf_id' => $call->c_conference_id])) {
            throw new BadRequestHttpException('Not found conference. SID: ' . $call->c_conference_sid);
        }

        if (!$conference->isCreator($userId)) {
            throw new BadRequestHttpException('You are not conference creator. Sid: ' . $sid);
        }

        if (!$participants = $conference->conferenceParticipants) {
            throw new BadRequestHttpException('Not found participants on Conference Sid: ' . $call->c_conference_sid);
        }

        if (count($participants) < 2) {
            throw new BadRequestHttpException('Please wait. Count participant must be more then 2');
        }

        $keeperSid = null;

        $callIsOneOfParticipants = false;
        foreach ($participants as $participant) {
            if ($participant->cp_call_id === $call->c_id) {
                $callIsOneOfParticipants = true;
                $keeperSid = $participant->cp_call_sid;
                break;
            }
        }

        if (!$callIsOneOfParticipants) {
            throw new BadRequestHttpException('Call is not One of participants on Conference Sid: ' . $call->c_conference_sid);
        }

        return [
            'conferenceSid' => $conference->cf_sid,
            'keeperSid' => $keeperSid,
            'call' => $call,
        ];
    }

    private function getCallForMuteUnmuteParticipant(string $sid, int $userId): Call
    {
        if (!$sid) {
            throw new BadRequestHttpException('Not found Call SID in request');
        }

        if (!$call = Call::findOne(['c_call_sid' => $sid])) {
            throw new BadRequestHttpException('Not found Call. Sid: ' . $sid);
        }

        if (!$call->isOwner($userId)) {
            throw new BadRequestHttpException('Is not your Call');
        }

        if ($call->isJoin() && $call->c_source_type_id === Call::SOURCE_LISTEN) {
            throw new BadRequestHttpException('Invalid type of Call');
        }

        if (!$participant = $call->currentParticipant) {
            throw new BadRequestHttpException('Not found Participant');
        }

        if (!($participant->isAgent() || $participant->isUser())) {
            throw new BadRequestHttpException('Invalid type of Participant');
        }

        if (!$participant->isJoin()) {
            throw new BadRequestHttpException('Participant status is invalid');
        }

        if (!$call->isStatusInProgress()) {
            throw new BadRequestHttpException('Invalid Call Status');
        }

        if (!$call->isConferenceType()) {
            throw new BadRequestHttpException('Call is not conference Call. Sid: ' . $sid);
        }

        if (!$call->c_conference_id) {
            throw new BadRequestHttpException('Call not updated. Please wait some seconds.');
        }

        if (!$conference = Conference::findOne(['cf_id' => $call->c_conference_id])) {
            throw new BadRequestHttpException('Not found conference. SID: ' . $call->c_conference_sid);
        }

        if ($conference->isEnd()) {
            throw new BadRequestHttpException('Invalid Conference status. SID: ' . $call->c_conference_sid);
        }

        if (!$participants = $conference->conferenceParticipants) {
            throw new BadRequestHttpException('Not found participants on Conference Sid: ' . $call->c_conference_sid);
        }

        $callIsOneOfParticipants = false;
        foreach ($participants as $participant) {
            if ($participant->cp_call_id === $call->c_id) {
                $callIsOneOfParticipants = true;
                break;
            }
        }

        if (!$callIsOneOfParticipants) {
            throw new BadRequestHttpException('Call is not One of participants on Conference Sid: ' . $call->c_conference_sid);
        }

        return $call;
    }

    public function actionSendDigit(): Response
    {
        try {
            $sid = (string)Yii::$app->request->post('conference_sid');
            $digit = (string)Yii::$app->request->post('digit');

            if (!$sid) {
                throw new BadRequestHttpException('Not found Conference SID in request');
            }

            if (!$digit && $digit !== '0') {
                throw new BadRequestHttpException('Not found Digit in request');
            }

            if (!$conference = Conference::findOne(['cf_sid' => $sid])) {
                throw new BadRequestHttpException('Not found Conference. Sid: ' . $sid);
            }

            if ($conference->isEnd()) {
                throw new BadRequestHttpException('Conference is completed. Sid: ' . $sid);
            }

            if (!$conference->isCreator(Auth::id())) {
                throw new BadRequestHttpException('You are not conference creator. Sid: ' . $sid);
            }

            $result = Yii::$app->communication->sendDigitToConference($sid, $digit);

        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return $this->asJson($result);
    }

    public function actionPrepareCurrentCalls()
    {
        $result = ['error' => false, 'message' => ''];
        try {
            $prepare = new PrepareCurrentCallsForNewCall();
            if (!$prepare->prepare(Auth::id())) {
                $result = [
                    'error' => true,
                    'message' => 'Error. Please try again later.',
                ];
            }
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return $this->asJson($result);
    }

    public function actionCreateInternalCall()
    {
        try {

            $createdUser = Auth::user();
            $key = 'call_user_to_user_' . $createdUser->id;
            $this->guardFromUserIsFree($createdUser->id, $key);

            $user_id = (int)Yii::$app->request->post('user_id');
            $this->guardValidToUser($user_id);

            $this->guardPermissionUserToUserCall($createdUser->id, $user_id);

            Yii::$app->cache->set($key, (time() + 10), 10);

            $result = Yii::$app->communication->callToUser(
                UserCallIdentity::getClientId($createdUser->id),
                UserCallIdentity::getClientId($user_id),
                $createdUser->id,
                [
                    'status' => 'Ringing',
                    'duration' => 0,
                    'typeId' => Call::CALL_TYPE_IN,
                    'type' => 'Incoming',
                    'source_type_id' => Call::SOURCE_INTERNAL,
                    'fromInternal' => 'false',
                    'isInternal' => 'true',
                    'isHold' => 'false',
                    'holdDuration' => 0,
                    'isListen' => 'false',
                    'isCoach' => 'false',
                    'isMute' => 'false',
                    'isBarge' => 'false',
                    'project' => '',
                    'source' => Call::SOURCE_LIST[Call::SOURCE_INTERNAL],
                    'isEnded' => 'false',
                    'contact' => [
                        'name' => $createdUser->nickname ?: $createdUser->username,
                        'phone' => '',
                        'company' => '',
                    ],
                    'department' => '',
                    'queue' => Call::QUEUE_DIRECT,
                    'conference' => [],
                    'isConferenceCreator' => 'false',
                ]
            );

        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $this->asJson($result);
    }

    private function guardPermissionUserToUserCall(int $fromUserId, int $toUserId): void
    {
        if ($fromUserId === $toUserId) {
            throw new \DomainException('Invalid To userId');
        }
        //todo
    }

    private function guardValidToUser(int $userId): void
    {
        if (!$user = Employee::findOne(['id' => $userId])) {
            throw new \DomainException('Not found user. Id: ' . $userId);
        }
        if (!$user->isOnline()) {
            throw new \DomainException('User ' . ($user->nickname ?: $user->full_name) . ' is offline');
        }
        if (!$user->isCallFree()) {
            throw new \DomainException('User ' . ($user->nickname ?: $user->full_name) . ' is occupied');
        }
    }

    private function guardFromUserIsFree($userId, $key): void
    {
        if ($result = Yii::$app->cache->get($key)) {
            throw new \DomainException('Please wait ' . abs($result - time()) . ' seconds.');
        }
    }

    public function actionGetUserByPhone(): Response
    {
        try {
            $phone = (string)Yii::$app->request->post('phone');
            if ($uPp = UserProjectParams::find()->byPhone($phone, false)->limit(1)->one()) {
                $result = [
                    'error' => false,
                    'userId' => $uPp->upp_user_id,
                    'nickname' => $uPp->uppUser->nickname ?: $uPp->uppUser->full_name,
                ];
            } else {
                $result = [
                    'error' => false,
                    'userId' => null
                ];
            }
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $this->asJson($result);
    }
}
