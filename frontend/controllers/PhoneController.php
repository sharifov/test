<?php

namespace frontend\controllers;

use common\components\CommunicationService;
use common\models\Call;
use common\models\ClientPhone;
use common\models\Notifications;
use common\models\Project;
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
        $call_sid = Yii::$app->request->post('call_sid');
        $call_acc_sid = Yii::$app->request->post('call_acc_sid');

        $call_from = Yii::$app->request->post('call_from');
        $call_to = Yii::$app->request->post('call_to');
        $call_status = Yii::$app->request->post('call_status', Call::CALL_STATUS_RINGING);

        $lead_id = Yii::$app->request->post('lead_id');
        $project_id = Yii::$app->request->post('project_id');

        $out = ['error' => '', 'data' => []];

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

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

            if(!$call->c_project_id && $project_id) {
                $call->c_project_id = (int) $project_id;
            }

            $call->c_call_status = $call_status;
            $call->c_updated_dt = date('Y-m-d H:i:s');

            /*if(!$call->save()) {
                $out['error'] = VarDumper::dumpAsString($call->errors);
                Yii::error($out['error'], 'PhoneController:actionAjaxSaveCall:Call:save');
            } else {
                $out['data'] = $call->attributes;
            }

            //Notifications::create(Yii::$app->user->id, 'Outgoing Call from '.$call_from, 'Outgoing Call from ' . $call_from .' to '.$call_to, Notifications::TYPE_WARNING, true);
            //Notifications::socket(Yii::$app->user->id, null, 'getNewNotification', [], true);
            Notifications::socket(Yii::$app->user->id, null, 'callUpdate', ['status' => Call::CALL_STATUS_RINGING, 'duration' => 0, 'snr' => 0], true);*/

        }

        return $out;
    }

    public function actionAjaxCallRedirect()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $sid = Yii::$app->request->post('sid');
        $type = Yii::$app->request->post('type');
        $from = Yii::$app->request->post('from');
        $to = Yii::$app->request->post('to');

        /**
         * @var CommunicationService $communication
         */
        $communication = \Yii::$app->communication;
        $result = $communication->callRedirect($sid, $type, $from, $to);
        return $result;
    }

}
