<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\components\CommunicationService;
use common\models\Call;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Department;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\GlobalLog;
use common\models\Lead;
use common\models\LeadCallExpert;
use common\models\LeadChecklist;
use common\models\LeadFlow;
//use common\models\LeadLog;
use common\models\LeadTask;
use common\models\local\LeadAdditionalInformation;
use common\models\Note;
use common\models\ProjectEmailTemplate;
use common\models\search\LeadCallExpertSearch;
use common\models\search\LeadChecklistSearch;
use modules\offer\src\entities\offer\search\OfferSearch;
use modules\offer\src\entities\offerSendLog\CreateDto;
use modules\offer\src\entities\offerSendLog\OfferSendLogType;
use modules\offer\src\services\OfferSendLogService;
use modules\order\src\entities\order\search\OrderCrudSearch;
use common\models\Sms;
use common\models\SmsTemplateType;
use common\models\UserProjectParams;
use frontend\models\CommunicationForm;
use frontend\models\LeadForm;
use frontend\models\LeadPreviewEmailForm;
use frontend\models\LeadPreviewSmsForm;
use frontend\models\SendEmailForm;
use modules\order\src\entities\order\search\OrderSearch;
use PHPUnit\Framework\Warning;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use sales\forms\CompositeFormHelper;
use sales\forms\lead\CloneReasonForm;
use sales\forms\lead\ItineraryEditForm;
use sales\forms\lead\LeadCreateForm;
use sales\forms\leadflow\TakeOverReasonForm;
use sales\logger\db\GlobalLogInterface;
use sales\logger\db\LogDTO;
use sales\model\lead\useCases\lead\create\LeadManageForm;
use sales\model\lead\useCases\lead\import\LeadImportForm;
use sales\model\lead\useCases\lead\import\LeadImportParseService;
use sales\model\lead\useCases\lead\import\LeadImportService;
use sales\model\lead\useCases\lead\import\LeadImportUploadForm;
use sales\repositories\cases\CasesRepository;
use sales\repositories\lead\LeadRepository;
use sales\repositories\NotFoundException;
use sales\services\lead\LeadAssignService;
use sales\services\lead\LeadCloneService;
use sales\services\lead\LeadManageService;
use Yii;
use yii\caching\DbDependency;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Cookie;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use common\models\Quote;
use common\models\Employee;
use common\models\search\LeadSearch;
use frontend\models\ProfitSplitForm;
use common\models\QuotePrice;
use frontend\models\TipsSplitForm;
use common\models\local\LeadLogMessage;


/**
 * Class LeadController
 * @property LeadManageService $leadManageService
 * @property LeadAssignService $leadAssignService
 * @property LeadRepository $leadRepository
  * @property LeadCloneService $leadCloneService
 * @property CasesRepository $casesRepository
 * @property LeadImportParseService $leadImportParseService
 * @property LeadImportService $leadImportService
 */
class LeadController extends FController
{
    private $leadManageService;
    private $leadAssignService;
    private $leadRepository;
    private $leadCloneService;
    private $casesRepository;
    private $leadImportParseService;
    private $leadImportService;

    public function __construct(
        $id,
        $module,
        LeadManageService $leadManageService,
        LeadAssignService $leadAssignService,
        LeadRepository $leadRepository,
        LeadCloneService $leadCloneService,
        CasesRepository $casesRepository,
        LeadImportParseService $leadImportParseService,
        LeadImportService $leadImportService,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->leadManageService = $leadManageService;
        $this->leadAssignService = $leadAssignService;
        $this->leadRepository = $leadRepository;
        $this->leadCloneService = $leadCloneService;
        $this->casesRepository = $casesRepository;
        $this->leadImportParseService = $leadImportParseService;
        $this->leadImportService = $leadImportService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'view',
                    'take',
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * {@inheritdoc}
     */
//
//    public function beforeAction($action)
//    {
//        if (parent::beforeAction($action)) {
//            if (in_array($action->id, ['create'])) {
//                //Yii::$app->setLayoutPath('@frontend/views/layouts');
//                //$this->layout = 'sale';
//                $this->layout = '@app/themes/gentelella_v2/views/layouts/main_lead';
//            }
//            return true;
//        }
//
//        return parent::beforeAction($action);
//    }


    /**
     * @param string $gid
     * @return array|string|Response
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
        $lead = Lead::find()->where(['gid' => $gid])->limit(1)->one();

        if (!$lead) {
            throw new NotFoundHttpException('Not found lead ID: ' . $gid);
        }

        if (!Auth::can('lead/view', ['lead' => $lead])) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        $user = Auth::user();

        $itineraryForm = new ItineraryEditForm($lead);

        $is_admin = $user->isAdmin();
        $isQA = $user->isQa();
        $is_supervision = $user->isSupervision();
        $is_agent = $user->isAgent();


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
                            // todo delete
//                            $leadLog = new LeadLog((new LeadLogMessage()));
//                            $leadLog->logMessage->oldParams = ['selling' => $sellingOld];
//                            $leadLog->logMessage->newParams = ['selling' => $priceData['total']['selling']];
//                            $leadLog->logMessage->title = 'Update';
//                            $leadLog->logMessage->model = sprintf('%s (%s)', $quote->formName(), $quote->uid);
//                            $leadLog->addLog([
//                                'lead_id' => $lead->id,
//                            ]);

                            (\Yii::createObject(GlobalLogInterface::class))->log(
                                new LogDTO(
                                    get_class($quote),
                                    $quote->id,
                                    \Yii::$app->id,
                                    $user->id,
                                    Json::encode(['selling' => $sellingOld]),
                                    Json::encode(['selling' => $priceData['total']['selling']]),
                                    null,
                                    GlobalLog::ACTION_TYPE_UPDATE
                                )
                            );

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
                $mail->body_html = $previewEmailForm->e_email_message;
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


                    //$mailPreview = $communication->mailPreview(7, 'cl_offer', 'test@gmail.com', 'test2@gmail.com', $data, 'ru-RU');
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
                        //$mailSend = $communication->mailSend(7, 'cl_offer', 'test@gmail.com', 'test2@gmail.com', $content_data, $data, 'ru-RU', 10);


                        //VarDumper::dump($content_data, 10 , true); exit;

                        if ($comForm->offerList) {
                            $content_data = $lead->getOfferEmailData($comForm->offerList, $projectContactInfo);
                        } else {
                            $content_data = $lead->getEmailData2($comForm->quoteList, $projectContactInfo);
                        }


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

                                if ($comForm->offerList) {
                                    $service = Yii::createObject(OfferSendLogService::class);
                                    foreach ($comForm->offerList as $offerId) {
                                        $service->log(new CreateDto($offerId, OfferSendLogType::EMAIL, $user->id, $comForm->c_email_to));
                                    }
                                }

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

                                    if ($comForm->offerList) {
                                        $service = Yii::createObject(OfferSendLogService::class);
                                        foreach ($comForm->offerList as $offerId) {
                                            $service->log(new CreateDto($offerId, OfferSendLogType::SMS, $user->id, $comForm->c_phone_number));
                                        }
                                    }

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

                                    $call->c_to = $comForm->c_phone_number; //$dataCall['to'];
                                    $call->c_from = $upp->upp_tw_phone_number; //$dataCall['from'];
                                    $call->c_caller_name = $dataCall['from'];
                                    $call->c_call_status = $dataCall['status'];
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


//        $query3 = (new \yii\db\Query())
//            ->select(['c_id AS id', new Expression('"voice" AS type'), 'c_lead_id AS lead_id', 'c_created_dt AS created_dt'])
//            ->from('call')
//            ->where(['c_lead_id' => $lead->id, 'c_parent_id' => null]);

        $query3 = (new \yii\db\Query())
            ->select(['id' => new Expression('if (c_parent_id IS NULL, c_id, c_parent_id)')])
            ->addSelect(['type' => new Expression('"voice"')])
            ->addSelect(['lead_id' => 'c_lead_id', 'created_dt' => 'MAX(c_created_dt)'])
            ->from('call')
            ->where(['c_lead_id' => $lead->id])
//            ->addGroupBy(['id', 'c_lead_id', 'c_created_dt']);
            ->addGroupBy(['id']);

//        VarDumper::dump($query3->createCommand()->getRawSql());die;

        $unionQuery = (new \yii\db\Query())
            ->from(['union_table' => $query1->union($query2)->union($query3)])
            ->orderBy(['created_dt' => SORT_ASC]);

        //echo $query3->createCommand()->getRawSql(); exit;

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


//        $enableCommunication = false;
//
//        if (!$leadForm->getLead()->isNewRecord) {
//
//            //$leadForm->mode === $leadForm::VIEW_MODE
//
//            if ($is_admin || $isQA) {
//                $enableCommunication = true;
//            } elseif ($is_supervision) {
//                if ($leadFormEmployee_id = $leadForm->getLead()->employee_id) {
//                    $enableCommunication = Employee::isSupervisionAgent($leadFormEmployee_id);
//                }
//                if (!$leadForm->getLead()->hasOwner()) {
//                    $enableCommunication = true;
//                }
//            } elseif ($is_agent) {
//                if ($leadForm->getLead()->employee_id == Yii::$app->user->id) {
//                    $enableCommunication = true;
//                }
//            }
//
//        }

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


        $searchModelOffer = new OfferSearch();
        $params = Yii::$app->request->queryParams;
        $params['OfferSearch']['of_lead_id'] = $lead->id;
        $dataProviderOffers = $searchModelOffer->searchByLead($params, $user);

        $dataProviderOrders = (new OrderSearch())->searchByLead($lead->id);

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
            $modelNote->employee_id = Yii::$app->user->id;
            $modelNote->lead_id = $lead->id;
            $modelNote->created = date('Y-m-d H:i:s');
            if (!$modelNote->save()) {
                Yii::error('Lead id: '.$lead->id . ', ' . VarDumper::dumpAsString($modelNote->errors), 'Lead:view:Note:save');
            } else {
                $modelNote->message = '';
            }
        }

        $dataProviderNotes = new ActiveDataProvider([
            'query' => Note::find()->where(['lead_id' => $lead->id])->orderBy(['id' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 10,
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
//            'enableCommunication' => $enableCommunication,
            'dataProviderCallExpert' => $dataProviderCallExpert,
            'modelLeadCallExpert' => $modelLeadCallExpert,
            'dataProviderChecklist' => $dataProviderChecklist,
            'modelLeadChecklist' => $modelLeadChecklist,
            'itineraryForm' => $itineraryForm,
            'dataProviderNotes' => $dataProviderNotes,
            'modelNote' => $modelNote,

            'dataProviderOffers'    => $dataProviderOffers,
            'dataProviderOrders'    => $dataProviderOrders,
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
                    $lead->setAdditionalInformationFormFirstElementPnr($attr['pnr']);
                    $quote = $lead->getAppliedAlternativeQuotes();
                    if ($quote !== null) {
                        $quote->record_locator = $lead->getAdditionalInformationFormFirstElement()->pnr;
                        $quote->save();
                    }
                    $lead->save();
                    $data = [
                        'FlightRequest' => [
                            'id' => $lead->bo_flight_id,
                            'sub_sources_id' => $lead->source_id,
                            'pnr' => $lead->getAdditionalInformationFormFirstElement()->pnr
                        ]
                    ];
                    $result = BackOffice::sendRequest('lead/add-pnr', 'POST', json_encode($data));
                    if ($result['status'] != 'Success') {
                        $quote->record_locator = null;
                        $lead->setAdditionalInformationFormFirstElementPnr(null);
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



    public function actionSetRating($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null && $lead->isProcessing() && Yii::$app->request->isPost) {
            $rating = (int)Yii::$app->request->post('rating', 0);
            try {
                $lead->changeRating($rating);
                $this->leadRepository->save($lead);
                return true;
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                return false;
            }
        }
        return false;
    }

    /**
     * @param string $gid
     * @return string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionTake(string $gid)
    {
        $lead = $this->findLeadByGid($gid);

        if (!Auth::can('lead/view', ['lead' => $lead])) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        if (Yii::$app->request->isAjax && Yii::$app->request->get('over')) {
            if ($lead->isAvailableToTakeOver()) {
                $reasonForm = new TakeOverReasonForm($lead);
                return $this->renderAjax('/lead-change-state/reason_take_over', [
                    'reasonForm' => $reasonForm,
                ]);
            }
            Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to "Take Over" now!');
            return $this->redirect(['lead/view', 'gid' => $lead->gid]);
        }

        try {
            /** @var Employee $user */
            $user = Yii::$app->user->identity;
            $this->leadAssignService->take($lead, $user, Yii::$app->user->id, 'Take');
            Yii::$app->getSession()->setFlash('success', 'Lead taken!');
        } catch (\DomainException $e) {
            // Yii::info($e, 'info\Lead:Take');
            Yii::$app->getSession()->setFlash('warning', $e->getMessage());
        } catch (\Throwable $e) {
            Yii::$app->errorHandler->logException($e);
            throw $e;
        }

        return $this->redirect(['lead/view', 'gid' => $lead->gid]);


//        $lead = $this->findLeadByGid($gid);
//
//        if ($lead->isCompleted()) {
//            Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to "Take" now!');
//            return $this->redirect(Yii::$app->request->referrer ?: ['/']);
//        }
//
//        if (Yii::$app->request->isAjax && Yii::$app->request->get('over')) {
//            if ($lead->isAvailableToTakeOver()) {
//                $reason = new Reason();
//                $reason->queue = 'processing-over';
//                return $this->renderAjax('partial/_reason', [
//                    'reason' => $reason,
//                    'lead' => $lead
//                ]);
//            }
//            Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to "Take Over" now!');
//            return $this->redirect(['lead/view', 'gid' => $lead->gid]);
//        }
//
//        if (!$lead->isAvailableToTake()) {
//            Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to "Take" now!');
//            return $this->redirect(Yii::$app->request->referrer ?: ['/']);
//        }
//
//        /** @var Employee $user */
//        $user = Yii::$app->user->identity;
//
//        if ($user->isAgent()) {
//            $isAgent = true;
//        } else {
//            $isAgent = false;
//        }
//
//        /*if($user->canRole('supervision')) {
//            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
//        }*/
//
//
////        $allowLead = Lead::find()->where([
////            'gid' => $gid
////        ])->andWhere([
////            'IN', 'status', [Lead::STATUS_BOOKED, Lead::STATUS_SOLD]
////        ])->one();
////        if ($allowLead !== null) {
////            Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to "Take" now!');
////            return $this->redirect(Yii::$app->request->referrer);
////        }
//
//
//        /*$inProcessing = Lead::find()
//            ->where([
//                'employee_id' => $user->getId(),
//                'status' => Lead::STATUS_PROCESSING
//            ])->one();
//        if ($inProcessing !== null) {
//            $inProcessing->status = Lead::STATUS_ON_HOLD;
//            $inProcessing->save();
//            $inProcessing = null;
//        }*/
//
////        $model = Lead::find()
////            ->where(['gid' => $gid])
////            ->andWhere(['IN', 'status', [
////                Lead::STATUS_PENDING,
////                Lead::STATUS_FOLLOW_UP,
////                Lead::STATUS_SNOOZE
////            ]])->one();
//
//
////
////        if ($model === null) {
////
////            if (Yii::$app->request->get('over', 0)) {
////                $lead = Lead::findOne(['gid' => $gid]);
////                if ($lead !== null) {
////                    $reason = new Reason();
////                    $reason->queue = 'processing-over';
////                    return $this->renderAjax('partial/_reason', [
////                        'reason' => $reason,
////                        'lead' => $lead
////                    ]);
////                }
////                return null;
////
////            } else {
////                $model = Lead::findOne([
////                    'gid' => $gid,
////                    'employee_id' => $user->getId()
////                ]);
////                if ($model === null) {
////                    Yii::$app->getSession()->setFlash('warning', 'Lead is unavailable to access now!');
////                    return $this->redirect(Yii::$app->request->referrer);
////                }
////            }
////        }
//
//
//
////        if (!$lead->permissionsView()) {
////            throw new UnauthorizedHttpException('Not permissions view lead GID: ' . $gid);
////        }
//
//
//        if ($lead->status == Lead::STATUS_PENDING && $isAgent) {
//            $isAccessNewLead = $user->accessTakeNewLead();
//            if (!$isAccessNewLead) {
//                throw new ForbiddenHttpException('Access is denied (limit) - "Take lead"');
//            }
//
//            $isAccessNewLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes();
//            if (!$isAccessNewLeadByFrequency['access']) {
//                throw new ForbiddenHttpException('Access is denied (frequency) - "Take lead"');
//            }
//        }
//
//        if ($lead->status == Lead::STATUS_FOLLOW_UP) {
//            $checkProccessingByAgent = LeadFlow::findOne([
//                'lead_id' => $lead->id,
//                'status' => $lead::STATUS_PROCESSING,
//                'employee_id' => $user->getId()
//            ]);
//            if ($checkProccessingByAgent === null) {
//                $lead->called_expert = false;
//            }
//        }
//
//
//        $lead->employee_id = $user->getId();
//
//        /* if ($model->status != Lead::STATUS_ON_HOLD && $model->status != Lead::STATUS_SNOOZE && !$model->l_answered) {
//            LeadTask::createTaskList($model->id, $model->employee_id, 1, '', Task::CAT_NOT_ANSWERED_PROCESS);
//            LeadTask::createTaskList($model->id, $model->employee_id, 2, '', Task::CAT_NOT_ANSWERED_PROCESS);
//            LeadTask::createTaskList($model->id, $model->employee_id, 3, '', Task::CAT_NOT_ANSWERED_PROCESS);
//        }
//
//        if($model->l_answered && $model->status == Lead::STATUS_SNOOZE) {
//            LeadTask::createTaskList($model->id, $model->employee_id, 1, '', Task::CAT_ANSWERED_PROCESS);
//            LeadTask::createTaskList($model->id, $model->employee_id, 2, '', Task::CAT_ANSWERED_PROCESS);
//            LeadTask::createTaskList($model->id, $model->employee_id, 3, '', Task::CAT_ANSWERED_PROCESS);
//        } */
//
//        $lead->status = Lead::STATUS_PROCESSING;
//        $lead->save();
//
//
//        //$taskList = ['call1', 'call2', 'voice-mail', 'email'];
//
//        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }

    /**
     * @param string $gid
     * @return Response
     * @throws \Throwable
     */
    public function actionAutoTake(string $gid)
    {

        /** @var Employee $user */
        $user = Yii::$app->user->identity;


        if ($user->isAgent()) {
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

        if ($lead->isPending()) {

            try {
                $this->leadAssignService->take($lead, $user, $user->id, 'Auto Dial');
            } catch (\DomainException $e) {
                Yii::info($e, 'info\Lead:AutoTake');
                Yii::$app->getSession()->setFlash('warning', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::$app->errorHandler->logException($e);
                throw $e;
            }

        } else {
            Yii::$app->session->setFlash('warning', 'Error: Lead not in status Pending (' . $lead->id . ')');
            Yii::warning('Error: Lead not in status Pending - user: ' . $user->username . ' (' . $user->id . '), lead: ' . $lead->id, 'ControllerLead:actionAutoTake');
            return $this->redirect(Yii::$app->request->referrer);
        }

        //$taskList = ['call1', 'call2', 'voice-mail', 'email'];

        return $this->redirect(['lead/view', 'gid' => $lead->gid]);
    }

    /**
     * @return string
     */
    public function actionProcessing(): string
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($user->isAgent()) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        $dataProvider = $searchModel->searchProcessing($params, $user);

        return $this->render('processing', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }


    /**
     * @return string
     */
    public function actionFollowUp(): string
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($user->isAgent()) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        $dataProvider = $searchModel->searchFollowUp($params, $user);

        return $this->render('follow-up', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }


    /**
     * @return string
     */
    public function actionBonus(): string
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($user->isAgent()) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        $dataProvider = $searchModel->searchBonus($params, $user);

        return $this->render('bonus', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }


    /**
     * @return string
     */
    public function actionPending(): string
    {
        $searchModel = new LeadSearch();

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $dataProvider = $searchModel->searchPending(Yii::$app->request->queryParams, $user);

        return $this->render('pending', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string
     */
    public function actionNew(): string
    {
        $searchModel = new LeadSearch();

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $dataProvider = $searchModel->searchNew(Yii::$app->request->queryParams, $user);

        return $this->render('new', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInbox(): string
    {

        $params = Yii::$app->request->queryParams;

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($user->isAgent()) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        $checkShiftTime = true;

        if ($isAgent) {
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

        $searchModel = new LeadSearch();
        //$dataProvider = $searchModel->searchInbox($params, $user);

        $user_id = \Yii::$app->user->id;
        $cache = \Yii::$app->cache;

        $sql = \common\models\Lead::find()->select('COUNT(*)')->where(['status' => Lead::STATUS_PENDING])->createCommand()->rawSql;

        $duration = null;
        $dependency = new DbDependency();
        $dependency->sql = $sql;

        //$key = 'queue_inbox_' . $user_id;

        //$cache->delete($key);

        //$result = $cache->get($key);
//        if ($result === false) {
            $result['isAccessNewLead'] = $user->accessTakeNewLead();
            $result['taskSummary'] = $user->getCurrentShiftTaskInfoSummary();
            $result['dataProvider'] = $searchModel->searchInbox($params, $user);

//            $cache->set($key, $result, $duration, $dependency);

            //echo 123; exit;
//        } else {
            //echo 'cache'; exit;
//        }

        $isAccessNewLead = $result['isAccessNewLead']; //$user->accessTakeNewLead();
        $taskSummary = $result['taskSummary']; //$user->getCurrentShiftTaskInfoSummary();
        $dataProvider = $result['dataProvider'];


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
            'newLeadsCount' => $user->getCountNewLeadCurrentShift(),
            'taskSummary' => $taskSummary
        ]);
    }

    /**
     * @return string
     */
    public function actionSold(): string
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        if ($user->isAgent()) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        $dataProvider = $searchModel->searchSold($params, $user);

        $tmpl = $user->isQa() ? 'sold_qa' : 'sold';

        return $this->render($tmpl, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionTrash(): string
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();

        $params = array_merge($params, $params2);

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $dataProvider = $searchModel->searchTrash($params, $user);

        return $this->render('trash', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user' => $user
        ]);
    }

    /**
     * @return string
     */
    public function actionDuplicate(): string
    {
        $searchModel = new LeadSearch();

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $dataProvider = $searchModel->searchDuplicate(Yii::$app->request->queryParams, $user);

        return $this->render('duplicate', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string
     */
    public function actionBooked(): string
    {
        $searchModel = new LeadSearch();

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($user->isAgent()) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        $dataProvider = $searchModel->searchBooked(Yii::$app->request->queryParams, $user);

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

        $lead_id = (int)Yii::$app->request->get('id');
        $action = Yii::$app->request->get('act');

        $lead = $this->findLeadById($lead_id);

        if ($action === 'answer') {
            $lead->changeAnswered();
            $this->leadRepository->save($lead);

//            $lead->l_answered = $lead->l_answered ? 0 : 1;
//            if ($lead->update()) {
//                /* if($lead->l_answered) {
//                    LeadTask::deleteAll('lt_lead_id = :lead_id AND lt_date >= :date AND lt_completed_dt IS NULL',
//                        [':lead_id' => $lead->id, ':date' => date('Y-m-d') ]);
//
//                    LeadTask::createTaskList($lead->id, $lead->employee_id, 1, '', Task::CAT_ANSWERED_PROCESS);
//                    LeadTask::createTaskList($lead->id, $lead->employee_id, 2, '', Task::CAT_ANSWERED_PROCESS);
//                    LeadTask::createTaskList($lead->id, $lead->employee_id, 3, '', Task::CAT_ANSWERED_PROCESS);
//
//                } else {
//                    LeadTask::deleteAll('lt_lead_id = :lead_id AND lt_date >= :date AND lt_completed_dt IS NULL',
//                        [':lead_id' => $lead->id, ':date' => date('Y-m-d') ]);
//
//                    LeadTask::createTaskList($lead->id, $lead->employee_id, 1, '', Task::CAT_NOT_ANSWERED_PROCESS);
//                } */
//            }
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
        $form->assignDep(Department::DEPARTMENT_SALES);
        if ($form->load($data['post']) && $form->validate()) {
            try {
                $lead = $this->leadManageService->createManuallyByDefault($form, Yii::$app->user->id, Yii::$app->user->id, LeadFlow::DESCRIPTION_MANUAL_CREATE);
                Yii::$app->session->setFlash('success', 'Lead save');
                return $this->redirect(['/lead/view', 'gid' => $lead->gid]);
            } catch (\Throwable $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['/lead/create']);
            }
        }
        return $this->render('create', ['leadForm' => $form]);
    }

	/**
	 * @throws NotFoundHttpException
	 */
	public function actionCreate2()
	{
		if (Yii::$app->request->isAjax || Yii::$app->request->isPjax) {
			$data = CompositeFormHelper::prepareDataForMultiInput(
				Yii::$app->request->post(),
				'LeadManageForm',
				[]
			);
			$form = new LeadManageForm(0);
			$form->assignDep(Department::DEPARTMENT_SALES);
			if (Yii::$app->request->isPjax && $form->load($data['post']) && $form->validate()) {
				try {
					$leadManageService = Yii::createObject(\sales\model\lead\useCases\lead\create\LeadManageService::class);
					$lead = $leadManageService->createManuallyByDefault($form, Yii::$app->user->id, Yii::$app->user->id, LeadFlow::DESCRIPTION_MANUAL_CREATE);
					Yii::$app->session->setFlash('success', 'Lead save');
					return $this->redirect(['/lead/view', 'gid' => $lead->gid]);
				} catch (\Throwable $e) {
					Yii::$app->errorHandler->logException($e);
					Yii::$app->session->setFlash('error', $e->getMessage());
				}
			}
			return $this->renderAjax('partial/_lead_create', ['leadForm' => $form]);
		}
		throw new NotFoundHttpException('Page not exist');
	}

	/**
     * @return string|Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionCreateCase()
    {
        $case = $this->findCase((string)Yii::$app->request->get('case_gid', 'null'));
        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'LeadCreateForm',
            ['emails' => 'EmailCreateForm', 'phones' => 'PhoneCreateForm', 'segments' => 'SegmentCreateForm']
        );
        $form = new LeadCreateForm(count($data['post']['EmailCreateForm']), count($data['post']['PhoneCreateForm']), count($data['post']['SegmentCreateForm']));
        $form->assignCase($case->cs_gid);
        $form->assignDep(Department::DEPARTMENT_EXCHANGE);
        if ($form->load($data['post']) && $form->validate()) {
            try {
                $lead = $this->leadManageService->createManuallyFromCase($form, Yii::$app->user->id, Yii::$app->user->id, 'Manual create form Case');
                Yii::$app->session->setFlash('success', 'Lead save');
                return $this->redirect(['/lead/view', 'gid' => $lead->gid]);
            } catch (\Throwable $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['/lead/create-case', 'case_gid' => $case->cs_gid]);
            }
        }
        return $this->render('create', ['leadForm' => $form]);
    }

    /**
     * @param $caseGid
     * @return Cases
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    private function findCase($caseGid): Cases
    {
        try {
            $case = $this->casesRepository->findFreeByGid((string)$caseGid);
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException('Case is not found');
        } catch (\DomainException $e) {
            throw new BadRequestHttpException('Case is already assigned to Lead');
        }
        return $case;
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
        $form->assignCase((string)Yii::$app->request->get('case_gid'));
        $form->assignDep((int)Yii::$app->request->get('depId'));
        return CompositeFormHelper::ajaxValidate($form, $data['keys']);
    }

//    public function actionCreate()
//    {
//        $this->view->title = sprintf('Create Lead');
//
//        $leadForm = new LeadForm(null);
//
//        if (Yii::$app->request->isAjax) {
//            Yii::$app->response->format = Response::FORMAT_JSON;
//            $data = [
//                'load' => false,
//                'errors' => []
//            ];
//            if ($leadForm->loadModels(Yii::$app->request->post())) {
//                $data['load'] = true;
//                $data['errors'] = ActiveForm::validate($leadForm);
//            }
//
//            $errors = [];
//            $leadForm->getLead()->employee_id = \Yii::$app->user->identity->getId();
//            $leadForm->getLead()->status = Lead::STATUS_PROCESSING;
//            if (empty($data['errors']) && $data['load'] && $leadForm->save($errors)) {
//                $model = $leadForm->getLead();
//                /* LeadTask::createTaskList($model->id, $model->employee_id, 1, '', Task::CAT_NOT_ANSWERED_PROCESS);
//                LeadTask::createTaskList($model->id, $model->employee_id, 2, '', Task::CAT_NOT_ANSWERED_PROCESS);
//                LeadTask::createTaskList($model->id, $model->employee_id, 3, '', Task::CAT_NOT_ANSWERED_PROCESS); */
//
//                return $this->redirect(['lead/view', 'gid' => $leadForm->getLead()->gid]);
//            }
//
//            if (!empty($errors)) {
//                $data['errors'] = $errors;
//            }
//
//            return $data;
//        }
//
//        return $this->render('view_last', [
//            'leadForm' => $leadForm,
//            'enableCommunication' => false
//        ]);
//    }


    public function actionClone($id)
    {
        $id = (int)$id;
        $lead = $this->findLeadById($id);

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($lead->isSold() && !$user->isAdmin()) {
            throw new ForbiddenHttpException('Access denied! Lead is sold.');
        }

        $form = new CloneReasonForm($lead);

        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $clone = $this->leadCloneService->cloneLead($lead, Yii::$app->user->id, Yii::$app->user->id, $form->description);
                Yii::$app->session->setFlash('success', 'Success');
                return $this->redirect(['lead/view', 'gid' => $clone->gid]);
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['lead/view', 'gid' => $form->leadGid]);
            } catch (\Throwable $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', 'Error');
                return $this->redirect(['lead/view', 'gid' => $form->leadGid]);
            }
        }
        return $this->renderAjax('partial/_clone', [
            'reasonForm' => $form
        ]);
    }

//    public function actionClone($id)
//    {
//        $errors = [];
//        $lead = Lead::findOne(['id' => $id]);
//        if ($lead !== null) {
//            $newLead = new Lead();
//            $newLead->attributes = $lead->attributes;
//            if (Yii::$app->request->isAjax) {
//                return $this->renderAjax('partial/_clone', [
//                    'lead' => $newLead,
//                    'errors' => $errors,
//                ]);
//            } elseif (Yii::$app->request->isPost) {
//                $data = Yii::$app->request->post();
//
//                if ($data['Lead']['description'] != 0) {
//                    if (isset(Lead::CLONE_REASONS[$data['Lead']['description']])) {
//                        $newLead->description = Lead::CLONE_REASONS[$data['Lead']['description']];
//                    }
//                } else {
//                    if (isset($data['other'])) {
//                        $newLead->description = trim($data['other']);
//                    }
//                }
//                $newLead->status = Lead::STATUS_PROCESSING;
//                $newLead->clone_id = $id;
//                $newLead->employee_id = Yii::$app->user->id;
//                $newLead->notes_for_experts = null;
//                $newLead->rating = 0;
//                $newLead->additional_information = null;
//                $newLead->l_answered = 0;
//                $newLead->snooze_for = null;
//                $newLead->called_expert = false;
//                $newLead->created = null;
//                $newLead->updated = null;
//                $newLead->tips = 0;
//                $newLead->gid = null;
//
//                if (!$newLead->save()) {
//                    $errors = array_merge($errors, $newLead->getErrors());
//                }
//
//                if (empty($errors)) {
//                    $flightSegments = LeadFlightSegment::findAll(['lead_id' => $id]);
//                    foreach ($flightSegments as $segment) {
//                        $flightSegment = new LeadFlightSegment();
//                        $flightSegment->attributes = $segment->attributes;
//                        $flightSegment->lead_id = $newLead->id;
//                        if (!$flightSegment->save()) {
//                            $errors = array_merge($errors, $flightSegment->getErrors());
//                        }
//                    }
//                }
//
//                if (!empty($errors)) {
//                    return $this->renderAjax('partial/_clone', [
//                        'lead' => $newLead,
//                        'errors' => $errors,
//                    ]);
//                } else {
//                    Lead::sendClonedEmail($newLead);
//                    return $this->redirect(['lead/view', 'gid' => $newLead->gid]);
//                }
//            }
//
//        }
//        return null;
//    }


    public function actionSplitProfit($id)
    {
        $errors = [];
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $totalProfit = $lead->getFinalProfit() ?: $lead->getBookedQuote()->getEstimationProfit();
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

	/**
	 * @throws BadRequestHttpException
	 */
	public function actionCheckPercentageOfSplitValidation()
	{
		if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
			Yii::$app->response->format = Response::FORMAT_JSON;
			$data = Yii::$app->request->post();
			$lead = Lead::findOne(['id' => $data['leadId'] ?? null]);

			if ($lead) {
				$splitForm = new ProfitSplitForm($lead);
				$splitForm->setScenario(ProfitSplitForm::SCENARIO_CHECK_PERCENTAGE);

				$load = $splitForm->loadModels($data);
				return ActiveForm::validate($splitForm)['profitsplitform-warnings'][0] ?? null;
			}
		}

		throw new BadRequestHttpException();
	}

    public function actionSplitTips($id)
    {
        $errors = [];
        $lead = Lead::findOne(['id' => $id]);
        if ($lead !== null) {
            $totalTips = $lead->getTotalTips();
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

    public function actionImport()
    {
        $form = new LeadImportUploadForm();
        $logResult = [];

        if (Yii::$app->request->isPost) {
            $form->file = UploadedFile::getInstance($form, 'file');
            if ($form->validate()) {
                try {
                    $content = file_get_contents($form->file->tempName);
                    $rows = explode("\r\n", $content);
                    $parse = $this->leadImportParseService->parsing($rows);
                    $log = $this->leadImportService->import($parse->getForms(), Yii::$app->user->id);
                    foreach ($parse->getErrors() as $key => $error) {
                        $logResult[] = 'Row: ' . $key . '. Error. Message: ' . $error . '.';
                    }
                    foreach ($log->getMessages() as $message) {
                        if ($message->isValid()) {
                            $logResult[] = 'Row: ' . $message->getRow() . '. Lead created. Lead Id: ' . $message->getLeadId();
                        } else {
                            $logResult[] = 'Row: ' . $message->getRow() . '. Error. Message: ' . $message->getMessage();
                        }
                    }
                } catch (\Throwable $e) {
                    $logResult[] = $e->getMessage();
                }
            } else {
                $logResult[] = VarDumper::dumpAsString($form->getErrors());
            }
            $form->file = null;
        }

        return $this->render('import', ['model' => $form, 'log' => $logResult]);
    }

    /**
     * @param $id
     * @return Lead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findLeadById($id): Lead
    {
        if ($model = Lead::findOne($id)) {
            return $model;
        }
        throw new NotFoundHttpException('Not found lead ID:' . $id);
    }

    /**
     * @param string $gid
     * @return Lead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findLeadByGid($gid): Lead
    {
        if ($model = Lead::findOne(['gid' => $gid])) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
