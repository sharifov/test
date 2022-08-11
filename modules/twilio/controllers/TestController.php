<?php

namespace modules\twilio\controllers;

use common\components\CommunicationService;
use common\models\Employee;
use common\models\Sms;
use modules\twilio\components\TwilioCommunicationService;
use yii\web\Controller;

class TestController extends Controller
{
    private $comService;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
//      $twComService = \Yii::createObject(TwilioCommunicationService::class);
//      $twComService->host = 'https://api.sales.test';
//      $twComService = \Yii::createObject(CommunicationService::class);
//      $twComService->url = 'https://communication-dev.api.travelinsides.com/v1/';
//      $twComService->username = 'sales';
//      $twComService->password = 'Sales2018!';
//      $twComService->init();

        $this->comService = \Yii::$app->comms;
        var_dump($this->comService);
        die;
    }

    public function actionTest()
    {
        $sms = new Sms();
        $sms->s_project_id = 6;
        $sms->s_lead_id = 513017;
//      if ($previewSmsForm->s_sms_tpl_id) {
//          $sms->s_template_type_id = $previewSmsForm->s_sms_tpl_id;
//      }
        $sms->s_type_id = Sms::TYPE_OUTBOX;
        $sms->s_status_id = Sms::STATUS_PENDING;

        $sms->s_sms_text = 'test message';
        $sms->s_phone_from = '+18553408251';
        $sms->s_phone_to = '+37378077519';

//      if ($previewSmsForm->s_language_id) {
//          $sms->s_language_id = $previewSmsForm->s_language_id;
//      }

        //$sms->s_email_data = [];

        $sms->s_created_dt = date('Y-m-d H:i:s');
        $sms->s_created_user_id = 464;

        $smsResponse = $sms->sendSms();

        echo '<pre>';
        print_r($smsResponse);
        die;
    }

    public function actionTestSmsSend()
    {
        $content_data['sms_text'] = 'test message';
        $data['project_id'] = 6;
//      $data['s_id'] = $this->s_id;
        $response = $this->comService->smsSend(6, null, '+18553408252', '+37378077519', $content_data, $data);
        echo '<pre>';
        print_r($response);
        die;
    }

    public function actionSmsGetMessages()
    {
        $response = $this->comService->smsGetMessages([]);
        echo '<pre>';
        print_r($response);
        die;
    }

    public function actionUpdateCall()
    {
        $response = $this->comService->updateCall('testsid', ['status' => 'completed']);
        echo '<pre>';
        var_dump($response);
        die;
    }

    public function actionRedirectCall()
    {
        $data['c_id'] = 3368195;
        $callbackUrl = \Yii::$app->params['url_api'] . '/twilio/cancel-call?id=' . 3368195;
        $response = $this->comService->redirectCall('CA276ac3bf5c1eda08cafaaf304804899a', $data, $callbackUrl);
        var_dump($response);
        die;
    }

    public function actionCallRedirect()
    {
        $response = $this->comService->callRedirect('CA276ac3bf5c1eda08cafaaf304804899a', 'client', '+14159801855', '+37378077519', true);
        var_dump($response);
        die;
    }
}
