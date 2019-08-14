<?php

namespace frontend\controllers;

use common\components\CommunicationService;
use common\models\Call;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Quote;
use common\models\Sms;
use common\models\SmsTemplateType;
use common\models\UserProjectParams;
use frontend\models\CaseCommunicationForm;
use frontend\models\CasePreviewEmailForm;
use frontend\models\CasePreviewSmsForm;
use frontend\models\CommunicationForm;
use frontend\models\LeadPreviewEmailForm;
use frontend\models\LeadPreviewSmsForm;
use Yii;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesSearch;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CasesController implements the CRUD actions for Cases model.
 */
class CasesController extends FController
{
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all Cases models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CasesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Cases model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        $previewEmailForm = new CasePreviewEmailForm();
        $previewEmailForm->is_send = false;


        if ($previewEmailForm->load(Yii::$app->request->post())) {
            $previewEmailForm->e_case_id = $model->cs_id;
            if ($previewEmailForm->validate()) {

                $mail = new Email();
                $mail->e_project_id = $model->cs_project_id;
                $mail->e_case_id = $model->cs_id;
                if ($previewEmailForm->e_email_tpl_id) {
                    $mail->e_template_type_id = $previewEmailForm->e_email_tpl_id;
                }
                $mail->e_type_id = Email::TYPE_OUTBOX;
                $mail->e_status_id = Email::STATUS_PENDING;
                $mail->e_email_subject = $previewEmailForm->e_email_subject;
                $mail->e_email_body_html = $previewEmailForm->e_email_message;
                $mail->e_email_from = $previewEmailForm->e_email_from;

                $mail->e_email_from_name = $previewEmailForm->e_email_from_name;
                $mail->e_email_to_name = $previewEmailForm->e_email_to_name;

                if ($previewEmailForm->e_language_id) {
                    $mail->e_language_id = $previewEmailForm->e_language_id;
                }

                $mail->e_email_to = $previewEmailForm->e_email_to;
                //$mail->e_email_data = [];
                $mail->e_created_dt = date('Y-m-d H:i:s');
                $mail->e_created_user_id = Yii::$app->user->id;

                if ($mail->save()) {

                    $mail->e_message_id = $mail->generateMessageId();
                    $mail->update();

                    $previewEmailForm->is_send = true;

                    $mailResponse = $mail->sendMail();

                    if (isset($mailResponse['error']) && $mailResponse['error']) {
                        //echo $mailResponse['error']; exit; //'Error: <strong>Email Message</strong> has not been sent to <strong>'.$mail->e_email_to.'</strong>'; exit;
                        Yii::$app->session->setFlash('send-error', 'Error: <strong>Email Message</strong> has not been sent to <strong>' . $mail->e_email_to . '</strong>');
                        Yii::error('Error: Email Message has not been sent to ' . $mail->e_email_to . "\r\n " . $mailResponse['error'], 'CaseController:view:Email:sendMail');
                    } else {
                        //echo '<strong>Email Message</strong> has been successfully sent to <strong>'.$mail->e_email_to.'</strong>'; exit;


                        if ($quoteList = @json_decode($previewEmailForm->e_quote_list)) {
                            if (is_array($quoteList)) {
                                foreach ($quoteList as $quoteId) {
                                    $quoteId = (int)$quoteId;
                                    $quote = Quote::findOne($quoteId);
                                    if ($quote) {
                                        $quote->status = Quote::STATUS_SEND;
                                        if (!$quote->save()) {
                                            Yii::error($quote->errors, 'CaseController:view:Email:Quote:save');
                                        }
                                    }
                                }
                            }
                        }

                        Yii::$app->session->setFlash('send-success', '<strong>Email Message</strong> has been successfully sent to <strong>' . $mail->e_email_to . '</strong>');
                    }

                    $this->refresh('#communication-form');

                } else {
                    $previewEmailForm->addError('e_email_subject', VarDumper::dumpAsString($mail->errors));
                    Yii::error(VarDumper::dumpAsString($mail->errors), 'CaseController:view:Email:save');
                }
                //VarDumper::dump($previewEmailForm->attributes, 10, true);              exit;
            }
        }


        $previewSmsForm = new CasePreviewSmsForm();
        $previewSmsForm->is_send = false;

        if ($previewSmsForm->load(Yii::$app->request->post())) {
            $previewSmsForm->s_case_id = $model->cs_id;
            if ($previewSmsForm->validate()) {

                $sms = new Sms();
                $sms->s_project_id = $model->cs_project_id;
                $sms->s_case_id = $model->cs_id;
                if ($previewSmsForm->s_sms_tpl_id) {
                    $sms->s_template_type_id = $previewSmsForm->s_sms_tpl_id;
                }
                $sms->s_type_id = Sms::TYPE_OUTBOX;
                $sms->s_status_id = Sms::STATUS_PENDING;

                $sms->s_sms_text = $previewSmsForm->s_sms_message;
                $sms->s_phone_from = $previewSmsForm->s_phone_from;
                $sms->s_phone_to = $previewSmsForm->s_phone_to;

                if ($previewSmsForm->s_language_id) {
                    $sms->s_language_id = $previewSmsForm->s_language_id;
                }

                //$sms->s_email_data = [];

                $sms->s_created_dt = date('Y-m-d H:i:s');
                $sms->s_created_user_id = Yii::$app->user->id;

                if ($sms->save()) {

                    $previewSmsForm->is_send = true;


                    $smsResponse = $sms->sendSms();

                    if (isset($smsResponse['error']) && $smsResponse['error']) {
                        Yii::$app->session->setFlash('send-error', 'Error: <strong>SMS Message</strong> has not been sent to <strong>' . $sms->s_phone_to . '</strong>');
                        Yii::error('Error: SMS Message has not been sent to ' . $sms->s_phone_to . "\r\n " . $smsResponse['error'], 'CaseController:view:Sms:sendSms');
                    } else {

                        if ($quoteList = @json_decode($previewSmsForm->s_quote_list)) {
                            if (is_array($quoteList)) {
                                foreach ($quoteList as $quoteId) {
                                    $quoteId = (int)$quoteId;
                                    $quote = Quote::findOne($quoteId);
                                    if ($quote) {
                                        $quote->status = Quote::STATUS_SEND;
                                        if (!$quote->save()) {
                                            Yii::error($quote->errors, 'CaseController:view:Sms:Quote:save');
                                        }
                                    }
                                }
                            }
                        }

                        Yii::$app->session->setFlash('send-success', '<strong>SMS Message</strong> has been successfully sent to <strong>' . $sms->s_phone_to . '</strong>');
                    }

                    $this->refresh('#communication-form');

                } else {
                    $previewSmsForm->addError('s_sms_text', VarDumper::dumpAsString($sms->errors));
                    Yii::error(VarDumper::dumpAsString($sms->errors), 'CaseController:view:Sms:save');
                }
                //VarDumper::dump($previewEmailForm->attributes, 10, true);              exit;
            }
        }


        $comForm = new CaseCommunicationForm();
        $comForm->c_preview_email = 0;
        $comForm->c_preview_sms = 0;
        $comForm->c_voice_status = 0;


        if ($comForm->load(Yii::$app->request->post())) {

            $comForm->c_case_id = $model->cs_id;

            if ($comForm->validate()) {

                $project = $model->project;

                if ($comForm->c_type_id == CommunicationForm::TYPE_EMAIL) {


                    //VarDumper::dump($comForm->quoteList, 10, true); exit;

                    $comForm->c_preview_email = 1;

                    $mailFrom = Yii::$app->user->identity->email;

                    /** @var CommunicationService $communication */
                    $communication = Yii::$app->communication;
                    $data['origin'] = '';


                    //$mailPreview = $communication->mailPreview(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $data, 'ru-RU');
                    //$mailTypes = $communication->mailTypes(7);

                    $content_data['email_body_html'] = $comForm->c_email_message;
                    //$content_data['email_body_text'] = '2';
                    $content_data['email_subject'] = $comForm->c_email_subject;

                    $content_data['email_reply_to'] = $mailFrom;
                    //$content_data['email_cc'] = 'chalpet-cc@gmail.com';
                    //$content_data['email_bcc'] = 'chalpet-bcc@gmail.com';


                    $upp = null;
                    if ($model->cs_project_id) {
                        $upp = UserProjectParams::find()->where(['upp_project_id' => $model->cs_project_id, 'upp_user_id' => Yii::$app->user->id])->one();
                        if ($upp) {
                            $mailFrom = $upp->upp_email;
                        }
                    }


                    $projectContactInfo = [];

                    if ($project && $project->contact_info) {
                        $projectContactInfo = @json_decode($project->contact_info, true);
                    }

                    $previewEmailForm->e_quote_list = @json_encode([]);


                    $language = $comForm->c_language_id ?: 'en-US';

                    $previewEmailForm->e_case_id = $model->cs_id;
                    $previewEmailForm->e_email_tpl_id = $comForm->c_email_tpl_id;
                    $previewEmailForm->e_language_id = $comForm->c_language_id;

                    if ($comForm->c_email_tpl_id > 0) {

                        $previewEmailForm->e_email_tpl_id = $comForm->c_email_tpl_id;

                        $tpl = EmailTemplateType::findOne($comForm->c_email_tpl_id);
                        //$mailSend = $communication->mailSend(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $content_data, $data, 'ru-RU', 10);


                        //VarDumper::dump($content_data, 10 , true); exit;
                        $content_data = []; //$lead->getEmailData2($comForm->quoteList, $projectContactInfo);
                        $content_data['content'] = $comForm->c_email_message;
                        $content_data['subject'] = $comForm->c_email_subject;

                        $previewEmailForm->e_email_subject = $comForm->c_email_subject;
                        $previewEmailForm->e_content_data = $content_data;

                        //echo json_encode($content_data); exit;

                        //echo (Html::encode(json_encode($content_data)));
                        //VarDumper::dump($content_data, 10 , true); exit;

                        $mailPreview = $communication->mailPreview($model->cs_project_id, ($tpl ? $tpl->etp_key : ''), $mailFrom, $comForm->c_email_to, $content_data, $language);


                        if ($mailPreview && isset($mailPreview['data'])) {
                            if (isset($mailPreview['error']) && $mailPreview['error']) {

                                $errorJson = @json_decode($mailPreview['error'], true);
                                $comForm->addError('c_email_preview', 'Communication Server response: ' . ($errorJson['message'] ?? $mailPreview['error']));
                                Yii::error($mailPreview['error'], 'CaseController:view:mailPreview');
                                $comForm->c_preview_email = 0;
                            } else {

                                $previewEmailForm->e_email_message = $mailPreview['data']['email_body_html'];
                                if (isset($mailPreview['data']['email_subject']) && $mailPreview['data']['email_subject']) {
                                    $previewEmailForm->e_email_subject = $mailPreview['data']['email_subject'];
                                }
                                $previewEmailForm->e_email_from = $mailFrom; //$mailPreview['data']['email_from'];
                                $previewEmailForm->e_email_to = $comForm->c_email_to; //$mailPreview['data']['email_to'];
                                $previewEmailForm->e_email_from_name = Yii::$app->user->identity->username;
                                $previewEmailForm->e_email_to_name = $model->client ? $model->client->full_name : '';
                                $previewEmailForm->e_quote_list = @json_encode($comForm->quoteList);
                            }
                        }

                        //VarDumper::dump($mailPreview, 10, true);// exit;
                    } else {
                        $previewEmailForm->e_email_message = $comForm->c_email_message;
                        $previewEmailForm->e_email_subject = $comForm->c_email_subject;
                        $previewEmailForm->e_email_from = $mailFrom;
                        $previewEmailForm->e_email_to = $comForm->c_email_to;
                        $previewEmailForm->e_email_from_name = Yii::$app->user->identity->username;
                        $previewEmailForm->e_email_to_name = $model->client ? $model->client->full_name : '';
                    }

                }


                if ($comForm->c_type_id == CommunicationForm::TYPE_SMS) {

                    $comForm->c_preview_sms = 1;

                    /** @var CommunicationService $communication */
                    $communication = Yii::$app->communication;

                    //$data['origin'] = 'ORIGIN';
                    //$data['destination'] = 'DESTINATION';


                    $content_data['message'] = $comForm->c_sms_message;
                    $content_data['project_id'] = $model->cs_project_id;
                    $phoneFrom = '';

                    if ($model->cs_project_id) {
                        $upp = UserProjectParams::find()->where(['upp_project_id' => $model->cs_project_id, 'upp_user_id' => Yii::$app->user->id])->one();
                        if ($upp) {
                            $phoneFrom = $upp->upp_tw_phone_number;
                        }
                    }

                    $projectContactInfo = [];

                    if ($project && $project->contact_info) {
                        $projectContactInfo = @json_decode($project->contact_info, true);
                    }

                    $previewSmsForm->s_quote_list = @json_encode([]);

                    if (!$phoneFrom) {
                        $comForm->c_preview_sms = 0;
                        $comForm->addError('c_sms_preview', 'Config Error: Not found phone number for Project Id: ' . $model->cs_project_id . ', agent: "' . Yii::$app->user->identity->username . '"');

                    } else {


                        $previewSmsForm->s_phone_to = $comForm->c_phone_number;
                        $previewSmsForm->s_phone_from = $phoneFrom;

                        if ($comForm->c_language_id) {
                            $previewSmsForm->s_language_id = $comForm->c_language_id; //$language;
                        }


                        if ($comForm->c_sms_tpl_id > 0) {

                            $previewSmsForm->s_sms_tpl_id = $comForm->c_sms_tpl_id;

                            $content_data = []; //$lead->getEmailData2($comForm->quoteList, $projectContactInfo);
                            $content_data['content'] = $comForm->c_sms_message;

                            //VarDumper::dump($content_data, 10, true); exit;

                            $language = $comForm->c_language_id ?: 'en-US';

                            $tpl = SmsTemplateType::findOne($comForm->c_sms_tpl_id);
                            //$mailSend = $communication->mailSend(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $content_data, $data, 'ru-RU', 10);

                            $smsPreview = $communication->smsPreview($model->cs_project_id, ($tpl ? $tpl->stp_key : ''), $phoneFrom, $comForm->c_phone_number, $content_data, $language);


                            if ($smsPreview && isset($smsPreview['data'])) {
                                if (isset($smsPreview['error']) && $smsPreview['error']) {

                                    $errorJson = @json_decode($smsPreview['error'], true);
                                    $comForm->addError('c_email_preview', 'Communication Server response: ' . ($errorJson['message'] ?? $smsPreview['error']));
                                    Yii::error($communication->url . "\r\n " . $smsPreview['error'], 'CaseController:view:smsPreview');
                                    $comForm->c_preview_sms = 0;
                                } else {
                                    //$previewSmsForm->s_phone_from = $smsPreview['data']['phone_from'];
                                    $previewSmsForm->s_sms_message = $smsPreview['data']['sms_text'];
                                    $previewSmsForm->s_quote_list = @json_encode($comForm->quoteList);
                                }
                            }


                            //VarDumper::dump($mailPreview, 10, true);// exit;
                        } else {
                            $previewSmsForm->s_sms_message = $comForm->c_sms_message;

                        }
                    }

                }

                if ($comForm->c_type_id == CommunicationForm::TYPE_VOICE) {

                    //$comForm->c_voice_status = 0;
                    /** @var CommunicationService $communication */
                    $communication = Yii::$app->communication;

                    $upp = null;
                    if ($model->cs_project_id) {
                        $upp = UserProjectParams::find()->where(['upp_project_id' => $model->cs_project_id, 'upp_user_id' => Yii::$app->user->id])->one();
                    }


                    /** @var Employee $userModel */
                    $userModel = Yii::$app->user->identity;


                    if ($upp && $userModel) {

                        if (!$upp->upp_tw_phone_number) {
                            $comForm->addError('c_sms_preview', 'Config Error: Not found TW phone number for Project Id: ' . $model->cs_project_id . ', agent: "' . Yii::$app->user->identity->username . '"');
                        } elseif (!$userModel->userProfile->up_sip) {
                            $comForm->addError('c_sms_preview', 'Config Error: Not found TW SIP account for Project Id: ' . $model->cs_project_id . ', agent: "' . Yii::$app->user->identity->username . '"');
                        } else {


                            /*if($comForm->c_voice_status == 1) {
                                $comForm->c_voice_sid = 'test';
                            }*/

                            if ($comForm->c_voice_status == 2) {

                                if ($comForm->c_voice_sid) {

                                    $response = $communication->updateCall($comForm->c_voice_sid, ['status' => 'completed']);

                                    Yii::info('sid: ' . $comForm->c_voice_sid . " Logs: \r\n" . VarDumper::dumpAsString($response, 10), 'info/CaseController:updateCall');


                                    if ($response && isset($response['data']['call'])) {
                                        $dataCall = $response['data']['call'];

                                        /*if(isset($dataCall['sid'])) {
                                            $comForm->c_voice_sid = $dataCall['sid'];
                                        }*/

                                    } else {
                                        $comForm->c_voice_status = 5; // Error

                                        if (isset($response['error']) && $response['error']) {
                                            $error = $response['error'];
                                        } else {
                                            $error = VarDumper::dumpAsString($response, 10);
                                        }

                                        $comForm->addError('c_sms_preview', 'Error call: ' . $error);
                                    }
                                } else {
                                    $comForm->addError('c_sms_preview', 'Error: Not found Call SID');
                                }
                            }

                            if ($comForm->c_voice_status == 1) {

                                $response = $communication->callToPhone($model->cs_project_id, 'sip:' . $userModel->userProfile->up_sip, $upp->upp_tw_phone_number, $comForm->c_phone_number, Yii::$app->user->identity->username);

                                Yii::info('ProjectId: ' . $model->cs_project_id . ', sip:' . $userModel->userProfile->up_sip . ', phoneFrom:' . $upp->upp_tw_phone_number . ', phoneTo:' . $comForm->c_phone_number . " Logs: \r\n" . VarDumper::dumpAsString($response, 10), 'info/CaseController:callToPhone');


                                if ($response && isset($response['data']['call'])) {


                                    $dataCall = $response['data']['call'];


                                    $call = new Call();

                                    $call->c_com_call_id = isset($response['data']['com_call_id']) ? (int)$response['data']['com_call_id'] : null;

                                    $call->c_call_type_id = 1;
                                    $call->c_call_sid = $dataCall['sid'];
                                    $call->c_account_sid = $dataCall['account_sid'];

                                    $call->c_to = $comForm->c_phone_number; //$dataCall['to'];
                                    $call->c_from = $upp->upp_tw_phone_number; //$dataCall['from'];
                                    $call->c_sip = $userModel->userProfile->up_sip;
                                    $call->c_caller_name = $dataCall['from'];
                                    $call->c_call_status = $dataCall['status'];
                                    $call->c_api_version = $dataCall['api_version'];
                                    $call->c_direction = $dataCall['direction'];
                                    $call->c_uri = $dataCall['uri'];
                                    $call->c_case_id = $model->cs_id;
                                    $call->c_project_id = $model->cs_project_id;

                                    $call->c_created_dt = date('Y-m-d H:i:s');
                                    $call->c_created_user_id = Yii::$app->user->id;

                                    if (!$call->save()) {
                                        Yii::error(VarDumper::dumpAsString($call->errors, 10), '');
                                        $comForm->addError('c_sms_preview', 'Error call: ' . VarDumper::dumpAsString($call->errors, 10));
                                    } else {
                                        $comForm->c_call_id = $call->c_id;
                                    }

                                    if (isset($dataCall['sid'])) {
                                        $comForm->c_voice_sid = $dataCall['sid'];
                                    }

//
//                                    $response['call']['sid'] = $call->sid;
//                                    $response['call']['to'] = $call->to;
//                                    $response['call']['from'] = $call->from;
//                                    $response['call']['status'] = $call->status;
//                                    $response['call']['price'] = $call->price;
//                                    $response['call']['account_sid'] = $call->accountSid;
//                                    $response['call']['api_version'] = $call->apiVersion;
//                                    $response['call']['annotation'] = $call->annotation;
//                                    $response['call']['uri'] = $call->uri;
//                                    $response['call']['direction'] = $call->direction;
//                                    $response['call']['phone_number_sid'] = $call->phoneNumberSid;
//                                    $response['call']['caller_name'] = $call->callerName;
//                                    $response['call']['start_time'] = $call->startTime;
//                                    $response['call']['date_created'] = $call->dateCreated;
//                                    $response['call']['date_updated'] = $call->dateUpdated;


//                                "response": {
//                                    "url": "https://communication.api.travelinsides.com/v1/twilio/voice-request?callerId=sip%3Aalex.connor%40kivork.sip.us1.twilio.com&number=%2B37369594567",
//                                    "statusCallback": "https://communication.api.travelinsides.com/v1/twilio/voice-status-callback",
//                                    "statusCallbackMethod": "POST",
//                                    "statusCallbackEvent": [
//                                                                "initiated",
//                                                                "ringing",
//                                                                "answered",
//                                                                "completed"
//                                                            ],
//                                    "call": {
//                                        "sid": "CAc447aee392051e4733fa59ade185db67",
//                                        "to": "sip:alex.connor@kivork.sip.us1.twilio.com",
//                                        "from": "BotDialer",
//                                        "status": "queued",
//                                        "price": null,
//                                        "account_sid": "AC10f3c74efba7b492cbd7dca86077736c",
//                                        "api_version": "2010-04-01",
//                                        "annotation": null,
//                                        "uri": "/2010-04-01/Accounts/AC10f3c74efba7b492cbd7dca86077736c/Calls/CAc447aee392051e4733fa59ade185db67.json",
//                                        "direction": "outbound-api",
//                                        "phone_number_sid": null
//                                    }
//                                },
                                } else {
                                    $comForm->c_voice_status = 5; // Error

                                    if (isset($response['error']) && $response['error']) {
                                        $error = $response['error'];
                                    } else {
                                        $error = VarDumper::dumpAsString($response, 10);
                                    }

                                    $comForm->addError('c_sms_preview', 'Error call: ' . $error);
                                }

                            }

                            //$comForm->c_voice_status = 1;
                            //$comForm->addError('c_sms_preview', 'Ok: Not found TW SIP account for Project Id: ' . $lead->project_id . ', agent: "' . Yii::$app->user->identity->username . '"');

                            /*$previewSmsForm->s_phone_to = $comForm->c_phone_number;
                            $previewSmsForm->s_phone_from = $phoneFrom;

                            if($comForm->c_language_id) {
                                $previewSmsForm->s_language_id =  $comForm->c_language_id; //$language;
                            }


                            if ($comForm->c_sms_tpl_id > 0) {

                                $previewSmsForm->s_sms_tpl_id = $comForm->c_sms_tpl_id;

                                $content_data = $lead->getEmailData2($comForm->quoteList);

                                $language = $comForm->c_language_id ?: 'en-US';

                                $tpl = SmsTemplateType::findOne($comForm->c_sms_tpl_id);
                                //$mailSend = $communication->mailSend(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $content_data, $data, 'ru-RU', 10);

                                $smsPreview = $communication->smsPreview($lead->project_id, ($tpl ? $tpl->stp_key : ''), $phoneFrom, $comForm->c_phone_number, $content_data, $language);


                                if ($smsPreview && isset($smsPreview['data'])) {
                                    if (isset($smsPreview['error']) && $smsPreview['error']) {

                                        $errorJson = @json_decode($smsPreview['error'], true);
                                        $comForm->addError('c_email_preview', 'Communication Server response: ' . ($errorJson['message'] ?? $smsPreview['error']));
                                        Yii::error($communication->url ."\r\n ".$smsPreview['error'], 'LeadController:view:smsPreview');
                                        $comForm->c_preview_sms = 0;
                                    } else {
                                        //$previewSmsForm->s_phone_from = $smsPreview['data']['phone_from'];
                                        $previewSmsForm->s_sms_message = $smsPreview['data']['sms_text'];
                                    }
                                }


                                //VarDumper::dump($mailPreview, 10, true);// exit;
                            } else {
                                $previewSmsForm->s_sms_message = $comForm->c_sms_message;

                            }*/
                        }
                    } else {
                        $comForm->addError('c_sms_preview', 'Config Error: Not found User Params for Project Id: ' . $model->cs_project_id . ', agent: "' . Yii::$app->user->identity->username . '"');
                    }

                }

            }
            //return $this->redirect(['view', 'id' => $model->al_id]);
        } else {
            $comForm->c_type_id = ''; //CommunicationForm::TYPE_VOICE;
        }

        if ($previewEmailForm->is_send || $previewSmsForm->is_send) {
            $comForm->c_preview_email = 0;
            $comForm->c_preview_sms = 0;
        }


        $query1 = (new \yii\db\Query())
            ->select(['e_id AS id', new Expression('"email" AS type'), 'e_case_id AS case_id', 'e_created_dt AS created_dt'])
            ->from('email')
            ->where(['e_case_id' => $model->cs_id]);

        $query2 = (new \yii\db\Query())
            ->select(['s_id AS id', new Expression('"sms" AS type'), 's_case_id AS case_id', 's_created_dt AS created_dt'])
            ->from('sms')
            ->where(['s_case_id' => $model->cs_id]);


        $query3 = (new \yii\db\Query())
            ->select(['c_id AS id', new Expression('"voice" AS type'), 'c_case_id AS case_id', 'c_created_dt AS created_dt'])
            ->from('call')
            ->where(['c_case_id' => $model->cs_id]);


        $unionQuery = (new \yii\db\Query())
            ->from(['union_table' => $query1->union($query2)->union($query3)])
            ->orderBy(['created_dt' => SORT_ASC]);

        //echo $query1->count(); exit;

        $dataProviderCommunication = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => [
                'pageSize' => 10,
                //'page' => 0
            ],
        ]);


        if (!Yii::$app->request->isAjax || !Yii::$app->request->get('page')) {
            $pageCount = ceil($dataProviderCommunication->totalCount / $dataProviderCommunication->pagination->pageSize) - 1;
            if ($pageCount < 0) {
                $pageCount = 0;
            }
            $dataProviderCommunication->pagination->page = $pageCount;
        }


        $enableCommunication = true;
        $isAdmin = true;

        return $this->render('view', [
            'model' => $model,
            'previewEmailForm' => $previewEmailForm,
            'previewSmsForm' => $previewSmsForm,
            'comForm' => $comForm,
            'enableCommunication' => $enableCommunication,
            'dataProviderCommunication' => $dataProviderCommunication,
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * Creates a new Cases model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Cases();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cs_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Cases model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->cs_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Cases model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Cases model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cases the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cases::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
