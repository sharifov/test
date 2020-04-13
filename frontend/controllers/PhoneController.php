<?php

namespace frontend\controllers;

use common\components\CommunicationService;
use common\models\Call;
use common\models\ClientPhone;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Employee;
use common\models\Notifications;
use common\models\PhoneBlacklist;
use common\models\Project;
use common\models\UserProfile;
use common\models\UserProjectParams;
use sales\entities\cases\Cases;
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
                if(!$param->getPhone()) {
                    continue;
                }
//                $fromPhoneNumbers[$param->upp_tw_phone_number] = $param->uppProject->name . ' (' . $param->upp_tw_phone_number . ')';
                $fromPhoneNumbers[$param->getPhone()] = $param->uppProject->name . ' (' . $param->getPhone() . ')';

                if($project_id  && $project_id == $param->upp_project_id) {
//                    $selectProjectPhone = $param->upp_tw_phone_number;
                    $selectProjectPhone = $param->getPhone();
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

        $currentCall = Call::find()->where(['c_created_user_id' => Yii::$app->user->id, 'c_status_id' => [Call::STATUS_RINGING, Call::STATUS_QUEUE, Call::STATUS_IN_PROGRESS]])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
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
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $username = 'seller'. $user->id;
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

                // $call->c_call_status = Call::CALL_STATUS_RINGING;

                if ($depId) {
                    $call->c_dep_id = $depId;
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
            $call->setStatusByTwilioStatus($call->c_call_status);

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
        return $this->asJson(['success' => true]);
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
            $userId = (int) Yii::$app->request->post('user_id');
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


//            $to_id = (int)Yii::$app->request->post('to_id');
//            $projectId = (int)Yii::$app->request->post('project_id');
//            $lead_id = (int)Yii::$app->request->post('lead_id');
//            $case_id = (int)Yii::$app->request->post('case_id');
            //$call = null;

            $firstTransferToNumber = false;

            $originalCall = Call::find()->andWhere(['c_call_sid' => $sid])->one();
            $originalCall->c_is_transfer = true;

            $lastChild = null;

            if ($originalCall->isGeneralParent()) {
                if ($lastChild = Call::find()->lastChild($originalCall->c_id)->one()) {
                    $lastChild->c_is_transfer = true;
                    $sid = $lastChild->c_call_sid;
                    $firstTransferToNumber = true;
                }
            } else {
                $originalCall->cParent->c_source_type_id = Call::SOURCE_TRANSFER_CALL;
                $originalCall->cParent->c_group_id = $originalCall->c_id;
                if (!$originalCall->cParent->save()) {
                    Yii::error('Can save parent call', 'PhoneController:actionAjaxCallRedirect');
                }
            }

            if (!$originalCall->c_group_id) {
                if ($lastChild) {
                    if ($originalCall->isIn()) {
                        $lastChild->c_group_id = $originalCall->c_id;
                        $originalCall->c_group_id = $originalCall->c_id;
                    } else {
                        $lastChild->c_group_id = $lastChild->c_id;
                       // $originalCall->c_group_id = $lastChild->c_id;
                    }
                } else {
                    $originalCall->c_group_id = $originalCall->c_id;
                }
            }



            if (!$originalCall->save()) {
                Yii::error(VarDumper::dumpAsString(['message' => 'Cant save original call', 'errors' => $originalCall->getErrors()]), 'PhoneController:actionAjaxCallRedirect');
            }

            if ($lastChild && !$lastChild->save()) {
                Yii::error(VarDumper::dumpAsString(['message' => 'Cant save last child call', 'errors' => $lastChild->getErrors()]), 'PhoneController:actionAjaxCallRedirect');
            }

            if (!$from) {
                $from = 'client:seller' . Yii::$app->user->id;
            }

            $communication = \Yii::$app->communication;
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

            $message = 'Error: ' . $e->getMessage() . ', Code: ' . $e->getCode() .  ',   ' . $e->getFile() . ':' . $e->getLine();
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

        $userId = Yii::$app->user->id;
        $users = [];
        $error = null;

        try {

            if (!$sid) {
                throw new \Exception('Error: CallSID is empty', 2);
            }

            if (!$userId) {
                throw new \Exception('Error: UserID is empty', 3);
            }

            $call = Call::findOne(['c_call_sid' => $sid]);

            if (!$call) {
                $call = Call::find()->where(['c_created_user_id' => $userId])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();
            }

            if (!$call) {
                throw new \Exception('Call not found by callSID: ' . $sid, 5);
            }

            $project_id =  $call->c_project_id;

            if (!$project_id) {
                throw new \Exception('Project id not found in call by callSID: ' . $sid);
            }

            $userList = Employee::getUsersForRedirectCall($call);

            if($userList) {
                foreach ($userList as $userItem) {
                    $agentId = (int) $userItem['tbl_user_id'];
                    if($agentId === $userId) {
                        continue;
                    }
                    $userModel = \common\models\Employee::findOne($agentId);
                    if ($userModel->isAgent() || $userModel->isSupAgent() || $userModel->isExAgent() || $userModel->isSupervision() || $userModel->isSupSuper() || $userModel->isExSuper()) {
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


        $departments = DepartmentPhoneProject::find()->where(['dpp_project_id' => $call->c_project_id, 'dpp_enable' => true])->andWhere(['>', 'dpp_dep_id', 0])->withPhoneList()->orderBy(['dpp_dep_id' => SORT_ASC])->all();
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
            $id = (int) Yii::$app->request->post('id');

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

            $originCall = Call::find()->where(['c_call_sid' => $sid])->one();

            if (!$originCall) {
                $originCall = Call::find()->where(['c_call_sid' => $sid])->one();
            }

            if (!$originCall) {
                throw new BadRequestHttpException('Not found Call', 5);
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

            //$updateData = ['status' => 'completed'];
            /*$updateData = [
                'method'    =>  'POST',
                'url'       =>  Yii::$app->params['url_api_address'] . '/twilio/redirect-call-user?user_id='.$user->id
            ];*/



            $callbackUrl = Yii::$app->params['url_api_address'] . '/twilio/redirect-call?id=' . $id . '&type=' . $type;
            $data['type'] = $type;
            $data['isTransfer'] = true;

            if ($originCall->cParent) {

                $originCall->c_is_transfer = true;
                $originCall->cParent->c_is_transfer = true;

                if (!$originCall->c_group_id) {
                    $originCall->c_group_id = $originCall->c_id;
                    $originCall->cParent->c_group_id = $originCall->c_id;
                }

                if (!$originCall->save()) {
                    Yii::error('Cant save original call', 'PhoneController:AjaxCallTransfer');
                }
                if (!$originCall->cParent->save()) {
                    Yii::error('Cant save original->parent call', 'PhoneController:AjaxCallTransfer');
                }

                $callSid = $originCall->cParent->c_call_sid;
                $result = $communication->redirectCall($callSid, $data, $callbackUrl);
            } else {
                $childCall = Call::find()->where(['c_parent_id' => $originCall->c_id])->orderBy(['c_id' => SORT_DESC])->limit(1)->one();

                if ($childCall) {

                    $originCall->c_is_transfer = true;
                    $childCall->c_is_transfer = true;

                    if (!$originCall->c_group_id) {
                        $originCall->c_group_id = $originCall->c_id;
                        $childCall->c_group_id = $originCall->c_id;
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

            if(!isset($result['error'])) {
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
//                    $call->c_to = 'client:seller' . $userId;//$result['data']['result']['forwardedFrom'] ?? null;
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

}
