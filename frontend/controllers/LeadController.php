<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\components\CommunicationService;
use common\models\Call;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Lead;
use common\models\LeadCallExpert;
use common\models\LeadChecklist;
use common\models\LeadFlow;
use common\models\LeadLog;
use common\models\LeadTask;
use common\models\local\LeadAdditionalInformation;
use common\models\Note;
use common\models\ProjectEmailTemplate;
use common\models\Reason;
use common\models\search\LeadCallExpertSearch;
use common\models\search\LeadChecklistSearch;
use common\models\Sms;
use common\models\SmsTemplateType;
use common\models\UserProjectParams;
use frontend\models\CommunicationForm;
use frontend\models\LeadForm;
use frontend\models\LeadPreviewEmailForm;
use frontend\models\LeadPreviewSmsForm;
use frontend\models\SendEmailForm;
use sales\forms\CompositeFormHelper;
use sales\forms\lead\ItineraryEditForm;
use sales\forms\lead\LeadCreateForm;
use sales\repositories\lead\LeadRepository;
use sales\services\lead\LeadManageService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Cookie;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\widgets\ActiveForm;
use common\models\LeadFlightSegment;
use common\models\Quote;
use common\models\Employee;
use common\models\search\LeadSearch;
use frontend\models\ProfitSplitForm;
use common\models\QuotePrice;
use frontend\models\TipsSplitForm;
use common\models\local\LeadLogMessage;
use yii2mod\rbac\filters\AccessControl;

/**
 * Site controller
 */
class LeadController extends FController
{
    private $leadManageService;

    public function __construct($id, $module, LeadManageService $leadManageService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->leadManageService = $leadManageService;
    }

    /**
     * {@inheritdoc}
     */

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (in_array($action->id, ['create', 'view'])) {
                //Yii::$app->setLayoutPath('@frontend/views/layouts');
                //$this->layout = 'sale';
                $this->layout = '@app/themes/gentelella/views/layouts/main_lead';
            }
            return true;
        }

        return parent::beforeAction($action);
    }

    /**
     * @param string $gid
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     * @throws \yii\httpclient\Exception
     */
    public function actionView(string $gid)
    {

        $gid = mb_substr($gid, 0, 32);

        //$id = (int) $id;
        //$lead = Lead::findOne($id);

        $lead = Lead::find()->where(['gid' => $gid])->limit(1)->one();

        if (!$lead) {
            throw new NotFoundHttpException('Not found lead ID: ' . $gid);
        }

        if ($lead->status == Lead::STATUS_TRASH && Yii::$app->user->identity->canRole('agent')) {
            throw new ForbiddenHttpException('Access Denied for Agent');
        }


        $itineraryForm = new ItineraryEditForm($lead);


        $user_id = Yii::$app->user->id;
        $user = Yii::$app->user->identity;

        $is_admin = $user->canRole('admin');
        $isQA = $user->canRole('qa');
        $is_supervision = $user->canRole('supervision');
        $is_agent = $user->canRole('agent');


        if (Yii::$app->request->post('hasEditable')) {

            $value = '';
            $message = '';

            // use Yii's response format to encode output as JSON
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            // read your posted model attributes
            if (Yii::$app->request->isPost && $extraMarkup = Yii::$app->request->post('extra_markup')) {
                $paxCode = key($extraMarkup);
                if ($paxCode) {
                    $quoteId = key($extraMarkup[$paxCode]);
                    if ($quoteId) {
                        $qPrices = QuotePrice::find()->where(['quote_id' => $quoteId, 'passenger_type' => $paxCode])->all();
                        if (count($qPrices)) {
                            $quote = Quote::findOne(['id' => $quoteId]);
                            $priceData = $quote->getPricesData();
                            $sellingOld = $priceData['total']['selling'];
                            foreach ($qPrices as $qPrice) {
                                $qPrice->extra_mark_up = $extraMarkup[$paxCode][$quoteId];
                                $qPrice->update();
                            }

                            $quote = Quote::findOne(['id' => $quoteId]);
                            $priceData = $quote->getPricesData();
                            //log messages
                            $leadLog = new LeadLog((new LeadLogMessage()));
                            $leadLog->logMessage->oldParams = ['selling' => $sellingOld];
                            $leadLog->logMessage->newParams = ['selling' => $priceData['total']['selling']];
                            $leadLog->logMessage->title = 'Update';
                            $leadLog->logMessage->model = sprintf('%s (%s)', $quote->formName(), $quote->uid);
                            $leadLog->addLog([
                                'lead_id' => $lead->id,
                            ]);

                            if ($lead->called_expert) {
                                $data = $quote->getQuoteInformationForExpert(true);
                                $response = BackOffice::sendRequest('lead/update-quote', 'POST', json_encode($data));
                                if ($response['status'] != 'Success' || !empty($response['errors'])) {
                                    \Yii::$app->getSession()->setFlash('warning', sprintf(
                                        'Update info quote [%s] for expert failed! %s',
                                        $quote->uid,
                                        print_r($response['errors'], true)
                                    ));
                                }
                            }

                            return ['output' => $extraMarkup[$paxCode][$quoteId]];
                        }
                        return [];
                    }
                    return [];
                }

                return [];
            } elseif (Yii::$app->request->isPost && $taskNotes = Yii::$app->request->post('task_notes')) {

                $taskId = $taskDate = $userId = $leadId = null;

                $leadId = $lead->id; //Yii::$app->request->get('lead_id');

                $taskKey = key($taskNotes);

                if ($taskKey) {
                    list($taskId, $taskDate, $userId) = explode('_', $taskKey);
                }

                $value = $taskNotes[$taskKey];


                if (!$taskId) {
                    $message = 'Not found Task ID data';
                } elseif (!$taskDate) {
                    $message = 'Not found Task Date data';
                } elseif (!$userId) {
                    $message = 'Not found Task User ID data';
                } elseif (!$leadId) {
                    $message = 'Not found Lead ID data';
                } else {

                    if ($taskDate && $taskId && $leadId && $userId) {
                        $lt = LeadTask::find()->where(['lt_lead_id' => $leadId, 'lt_date' => $taskDate, 'lt_task_id' => $taskId, 'lt_user_id' => $userId])->one();
                        if ($lt) {
                            $lt->lt_notes = $value;
                            $lt->lt_updated_dt = date('Y-m-d H:i:s');
                            $lt->update();
                        }
                    }

                }

            } elseif (Yii::$app->request->isPost && Yii::$app->request->post('notes_for_experts', null) !== null) {
                $lead->notes_for_experts = Yii::$app->request->post('notes_for_experts');
                if ($lead->save()) {
                    $value = $lead->notes_for_experts;
                } else {
                    $message = 'Not save lead';
                }
            } else {
                $message = 'Not found data';
            }


            return ['output' => nl2br(Html::encode($value)), 'message' => $message];
        }

        if (Yii::$app->request->isPjax) {
            $taskDate = Yii::$app->request->get('date');
            $taskId = Yii::$app->request->get('task_id');
            $leadId = $lead->id; //Yii::$app->request->get('lead_id');
            $userId = Yii::$app->request->get('user_id'); // Yii::$app->user->id;

            if ($taskDate && $taskId && $leadId && $userId) {
                $lt = LeadTask::find()->where(['lt_lead_id' => $leadId, 'lt_date' => $taskDate, 'lt_task_id' => $taskId, 'lt_user_id' => $userId])->one();
                if ($lt) {
                    if ($lt->lt_completed_dt) {
                        if ($is_admin) {
                            $lt->lt_completed_dt = null;
                        }
                    } else {
                        $lt->lt_completed_dt = date('Y-m-d H:i:s');
                    }
                    $lt->lt_updated_dt = date('Y-m-d H:i:s');
                    $lt->update();
                }
                $leadToUpdate = Lead::findOne($leadId);
                $leadToUpdate->updated = date('Y-m-d H:i:s');
                $leadToUpdate->update();
            }

        }



        Yii::$app->cache->delete(sprintf('quick-search-%d-%d', $lead->id, Yii::$app->user->identity->getId()));
        if (!$isQA && !$lead->permissionsView()) {
            throw new UnauthorizedHttpException('Not permissions view lead ID: ' . $lead->id);
        }
        $leadForm = new LeadForm($lead);
        if ($leadForm->getLead()->status != Lead::STATUS_PROCESSING ||
            $leadForm->getLead()->employee_id != Yii::$app->user->identity->getId()
        ) {
            $leadForm->mode = $leadForm::VIEW_MODE;
        }

        if ($itineraryForm->segments) {
            foreach ($itineraryForm->segments as $segment) {
                $this->view->title = 'Lead #' . $lead->id . ' ✈ ' . $segment->destination;
                break;
            }
        } else {
            $this->view->title = 'Lead #' . $lead->id;
        }

        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = [
                'load' => false,
                'errors' => []
            ];
            if ($leadForm->loadModels(Yii::$app->request->post())) {
                $data['load'] = true;
                $data['errors'] = ActiveForm::validate($leadForm);
            }

            $errors = [];
            if (empty($data['errors']) && $data['load'] && $leadForm->save($errors)) {

                /*if ($lead->called_expert) {
                    $lead = Lead::findOne(['id' => $lead->id]);
                    $data = $lead->getLeadInformationForExpert();
                    $result = BackOffice::sendRequest('lead/update-lead', 'POST', json_encode($data));
                    if ($result['status'] != 'Success' || !empty($result['errors'])) {
                        Yii::$app->getSession()->setFlash('warning', sprintf(
                            'Update info lead for expert failed! %s',
                            print_r($result['errors'], true)
                        ));
                    }
                }*/

                return $this->redirect(['lead/view', 'gid' => $leadForm->getLead()->gid]);
            }

            if (!empty($errors)) {
                $data['errors'] = $errors;
            }

            return $data;
        }


        $previewEmailForm = new LeadPreviewEmailForm();
        $previewEmailForm->is_send = false;


        if ($previewEmailForm->load(Yii::$app->request->post())) {
            $previewEmailForm->e_lead_id = $lead->id;
            if ($previewEmailForm->validate()) {

                $mail = new Email();
                $mail->e_project_id = $lead->project_id;
                $mail->e_lead_id = $lead->id;
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
                        Yii::error('Error: Email Message has not been sent to ' . $mail->e_email_to . "\r\n " . $mailResponse['error'], 'LeadController:view:Email:sendMail');
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
                                            Yii::error($quote->errors, 'LeadController:view:Email:Quote:save');
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
                    Yii::error(VarDumper::dumpAsString($mail->errors), 'LeadController:view:Email:save');
                }
                //VarDumper::dump($previewEmailForm->attributes, 10, true);              exit;
            }
        }


        $previewSmsForm = new LeadPreviewSmsForm();
        $previewSmsForm->is_send = false;

        if ($previewSmsForm->load(Yii::$app->request->post())) {
            $previewSmsForm->s_lead_id = $lead->id;
            if ($previewSmsForm->validate()) {

                $sms = new Sms();
                $sms->s_project_id = $lead->project_id;
                $sms->s_lead_id = $lead->id;
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
                        Yii::error('Error: SMS Message has not been sent to ' . $sms->s_phone_to . "\r\n " . $smsResponse['error'], 'LeadController:view:Sms:sendSms');
                    } else {

                        if ($quoteList = @json_decode($previewSmsForm->s_quote_list)) {
                            if (is_array($quoteList)) {
                                foreach ($quoteList as $quoteId) {
                                    $quoteId = (int)$quoteId;
                                    $quote = Quote::findOne($quoteId);
                                    if ($quote) {
                                        $quote->status = Quote::STATUS_SEND;
                                        if (!$quote->save()) {
                                            Yii::error($quote->errors, 'LeadController:view:Sms:Quote:save');
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
                    Yii::error(VarDumper::dumpAsString($sms->errors), 'LeadController:view:Sms:save');
                }
                //VarDumper::dump($previewEmailForm->attributes, 10, true);              exit;
            }
        }


        $comForm = new CommunicationForm();
        $comForm->c_preview_email = 0;
        $comForm->c_preview_sms = 0;
        $comForm->c_voice_status = 0;


        if ($comForm->load(Yii::$app->request->post())) {

            $comForm->c_lead_id = $lead->id;

            if ($comForm->validate()) {

                $project = $lead->project;

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
                    if ($lead->project_id) {
                        $upp = UserProjectParams::find()->where(['upp_project_id' => $lead->project_id, 'upp_user_id' => Yii::$app->user->id])->one();
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

                    $previewEmailForm->e_lead_id = $lead->id;
                    $previewEmailForm->e_email_tpl_id = $comForm->c_email_tpl_id;
                    $previewEmailForm->e_language_id = $comForm->c_language_id;

                    if ($comForm->c_email_tpl_id > 0) {

                        $previewEmailForm->e_email_tpl_id = $comForm->c_email_tpl_id;

                        $tpl = EmailTemplateType::findOne($comForm->c_email_tpl_id);
                        //$mailSend = $communication->mailSend(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $content_data, $data, 'ru-RU', 10);


                        //VarDumper::dump($content_data, 10 , true); exit;
                        $content_data = $lead->getEmailData2($comForm->quoteList, $projectContactInfo);
                        $content_data['content'] = $comForm->c_email_message;
                        $content_data['subject'] = $comForm->c_email_subject;

                        $previewEmailForm->e_email_subject = $comForm->c_email_subject;
                        $previewEmailForm->e_content_data = $content_data;

                        //echo json_encode($content_data); exit;

                        //echo (Html::encode(json_encode($content_data)));
                        //VarDumper::dump($content_data, 10 , true); exit;

                        $mailPreview = $communication->mailPreview($lead->project_id, ($tpl ? $tpl->etp_key : ''), $mailFrom, $comForm->c_email_to, $content_data, $language);


                        if ($mailPreview && isset($mailPreview['data'])) {
                            if (isset($mailPreview['error']) && $mailPreview['error']) {

                                $errorJson = @json_decode($mailPreview['error'], true);
                                $comForm->addError('c_email_preview', 'Communication Server response: ' . ($errorJson['message'] ?? $mailPreview['error']));
                                Yii::error($mailPreview['error'], 'LeadController:view:mailPreview');
                                $comForm->c_preview_email = 0;
                            } else {

                                $previewEmailForm->e_email_message = $mailPreview['data']['email_body_html'];
                                if (isset($mailPreview['data']['email_subject']) && $mailPreview['data']['email_subject']) {
                                    $previewEmailForm->e_email_subject = $mailPreview['data']['email_subject'];
                                }
                                $previewEmailForm->e_email_from = $mailFrom; //$mailPreview['data']['email_from'];
                                $previewEmailForm->e_email_to = $comForm->c_email_to; //$mailPreview['data']['email_to'];
                                $previewEmailForm->e_email_from_name = Yii::$app->user->identity->username;
                                $previewEmailForm->e_email_to_name = $lead->client ? $lead->client->full_name : '';
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
                        $previewEmailForm->e_email_to_name = $lead->client ? $lead->client->full_name : '';
                    }

                }


                if ($comForm->c_type_id == CommunicationForm::TYPE_SMS) {

                    $comForm->c_preview_sms = 1;

                    /** @var CommunicationService $communication */
                    $communication = Yii::$app->communication;

                    //$data['origin'] = 'ORIGIN';
                    //$data['destination'] = 'DESTINATION';


                    $content_data['message'] = $comForm->c_sms_message;
                    $content_data['project_id'] = $lead->project_id;
                    $phoneFrom = '';

                    if ($lead->project_id) {
                        $upp = UserProjectParams::find()->where(['upp_project_id' => $lead->project_id, 'upp_user_id' => Yii::$app->user->id])->one();
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
                        $comForm->addError('c_sms_preview', 'Config Error: Not found phone number for Project Id: ' . $lead->project_id . ', agent: "' . Yii::$app->user->identity->username . '"');

                    } else {


                        $previewSmsForm->s_phone_to = $comForm->c_phone_number;
                        $previewSmsForm->s_phone_from = $phoneFrom;

                        if ($comForm->c_language_id) {
                            $previewSmsForm->s_language_id = $comForm->c_language_id; //$language;
                        }


                        if ($comForm->c_sms_tpl_id > 0) {

                            $previewSmsForm->s_sms_tpl_id = $comForm->c_sms_tpl_id;

                            $content_data = $lead->getEmailData2($comForm->quoteList, $projectContactInfo);
                            $content_data['content'] = $comForm->c_sms_message;

                            //VarDumper::dump($content_data, 10, true); exit;

                            $language = $comForm->c_language_id ?: 'en-US';

                            $tpl = SmsTemplateType::findOne($comForm->c_sms_tpl_id);
                            //$mailSend = $communication->mailSend(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $content_data, $data, 'ru-RU', 10);

                            $smsPreview = $communication->smsPreview($lead->project_id, ($tpl ? $tpl->stp_key : ''), $phoneFrom, $comForm->c_phone_number, $content_data, $language);


                            if ($smsPreview && isset($smsPreview['data'])) {
                                if (isset($smsPreview['error']) && $smsPreview['error']) {

                                    $errorJson = @json_decode($smsPreview['error'], true);
                                    $comForm->addError('c_email_preview', 'Communication Server response: ' . ($errorJson['message'] ?? $smsPreview['error']));
                                    Yii::error($communication->url . "\r\n " . $smsPreview['error'], 'LeadController:view:smsPreview');
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
                    if ($lead->project_id) {
                        $upp = UserProjectParams::find()->where(['upp_project_id' => $lead->project_id, 'upp_user_id' => Yii::$app->user->id])->one();
                    }


                    /** @var Employee $userModel */
                    $userModel = Yii::$app->user->identity;


                    if ($upp && $userModel) {

                        if (!$upp->upp_tw_phone_number) {
                            $comForm->addError('c_sms_preview', 'Config Error: Not found TW phone number for Project Id: ' . $lead->project_id . ', agent: "' . Yii::$app->user->identity->username . '"');
                        } elseif (!$userModel->userProfile->up_sip) {
                            $comForm->addError('c_sms_preview', 'Config Error: Not found TW SIP account for Project Id: ' . $lead->project_id . ', agent: "' . Yii::$app->user->identity->username . '"');
                        } else {


                            /*if($comForm->c_voice_status == 1) {
                                $comForm->c_voice_sid = 'test';
                            }*/

                            if ($comForm->c_voice_status == 2) {

                                if ($comForm->c_voice_sid) {

                                    $response = $communication->updateCall($comForm->c_voice_sid, ['status' => 'completed']);

                                    Yii::info('sid: ' . $comForm->c_voice_sid . " Logs: \r\n" . VarDumper::dumpAsString($response, 10), 'info/LeadController:updateCall');


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

                                $response = $communication->callToPhone($lead->project_id, 'sip:' . $userModel->userProfile->up_sip, $upp->upp_tw_phone_number, $comForm->c_phone_number, Yii::$app->user->identity->username);

                                Yii::info('ProjectId: ' . $lead->project_id . ', sip:' . $userModel->userProfile->up_sip . ', phoneFrom:' . $upp->upp_tw_phone_number . ', phoneTo:' . $comForm->c_phone_number . " Logs: \r\n" . VarDumper::dumpAsString($response, 10), 'info/LeadController:callToPhone');


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
                                    $call->c_lead_id = $lead->id;
                                    $call->c_project_id = $lead->project_id;

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
                        $comForm->addError('c_sms_preview', 'Config Error: Not found User Params for Project Id: ' . $lead->project_id . ', agent: "' . Yii::$app->user->identity->username . '"');
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


        $quotesProvider = $lead->getQuotesProvider([]);


        $query1 = (new \yii\db\Query())
            ->select(['e_id AS id', new Expression('"email" AS type'), 'e_lead_id AS lead_id', 'e_created_dt AS created_dt'])
            ->from('email')
            ->where(['e_lead_id' => $lead->id]);

        $query2 = (new \yii\db\Query())
            ->select(['s_id AS id', new Expression('"sms" AS type'), 's_lead_id AS lead_id', 's_created_dt AS created_dt'])
            ->from('sms')
            ->where(['s_lead_id' => $lead->id]);


        $query3 = (new \yii\db\Query())
            ->select(['c_id AS id', new Expression('"voice" AS type'), 'c_lead_id AS lead_id', 'c_created_dt AS created_dt'])
            ->from('call')
            ->where(['c_lead_id' => $lead->id]);


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


        $enableCommunication = false;

        if (!$leadForm->getLead()->isNewRecord) {

            //$leadForm->mode === $leadForm::VIEW_MODE

            if ($is_admin || $isQA) {
                $enableCommunication = true;
            } elseif ($is_supervision) {
                if ($leadFormEmployee_id = $leadForm->getLead()->employee_id) {
                    $enableCommunication = Employee::isSupervisionAgent($leadFormEmployee_id);
                }
            } elseif ($is_agent) {
                if ($leadForm->getLead()->employee_id == Yii::$app->user->id) {
                    $enableCommunication = true;
                }
            }

        }

        //$dataProviderCommunication

        $modelLeadCallExpert = new LeadCallExpert();


        if ($modelLeadCallExpert->load(Yii::$app->request->post())) {

            $modelLeadCallExpert->lce_agent_user_id = Yii::$app->user->id;
            $modelLeadCallExpert->lce_lead_id = $lead->id;
            $modelLeadCallExpert->lce_status_id = LeadCallExpert::STATUS_PENDING;
            $modelLeadCallExpert->lce_request_dt = date('Y-m-d H:i:s');

            if ($modelLeadCallExpert->save()) {
                $modelLeadCallExpert->lce_request_text = '';
                //Yii::info(VarDumper::dumpAsString($modelLeadCallExpert->attributes), 'info\LeadController:view:LeadCallExpert');
            }
            //$modelLeadCallExpert =
            //return $this->redirect(['view', 'id' => $model->lce_id]);
        }


        $searchModelCallExpert = new LeadCallExpertSearch();
        $params = Yii::$app->request->queryParams;
        $params['LeadCallExpertSearch']['lce_lead_id'] = $lead->id;
        $dataProviderCallExpert = $searchModelCallExpert->searchByLead($params);



        $modelLeadChecklist = new LeadChecklist();

        if ($modelLeadChecklist->load(Yii::$app->request->post())) {

            $modelLeadChecklist->lc_user_id = Yii::$app->user->id;
            $modelLeadChecklist->lc_lead_id = $lead->id;
            $modelLeadChecklist->lc_created_dt = date('Y-m-d H:i:s');

            if($modelLeadChecklist->save()) {
                $modelLeadChecklist->lc_notes = null;
            } else {
                Yii::error('Lead id: '.$lead->id . ', ' . VarDumper::dumpAsString($modelLeadCallExpert->errors), 'Lead:view:LeadChecklist:save');
            }

            //return $this->redirect(['view', 'id' => $model->lce_id]);
        }

        $searchModelLeadChecklist= new LeadChecklistSearch();
        $params = Yii::$app->request->queryParams;
        $params['LeadChecklistSearch']['lc_lead_id'] = $lead->id;
        if ($is_agent) {
            $params['LeadChecklistSearch']['lc_user_id'] = Yii::$app->user->id;
        }
        $dataProviderChecklist = $searchModelLeadChecklist->searchByLead($params);

        $modelNote = new Note();
        if ($modelNote->load(Yii::$app->request->post())) {
            $model = new Note();
            $model->employee_id = Yii::$app->user->identity->getId();
            $model->lead_id = $lead->id;
            $model->message = $modelNote->message;
            $model->created = date('Y-m-d H:i:s');
            $model->save();
        }

        $dataProviderNotes = new ActiveDataProvider([
            'query' => Note::find()/*->where(['employee_id' => Yii::$app->user->id])*/->where(['lead_id' => $lead->id])->orderBy('id ASC'),
            'pagination' => [
                'pageSize' => 2,
            ],
        ]);



        //VarDumper::dump(enableCommunication); exit;

        //$dataProviderCommunication = $lead->getQuotesProvider([]);

        $tmpl = $isQA ? 'view_qa' : 'view';

        return $this->render($tmpl, [
            'leadForm' => $leadForm,
            'previewEmailForm' => $previewEmailForm,
            'previewSmsForm' => $previewSmsForm,
            'comForm' => $comForm,
            'quotesProvider' => $quotesProvider,
            'dataProviderCommunication' => $dataProviderCommunication,
            'enableCommunication' => $enableCommunication,
            'dataProviderCallExpert' => $dataProviderCallExpert,
            'modelLeadCallExpert' => $modelLeadCallExpert,
            'dataProviderChecklist' => $dataProviderChecklist,
            'modelLeadChecklist' => $modelLeadChecklist,
            'itineraryForm' => $itineraryForm,
            'dataProviderNotes' => $dataProviderNotes,
            'modelNote' => $modelNote
        ]);

    }

    public function actionGetAirport($term)
    {
        return parent::actionGetAirport($term);
    }

    public function actionAddPnr($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);
        if ($lead !== null) {
            if (Yii::$app->request->isPost) {
                $model = new LeadAdditionalInformation();
                $attr = Yii::$app->request->post($model->formName());
                if (empty($attr['pnr'])) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $errors[Html::getInputId($model, 'pnr')] = sprintf('Cannot be blank');
                    return [
                        'errors' => $errors
                    ];
                } else {
                    $lead->additionalInformationForm[0]->pnr = $attr['pnr'];
                    $quote = $lead->getAppliedAlternativeQuotes();
                    if ($quote !== null) {
                        $quote->record_locator = $lead->additionalInformationForm[0]->pnr;
                        $quote->save();
                    }
                    $lead->save();
                    $data = [
                        'FlightRequest' => [
                            'id' => $lead->bo_flight_id,
                            'sub_sources_id' => $lead->source_id,
                            'pnr' => $lead->additionalInformationForm[0]->pnr
                        ]
                    ];
                    $result = BackOffice::sendRequest('lead/add-pnr', 'POST', json_encode($data));
                    if ($result['status'] != 'Success') {
                        $quote->record_locator = null;
                        $lead->additionalInformationForm[0]->pnr = null;
                        $quote->save();
                        $lead->save();
                        Yii::$app->getSession()->setFlash('warning', sprintf(
                            'Add PNR failed! %s',
                            print_r($result['errors'], true)
                        ));
                    }
                    return $this->redirect(['lead/view', 'gid' => $lead->gid]);
                }
            }
            return $this->renderAjax('partial/_paxInfo', [
                'lead' => $lead
            ]);
        }
        return null;
    }

    public function actionFlowTransition($leadId)
    {
        $lead = Lead::findOne(['id' => $leadId]);
        if ($lead !== null) {
            return $this->renderAjax('partial/_flowTransition', [
                'flightRequestFlow' => $lead->getFlowTransition(),
            ]);
        }
        return null;
    }

    /*public function actionCheckUpdates($leadId, $lastUpdate)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'needRefresh' => false
        ];
        $model = Lead::findOne([
            'id' => $leadId
        ]);
        if ($model !== null) {
            $query = LeadLog::find()
                ->where(['lead_id' => $leadId])
                ->andWhere('created > :lastUpdate', [':lastUpdate' => $lastUpdate]);

            $logs = $query->all();
            if (count($logs)) {
                $response['logs'] = $this->renderAjax('partial/_leadLog', [
                    'logs' => $model->leadLogs
                ]);
                $response['checkUpdatesUrl'] = Url::to([
                    'lead/check-updates',
                    'leadId' => $leadId,
                    'lastUpdate' => date('Y-m-d H:i:s'),
                ]);
                $response['content'] = $this->renderAjax('partial/_updateModal');
            } else {
                $response['logs'] = '';
                $response['checkUpdatesUrl'] = Url::to([
                    'lead/check-updates',
                    'leadId' => $leadId,
                    'lastUpdate' => $lastUpdate,
                ]);
            }
            $needRefresh = $query->andWhere('employee_id <> :employee_id OR employee_id IS NULL', [
                ':employee_id' => Yii::$app->user->identity->getId()
            ])->all();

            $response['needRefresh'] = count($needRefresh);
        }

        return $response;
    }*/

    public function actionGetBadges()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = Lead::getBadgesSingleQuery();
        return $response;
    }

    public function actionSendEmail($id)
    {
        /**
         * @var $lead Lead
         */

        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $preview = false;
            $sendEmailModel = new SendEmailForm();
            $sendEmailModel->employee = $lead->employee;
            $sendEmailModel->project = $lead->project;

            $userProjectParams = UserProjectParams::findOne([
                'upp_user_id' => $sendEmailModel->employee->id,
                'upp_project_id' => $sendEmailModel->project->id
            ]);


            if (!$userProjectParams) {
                throw new BadRequestHttpException('Not found UserProjectParams (user_id: ' . $sendEmailModel->employee->id . ', project_id: ' . $sendEmailModel->project->id . ' )');
            }

            $templates = ProjectEmailTemplate::getTypesForSellers();
            if (Yii::$app->request->isAjax) {
                $sendEmailModel->type = Yii::$app->request->get('type');
                $template = $sendEmailModel->getTemplate();
                if (Yii::$app->request->isGet) {
                    if ($template !== null) {
                        $sendEmailModel->populate($template, $lead->client, $userProjectParams);
                    }
                } else {
                    $attr = Yii::$app->request->post();
                    if (isset($attr['extra_body']) && isset($attr['subject'])) {
                        $sendEmailModel->extraBody = $attr['extra_body'];
                        $sendEmailModel->subject = $attr['subject'];
                        $preview = true;
                    }
                    if ($template !== null) {
                        $sendEmailModel->populate($template, $lead->client, $userProjectParams);
                    }
                }
                return $this->renderAjax('partial/_sendEmail', [
                    'templates' => $templates,
                    'sendEmailModel' => $sendEmailModel,
                    'lead' => $lead,
                    'preview' => $preview
                ]);
            }
            if (Yii::$app->request->isPost) {
                $attr = Yii::$app->request->post($sendEmailModel->formName());
                $sendEmailModel->attributes = $attr;
                $template = $sendEmailModel->getTemplate();
                if ($template !== null) {
                    $sendEmailModel->populate($template, $lead->client, $userProjectParams);
                }
                $isSent = $sendEmailModel->sentEmail($lead);
                if ($isSent) {
                    Yii::$app->getSession()->setFlash('success', sprintf('Sent email \'%s\' succeed.', $sendEmailModel->subject));
                } else {
                    Yii::$app->getSession()->setFlash('danger', sprintf('Sent email \'%s\' failed. Please verify your email or password from email!', $sendEmailModel->subject));
                }

                return $this->redirect(['lead/view', 'gid' => $lead->gid]);

            } else {
                return $this->renderAjax('partial/_sendEmail', [
                    'templates' => $templates,
                    'sendEmailModel' => $sendEmailModel,
                    'lead' => $lead,
                    'preview' => $preview
                ]);
            }
        }
        throw new BadRequestHttpException();
    }

    /*public function actionCallExpert($id)
    {
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null && !$lead->called_expert) {
            $data = $lead->getLeadInformationForExpert();
            $data['call_expert'] = true;
            $result = BackOffice::sendRequest('lead/update-lead', 'POST', json_encode($data));

            $lead->notes_for_experts = Yii::$app->request->post('notes');

            if ($result['status'] == 'Success' && empty($result['errors'])) {
                $lead->called_expert = true;
                Yii::$app->getSession()->setFlash('success', 'Call expert request succeeded');
            } else {
                Yii::$app->getSession()->setFlash('warning', print_r($result['errors'], true));
            }
            $lead->save();
        }
        return $this->redirect(Yii::$app->request->referrer);
    }*/

    public function actionUnprocessed($show)
    {
        if ($show) {
            Yii::$app->response->cookies->remove(Lead::getCookiesKey());
        } else {
            Yii::$app->response->cookies->add(new Cookie([
                'name' => Lead::getCookiesKey(),
                'value' => false,
                'expire' => strtotime('+1 day')
            ]));
        }
        return $this->redirect(['follow-up']);
    }

    /*public function actionAddNote()
    {
        $lead = Lead::findOne(['id' => Yii::$app->request->get('id', 0)]);

        if ($lead !== null && Yii::$app->request->isPost) {
            $model = new Note();
            $attr = Yii::$app->request->post($model->formName());
            $model->attributes = $attr;
            $model->employee_id = Yii::$app->user->identity->getId();
            $model->lead_id = $lead->id;
            $model->save();
        }
        return $this->redirect(Yii::$app->request->referrer);
    }*/

    public function actionSetRating($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null &&
            $lead->status == Lead::STATUS_PROCESSING &&
            Yii::$app->request->isPost
        ) {
            $rating = Yii::$app->request->post('rating', 0);
            $lead->rating = $rating;
            $lead->save(false);
            return true;
        }
        return false;
    }

    public function actionUnassign($id)
    {
        /**
         * @var $model Lead
         */
        $model = Lead::find()->where([
            'id' => $id
        ])->andWhere([
            'NOT IN', 'status', [Lead::STATUS_BOOKED, Lead::STATUS_SOLD]
        ])->one();

        $type = 'inbox';

        if ($model !== null) {
            $reason = new Reason();
            $attr = Yii::$app->request->post($reason->formName());
            if (empty($attr)) {
                if ($attr['queue'] == 'processing') {
                    $model->status = $model::STATUS_PROCESSING;
                    $model->snooze_for = '';
                    $model->save();

                    return $this->redirect(['lead/view', 'gid' => $model->gid]);

                } elseif ($attr['queue'] == 'reject') {
                    $model->status = $model::STATUS_REJECT;
                    $model->save();
                    return $this->redirect(['trash']);
                }
            } else {
                $reason->attributes = $attr;
                $reason->employee_id = Yii::$app->user->identity->getId();
                $reason->lead_id = $model->id;
                $reason->save();
                if ($reason->queue == 'follow-up') {
                    $model->status = $model::STATUS_FOLLOW_UP;
                    $model->employee_id = null;
                    $model->save();
                    return $this->redirect(['follow-up']);

                } elseif ($reason->queue == 'trash') {
                    $model->status = $model::STATUS_TRASH;

                    if ($reason->duplicateLeadId) {
                        $model->l_duplicate_lead_id = $reason->duplicateLeadId;
                    }

                    //$type = 'trash';
                } elseif ($reason->queue == 'snooze') {
                    $modelAttr = Yii::$app->request->post($model->formName());
                    $model->snooze_for = $modelAttr['snooze_for'];
                    $model->status = $model::STATUS_SNOOZE;
                } elseif ($reason->queue == 'return') {
                    $attrAgent = Yii::$app->request->post('agent', null);
                    if ($reason->returnToQueue == 'follow-up') {
                        $model->status = $model::STATUS_FOLLOW_UP;
                    } elseif ($attrAgent !== null) {
                        $model->employee_id = $attrAgent;
                        $model->status = $model::STATUS_PROCESSING;
                    }
                } elseif ($reason->queue == 'processing-over') {
                    $model->status = $model::STATUS_PROCESSING;
                    $lastAgent = $model->employee->username;
                    $model->employee_id = $reason->employee_id;
                    $model->save();

                    $note = new Note();
                    $note->employee_id = Yii::$app->user->identity->getId();
                    $note->lead_id = $model->id;
                    $note->message = sprintf('Take Over in PROCESSING status.<br>Reason: %s<br>Last Agent: %s',
                        $reason->reason,
                        $lastAgent
                    );
                    $note->save();

                    return $this->redirect(['lead/view', 'gid' => $model->gid]);

                } elseif ($reason->queue == 'reject') {
                    $model->status = $model::STATUS_REJECT;
                    $model->save();
                    return $this->redirect(['trash']);
                } else {
                    $model->status = $model::STATUS_PROCESSING;
                }

                $model->save();
            }
        }

        return $this->redirect(['processing']);
    }

    public function actionChangeState($id, $queue)
    {
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $activeLeads = Lead::find()
                ->where([
                    'status' => [
                        Lead::STATUS_ON_HOLD, Lead::STATUS_PROCESSING,
                        Lead::STATUS_SNOOZE, Lead::STATUS_FOLLOW_UP
                    ]
                ])->andWhere(['<>', 'id', $id]);

            $activeLeadIds = ArrayHelper::map($activeLeads->asArray()->all(), 'id', 'id');
            $activeLeadIds = $activeLeadIds ?: [];

            $reason = new Reason();
            $reason->queue = $queue;
            return $this->renderAjax('partial/_reason', [
                'reason' => $reason,
                'lead' => $lead,
                'activeLeadIds' => $activeLeadIds
            ]);
        }
        return null;
    }


    /**
     * @param string $gid
     * @return string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionTake(string $gid)
    {
        /**
         * @var $inProcessing Lead
         */

        $lead = $this->findLeadByGid($gid);

        if ($lead->isCompleted()) {
            Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to "Take" now!');
            return $this->redirect(Yii::$app->request->referrer ?: ['/']);
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->get('over')) {
            if ($lead->isAvailableToTakeOver()) {
                $reason = new Reason();
                $reason->queue = 'processing-over';
                return $this->renderAjax('partial/_reason', [
                    'reason' => $reason,
                    'lead' => $lead
                ]);
            }
            Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to "Take Over" now!');
            return $this->redirect(['lead/view', 'gid' => $lead->gid]);
        }

        if (!$lead->isAvailableToTake()) {
            Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to "Take" now!');
            return $this->redirect(Yii::$app->request->referrer ?: ['/']);
        }

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($user->canRole('agent')) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        /*if($user->canRole('supervision')) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }*/


//        $allowLead = Lead::find()->where([
//            'gid' => $gid
//        ])->andWhere([
//            'IN', 'status', [Lead::STATUS_BOOKED, Lead::STATUS_SOLD]
//        ])->one();
//        if ($allowLead !== null) {
//            Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to "Take" now!');
//            return $this->redirect(Yii::$app->request->referrer);
//        }


        /*$inProcessing = Lead::find()
            ->where([
                'employee_id' => $user->getId(),
                'status' => Lead::STATUS_PROCESSING
            ])->one();
        if ($inProcessing !== null) {
            $inProcessing->status = Lead::STATUS_ON_HOLD;
            $inProcessing->save();
            $inProcessing = null;
        }*/

//        $model = Lead::find()
//            ->where(['gid' => $gid])
//            ->andWhere(['IN', 'status', [
//                Lead::STATUS_PENDING,
//                Lead::STATUS_FOLLOW_UP,
//                Lead::STATUS_SNOOZE
//            ]])->one();


//
//        if ($model === null) {
//
//            if (Yii::$app->request->get('over', 0)) {
//                $lead = Lead::findOne(['gid' => $gid]);
//                if ($lead !== null) {
//                    $reason = new Reason();
//                    $reason->queue = 'processing-over';
//                    return $this->renderAjax('partial/_reason', [
//                        'reason' => $reason,
//                        'lead' => $lead
//                    ]);
//                }
//                return null;
//
//            } else {
//                $model = Lead::findOne([
//                    'gid' => $gid,
//                    'employee_id' => $user->getId()
//                ]);
//                if ($model === null) {
//                    Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to access now!');
//                    return $this->redirect(Yii::$app->request->referrer);
//                }
//            }
//        }



//        if (!$lead->permissionsView()) {
//            throw new UnauthorizedHttpException('Not permissions view lead GID: ' . $gid);
//        }


        if ($lead->status == Lead::STATUS_PENDING && $isAgent) {
            $isAccessNewLead = $user->accessTakeNewLead();
            if (!$isAccessNewLead) {
                throw new ForbiddenHttpException('Access is denied (limit) - "Take lead"');
            }

            $isAccessNewLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes();
            if (!$isAccessNewLeadByFrequency['access']) {
                throw new ForbiddenHttpException('Access is denied (frequency) - "Take lead"');
            }
        }

        if ($lead->status == Lead::STATUS_FOLLOW_UP) {
            $checkProccessingByAgent = LeadFlow::findOne([
                'lead_id' => $lead->id,
                'status' => $lead::STATUS_PROCESSING,
                'employee_id' => $user->getId()
            ]);
            if ($checkProccessingByAgent === null) {
                $lead->called_expert = false;
            }
        }


        $lead->employee_id = $user->getId();

        /* if ($model->status != Lead::STATUS_ON_HOLD && $model->status != Lead::STATUS_SNOOZE && !$model->l_answered) {
            LeadTask::createTaskList($model->id, $model->employee_id, 1, '', Task::CAT_NOT_ANSWERED_PROCESS);
            LeadTask::createTaskList($model->id, $model->employee_id, 2, '', Task::CAT_NOT_ANSWERED_PROCESS);
            LeadTask::createTaskList($model->id, $model->employee_id, 3, '', Task::CAT_NOT_ANSWERED_PROCESS);
        }

        if($model->l_answered && $model->status == Lead::STATUS_SNOOZE) {
            LeadTask::createTaskList($model->id, $model->employee_id, 1, '', Task::CAT_ANSWERED_PROCESS);
            LeadTask::createTaskList($model->id, $model->employee_id, 2, '', Task::CAT_ANSWERED_PROCESS);
            LeadTask::createTaskList($model->id, $model->employee_id, 3, '', Task::CAT_ANSWERED_PROCESS);
        } */

        $lead->status = Lead::STATUS_PROCESSING;
        $lead->save();


        //$taskList = ['call1', 'call2', 'voice-mail', 'email'];

        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }


    /**
     * @param string $gid
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionAutoTake(string $gid)
    {

        /** @var Employee $user */
        $user = Yii::$app->user->identity;


        if ($user->canRole('agent')) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        /*if($user->canRole('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }*/


        $lead = Lead::find()->where(['gid' => $gid])->one();

        if (!$lead) {
            Yii::$app->session->setFlash('warning', 'Not found Lead (' . $gid . ') !');
            return $this->redirect(Yii::$app->request->referrer);
        }


        Yii::info('user: ' . $user->username . ' (' . $user->id . '), lead: ' . $lead->id, 'info\ControllerLead:actionAutoTake');


        if ((int)$lead->status === Lead::STATUS_PENDING) {

            if ($isAgent) {
                $isAccessNewLead = $user->accessTakeNewLead();
                if (!$isAccessNewLead) {
                    throw new ForbiddenHttpException('ControllerLead:actionAutoTake: Access is denied (limit) - "Take lead"');
                }

                $isAccessNewLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes();
                if (!$isAccessNewLeadByFrequency['access']) {
                    throw new ForbiddenHttpException('ControllerLead:actionAutoTake: Access is denied (frequency) - "Take lead"');
                }
            }

            $lead->employee_id = $user->id;
            $lead->status = Lead::STATUS_PROCESSING;
            $lead->status_description = 'Auto Dial';
            if (!$lead->save()) {
                \Yii::error(VarDumper::dumpAsString($lead->errors, 10), 'ControllerLead:actionAutoTake:Lead:save(Auto Dial)');
            }
            /*if($lead->save()) {
                LeadTask::createTaskList($lead->id, $lead->employee_id, 1, '', Task::CAT_NOT_ANSWERED_PROCESS);
                LeadTask::createTaskList($lead->id, $lead->employee_id, 2, '', Task::CAT_NOT_ANSWERED_PROCESS);
                LeadTask::createTaskList($lead->id, $lead->employee_id, 3, '', Task::CAT_NOT_ANSWERED_PROCESS);
            } */

        } else {
            Yii::$app->session->setFlash('warning', 'Error: Lead not in status Pending (' . $lead->id . ')');
            Yii::warning('Error: Lead not in status Pending - user: ' . $user->username . ' (' . $user->id . '), lead: ' . $lead->id, 'ControllerLead:actionAutoTake');
            return $this->redirect(Yii::$app->request->referrer);
        }

        //$taskList = ['call1', 'call2', 'voice-mail', 'email'];

        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }


    public function actionProcessing()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if (Yii::$app->user->identity->canRole('agent')) {
            $params['LeadSearch']['employee_id'] = Yii::$app->user->id;
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchProcessing($params);

        return $this->render('processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }


    public function actionFollowUp()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if (Yii::$app->user->identity->canRole('agent')) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchFollowUp($params);

        return $this->render('follow-up', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }


    public function actionPending()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }
        $dataProvider = $searchModel->searchInbox($params);

        //$user = Yii::$app->user->identity;

        return $this->render('pending', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionInbox()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if (Yii::$app->user->identity->canRole('agent')) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }


        $checkShiftTime = true;

        if ($isAgent) {
            $user = Yii::$app->user->identity;
            /** @var Employee $user */
            $checkShiftTime = $user->checkShiftTime();
            $userParams = $user->userParams;

            if ($userParams) {
                if ($userParams->up_inbox_show_limit_leads > 0) {
                    $params['LeadSearch']['limit'] = $userParams->up_inbox_show_limit_leads;
                }
            } else {
                throw new NotFoundHttpException('Not set user params for agent! Please ask supervisor to set shift time and other.');
            }


            /*if($checkShiftTime = !$user->checkShiftTime()) {
                throw new ForbiddenHttpException('Access denied! Invalid Agent shift time');
            }*/
        }

        //$checkShiftTime = true;


        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchInbox($params);

        $user = Yii::$app->user->identity;

        $isAccessNewLead = $user->accessTakeNewLead();
        $accessLeadByFrequency = [];

        if ($isAccessNewLead) {
            $accessLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes();
            if (!$accessLeadByFrequency['access']) {
                $isAccessNewLead = $accessLeadByFrequency['access'];
            }
        }

        return $this->render('inbox', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'checkShiftTime' => $checkShiftTime,
            'isAgent' => $isAgent,
            'isAccessNewLead' => $isAccessNewLead,
            'accessLeadByFrequency' => $accessLeadByFrequency,
            'user' => $user,
            'newLeadsCount' => $user->getCountNewLeadCurrentShift()
        ]);
    }


    public function actionSold()
    {
        $searchModel = new LeadSearch();
        $salary = null;

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if (Yii::$app->user->identity->canRole('agent')) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        if ($isAgent) {
            $params['LeadSearch']['employee_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchSold($params);

        $tmpl = Yii::$app->user->identity->canRole('qa') ? 'sold_qa' : 'sold';

        return $this->render($tmpl, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    public function actionTrash()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if (Yii::$app->user->identity->canRole('agent')) {
            $params['LeadSearch']['employee_id'] = Yii::$app->user->id;
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');

        $dataProvider = $searchModel->searchTrash($params);

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    public function actionDuplicate()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchDuplicates($params);

        return $this->render('duplicate', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionBooked()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        if (Yii::$app->user->identity->canRole('agent')) {
            //$params['LeadSearch']['employee_id'] = Yii::$app->user->id;
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if (Yii::$app->user->identity->canRole('supervision')) {
            //$params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchBooked($params);

        return $this->render('booked', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    public function actionAddComment($type, $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = null;
        if ($type == 'email') {
            $model = ClientEmail::findOne(['id' => $id]);
        } elseif ($type == 'phone') {
            $model = ClientPhone::findOne(['id' => $id]);
        }
        if ($model !== null && Yii::$app->request->isPost) {
            /**
             * @var $model ClientEmail|ClientPhone
             */
            $attr = Yii::$app->request->post();
            $model->comments = $attr['comment'];
            $model->save();
            return [
                'error' => $model->getErrors(),
                'success' => !$model->hasErrors()
            ];
        }
        return null;
    }


    public function actionUpdate2()
    {

        //echo 123; exit;

        $lead_id = (int)Yii::$app->request->get('id');
        $action = Yii::$app->request->get('act');
        $lead = Lead::findOne(['id' => $lead_id]);
        if (!$lead) {
            throw new NotFoundHttpException('Not found lead ID: ' . $lead_id);
        }

        if ($action === 'answer') {
            $lead->l_answered = $lead->l_answered ? 0 : 1;
            if ($lead->update()) {
                /* if($lead->l_answered) {
                    LeadTask::deleteAll('lt_lead_id = :lead_id AND lt_date >= :date AND lt_completed_dt IS NULL',
                        [':lead_id' => $lead->id, ':date' => date('Y-m-d') ]);

                    LeadTask::createTaskList($lead->id, $lead->employee_id, 1, '', Task::CAT_ANSWERED_PROCESS);
                    LeadTask::createTaskList($lead->id, $lead->employee_id, 2, '', Task::CAT_ANSWERED_PROCESS);
                    LeadTask::createTaskList($lead->id, $lead->employee_id, 3, '', Task::CAT_ANSWERED_PROCESS);

                } else {
                    LeadTask::deleteAll('lt_lead_id = :lead_id AND lt_date >= :date AND lt_completed_dt IS NULL',
                        [':lead_id' => $lead->id, ':date' => date('Y-m-d') ]);

                    LeadTask::createTaskList($lead->id, $lead->employee_id, 1, '', Task::CAT_NOT_ANSWERED_PROCESS);
                } */
            }
        }

        $referrer = Yii::$app->request->referrer; //$_SERVER["HTTP_REFERER"];
        return $this->redirect($referrer);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'LeadCreateForm',
            ['emails' => 'EmailCreateForm', 'phones' => 'PhoneCreateForm', 'segments' => 'SegmentCreateForm']
        );
        $form = new LeadCreateForm(count($data['post']['EmailCreateForm']), count($data['post']['PhoneCreateForm']), count($data['post']['SegmentCreateForm']));
        if ($form->load($data['post']) && $form->validate()) {
            try {
                $lead = $this->leadManageService->create($form, Yii::$app->user->identity->getId());
                Yii::$app->session->setFlash('success', 'Lead save');
                return $this->redirect(['/lead/view', 'gid' => $lead->gid]);
            } catch (\Throwable $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('create', ['leadForm' => $form]);
    }

    /**
     * @return array
     */
    public function actionValidateLeadCreate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'LeadCreateForm',
            ['emails' => 'EmailCreateForm', 'phones' => 'PhoneCreateForm', 'segments' => 'SegmentCreateForm']
        );
        $form = new LeadCreateForm(count($data['post']['EmailCreateForm']), count($data['post']['PhoneCreateForm']), count($data['post']['SegmentCreateForm']));
        $form->load($data['post']);
        return CompositeFormHelper::ajaxValidate($form, $data['keys']);
    }

    public function actionCreate_last()
    {
        $this->view->title = sprintf('Create Lead');

        $leadForm = new LeadForm(null);

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = [
                'load' => false,
                'errors' => []
            ];
            if ($leadForm->loadModels(Yii::$app->request->post())) {
                $data['load'] = true;
                $data['errors'] = ActiveForm::validate($leadForm);
            }

            $errors = [];
            $leadForm->getLead()->employee_id = \Yii::$app->user->identity->getId();
            $leadForm->getLead()->status = Lead::STATUS_PROCESSING;
            if (empty($data['errors']) && $data['load'] && $leadForm->save($errors)) {
                $model = $leadForm->getLead();
                /* LeadTask::createTaskList($model->id, $model->employee_id, 1, '', Task::CAT_NOT_ANSWERED_PROCESS);
                LeadTask::createTaskList($model->id, $model->employee_id, 2, '', Task::CAT_NOT_ANSWERED_PROCESS);
                LeadTask::createTaskList($model->id, $model->employee_id, 3, '', Task::CAT_NOT_ANSWERED_PROCESS); */

                return $this->redirect(['lead/view', 'gid' => $leadForm->getLead()->gid]);
            }

            if (!empty($errors)) {
                $data['errors'] = $errors;
            }

            return $data;
        }

        return $this->render('view_last', [
            'leadForm' => $leadForm,
            'enableCommunication' => false
        ]);
    }

    public function actionGetUserActions($id)
    {
        $lead = Lead::findOne([
            'id' => $id
        ]);

        $activity = [];
        $quoteId = '';

        if ($lead !== null) {
            if (Yii::$app->request->isPost) {
                $quoteId = Yii::$app->request->post('discountId', $lead->discount_id);
            } else {
                $quoteId = $lead->discount_id;
            }
        }

        if (!empty($quoteId)) {

            $result = null;
            if ($lead->project) {
                $projectLink = $lead->project->link;
                $projectLink = str_replace('www.', '', $projectLink);

                $url = $projectLink . '/api/user-action-list/' . intval($quoteId);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['apiKey' => $lead->project->api_key]));
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                $result = curl_exec($ch);
            }

            $activity = json_decode($result);
        }

        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $activity;
        }
        return $this->renderAjax('partial/_requestLog', [
            'activity' => $activity,
            'discountId' => $quoteId,
            'lead' => $lead
        ]);
    }

    public function actionClone($id)
    {
        $errors = [];
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $newLead = new Lead();
            $newLead->attributes = $lead->attributes;
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('partial/_clone', [
                    'lead' => $newLead,
                    'errors' => $errors,
                ]);
            } elseif (Yii::$app->request->isPost) {
                $data = Yii::$app->request->post();

                if ($data['Lead']['description'] != 0) {
                    if (isset(Lead::CLONE_REASONS[$data['Lead']['description']])) {
                        $newLead->description = Lead::CLONE_REASONS[$data['Lead']['description']];
                    }
                } else {
                    if (isset($data['other'])) {
                        $newLead->description = trim($data['other']);
                    }
                }
                $newLead->status = Lead::STATUS_PROCESSING;
                $newLead->clone_id = $id;
                $newLead->employee_id = Yii::$app->user->id;
                $newLead->notes_for_experts = null;
                $newLead->rating = 0;
                $newLead->additional_information = null;
                $newLead->l_answered = 0;
                $newLead->l_grade = 0;
                $newLead->snooze_for = null;
                $newLead->called_expert = false;
                $newLead->created = null;
                $newLead->updated = null;
                $newLead->tips = 0;
                $newLead->gid = null;

                if (!$newLead->save()) {
                    $errors = array_merge($errors, $newLead->getErrors());
                }

                if (empty($errors)) {
                    $flightSegments = LeadFlightSegment::findAll(['lead_id' => $id]);
                    foreach ($flightSegments as $segment) {
                        $flightSegment = new LeadFlightSegment();
                        $flightSegment->attributes = $segment->attributes;
                        $flightSegment->lead_id = $newLead->id;
                        if (!$flightSegment->save()) {
                            $errors = array_merge($errors, $flightSegment->getErrors());
                        }
                    }
                }

                if (!empty($errors)) {
                    return $this->renderAjax('partial/_clone', [
                        'lead' => $newLead,
                        'errors' => $errors,
                    ]);
                } else {
                    Lead::sendClonedEmail($newLead);
                    return $this->redirect(['lead/view', 'gid' => $newLead->gid]);
                }
            }

        }
        return null;
    }

    public function actionSplitProfit($id)
    {
        $errors = [];
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $totalProfit = $lead->finalProfit ?: $lead->getBookedQuote()->getEstimationProfit();
            $splitForm = new ProfitSplitForm($lead);

            $mainAgentProfit = $totalProfit;

            if (Yii::$app->request->isPost) {
                $data = Yii::$app->request->post();

                if (!isset($data['ProfitSplit'])) {
                    $data['ProfitSplit'] = [];
                }

                $load = $splitForm->loadModels($data);
                if ($load) {
                    $errors = ActiveForm::validate($splitForm);
                }

                if (empty($errors) && $splitForm->save($errors)) {
                    return $this->redirect(['lead/view', 'gid' => $lead->gid]);
                }

                $splitProfit = $splitForm->getProfitSplit();
                if (!empty($splitProfit)) {
                    $percentSum = 0;
                    foreach ($splitProfit as $entry) {
                        if (!empty($entry->ps_percent)) {
                            $percentSum += $entry->ps_percent;
                        }
                    }
                    $mainAgentProfit -= $totalProfit * $percentSum / 100;
                }

                if (!empty($errors)) {
                    return $this->renderAjax('_split_profit', [
                        'lead' => $lead,
                        'splitForm' => $splitForm,
                        'totalProfit' => $totalProfit,
                        'mainAgentProfit' => $mainAgentProfit,
                        'errors' => $errors,
                    ]);
                }
            } elseif (Yii::$app->request->isAjax) {
                return $this->renderAjax('_split_profit', [
                    'lead' => $lead,
                    'splitForm' => $splitForm,
                    'totalProfit' => $totalProfit,
                    'mainAgentProfit' => $mainAgentProfit,
                    'errors' => $errors,
                ]);
            }

        }
        return null;
    }

    public function actionSplitTips($id)
    {
        $errors = [];
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $totalTips = $lead->totalTips;
            $splitForm = new TipsSplitForm($lead);

            $mainAgentTips = $totalTips;

            if (Yii::$app->request->isPost) {
                $data = Yii::$app->request->post();

                if (!isset($data['TipsSplit'])) {
                    $data['TipsSplit'] = [];
                }

                $load = $splitForm->loadModels($data);
                if ($load) {
                    $errors = ActiveForm::validate($splitForm);
                }

                if (empty($errors) && $splitForm->save($errors)) {
                    return $this->redirect(['lead/view', 'gid' => $lead->gid]);
                }

                $splitTips = $splitForm->getTipsSplit();
                if (!empty($splitTips)) {
                    $percentSum = 0;
                    foreach ($splitTips as $entry) {
                        if (!empty($entry->ts_percent)) {
                            $percentSum += $entry->ts_percent;
                        }
                    }
                    $mainAgentTips -= $totalTips * $percentSum / 100;
                }

                if (!empty($errors)) {
                    return $this->renderAjax('_split_tips', [
                        'lead' => $lead,
                        'splitForm' => $splitForm,
                        'totalTips' => $totalTips,
                        'mainAgentTips' => $mainAgentTips,
                        'errors' => $errors,
                    ]);
                }
            } elseif (Yii::$app->request->isAjax) {
                return $this->renderAjax('_split_tips', [
                    'lead' => $lead,
                    'splitForm' => $splitForm,
                    'totalTips' => $totalTips,
                    'mainAgentTips' => $mainAgentTips,
                    'errors' => $errors,
                ]);
            }

        }
        return null;
    }

    /**
     * @param string $gid
     * @return Lead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findLeadByGid($gid): Lead
    {
        if (($model = Lead::findOne(['gid' => $gid])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
