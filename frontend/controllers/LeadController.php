<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\components\CommunicationService;
use common\components\jobs\LeadPoorProcessingRemoverJob;
use common\models\Call;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\GlobalLog;
use common\models\Lead;
use common\models\LeadCallExpert;
use common\models\LeadChecklist;
use common\models\LeadFlow;
//use common\models\LeadLog;
use common\models\LeadQcall;
use common\models\LeadTask;
use common\models\local\LeadAdditionalInformation;
use common\models\Note;
use common\models\ProjectEmailTemplate;
use common\models\QuoteCommunication;
use common\models\search\LeadCallExpertSearch;
use common\models\search\LeadChecklistSearch;
use frontend\models\LeadUserRatingForm;
use kivork\rbacExportImport\src\formatters\FileSizeFormatter;
use modules\email\src\abac\dto\EmailPreviewDto;
use modules\email\src\abac\EmailAbacObject;
use modules\featureFlag\FFlag;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\services\url\UrlGenerator;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use modules\lead\src\abac\LeadExpertCallObject;
use modules\lead\src\abac\services\AbacLeadExpertCallService;
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
use modules\twilio\components\TwilioCommunicationService;
use PHPUnit\Framework\Warning;
use src\access\EmployeeAccess;
use src\auth\Auth;
use src\entities\cases\Cases;
use src\forms\CompositeFormHelper;
use src\forms\lead\CloneReasonForm;
use src\forms\lead\ItineraryEditForm;
use src\forms\lead\LeadCreateForm;
use src\forms\leadflow\TakeOverReasonForm;
use src\helpers\app\AppHelper;
use src\helpers\email\MaskEmailHelper;
use src\helpers\setting\SettingHelper;
use src\logger\db\GlobalLogInterface;
use src\logger\db\LogDTO;
use src\model\airportLang\helpers\AirportLangHelper;
use src\model\call\socket\CallUpdateMessage;
use src\model\call\useCase\createCall\fromLead\AbacCallFromNumberList;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\permissions\ClientChatActionPermission;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\clientChatLead\entity\ClientChatLeadRepository;
use src\model\contactPhoneData\entity\ContactPhoneData;
use src\model\contactPhoneList\service\ContactPhoneListService;
use src\model\department\department\DefaultPhoneType;
use src\model\email\useCase\send\fromLead\AbacEmailList;
use src\model\emailReviewQueue\EmailReviewQueueManageService;
use src\model\emailReviewQueue\entity\EmailReviewQueue;
use src\model\lead\useCases\lead\create\CreateLeadByChatDTO;
use src\model\lead\useCases\lead\create\LeadCreateByChatForm;
use src\model\lead\useCases\lead\create\LeadManageForm;
use src\model\lead\useCases\lead\create\LeadManageService as UseCaseLeadManageService;
use src\model\lead\useCases\lead\import\LeadImportForm;
use src\model\lead\useCases\lead\import\LeadImportParseService;
use src\model\lead\useCases\lead\import\LeadImportService;
use src\model\lead\useCases\lead\import\LeadImportUploadForm;
use src\model\lead\useCases\lead\link\LeadLinkChatForm;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadUserRating\abac\dto\LeadUserRatingAbacDto;
use src\model\leadUserRating\abac\LeadUserRatingAbacObject;
use src\model\leadUserRating\service\LeadUserRatingService;
use src\model\leadUserConversion\service\LeadUserConversionDictionary;
use src\model\leadUserConversion\service\LeadUserConversionService;
use src\model\sms\useCase\send\fromLead\AbacSmsFromNumberList;
use src\quoteCommunication\Repo;
use src\repositories\cases\CasesRepository;
use src\repositories\lead\LeadRepository;
use src\repositories\NotFoundException;
use src\repositories\quote\QuoteRepository;
use src\services\client\ClientManageService;
use src\services\email\EmailService;
use src\services\lead\LeadAssignService;
use src\services\lead\LeadCloneService;
use src\services\lead\LeadManageService;
use src\services\TransactionManager;
use Yii;
use yii\caching\DbDependency;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\validators\StringValidator;
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
 * @property QuoteRepository $quoteRepository
 * @property TransactionManager $transaction
 * @property ClientChatActionPermission $chatActionPermission
 * @property UrlGenerator $fileStorageUrlGenerator
 * @property UseCaseLeadManageService $useCaseLeadManageService
 * @property EmailReviewQueueManageService $emailReviewQueueManageService
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
    private $quoteRepository;
    private $transaction;
    private $chatActionPermission;
    private UrlGenerator $fileStorageUrlGenerator;
    private UseCaseLeadManageService $useCaseLeadManageService;
    private EmailReviewQueueManageService $emailReviewQueueManageService;

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
        QuoteRepository $quoteRepository,
        TransactionManager $transaction,
        ClientChatActionPermission $chatActionPermission,
        UrlGenerator $fileStorageUrlGenerator,
        UseCaseLeadManageService $useCaseLeadManageService,
        EmailReviewQueueManageService $emailReviewQueueManageService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->leadManageService = $leadManageService;
        $this->leadAssignService = $leadAssignService;
        $this->leadRepository = $leadRepository;
        $this->leadCloneService = $leadCloneService;
        $this->casesRepository = $casesRepository;
        $this->leadImportParseService = $leadImportParseService;
        $this->leadImportService = $leadImportService;
        $this->quoteRepository = $quoteRepository;
        $this->transaction = $transaction;
        $this->chatActionPermission = $chatActionPermission;
        $this->fileStorageUrlGenerator = $fileStorageUrlGenerator;
        $this->useCaseLeadManageService = $useCaseLeadManageService;
        $this->emailReviewQueueManageService = $emailReviewQueueManageService;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'view',
                    'take',
                    'ajax-take',
                    'create-by-chat',
                    'ajax-create-from-phone-widget',
                    'ajax-create-from-phone-widget-with-invalid-client',
                    'ajax-link-to-call',
                    'extra-queue',
                    'closed',
                    'create'
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

        /** @abac $abacDto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_ACCESS, Access to view lead  */
        if (!Yii::$app->abac->can(new LeadAbacDto($lead, Auth::id()), LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        $user = Auth::user();

        $callFromNumberList = new AbacCallFromNumberList($user, $lead);
        $smsFromNumberList = new AbacSmsFromNumberList($user, $lead);
        $emailFromList = new AbacEmailList($user, $lead);

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
        if (
            $leadForm->getLead()->status != Lead::STATUS_PROCESSING ||
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


        $previewEmailForm = new LeadPreviewEmailForm($emailFromList);
        $previewEmailForm->e_lead_id = $lead->id;
        $previewEmailForm->is_send = false;


        if ($previewEmailForm->load(Yii::$app->request->post())) {
            $previewEmailForm->e_lead_id = $lead->id;
            if ($previewEmailForm->validate()) {
                $abacDto = new EmailPreviewDto(
                    $previewEmailForm->e_email_tpl_id,
                    $previewEmailForm->isMessageEdited(),
                    $previewEmailForm->isSubjectEdited(),
                    $previewEmailForm->attachCount(),
                    $lead,
                    null
                );
                /** @abac $abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_SEND, Restrict access to send email in preview email */
                $canSend = Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_SEND);
                if ($canSend) {
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
                    $mail->e_created_dt = date('Y-m-d H:i:s');
                    $mail->e_created_user_id = Yii::$app->user->id;
                    $attachments = [];
                    /** @abac $abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_ATTACH_FILES, Restrict access to attach files in lead communication block */
                    $canAttachFiles = Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_ATTACH_FILES);
                    if ($canAttachFiles && FileStorageSettings::canEmailAttach() && $previewEmailForm->files) {
                        $attachments['files'] = $this->fileStorageUrlGenerator->generateForExternal($previewEmailForm->getFilesPath());
                    }
                    $mail->e_email_data = json_encode($attachments);
                    if ($mail->save()) {
                        $mail->e_message_id = $mail->generateMessageId();
                        $mail->update();

                        /** @abac $abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_SEND_WITHOUT_REVIEW, Restrict access to send without review email */
                        $canSendWithoutReview = Yii::$app->abac->can($abacDto, EmailAbacObject::OBJ_PREVIEW_EMAIL, EmailAbacObject::ACTION_SEND_WITHOUT_REVIEW);

                        if ($canSendWithoutReview) {
                            $previewEmailForm->is_send = true;
                            $mailResponse = $mail->sendMail($attachments);

                            if (isset($mailResponse['error']) && $mailResponse['error']) {
                                //echo $mailResponse['error']; exit; //'Error: <strong>Email Message</strong> has not been sent to <strong>'.$mail->e_email_to.'</strong>'; exit;
                                Yii::$app->session->setFlash('send-error', 'Error: <strong>Email Message</strong> has not been sent to <strong>' . MaskEmailHelper::masking($mail->e_email_to) . '</strong>');
                                Yii::error('Error: Email Message has not been sent to ' . $mail->e_email_to . "\r\n " . $mailResponse['error'], 'LeadController:view:Email:sendMail');
                            } else {
                                //echo '<strong>Email Message</strong> has been successfully sent to <strong>'.$mail->e_email_to.'</strong>'; exit;

                                if ($offerList = @json_decode($previewEmailForm->e_offer_list)) {
                                    if (is_array($offerList)) {
                                        $service = Yii::createObject(OfferSendLogService::class);
                                        foreach ($offerList as $offerId) {
                                            $service->log(new CreateDto($offerId, OfferSendLogType::EMAIL, $user->id, $previewEmailForm->e_email_to));
                                        }
                                    }
                                }

                                /*
                                 * TODO: The similar logic exist in `\frontend\controllers\EmailReviewQueueController::actionSend`. Need to shrink code duplications.
                                 */
                                /** @var string[] $quoteIds */
                                $quoteIds = Json::decode($previewEmailForm->e_quote_list);
                                /** @var Quote[] $quoteObjects */
                                $quoteObjects = Quote::find()->where(['IN', 'id', $quoteIds])->all();
                                foreach ($quoteObjects as $quoteObject) {
                                    Repo::createForEmail($mail->e_id, $quoteObject->id, $previewEmailForm->e_qc_uid);
                                    $quoteObject->setStatusSend();
                                    // - Do email should be sent if quote didn't change status?
                                    // - Do we should call saving request in loop? Calling all updates via one request would be better way
                                    if (!$this->quoteRepository->save($quoteObject)) {
                                        Yii::error($quoteObject->errors, 'LeadController:view:Email:Quote:save');
                                    }
                                }

                                Yii::$app->session->setFlash('send-success', '<strong>Email Message</strong> has been successfully sent to <strong>' . MaskEmailHelper::masking($mail->e_email_to) . '</strong>');
                            }
                            $this->refresh('#communication-form');
                        } else {
                            $mail->statusToReview();
                            $this->emailReviewQueueManageService->createByEmail($mail, $lead->l_dep_id);
                            /** @var string[] $quoteIds */
                            $quoteIds = Json::decode($previewEmailForm->e_quote_list);
                            /** @var Quote[] $quoteObjects */
                            $quoteObjects = Quote::find()->where(['IN', 'id', $quoteIds])->all();
                            foreach ($quoteObjects as $quoteObject) {
                                Repo::createForEmail($mail->e_id, $quoteObject->id, $previewEmailForm->e_qc_uid);
                                if (!$this->quoteRepository->save($quoteObject)) {
                                    Yii::error($quoteObject->errors, 'LeadController:view:Email:Quote:save');
                                }
                            }
                            $mail->update();
                            Yii::$app->session->setFlash('send-warning', '<strong>Email Message</strong> has been sent for review');
                            $this->refresh('#communication-form');
                        }
                    } else {
                        $previewEmailForm->addError('e_email_subject', VarDumper::dumpAsString($mail->errors));
                        Yii::error(VarDumper::dumpAsString($mail->errors), 'LeadController:view:Email:save');
                    }
                } else {
                    Yii::$app->session->setFlash('send-warning', 'Access denied: you dont have permission to send email');
                    $previewEmailForm->addError('general', 'Access denied: you dont have permission to send email');
                    $this->refresh('#communication-form');
                }
                //VarDumper::dump($previewEmailForm->attributes, 10, true);              exit;
            } else {
                Yii::$app->session->setFlash('send-error', 'Validation form error');
            }
        }

        $previewSmsForm = new LeadPreviewSmsForm($smsFromNumberList);
        $previewSmsForm->is_send = false;

        if ($smsFromNumberList->canSendSms() && $previewSmsForm->load(Yii::$app->request->post())) {
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
                        /** @var string[] $quoteIds */
                        $quoteIds = Json::decode($previewSmsForm->s_quote_list);
                        /** @var Quote[] $quoteObjects */
                        $quoteObjects = Quote::find()->where(['IN', 'id', $quoteIds])->all();
                        foreach ($quoteObjects as $quote) {
                            Repo::createForSms($sms->s_id, $quote->id, $previewSmsForm->s_qc_uid);
                            $quote->setStatusSend();
                            if (!$this->quoteRepository->save($quote)) {
                                Yii::error($quote->errors, 'LeadController:view:Sms:Quote:save');
                            }
                        }

                        Yii::$app->session->setFlash('send-success', '<strong>SMS Message</strong> has been successfully sent to <strong>' . $sms->s_phone_to . '</strong>');
                    }

                    $smsTemplate = $sms->sTemplateType;
                    if ($smsTemplate && in_array($smsTemplate->stp_key, SettingHelper::getSmsTemplateForRemovingLpp(), true)) {
                        LeadPoorProcessingService::addLeadPoorProcessingRemoverJob($lead->id, [LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER], 'SMS sent');
                    }

                    $this->refresh('#communication-form');
                } else {
                    $previewSmsForm->addError('s_sms_text', VarDumper::dumpAsString($sms->errors));
                    Yii::error(VarDumper::dumpAsString($sms->errors), 'LeadController:view:Sms:save');
                }
                //VarDumper::dump($previewEmailForm->attributes, 10, true);              exit;
            } else {
                Yii::$app->session->setFlash('send-error', 'Error: <strong>SMS Message</strong> has not been sent to <strong>' . $previewSmsForm->s_phone_to . '</strong> Validation form error');
            }
        }

        $comForm = new CommunicationForm($smsFromNumberList, $emailFromList, $lead->l_client_lang);
        $comForm->c_preview_email = 0;
        $comForm->c_preview_sms = 0;
        $comForm->c_voice_status = 0;

        if ($comForm->load(Yii::$app->request->post())) {
            $comForm->c_lead_id = $lead->id;

            if ($comForm->validate()) {
                $project = $lead->project;

                if ($comForm->c_type_id == CommunicationForm::TYPE_EMAIL) {
                    $comForm->c_preview_email = 1;

                    /** @var CommunicationService $communication */
                    $communication = Yii::$app->communication;
                    $data['origin'] = '';

                    $content_data['email_body_html'] = $comForm->c_email_message;
                    $content_data['email_subject'] = $comForm->c_email_subject;
                    $content_data['email_reply_to'] = Yii::$app->user->identity->email;

                    $projectContactInfo = [];

                    if ($project && $project->contact_info) {
                        $projectContactInfo = @json_decode($project->contact_info, true);
                    }

                    $previewEmailForm->e_quote_list = @json_encode([]);


                    $language = $comForm->c_language_id ?: 'en-US';
                    $lang = AirportLangHelper::getLangFromLocale($language);

                    $previewEmailForm->e_lead_id = $lead->id;
                    $previewEmailForm->e_email_tpl_id = $comForm->c_email_tpl_id;
                    $previewEmailForm->e_language_id = $comForm->c_language_id;

                    if ($comForm->c_email_tpl_id > 0) {
                        $previewEmailForm->e_email_tpl_id = $comForm->c_email_tpl_id;

                        // Initiate basic state of `$content_data`
                        $content_data = ($comForm->offerList)
                            ? $lead->getOfferEmailData($comForm->offerList, $projectContactInfo)
                            : $lead->getEmailData2($comForm->quoteList, $projectContactInfo, $lang);
                        $content_data['quotes'] = array_map(function ($quoteArray) use ($comForm) {
                            $quoteArray['qc'] = $comForm->c_qc_uid;
                            return $quoteArray;
                        }, $content_data['quotes'] ?? []);
                        $content_data['content'] = $comForm->c_email_message;
                        $content_data['subject'] = $comForm->c_email_subject;
                        $content_data['department'] = is_null($lead->lDep) ? [] : ['key' => $lead->lDep->dep_key, 'name' => $lead->lDep->dep_name];

                        $previewEmailForm->e_email_subject = $comForm->c_email_subject;
                        $previewEmailForm->e_content_data = $content_data;

                        $tpl = EmailTemplateType::findOne($comForm->c_email_tpl_id);
                        $templateType = $tpl ? $tpl->etp_key : '';
                        if ($tpl) {
                            if (isset($tpl->etp_params_json['quotes']['originalRequired']) && $tpl->etp_params_json['quotes']['originalRequired'] === true) {
                                $checkOriginalQuoteExistence = array_reduce($lead->quotes, function ($acc, $quote) {
                                    return $quote->type_id === Quote::TYPE_ORIGINAL ? true : $acc;
                                }, false);
                            }
                        }

                        $mailPreview = $communication->mailPreview($lead->project_id, $templateType, $comForm->c_email_from, $comForm->c_email_to, $content_data, $language);
                        if ($mailPreview && isset($mailPreview['data'])) {
                            $selectedQuotes = count($comForm->quoteList);
                            $tplConfigQuotes = $tpl->etp_params_json['quotes']; // << This row can make unexpected behavior

                            /*
                             * In this case we must use mutually exclusive condition sequence, so the conditions for
                             * them were taken out as apart variables. I hope this improve readability.
                             */
                            $mailPreviewScenario_1 = isset($mailPreview['error']) && $mailPreview['error'];
                            $mailPreviewScenario_2 = isset($checkOriginalQuoteExistence) && !$checkOriginalQuoteExistence;
                            $mailPreviewScenario_3 = (!(isset($tplConfigQuotes['minSelectedCount']) && $tplConfigQuotes['minSelectedCount'] <= $selectedQuotes) || !(isset($tplConfigQuotes['maxSelectedCount']) && $tplConfigQuotes['maxSelectedCount'] >= $selectedQuotes)) && $tplConfigQuotes['selectRequired'];

                            if ($mailPreviewScenario_1) {
                                $errorJson = @json_decode($mailPreview['error'], true);
                                $comForm->addError('c_email_preview', 'Communication Server response: ' . ($errorJson['message'] ?? $mailPreview['error']));

                                $errorLog = $mailPreview['error'];
                                if (is_array($errorJson)) {
                                    $errorLog = $errorJson;
                                    $errorLog['forAdmin'] = true;
                                    $errorLog['communicationUrl'] = $communication->url;
                                }
                                Yii::error($errorLog, 'LeadController:view:mailPreview');
                                $comForm->c_preview_email = 0;
                            } elseif ($mailPreviewScenario_2) {
                                $comForm->addError('originalQuotesRequired', 'Original quote required');
                                Yii::info('Lead dont have quote with type original', 'info\LeadController:view:mailPreview');
                                $comForm->c_preview_email = 0;
                            } elseif ($mailPreviewScenario_3) {
                                $comForm->addError('minMaxSelectedQuotes', 'Allowed quantity of selected quotes is from ' . $tplConfigQuotes['minSelectedCount'] . ' to ' . $tplConfigQuotes['maxSelectedCount'] . ' inclusive. You selected ' . $selectedQuotes . '.');
                                Yii::info('Allowed quantity of selected quotes is from ' . $tplConfigQuotes['minSelectedCount'] . ' to ' . $tplConfigQuotes['maxSelectedCount'] . ' inclusive. You selected ' . $selectedQuotes . '.', 'info\LeadController:view:mailPreview');
                                $comForm->c_preview_email = 0;
                            } else {
                                $emailBodyHtml = EmailService::prepareEmailBody($mailPreview['data']['email_body_html']);

                                $keyCache = md5($emailBodyHtml);
                                Yii::$app->cacheFile->set($keyCache, $emailBodyHtml, 60 * 60);
                                $previewEmailForm->keyCache = $keyCache;
                                $previewEmailForm->e_email_message = $emailBodyHtml;
                                $previewEmailForm->e_email_message_edited = false;

                                if (isset($mailPreview['data']['email_subject']) && $mailPreview['data']['email_subject'] && $comForm->isEmailBlankType() === false) {
                                    $previewEmailForm->e_email_subject = $mailPreview['data']['email_subject'];
                                    $previewEmailForm->e_email_subject_origin = $previewEmailForm->e_email_subject;
                                }
                                $previewEmailForm->e_email_from = $comForm->c_email_from;
                                $previewEmailForm->e_email_to = $comForm->c_email_to;
                                $previewEmailForm->e_email_from_name = Yii::$app->user->identity->nickname;
                                $previewEmailForm->e_email_to_name = $lead->client ? $lead->client->full_name : '';
                                $previewEmailForm->e_quote_list = @json_encode($comForm->quoteList);
                                $previewEmailForm->e_offer_list = @json_encode($comForm->offerList);
                            }
                        }

                        //VarDumper::dump($mailPreview, 10, true);// exit;
                    } else {
                        $previewEmailForm->e_email_message = $comForm->c_email_message;
                        $previewEmailForm->e_email_subject = $comForm->c_email_subject;
                        $previewEmailForm->e_email_subject_origin = $comForm->c_email_subject;
                        $previewEmailForm->e_email_message_edited = false;
                        $previewEmailForm->e_email_from = $comForm->c_email_from;
                        $previewEmailForm->e_email_to = $comForm->c_email_to;
                        $previewEmailForm->e_email_from_name = Yii::$app->user->identity->nickname;
                        $previewEmailForm->e_email_to_name = $lead->client ? $lead->client->full_name : '';
                    }
                }


                if ($comForm->c_type_id == CommunicationForm::TYPE_SMS && $smsFromNumberList->canSendSms()) {
                    $comForm->c_preview_sms = 1;

                    /** @var CommunicationService $communication */
                    $communication = Yii::$app->communication;

                    //$data['origin'] = 'ORIGIN';
                    //$data['destination'] = 'DESTINATION';


                    $content_data['message'] = $comForm->c_sms_message;
                    $content_data['project_id'] = $lead->project_id;

                    $projectContactInfo = [];

                    if ($project && $project->contact_info) {
                        $projectContactInfo = @json_decode($project->contact_info, true);
                    }

                    $previewSmsForm->s_quote_list = @json_encode([]);

                    $previewSmsForm->s_phone_to = $comForm->c_phone_number;
                    $previewSmsForm->s_phone_from = $comForm->c_sms_from;

                    if ($comForm->c_language_id) {
                        $previewSmsForm->s_language_id = $comForm->c_language_id; //$language;
                    }


                    if ($comForm->c_sms_tpl_id > 0) {
                        $previewSmsForm->s_sms_tpl_id = $comForm->c_sms_tpl_id;

                        $content_data = $lead->getEmailData2($comForm->quoteList, $projectContactInfo);
                        $content_data['content'] = $comForm->c_sms_message;
                        $content_data['quotes'] = array_map(function ($quoteArray) use ($comForm) {
                            $quoteArray['qc'] = $comForm->c_qc_uid;
                            return $quoteArray;
                        }, $content_data['quotes'] ?? []);

                        //VarDumper::dump($content_data, 10, true); exit;

                        $language = $comForm->c_language_id ?: 'en-US';

                        $tpl = SmsTemplateType::findOne($comForm->c_sms_tpl_id);
                        //$mailSend = $communication->mailSend(7, 'cl_offer', 'chalpet@gmail.com', 'chalpet2@gmail.com', $content_data, $data, 'ru-RU', 10);

                        $smsPreview = $communication->smsPreview($lead->project_id, ($tpl ? $tpl->stp_key : ''), $comForm->c_sms_from, $comForm->c_phone_number, $content_data, $language);


                        if ($smsPreview && isset($smsPreview['data'])) {
                            if (isset($smsPreview['error']) && $smsPreview['error']) {
                                $errorJson = @json_decode($smsPreview['error'], true);
                                $comForm->addError('c_email_preview', 'Communication Server response: ' . ($errorJson['message'] ?? $smsPreview['error']));

                                $errorLog = $communication->url . "\r\n " . $smsPreview['error'];
                                if (is_array($errorJson)) {
                                    $errorLog = $errorJson;
                                    $errorLog['forAdmin'] = true;
                                    $errorLog['communicationUrl'] = $communication->url;
                                }
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
        } else {
            $comForm->c_type_id = ''; //CommunicationForm::TYPE_VOICE;
        }

        if ($previewEmailForm->is_send || $previewSmsForm->is_send) {
            $comForm->c_preview_email = 0;
            $comForm->c_preview_sms = 0;
        }


        $quotesProvider = $lead->getQuotesProvider([]);


        $queryEmail = (new \yii\db\Query())
            ->select(['e_id AS id', new Expression('"email" AS type'), 'e_lead_id AS lead_id', 'e_created_dt AS created_dt'])
            ->from('email')
            ->where(['e_lead_id' => $lead->id]);

        $querySms = (new \yii\db\Query())
            ->select(['s_id AS id', new Expression('"sms" AS type'), 's_lead_id AS lead_id', 's_created_dt AS created_dt'])
            ->from('sms')
            ->where(['s_lead_id' => $lead->id]);

        $queryChats = (new \yii\db\Query())
            ->select(['ccl_chat_id AS id', new Expression('"chat" AS type'), 'ccl_lead_id AS lead_id', 'ccl_created_dt AS created_dt'])
            ->from('{{%client_chat_lead}}')
            ->where(['ccl_lead_id' => $lead->id]);

        $queryCallLog = (new Query())
            ->select(['id' => new Expression('cl_group_id')])
            ->addSelect(['type' => new Expression('"voice"')])
            ->addSelect(['lead_id' => 'call_log_lead.cll_lead_id', 'created_dt' => 'MIN(call_log.cl_call_created_dt)'])
            ->from('call_log_lead')
            ->innerJoin('call_log', 'call_log.cl_id = call_log_lead.cll_cl_id')
            ->where(['cll_lead_id' => $lead->id])
            ->andWhere(['call_log.cl_type_id' => [CallLogType::IN, CallLogType::OUT]])
            ->orderBy(['created_dt' => SORT_ASC])
            ->groupBy(['id', 'type', 'lead_id']);

        $queryUnionLog = $queryEmail->union($querySms)->union($queryChats);

        $unionQueryLog = (new Query())
            ->from(['union_table' => $queryUnionLog->union($queryCallLog)])
            ->orderBy(['created_dt' => SORT_ASC]);

        $dataProviderCommunicationLog = new ActiveDataProvider([
            'query' => $unionQueryLog,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        if (!Yii::$app->request->isAjax || !Yii::$app->request->get('page')) {
            $pageCountLog = ceil($dataProviderCommunicationLog->totalCount / $dataProviderCommunicationLog->pagination->pageSize) - 1;
            if ($pageCountLog < 0) {
                $pageCountLog = 0;
            }
            $dataProviderCommunicationLog->pagination->page = $pageCountLog;
        }

        $modelLeadCallExpert = new LeadCallExpert();
        $expertCallAbacDto = (new AbacLeadExpertCallService($lead, $user))->getLeadExpertCallDto();
        /** @abac $expertCallAbacDto, LeadExpertCallObject::ACT_CALL, LeadExpertCallObject::ACTION_ACCESS, access new expert call */
        $abacActNewExpertCall = \Yii::$app->abac->can($expertCallAbacDto, LeadExpertCallObject::ACT_CALL, LeadExpertCallObject::ACTION_ACCESS);
        if (!$lead->client->isExcluded() && $abacActNewExpertCall) {
            if ($modelLeadCallExpert->load(Yii::$app->request->post())) {
                $modelLeadCallExpert->lce_agent_user_id = Yii::$app->user->id;
                $modelLeadCallExpert->lce_lead_id = $lead->id;
                $modelLeadCallExpert->lce_status_id = LeadCallExpert::STATUS_PENDING;
                $modelLeadCallExpert->lce_request_dt = date('Y-m-d H:i:s');

                if ($modelLeadCallExpert->save()) {
                    $modelLeadCallExpert->lce_request_text = '';

                    LeadPoorProcessingService::addLeadPoorProcessingRemoverJob(
                        $lead->id,
                        [LeadPoorProcessingDataDictionary::KEY_NO_ACTION],
                        LeadPoorProcessingLogStatus::REASON_CALL_EXPERT
                    );
                }
            }
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

            if ($modelLeadChecklist->save()) {
                $modelLeadChecklist->lc_notes = null;
            } else {
                Yii::error('Lead id: ' . $lead->id . ', ' . VarDumper::dumpAsString($modelLeadCallExpert->errors), 'Lead:view:LeadChecklist:save');
            }
        }

        $searchModelLeadChecklist = new LeadChecklistSearch();
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
                Yii::error('Lead id: ' . $lead->id . ', ' . VarDumper::dumpAsString($modelNote->errors), 'Lead:view:Note:save');
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

        $isCreatedFlightRequest = false;

        if ($lead->status === Lead::STATUS_PROCESSING && $lead->leadFlightSegmentsCount > 0 && $lead->quotesCount === 0) {
            $isCreatedFlightRequest = true;
        }

//        $tmpl = $isQA ? 'view_qa' : 'view';
        $tmpl = 'view';

        return $this->render($tmpl, [
            'leadForm' => $leadForm,
            'previewEmailForm' => $previewEmailForm,
            'previewSmsForm' => $previewSmsForm,
            'comForm' => $comForm,
            'quotesProvider' => $quotesProvider,
            'dataProviderCommunicationLog' => $dataProviderCommunicationLog,
            'dataProviderCallExpert' => $dataProviderCallExpert,
            'modelLeadCallExpert' => $modelLeadCallExpert,
            'dataProviderChecklist' => $dataProviderChecklist,
            'modelLeadChecklist' => $modelLeadChecklist,
            'itineraryForm' => $itineraryForm,
            'dataProviderNotes' => $dataProviderNotes,
            'modelNote' => $modelNote,

            'dataProviderOffers' => $dataProviderOffers,
            'dataProviderOrders' => $dataProviderOrders,

            'callFromNumberList' => $callFromNumberList,
            'smsFromNumberList' => $smsFromNumberList,
            'emailFromList' => $emailFromList,

            'isCreatedFlightRequest' => $isCreatedFlightRequest,
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


    public function actionSetUserRating()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
                throw new \RuntimeException('Access Denied');
            }
            $user = Auth::user();
            $form = new LeadUserRatingForm($user);
            $form->load(Yii::$app->request->post());
            if (!$form->validate()) {
                throw new \RuntimeException(implode(', ', $form->getErrorSummary(true)));
            }
            $rating = $form->rating;
            $leadId = $form->leadId;
            $lead = Lead::findOne(['id' => $leadId]);
            $leadUserRatingAbacDto = new LeadUserRatingAbacDto($lead, $user->id);
            /** @abac leadUserRatingAbacDto, LeadUserRatingAbacObject::LEAD_RATING_FORM, LeadUserRatingAbacObject::ACTION_EDIT, Lead User Rating edit */
            $can = Yii::$app->abac->can(
                $leadUserRatingAbacDto,
                LeadUserRatingAbacObject::LEAD_RATING_FORM,
                LeadUserRatingAbacObject::ACTION_EDIT
            );
            if (!$can) {
                throw new \RuntimeException('Access Denied');
            }
            LeadUserRatingService::createOrUpdate($leadId, $user->id, $rating);
            return [
                'success' => true,
            ];
        } catch (\RuntimeException | \DomainException $e) {
            Yii::warning(AppHelper::throwableFormatter($e), 'LeadController::actionSetRating:exception');
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e), 'LeadController:actionSetRating:Throwable');
            return [
                'success' => false,
                'error' => 'Server Error'
            ];
        }
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionAjaxTake(): array
    {
        $result = ['success' => false, 'error' => 'GID parameter not found'];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $gid = Yii::$app->request->get('gid');
        if (Yii::$app->request->isAjax && $gid) {
            try {
                $lead = $this->findLeadByGid($gid);
                $oldStatus = $lead->status;
                $allowRbac = Auth::can('leadSection', ['lead' => $lead]);
                if ($allowRbac) {
                    $user = Auth::user();
                    $leadAbacDto = new LeadAbacDto($lead, $user->getId());
                    /** @abac $leadAbacDto, LeadAbacObject::ACT_TAKE_LEAD_FROM_CHAT, LeadAbacObject::ACTION_ACCESS, Access to take lead from chat */
                    if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_TAKE_LEAD_FROM_CHAT, LeadAbacObject::ACTION_ACCESS)) {
                        $lead->processing($user->getId(), Yii::$app->user->getId(), 'Take');

                        $this->transaction->wrap(function () use ($lead) {
                            if ($qCall = LeadQcall::find()->andWhere(['lqc_lead_id' => $lead->id])->one()) {
                                $qCall->delete();
                            }
                            $this->leadRepository->save($lead);
                        });

                        if ($oldStatus === Lead::STATUS_PENDING) {
                            $leadUserConversionService = Yii::createObject(LeadUserConversionService::class);
                            $leadUserConversionService->addAutomate(
                                $lead->id,
                                $user->getId(),
                                LeadUserConversionDictionary::DESCRIPTION_TAKE,
                                $user->getId()
                            );
                        }
                        $result['success'] = true;
                        $result['error'] = '';
                    } else {
                        $result ['error'] = 'Access Denied (ABAC)!';
                    }
                } else {
                    $result ['error'] = 'Access Denied (RBAC)!';
                }
            } catch (\RuntimeException | \DomainException $exception) {
                Yii::warning(AppHelper::throwableLog($exception, true), 'LeadController:actionAjaxTake::DomainException');
                $result['error'] = $exception->getMessage();
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'LeadController:actionAjaxTake:Throwable');
                $result['error'] = $throwable->getMessage();
            }
        }

        return $result;
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

        if (!Auth::can('leadSection', ['lead' => $lead])) {
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
            $oldStatus = $lead->status;

            /** @var Employee $user */
            $user = Yii::$app->user->identity;
            $this->leadAssignService->take($lead, $user, Yii::$app->user->id, 'Take');

            if ($oldStatus === Lead::STATUS_PENDING) {
                $leadUserConversionService = Yii::createObject(LeadUserConversionService::class);
                $leadUserConversionService->addAutomate(
                    $lead->id,
                    $user->getId(),
                    LeadUserConversionDictionary::DESCRIPTION_TAKE,
                    $user->getId()
                );
            }

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

    public function actionAlternative(): string
    {
        $user = Auth::user();
        $checkShiftTime = true;
        $isAccessNewLead = true;
        $accessLeadByFrequency = [];
        $limit = null;

        if ($user->isAgent()) {
            $checkShiftTime = $user->checkShiftTime();
            $userParams = $user->userParams;
            if ($userParams) {
                if ($userParams->up_inbox_show_limit_leads > 0) {
                    $limit = $userParams->up_inbox_show_limit_leads;
                }
            } else {
                throw new NotFoundHttpException('Not set user params for agent! Please ask supervisor to set shift time and other.');
            }
            $accessLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes([], [Lead::STATUS_BOOK_FAILED]);
            if (!$accessLeadByFrequency['access']) {
                $isAccessNewLead = $accessLeadByFrequency['access'];
            }
        }

        $searchModel = new LeadSearch();
        $dataProvider = $searchModel->searchAlternative(Yii::$app->request->queryParams, $user, $limit);

        return $this->render('alternative', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'checkShiftTime' => $checkShiftTime,
            'isAccessNewLead' => $isAccessNewLead,
            'accessLeadByFrequency' => $accessLeadByFrequency,
            'user' => $user,
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
    public function actionBusinessInbox(): string
    {
        $searchModel = new LeadSearch();

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $dataProvider = $searchModel->searchBusinessInbox(Yii::$app->request->queryParams, $user);

        return $this->render('business-inbox', [
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
     * @throws NotFoundHttpException
     */
    public function actionFailedBookings(): string
    {
        $user = Auth::user();

        $checkShiftTime = true;
        $isAccessNewLead = true;
        $accessLeadByFrequency = [];
        $limit = null;

        if ($user->isAgent()) {
            $checkShiftTime = $user->checkShiftTime();
            $userParams = $user->userParams;
            if ($userParams) {
                if ($userParams->up_inbox_show_limit_leads > 0) {
                    $limit = $userParams->up_inbox_show_limit_leads;
                }
            } else {
                throw new NotFoundHttpException('Not set user params for agent! Please ask supervisor to set shift time and other.');
            }
            $accessLeadByFrequency = $user->accessTakeLeadByFrequencyMinutes([], [Lead::STATUS_BOOK_FAILED]);
            if (!$accessLeadByFrequency['access']) {
                $isAccessNewLead = $accessLeadByFrequency['access'];
            }
        }

        $searchModel = new LeadSearch();
        $dataProvider = $searchModel->searchFailedBookings(Yii::$app->request->queryParams, $user, $limit);

        return $this->render('failed-bookings', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'checkShiftTime' => $checkShiftTime,
            'isAccessNewLead' => $isAccessNewLead,
            'accessLeadByFrequency' => $accessLeadByFrequency,
            'user' => $user,
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

        //$tmpl = $user->isQa() ? 'sold_qa' : 'sold';
        $tmpl = 'sold';

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

    public function actionClosed(): string
    {
        /** @abac null, LeadAbacObject::OBJ_CLOSED_QUEUE, LeadAbacObject::ACTION_ACCESS, Access to page lead/closed */
        if (!\Yii::$app->abac->can(null, LeadAbacObject::OBJ_CLOSED_QUEUE, LeadAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        $searchModel = new LeadSearch();

        $user = Auth::user();

        if ($user->isAgent()) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        $dataProvider = $searchModel->searchClosed(Yii::$app->request->queryParams, $user);

        return $this->render('closed', [
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
        /** @abac null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CREATE, Access to create lead */
        if (!Yii::$app->abac->can(null, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CREATE)) {
            throw new ForbiddenHttpException('Access Denied.');
        }

        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'LeadCreateForm',
            ['emails' => 'EmailCreateForm', 'phones' => 'PhoneCreateForm', 'segments' => 'SegmentCreateForm']
        );
        $dto = new LeadAbacDto(null, Auth::id());
        $delayedChargeAccess = Yii::$app->abac->can($dto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CREATE_DELAY_CHARGE, Auth::user());
        $form = new LeadCreateForm(count($data['post']['EmailCreateForm']), count($data['post']['PhoneCreateForm']), count($data['post']['SegmentCreateForm']));
        $form->assignDep(Department::DEPARTMENT_SALES);
        if ($form->load($data['post']) && $form->validate()) {
            try {
                if (!$delayedChargeAccess) {
                    $form->delayedCharge = false;
                }
                $form->client->projectId = $form->projectId;
                $form->client->typeCreate = Client::TYPE_CREATE_LEAD;
                $lead = $this->leadManageService->createManuallyByDefault($form, Yii::$app->user->id, Yii::$app->user->id, LeadFlow::DESCRIPTION_MANUAL_CREATE);

                if (!empty($form->requestIp)) {
                    $clientManageService = \Yii::createObject(ClientManageService::class);
                    $clientManageService->checkIpChanged($lead->client, $form->requestIp);
                }

                $leadUserConversionService = Yii::createObject(LeadUserConversionService::class);
                $leadUserConversionService->addAutomate(
                    $lead->id,
                    Yii::$app->user->id,
                    LeadUserConversionDictionary::DESCRIPTION_MANUAL,
                    Yii::$app->user->id
                );

                Yii::$app->session->setFlash('success', 'Lead save');
                return $this->redirect(['/lead/view', 'gid' => $lead->gid]);
            } catch (\Throwable $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['/lead/create']);
            }
        }
        return $this->render('create', ['leadForm' => $form, 'delayedChargeAccess' => $delayedChargeAccess]);
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
            if (Yii::$app->request->isPjax && $form->load($data['post']) && $form->validate()) {
                try {
                    $leadManageService = Yii::createObject(UseCaseLeadManageService::class);
                    $form->client->projectId = $form->projectId;
                    $form->client->typeCreate = Client::TYPE_CREATE_LEAD;
                    $lead = $leadManageService->createManuallyByDefault($form, Yii::$app->user->id, Yii::$app->user->id, LeadFlow::DESCRIPTION_MANUAL_CREATE);

                    $leadUserConversionService = Yii::createObject(LeadUserConversionService::class);
                    $leadUserConversionService->addAutomate(
                        $lead->id,
                        Yii::$app->user->id,
                        LeadUserConversionDictionary::DESCRIPTION_MANUAL,
                        Yii::$app->user->id
                    );

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

    public function actionCreateByChat()
    {
        if (!(Yii::$app->request->isAjax || Yii::$app->request->isPjax)) {
            throw new BadRequestHttpException('Bad request.');
        }

        $chatId = (int)Yii::$app->request->get('chat_id');
        $chat = ClientChat::findOne(['cch_id' => $chatId]);

        if (!$chat) {
            throw new NotFoundHttpException('Client chat not found');
        }

        if (!$this->chatActionPermission->canCreateLead($chat)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        if (!$chat->cchClient) {
            return 'Client not found';
        }

        $userId = Auth::id();
        $form = new LeadCreateByChatForm($chat);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            if (empty($form->source)) {
                Yii::warning(VarDumper::dumpAsString([
                    'post' => Yii::$app->request->post($form->formName()),
                    'chatId' => $chatId,
                ]), 'LeadController:actionCreateByChat:sourceNotFound');
            }

            try {
                $leadManageService = Yii::createObject(UseCaseLeadManageService::class);
                $lead = $leadManageService->createByClientChat((new CreateLeadByChatDTO($form, $chat, $userId))->leadInProgressDataPrepare());

                $leadUserConversionService = Yii::createObject(LeadUserConversionService::class);
                $leadUserConversionService->addAutomate(
                    $lead->id,
                    $userId,
                    LeadUserConversionDictionary::DESCRIPTION_CLIENT_CHAT_MANUAL,
                    $userId
                );

                return "<script> $('#modal-md').modal('hide');refreshChatInfo('" . $chat->cch_id . "')</script>";
            } catch (\Throwable $e) {
                Yii::error(AppHelper::throwableFormatter($e), 'LeadController:actionCreateByChat');
                return "<script> $('#modal-md').modal('hide');createNotify('Create Lead', '" . $e->getMessage() . "', 'error');</script>";
            }
        }

        return $this->renderAjax('partial/_lead_create_by_chat', ['chat' => $chat, 'form' => $form]);
    }

    public function actionAjaxCreateFromPhoneWidget()
    {
        $callSid = Yii::$app->request->post('callSid');

        if (!$call = Call::findOne(['c_call_sid' => $callSid])) {
            throw new NotFoundHttpException('Call not found');
        }

        if ($this->clientOnCallIsInvalid($call)) {
            return $this->asJson([
                'error' => false,
                'message' => 'client is invalid',
            ]);
        }

        $leadAbacDto = new LeadAbacDto(null, $call->c_created_user_id);
        /** @abac new LeadAbacDto(null, $call->c_created_user_id), LeadAbacObject::ACT_CREATE_FROM_PHONE_WIDGET, LeadAbacObject::ACTION_CREATE, Restrict access to create lead in phone widget in contact info block */
        if (!(bool)\Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_CREATE_FROM_PHONE_WIDGET, LeadAbacObject::ACTION_CREATE, $call->cCreatedUser)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $result = [
            'error' => false,
            'message' => '',
            'warning' => false
        ];

        try {
            $lead = $this->useCaseLeadManageService->createFromPhoneWidget($call, Auth::user());
            $result['url'] = Url::to('/lead/view/' . $lead->gid);
            $result['contactData'] = (new CallUpdateMessage())->getContactData($call, Auth::id());
        } catch (\RuntimeException | \DomainException $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        } catch (\Throwable $e) {
            $result['error'] = true;
            $result['message'] = 'Internal server Error';
            Yii::error(AppHelper::throwableFormatter($e), 'LeadController:actionAjaxCreateFromPhoneWidget:Throwable');
        }

        return $this->asJson($result);
    }

    public function actionAjaxCreateFromPhoneWidgetWithInvalidClient()
    {
        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            throw new BadRequestHttpException();
        }

        $callSid = (string)Yii::$app->request->get('callSid');
        if (!$callSid) {
            throw new BadRequestHttpException('Not found CallSid');
        }

        if (!$call = Call::findOne(['c_call_sid' => $callSid])) {
            throw new NotFoundHttpException('Call not found');
        }

        if ($call->isInternal()) {
            return 'Call is internal.';
        }

        if (!$this->clientOnCallIsInvalid($call)) {
            return 'Client already is Valid.';
        }

        $leadAbacDto = new LeadAbacDto(null, $call->c_created_user_id);
        /** @abac new LeadAbacDto(null, $call->c_created_user_id), LeadAbacObject::ACT_CREATE_FROM_PHONE_WIDGET, LeadAbacObject::ACTION_CREATE, Restrict access to create lead in phone widget in contact info block */
        if (!(bool)\Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_CREATE_FROM_PHONE_WIDGET, LeadAbacObject::ACTION_CREATE, $call->cCreatedUser)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $userId = Auth::id();

        try {
            $form = new \src\model\lead\useCases\lead\create\fromPhoneWidgetWithInvalidClient\Form($call, $userId);

            if (Yii::$app->request->isPjax && $form->load(Yii::$app->request->post()) && $form->validate()) {
                try {
                    $leadManageService = Yii::createObject(UseCaseLeadManageService::class);
                    $lead = $leadManageService->createFromPhoneWidgetWithInvalidClient($form, $call);
                    $data = json_encode((new CallUpdateMessage())->getContactData($call, Auth::id()));
                    $leadUrl = Url::to('/lead/view/' . $lead->gid);
                    return "<script> $('#modal-md').modal('hide');createNotify('Create Lead', 'Lead saved', 'success');PhoneWidgetContactInfo.load(" . $data . ");window.open('" . $leadUrl . "', '_blank').focus();</script>";
                } catch (\Throwable $e) {
                    Yii::error([
                        'message' => 'Create lead from phone widget error',
                        'error' => $e->getMessage(),
                        'callId' => $call->c_id,
                        'userId' => $userId,
                    ], 'LeadController:actionAjaxCreateFromPhoneWidgetWithInvalidClient:1');
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }

            return $this->renderAjax('partial/_lead_create_from_phone_widget_with_invalid_client', ['leadForm' => $form]);
        } catch (\Throwable $t) {
            Yii::error([
                'message' => 'Create lead from phone widget error',
                'error' => $t->getMessage(),
                'callId' => $call->c_id,
                'userId' => $userId,
            ], 'LeadController:actionAjaxCreateFromPhoneWidgetWithInvalidClient:2');
            return 'Server error. Please try again later.';
        }
    }

    private function clientOnCallIsInvalid(Call $call): bool
    {
        return $call->c_client_id === null || ContactPhoneListService::isInvalid($call->getClientPhoneNumber());
    }

    public function actionLinkChat()
    {
        if (!(Yii::$app->request->isAjax || Yii::$app->request->isPjax)) {
            throw new BadRequestHttpException('Bad request.');
        }

        $chatId = (int)Yii::$app->request->get('chat_id');
        $chat = ClientChat::findOne(['cch_id' => $chatId]);

        if (!$chat) {
            throw new NotFoundHttpException('Client chat not found.');
        }

        if (!$this->chatActionPermission->canLinkLead($chat)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $form = new LeadLinkChatForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $clientChatLeadRepository = Yii::createObject(ClientChatLeadRepository::class);
                $clientChatLead = ClientChatLead::create($form->chatId, $form->leadId, new \DateTimeImmutable('now'));
                $clientChatLeadRepository->save($clientChatLead);
                return "<script> $('#modal-sm').modal('hide');refreshChatInfo('" . $chat->cch_id . "')</script>";
            } catch (\Throwable $e) {
                Yii::error(AppHelper::throwableFormatter($e), 'LeadController:actionLinkChat');
                return "<script> $('#modal-sm').modal('hide');createNotify('Link Lead', '" . $e->getMessage() . "', 'error');</script>";
            }
        }

        $form->chatId = $chatId;
        return $this->renderAjax('partial/link_chat', [
            'model' => $form,
        ]);
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

        $dto = new LeadAbacDto(null, Auth::id());
        $delayedChargeAccess = Yii::$app->abac->can($dto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CREATE_DELAY_CHARGE, Auth::user());

        $form = new LeadCreateForm(count($data['post']['EmailCreateForm']), count($data['post']['PhoneCreateForm']), count($data['post']['SegmentCreateForm']));
        $form->assignCase($case->cs_gid);
        $form->assignDep(Department::DEPARTMENT_EXCHANGE);
        if ($form->load($data['post']) && $form->validate()) {
            try {
                if (!$delayedChargeAccess) {
                    $form->delayedCharge = false;
                }
                $form->client->projectId = $form->projectId;
                $form->client->typeCreate = Client::TYPE_CREATE_LEAD;
                $lead = $this->leadManageService->createManuallyFromCase($form, Yii::$app->user->id, Yii::$app->user->id, 'Manual create form Case');
                Yii::$app->session->setFlash('success', 'Lead save');
                return $this->redirect(['/lead/view', 'gid' => $lead->gid]);
            } catch (\Throwable $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['/lead/create-case', 'case_gid' => $case->cs_gid]);
            }
        }
        return $this->render('create', ['leadForm' => $form, 'delayedChargeAccess' => $delayedChargeAccess]);
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

        /*if ($lead->isSold() && !$user->isAdmin()) {
            throw new ForbiddenHttpException('Access denied! Lead is sold.');
        }*/

        $leadAbacDto = new LeadAbacDto($lead, $user->id);
        /** @abac $leadAbacDto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CLONE, Act clone lead */
        if (!(Auth::can('leadSection', ['lead' => $lead]) && Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_CLONE))) {
            throw new ForbiddenHttpException('Access denied');
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

                if ((int)$lead->employee_id !== $user->getId()) {
                    $leadUserConversionService = Yii::createObject(LeadUserConversionService::class);
                    $leadUserConversionService->addAutomate(
                        $clone->id,
                        $user->getId(),
                        LeadUserConversionDictionary::DESCRIPTION_CLONE,
                        $user->getId()
                    );
                }

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
            //$totalProfit = $lead->getFinalProfit() ?: $lead->getBookedQuote()->getEstimationProfit();
            $totalProfit = $lead->getFinalProfit() ?: 0;
            $splitForm = new ProfitSplitForm($lead);
            $splitForm->setZeroPercent(true);
            $mainAgentPercent = 0;

            foreach ($lead->profitSplits as $split) {
                if ($split->ps_user_id === $lead->employee_id) {
                    $mainAgentPercent = $split->ps_percent;
                }
            }

            $mainAgentProfit = $totalProfit * $mainAgentPercent / 100;

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
                        if ($entry->ps_user_id === $lead->employee_id) {
                            $mainAgentPercent = $entry->ps_percent;
                        }
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
                        'mainAgentPercent' => $mainAgentPercent,
                        'errors' => $errors,
                    ]);
                }
            } elseif (Yii::$app->request->isAjax) {
                return $this->renderAjax('_split_profit', [
                    'lead' => $lead,
                    'splitForm' => $splitForm,
                    'totalProfit' => $totalProfit,
                    'mainAgentProfit' => $mainAgentProfit,
                    'mainAgentPercent' => $mainAgentPercent,
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

                /** @abac null, LeadAbacObject::CHANGE_SPLIT_TIPS, LeadAbacObject::ACTION_UPDATE, hide split tips edition */
                if (!Yii::$app->abac->can(null, LeadAbacObject::CHANGE_SPLIT_TIPS, LeadAbacObject::ACTION_UPDATE)) {
                    $errors[] = 'Forbidden';
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
     * @return mixed|null
     */
    public function actionGetTemplate()
    {
        $keyCache = Yii::$app->request->get('key_cache');
        return Yii::$app->cacheFile->get($keyCache);
    }

    public function actionAjaxLinkToCall(): Response
    {
        $leadId = Yii::$app->request->post('leadId');
        $callId = Yii::$app->request->post('callId');

        if (!$lead = Lead::findOne($leadId)) {
            throw new BadRequestHttpException('Lead not found');
        }

        if (!$call = Call::findOne($callId)) {
            throw new BadRequestHttpException('Call not found');
        }

        $leadAbacDto = new LeadAbacDto($lead, Auth::id());
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_LINK_TO_CALL, LeadAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $result = [
            'error' => false,
            'message' => ''
        ];

        $call->c_lead_id = $lead->id;
        if (!$call->save()) {
            $result['error'] = true;
            $result['message'] = $call->getErrorSummary(true)[0];
        }
        return $this->asJson($result);
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionExtraQueue(): string
    {
        $leadAbacDto = new LeadAbacDto(null, (int)Auth::id());
        /** @abac $leadAbacDto, LeadAbacObject::OBJ_EXTRA_QUEUE, LeadAbacObject::ACTION_ACCESS, access to actionExtraQueue */
        $canLeadPoorProcessingLogs = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_EXTRA_QUEUE, LeadAbacObject::ACTION_ACCESS);

        if (!$canLeadPoorProcessingLogs) {
            throw new ForbiddenHttpException('Access denied.');
        }

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

        $dataProvider = $searchModel->searchExtraQueue($params, $user);

        return $this->render('extra-queue', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function actionBusinessExtraQueue(): string
    {
        /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
        if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE) === false) {
            throw new ForbiddenHttpException('Access denied');
        }

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

        $dataProvider = $searchModel->searchBusinessExtraQueue($params, $user);

        return $this->render('business-extra-queue', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
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
