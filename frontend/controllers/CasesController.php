<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\components\CommunicationService;
use common\models\CaseNote;
use common\models\CaseSale;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Department;
use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Lead;
use common\models\Quote;
use common\models\search\CaseSaleSearch;
use common\models\search\LeadSearch;
use common\models\search\SaleSearch;
use common\models\Sms;
use common\models\SmsTemplateType;
use common\models\UserProjectParams;
use common\widgets\Alert;
use frontend\helpers\JsonHelper;
use frontend\models\CaseCommunicationForm;
use frontend\models\CasePreviewEmailForm;
use frontend\models\CasePreviewSmsForm;
use sales\auth\Auth;
use sales\entities\cases\CasesSourceType;
use sales\entities\cases\CasesStatus;
use sales\entities\cases\CaseStatusLogSearch;
use sales\forms\cases\CasesAddEmailForm;
use sales\forms\cases\CasesAddPhoneForm;
use sales\forms\cases\CasesChangeStatusForm;
use sales\forms\cases\CasesClientUpdateForm;
use sales\forms\cases\CasesCreateByChatForm;
use sales\forms\cases\CasesCreateByWebForm;
use sales\forms\cases\CasesSaleForm;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\callLog\entity\callLog\CallLogType;
use sales\model\cases\useCases\cases\updateInfo\UpdateInfoForm;
use sales\guards\cases\CaseManageSaleInfoGuard;
use sales\model\cases\useCases\cases\updateInfo\Handler;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\permissions\ClientChatActionPermission;
use sales\model\clientChat\services\ClientChatAssignService;
use sales\model\coupon\entity\couponCase\CouponCase;
use sales\model\coupon\useCase\send\SendCouponsForm;
use sales\model\department\department\Params;
use sales\model\phone\AvailablePhoneList;
use sales\model\project\entity\CustomData;
use sales\model\saleTicket\useCase\create\SaleTicketService;
use sales\repositories\cases\CaseCategoryRepository;
use sales\repositories\cases\CasesRepository;
use sales\repositories\cases\CasesSaleRepository;
use sales\repositories\client\ClientEmailRepository;
use sales\repositories\NotFoundException;
use sales\repositories\quote\QuoteRepository;
use sales\services\cases\CasesSaleService;
use sales\services\cases\CasesCommunicationService;
use sales\repositories\user\UserRepository;
use sales\services\cases\CasesCreateService;
use sales\services\cases\CasesManageService;
use sales\services\client\ClientManageService;
use sales\services\client\ClientUpdateFromEntityService;
use sales\services\email\EmailService;
use sales\services\TransactionManager;
use Yii;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesSearch;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class CasesController
 *
 * @property CasesCreateService $casesCreateService
 * @property CasesManageService $casesManageService
 * @property CaseCategoryRepository $caseCategoryRepository
 * @property CasesRepository $casesRepository
 * @property CasesCommunicationService $casesCommunicationService
 * @property UserRepository $userRepository,
 * @property CasesSaleRepository $casesSaleRepository
 * @property CasesSaleService $casesSaleService
 * @property ClientUpdateFromEntityService $clientUpdateFromEntityService
 * @property Handler $updateHandler
 * @property SaleTicketService $saleTicketService
 * @property QuoteRepository $quoteRepository
 * @property TransactionManager $transaction
 * @property ClientChatActionPermission $chatActionPermission
 */
class CasesController extends FController
{
    private $casesCreateService;
    private $casesManageService;
    private $casesCommunicationService;
    private $caseCategoryRepository;
    private $casesRepository;
    private $userRepository;
    private $casesSaleRepository;
    private $casesSaleService;
    private $clientUpdateFromEntityService;
    private $updateHandler;
    private $saleTicketService;
    private $quoteRepository;
    private $transaction;
    private $chatActionPermission;

    public function __construct(
        $id,
        $module,
        CasesCreateService $casesCreateService,
        CasesManageService $casesManageService,
        CaseCategoryRepository $caseCategoryRepository,
        CasesRepository $casesRepository,
        CasesCommunicationService $casesCommunicationService,
        UserRepository $userRepository,
        CasesSaleRepository $casesSaleRepository,
        CasesSaleService $casesSaleService,
        ClientUpdateFromEntityService $clientUpdateFromEntityService,
        Handler $updateHandler,
        SaleTicketService $saleTicketService,
        QuoteRepository $quoteRepository,
        TransactionManager $transaction,
        ClientChatActionPermission $chatActionPermission,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->casesCreateService = $casesCreateService;
        $this->casesManageService = $casesManageService;
        $this->caseCategoryRepository = $caseCategoryRepository;
        $this->casesRepository = $casesRepository;
        $this->casesCommunicationService = $casesCommunicationService;
        $this->userRepository = $userRepository;
        $this->casesSaleRepository = $casesSaleRepository;
        $this->casesSaleService = $casesSaleService;
        $this->clientUpdateFromEntityService = $clientUpdateFromEntityService;
        $this->saleTicketService = $saleTicketService;
        $this->updateHandler = $updateHandler;
        $this->quoteRepository = $quoteRepository;
        $this->transaction = $transaction;
        $this->chatActionPermission = $chatActionPermission;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'mark-checked',
                    'view',
                    'ajax-update',
                    'add-sale',
                    'take',
                    'take-over',
                    'create-by-chat',
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

        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $params = Yii::$app->request->queryParams;

        $params['export_type'] = Yii::$app->request->post('export_type');

        $dataProvider = $searchModel->search($params, $user);

        if ($params['export_type']) {
            return $this->render('_search', [
                'model' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'user' => $user,
            ]);
        }
    }

    /**
     * Displays a single Cases model.
     *
     * @param $gid
     * @return string
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws \yii\httpclient\Exception
     */
    public function actionView($gid): string
    {
        $model = $this->findModelByGid((string)$gid);

        if (!Auth::can('cases/view', ['case' => $model])) {
            throw new ForbiddenHttpException('Access denied.');
        }

        /** @var Employee $userModel */
        $userModel = Yii::$app->user->identity;

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
                        Yii::error('Error: Email Message has not been sent to ' . $mail->e_email_to . "\r\n " . $mailResponse['error'], 'CaseController:view:Email:sendMail');
                    } else {
                        //echo '<strong>Email Message</strong> has been successfully sent to <strong>'.$mail->e_email_to.'</strong>'; exit;


                        if ($quoteList = @json_decode($previewEmailForm->e_quote_list)) {
                            if (is_array($quoteList)) {
                                foreach ($quoteList as $quoteId) {
                                    $quoteId = (int)$quoteId;
                                    $quote = Quote::findOne($quoteId);
                                    if ($quote) {
                                        $quote->setStatusSend();
                                        $this->quoteRepository->save($quote);
                                        if (!$this->quoteRepository->save($quote)) {
                                            Yii::error($quote->errors, 'CaseController:view:Email:Quote:save');
                                        }
                                    }
                                }
                            }
                        }

                        Yii::$app->session->setFlash('send-success', '<strong>Email Message</strong> has been successfully sent to <strong>' . $mail->e_email_to . '</strong>');
                    }

                    $this->refresh(); //'#communication-form'
                } else {
                    $previewEmailForm->addError('e_email_subject', VarDumper::dumpAsString($mail->errors));
                    Yii::error(VarDumper::dumpAsString($mail->errors), 'CaseController:view:Email:save');
                }
                //VarDumper::dump($previewEmailForm->attributes, 10, true);              exit;
            }
        }

        $smsEnabled = true;
        if (!$model->project->getParams()->sms->isEnabled()) {
            $smsEnabled = false;
        }

        $previewSmsForm = new CasePreviewSmsForm();
        $previewSmsForm->is_send = false;

        if ($smsEnabled && $previewSmsForm->load(Yii::$app->request->post())) {
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
                                        $quote->setStatusSend();
                                        if (!$this->quoteRepository->save($quote)) {
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

            $isTypeSMS = (int)$comForm->c_type_id === CaseCommunicationForm::TYPE_SMS && $smsEnabled;

            $isTypeEmail = (int)$comForm->c_type_id === CaseCommunicationForm::TYPE_EMAIL;

            if ($isTypeSMS && $model->isDepartmentSupport()) {
                $comForm->scenario = CaseCommunicationForm::SCENARIO_SMS_DEPARTMENT;
            }

            if ($isTypeEmail && $model->isDepartmentSupport()) {
                $comForm->scenario = CaseCommunicationForm::SCENARIO_EMAIL_DEPARTMENT;
            }

            if ($comForm->validate()) {
                $project = $model->project;

                if ($isTypeEmail) {
                    //VarDumper::dump($comForm->quoteList, 10, true); exit;

                    $comForm->c_preview_email = 1;

                    $mailFrom = $userModel->email;

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
                    if ($model->isDepartmentSupport() && $departmentEmail = DepartmentEmailProject::find()->andWhere(['dep_id' => $comForm->dep_email_id])->withEmailList()->one()) {
//                      $mailFrom = $departmentEmail->dep_email;
                        $mailFrom = $departmentEmail->getEmail();
                    } elseif ($model->cs_project_id) {
                        $upp = UserProjectParams::find()->where(['upp_project_id' => $model->cs_project_id, 'upp_user_id' => Yii::$app->user->id])->withEmailList()->one();
                        if ($upp) {
//                            $mailFrom = $upp->upp_email;
                            $mailFrom = $upp->getEmail();
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
                        //$mailSend = $communication->mailSend(7, 'cl_offer', 'test@gmail.com', 'test2@gmail.com', $content_data, $data, 'en-US', 10);


                        //VarDumper::dump($content_data, 10 , true); exit;
                        $content_data = $this->casesCommunicationService->getEmailData($model, $userModel, $comForm->c_language_id);
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
                                $emailBodyHtml = EmailService::prepareEmailBody($mailPreview['data']['email_body_html']);
                                $keyCache = md5($emailBodyHtml);
                                Yii::$app->cacheFile->set($keyCache, $emailBodyHtml, 60 * 60);
                                $previewEmailForm->keyCache = $keyCache;
                                $previewEmailForm->e_email_message = $emailBodyHtml;

                                if (isset($mailPreview['data']['email_subject']) && $mailPreview['data']['email_subject']) {
                                    $previewEmailForm->e_email_subject = $mailPreview['data']['email_subject'];
                                }
                                $previewEmailForm->e_email_from = $mailFrom; //$mailPreview['data']['email_from'];
                                $previewEmailForm->e_email_to = $comForm->c_email_to; //$mailPreview['data']['email_to'];
                                $previewEmailForm->e_email_from_name = $userModel->nickname;
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
                        $previewEmailForm->e_email_from_name = $userModel->nickname;
                        $previewEmailForm->e_email_to_name = $model->client ? $model->client->full_name : '';
                    }
                }


                if ($isTypeSMS) {
                    $comForm->c_preview_sms = 1;

                    /** @var CommunicationService $communication */
                    $communication = Yii::$app->communication;

                    //$data['origin'] = 'ORIGIN';
                    //$data['destination'] = 'DESTINATION';


                    $content_data['message'] = $comForm->c_sms_message;
                    $content_data['project_id'] = $model->cs_project_id;
                    $phoneFrom = '';

                    if ($model->isDepartmentSupport() && $departmentPhone = DepartmentPhoneProject::find()->andWhere(['dpp_id' => $comForm->dpp_phone_id])->withPhoneList()->one()) {
//                      $phoneFrom = $departmentPhone->dpp_phone_number;
                        $phoneFrom = $departmentPhone->getPhone();
                    } elseif ($model->cs_project_id) {
                        $upp = UserProjectParams::find()->where(['upp_project_id' => $model->cs_project_id, 'upp_user_id' => Yii::$app->user->id])->withPhoneList()->one();
                        if ($upp) {
//                            $phoneFrom = $upp->upp_tw_phone_number;
                            $phoneFrom = $upp->getPhone();
                        }
                    }

                    $projectContactInfo = [];

                    if ($project && $project->contact_info) {
                        $projectContactInfo = @json_decode($project->contact_info, true);
                    }

                    $previewSmsForm->s_quote_list = @json_encode([]);

                    if (!$phoneFrom) {
                        $comForm->c_preview_sms = 0;
                        $comForm->addError('c_sms_preview', 'Config Error: Not found phone number for Project Id: ' . $model->cs_project_id . ', agent: "' . $userModel->username . '"');
                    } else {
                        $previewSmsForm->s_phone_to = $comForm->c_phone_number;
                        $previewSmsForm->s_phone_from = $phoneFrom;

                        if ($comForm->c_language_id) {
                            $previewSmsForm->s_language_id = $comForm->c_language_id; //$language;
                        }


                        if ($comForm->c_sms_tpl_id > 0) {
                            $previewSmsForm->s_sms_tpl_id = $comForm->c_sms_tpl_id;

                            //$content_data = []; //$lead->getEmailData2($comForm->quoteList, $projectContactInfo);
                            $content_data = $this->casesCommunicationService->getEmailData($model, $userModel);
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
            }
            //return $this->redirect(['view', 'id' => $model->al_id]);
        } else {
            $comForm->c_type_id = ''; //CommunicationForm::TYPE_VOICE;
        }

        if ($previewEmailForm->is_send || $previewSmsForm->is_send) {
            $comForm->c_preview_email = 0;
            $comForm->c_preview_sms = 0;
        }


        $dataProviderCommunication = $this->getCommunicationDataProvider($model);
        $dataProviderCommunicationLog = $this->getCommunicationLogDataProvider($model);

        if (!Yii::$app->request->isAjax || !Yii::$app->request->get('page')) {
            $pageCount = ceil($dataProviderCommunication->totalCount / $dataProviderCommunication->pagination->pageSize) - 1;
            if ($pageCount < 0) {
                $pageCount = 0;
            }
            $dataProviderCommunication->pagination->page = $pageCount;

            $pageCount = ceil($dataProviderCommunicationLog->totalCount / $dataProviderCommunicationLog->pagination->pageSize) - 1;
            if ($pageCount < 0) {
                $pageCount = 0;
            }
            $dataProviderCommunicationLog->pagination->page = $pageCount;
        }

        // Sale Search
        $saleSearchModel = new SaleSearch();
        $params = Yii::$app->request->queryParams;

        try {
            if (Auth::can('cases/update', ['case' => $model])) {
                $saleDataProvider = $saleSearchModel->search($params);
            } else {
                $saleDataProvider = new ArrayDataProvider();
            }
        } catch (\Exception $exception) {
            $saleDataProvider = new ArrayDataProvider();
            Yii::error(VarDumper::dumpAsString([$exception->getFile(), $exception->getCode(), $exception->getMessage()]), 'SaleController:actionSearch');
            Yii::$app->session->setFlash('error', $exception->getMessage());
        }


        // Sale List
        $csSearchModel = new CaseSaleSearch();
        $params['CaseSaleSearch']['css_cs_id'] = $model->cs_id;
        $csDataProvider = $csSearchModel->searchByCase($params);

        // Lead Search
        $leadSearchModel = new LeadSearch();
        $leadDataProvider = $leadSearchModel->searchByCase($params);

        $casesUpdatePermission = Auth::can('cases/update', ['case' => $model]);
        $modelNote = new CaseNote();
        if ($casesUpdatePermission && $modelNote->load(Yii::$app->request->post())) {
            $modelNote->cn_user_id = Yii::$app->user->id;
            $modelNote->cn_cs_id = $model->cs_id;
            $modelNote->cn_created_dt = date('Y-m-d H:i:s');
            if (!$modelNote->save()) {
                Yii::error('Case id: ' . $model->cs_id . ', ' . VarDumper::dumpAsString($modelNote->errors), 'CaseController:view:CaseNote:save');
            } else {
                $modelNote->cn_text = '';
                $model->updateLastAction();
            }
        }

        $dataProviderNotes = new ActiveDataProvider([
            'query' => CaseNote::find()->where(['cn_cs_id' => $model->cs_id])->orderBy(['cn_id' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $coupons = CouponCase::find()->getByCaseId($model->cs_id)->all();
        $sendCouponForm = new SendCouponsForm($model->cs_id);

        //VarDumper::dump($dataProvider->allModels); exit;

        $fromPhoneNumbers = [];
        if (($department = $model->department) && $params = $department->getParams()) {
            $phoneList = new AvailablePhoneList(Auth::id(), $model->cs_project_id, $department->dep_id, $params->defaultPhoneType);
            foreach ($phoneList->getList() as $phoneItem) {
                $fromPhoneNumbers[$phoneItem['phone']] = $phoneItem['project']
                    . ' ' . ((int)$phoneItem['type_id'] === AvailablePhoneList::GENERAL_ID ? Department::DEPARTMENT_LIST[(int)$phoneItem['department_id']] : AvailablePhoneList::PERSONAL)
                    . ' (' . $phoneItem['phone'] . ')';
            }
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
            'dataProviderCommunicationLog' => $dataProviderCommunicationLog,
            'isAdmin' => $isAdmin,

            'saleSearchModel' => $saleSearchModel,
            'saleDataProvider' => $saleDataProvider,

            'csSearchModel' => $csSearchModel,
            'csDataProvider' => $csDataProvider,

            'leadSearchModel' => $leadSearchModel,
            'leadDataProvider' => $leadDataProvider,

            'modelNote' => $modelNote,
            'dataProviderNotes' => $dataProviderNotes,

            'coupons' => $coupons,
            'sendCouponsForm' => $sendCouponForm,

            'fromPhoneNumbers' => $fromPhoneNumbers,
            'smsEnabled' => $smsEnabled
        ]);
    }

    /**
     * @param Cases $model
     * @return ActiveDataProvider
     */
    private function getCommunicationDataProvider(Cases $model): ActiveDataProvider
    {
        $query1 = (new \yii\db\Query())
            ->select(['e_id AS id', new Expression('"email" AS type'), 'e_case_id AS case_id', 'e_created_dt AS created_dt'])
            ->from('email')
            ->where(['e_case_id' => $model->cs_id]);

        $query2 = (new \yii\db\Query())
            ->select(['s_id AS id', new Expression('"sms" AS type'), 's_case_id AS case_id', 's_created_dt AS created_dt'])
            ->from('sms')
            ->where(['s_case_id' => $model->cs_id]);

//        $query3 = (new \yii\db\Query())
//            ->select(['c_id AS id', new Expression('"voice" AS type'), 'c_case_id AS case_id', 'c_created_dt AS created_dt'])
//            ->from('call')
//            ->where(['c_case_id' => $model->cs_id, 'c_parent_id' => null]);

        $query3 = (new \yii\db\Query())
            ->addSelect(['id' => new Expression('if (c_parent_id IS NULL, c_id, c_parent_id)')])
            ->addSelect([new Expression('"voice" AS type'), 'c_case_id AS case_id', 'MAX(c_created_dt) AS created_dt'])
            ->from('call')
            ->where(['c_case_id' => $model->cs_id])
//            ->addGroupBy(['id', 'case_id', 'created_dt']);
            ->addGroupBy(['id']);

        $query4 = (new \yii\db\Query())
            ->select(['cccs_chat_id AS id', new Expression('"chat" AS type'), 'cccs_case_id AS case_id', 'cccs_created_dt AS created_dt'])
            ->from('{{%client_chat_case}}')
            ->where(['cccs_case_id' => $model->cs_id]);

        $unionQuery = (new \yii\db\Query())
            ->from(['union_table' => $query1->union($query2)->union($query3)->union($query4)])
            ->orderBy(['created_dt' => SORT_ASC]);

        //echo $query1->count(); exit;

        $dataProviderCommunication = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => [
                'pageSize' => 10,
                //'page' => 0
            ],
        ]);

        return $dataProviderCommunication;
    }

    private function getCommunicationLogDataProvider(Cases $model): ActiveDataProvider
    {
        $query1 = (new \yii\db\Query())
            ->select(['e_id AS id', new Expression('"email" AS type'), 'e_case_id AS case_id', 'e_created_dt AS created_dt'])
            ->from('email')
            ->where(['e_case_id' => $model->cs_id]);

        $query2 = (new \yii\db\Query())
            ->select(['s_id AS id', new Expression('"sms" AS type'), 's_case_id AS case_id', 's_created_dt AS created_dt'])
            ->from('sms')
            ->where(['s_case_id' => $model->cs_id]);

        $query3 = (new \yii\db\Query())
            ->select(['id' => new Expression('if (cl_group_id is null, cl_id, cl_group_id)')])
            ->addSelect(['type' => new Expression('"voice"')])
            ->addSelect(['case_id' => 'call_log_case.clc_case_id', 'created_dt' => 'MIN(call_log.cl_call_created_dt)'])
            ->from('call_log_case')
            ->innerJoin('call_log', 'call_log.cl_id = call_log_case.clc_cl_id')
            ->where(['clc_case_id' => $model->cs_id])
            ->andWhere(['call_log.cl_type_id' => [CallLogType::IN,CallLogType::OUT]])
            ->orderBy(['created_dt' => SORT_ASC])
            ->groupBy(['id', 'type', 'case_id']);

        $query4 = (new \yii\db\Query())
            ->select(['cccs_chat_id AS id', new Expression('"chat" AS type'), 'cccs_case_id AS case_id', 'cccs_created_dt AS created_dt'])
            ->from('{{%client_chat_case}}')
            ->where(['cccs_case_id' => $model->cs_id]);

        $unionQuery = (new \yii\db\Query())
            ->from(['union_table' => $query1->union($query2)->union($query3)->union($query4)])
            ->orderBy(['created_dt' => SORT_ASC]);


        return new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
    }

    /**
     * @return array
     */
    public function actionAddSale()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [
            'error' => '',
            'data' => [],
            'locale' => '',
            'marketing_country' => '',
            'updateCaseBookingId' => false,
            'updateCaseBookingHtml' => '',
            'caseBookingId' => ''
        ];

        $gid = Yii::$app->request->post('gid');
        $hash = Yii::$app->request->post('h');

        try {
            $transaction = Yii::$app->db->beginTransaction();
            $model = $this->findModelByGid($gid);

            if (!Auth::can('cases/update', ['case' => $model])) {
                throw new ForbiddenHttpException('Access denied.');
            }

            $arr = explode('|', base64_decode($hash));
            $id = (int)($arr[1] ?? 0);
            $saleData = $this->casesSaleService->detailRequestToBackOffice($id, 0, 120, 1);

            $cs = CaseSale::find()->where(['css_cs_id' => $model->cs_id, 'css_sale_id' => $saleData['saleId']])->limit(1)->one();
            if ($cs) {
                $out['error'] = 'This sale (' . $saleData['saleId'] . ') exist in this Case Id ' . $model->cs_id;
            } else {
                $cs = new CaseSale();
                $cs->css_cs_id = $model->cs_id;
                $cs->css_sale_id = $saleData['saleId'];
                $cs->css_sale_data = $saleData;
                $cs->css_sale_pnr = $saleData['pnr'] ?? null;
                $cs->css_sale_created_dt = $saleData['created'] ?? null;
                $cs->css_sale_book_id = $saleData['bookingId'] ?? null;
                $cs->css_sale_pax = isset($saleData['passengers']) && is_array($saleData['passengers']) ? count($saleData['passengers']) : null;
                $cs->css_sale_data_updated = $cs->css_sale_data;

                $cs = $this->casesSaleService->prepareAdditionalData($cs, $saleData);

                if (!$cs->save()) {
                    Yii::error(VarDumper::dumpAsString($cs->errors) . ' Data: ' . VarDumper::dumpAsString($saleData), 'CasesController:actionAddSale:CaseSale:save');
                    throw new \RuntimeException($cs->getErrorSummary(false)[0]);
                }

                if (empty($model->cs_order_uid)) {
                    $model->cs_order_uid = $cs->css_sale_book_id;
                    $out['caseBookingId'] = $model->cs_order_uid;
                } elseif ($model->cs_order_uid !== $cs->css_sale_book_id) {
                    $out['updateCaseBookingId'] = true;
                    $out['updateCaseBookingHtml'] = $this->renderPartial('sales/_sale_update_case_booking_id', [
                        'caseBookingId' => $model->cs_order_uid,
                        'saleBookingId' => $cs->css_sale_book_id,
                        'caseId' => $model->cs_id,
                        'saleId' => $cs->css_sale_id
                    ]);
                }
                $this->casesRepository->save($model);
                $this->saleTicketService->createSaleTicketBySaleData($cs, $saleData);

                if ($client = $model->client) {
                    $out['locale'] = (string) ClientManageService::setLocaleFromSaleDate($client, $saleData);
                    $out['marketing_country'] = (string) ClientManageService::setMarketingCountryFromSaleDate($client, $saleData);
                }
            }

            $out['data'] = ['sale_id' => $saleData['saleId'], 'gid' => $gid, 'h' => $hash];

            $transaction->commit();
        } catch (\Throwable $exception) {
            $out['error'] = $exception->getMessage();
            \Yii::info(VarDumper::dumpAsString($exception, 10), 'info\CasesController::actionAddSale:Exception');
            $transaction->rollBack();
        }

        return $out;
    }

    public function actionUpdateBookingIdBySale()
    {
        $caseId = Yii::$app->request->post('caseId', 0);
        $saleId = Yii::$app->request->post('saleId', 0);

        $response = [
            'error' => false,
            'message' => '',
            'newCaseBookingId' => ''
        ];

        try {
            $case = $this->casesRepository->find((int)$caseId);
            $sale = $this->casesSaleRepository->getSaleByPrimaryKeys($case->cs_id, (int) $saleId);

            $case->cs_order_uid = $sale->css_sale_book_id;
            $this->casesRepository->save($case);

            $response['message'] = 'Booking Id(' . $case->cs_order_uid . ') of case successfully updated';
            $response['newCaseBookingId'] = $case->cs_order_uid;
        } catch (NotFoundException $e) {
            $response['message'] = $e->getMessage();
            $response['error'] = true;
        } catch (\Throwable $e) {
            $response['message'] = 'Internal error has occurred';
            $response['error'] = true;
            Yii::error(AppHelper::throwableFormatter($e), 'CasesController:actionUpdateBookingIdBySale:Throwable');
        }

        return $this->asJson($response);
    }

    /**
     * @return array
     */
    public function actionAssignLead()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['error' => '', 'data' => []];

        $gid = Yii::$app->request->post('gid');
        $lead_gid = Yii::$app->request->post('lead_gid');

        try {
            $model = $this->findModelByGid($gid);
            $lead = $this->findLeadModel($lead_gid);

            if ($model->cs_lead_id != $lead->id) {
                $model->cs_lead_id = $lead->id;
                if (!$model->update()) {
                    Yii::error(VarDumper::dumpAsString($model->errors), 'CasesController:actionAssignLead:Case:save');
                } else {
                    $model->updateLastAction();
                }
            }

            $out['data'] = ['lead_id' => $lead->id, 'gid' => $gid];
        } catch (\Throwable $exception) {
            $out['error'] = $exception->getMessage();
        }

        return $out;
    }

    /**
     * @param $gid
     * @return Lead|null
     * @throws NotFoundHttpException
     */
    protected function findLeadModel($gid): ?Lead
    {
        if (($model = Lead::findOne(['gid' => $gid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested Lead does not exist.');
    }

    /**
     * @return string|yii\web\Response
     */
    public function actionCreate()
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $form = new CasesCreateByWebForm($user);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                /** @var Cases $case */
                $case = $this->casesCreateService->createByWeb($form, $user->id);
                $this->casesManageService->processing($case->cs_id, Yii::$app->user->id, Yii::$app->user->id);
                Yii::$app->session->setFlash('success', 'Case created');
                return $this->redirect(['view', 'gid' => $case->cs_gid]);
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                Yii::error($e, 'Case:Create:Web');
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    public function actionCreateByChat()
    {
        if (!(Yii::$app->request->isAjax || Yii::$app->request->isPjax)) {
            throw new BadRequestHttpException('Bad request.');
        }

        $chatId = (int)Yii::$app->request->get('chat_id');
        $chat = ClientChat::findOne(['cch_id' => $chatId]);

        if (!$chat) {
            throw new NotFoundHttpException('Client chat not found.');
        }

        if (!$this->chatActionPermission->canCreateCase($chat)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $user = Auth::user();
        $form = new CasesCreateByChatForm($user, $chat);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                /** @var Cases $case */
                $case = $this->transaction->wrap(function () use ($form, $chat, $user) {
                    $case = $this->casesCreateService->createByChat($form, $chat, $user->id);
                    $this->casesManageService->processing($case->cs_id, $user->id, $user->id);
                    return $case;
                });
                return "<script> $('#modal-md').modal('hide');refreshChatInfo('" . $chat->cch_id . "')</script>";
            } catch (\Throwable $e) {
                Yii::error(AppHelper::throwableFormatter($e), 'CasesController:actionCreateByChat');
                return "<script> $('#modal-md').modal('hide');createNotify('Create Case', '" . $e->getMessage() . "', 'error');</script>";
            }
        }

        return $this->renderAjax('create_by_chat', [
            'model' => $form,
        ]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionCreateValidation(): array
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $form = new CasesCreateByWebForm($user);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }


    /**
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionCheckPhoneForExistence(): array
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException('Access denied', 10);
        }

        $response = [];
        $clientPhone = Yii::$app->request->post('clientPhone');
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($clientPhone && $cases = $this->casesRepository->getOpenCasesByPhone($clientPhone)) {
            $casesLink = '';
            foreach ($cases as $case) {
                $casesLink .= Html::a('Case ' . $case->cs_id, '/cases/view/' . $case->cs_gid, ['target' => '_blank']) . ' ';
            }
            $response['clientPhoneResponse'] = 'This number is already used in ' . $casesLink;
        }

        return $response;
    }

    /**
     * @param $id
     * @return string
     */
    public function actionGetCategories($id): string
    {
        $id = (int)$id;
        $str = '';
        if ($categories = $this->caseCategoryRepository->getEnabledByDep($id)) {
            $str .= '<option>Choose a category</option>';
            foreach ($categories as $category) {
                $str .= '<option value="' . Html::encode($category->cc_id) . '">' . Html::encode($category->cc_name) . '</option>';
            }
        } else {
            $str = '<option>-</option>';
        }
        return $str;
    }

    /**
     * @param $gid
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionTake($gid): Response
    {
        $case = $this->findModelByGid((string)$gid);

        if (!Auth::can('cases/take', ['case' => $case])) {
            throw new ForbiddenHttpException('Access denied.');
        }

        try {
            $user = $this->userRepository->find(Auth::id());
            $this->casesManageService->take($case->cs_id, $user->id, $user->id);
            Yii::$app->session->setFlash('success', 'Success');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
    }

    /**
     * @param $gid
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionTakeOver($gid): Response
    {
        $case = $this->findModelByGid((string)$gid);

        if (!Auth::can('cases/takeOver', ['case' => $case])) {
            throw new ForbiddenHttpException('Access denied.');
        }

        try {
            $user = $this->userRepository->find(Auth::id());
            $this->casesManageService->takeOver($case->cs_id, $user->id, $user->id);
            Yii::$app->session->setFlash('success', 'Success');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
    }

    /**
     * Finds the Cases model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Cases
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Cases
    {
        if (($model = Cases::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $gid
     * @return Cases
     * @throws NotFoundHttpException
     */
    protected function findModelByGid($gid): Cases
    {
        if (($model = Cases::findOne(['cs_gid' => $gid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested case does not exist.');
    }

    /**
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionChangeStatus()
    {
        $gid = (string)Yii::$app->request->get('gid');
        $case = $this->findModelByGid($gid);
        $user = Auth::user();

        $statusForm = new CasesChangeStatusForm($case, $user);

        if (Yii::$app->request->isAjax && $statusForm->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($statusForm);
        }

        if ($statusForm->load(Yii::$app->request->post()) && $statusForm->validate()) {
            try {
                if ($user->isSimpleAgent() && empty($case->cs_category_id)) {
                    throw new \DomainException('Status of a case without a category cannot be changed!');
                }

                if ($case->isTrash() && !Auth::can('cases/take_Trash', ['case' => $case])) {
                    throw new \DomainException('Access denied, permission "cases/take_Trash" failed.');
                }

                switch ((int)$statusForm->statusId) {
                    case CasesStatus::STATUS_FOLLOW_UP:
                        $this->casesManageService->followUp($case->cs_id, $user->id, $statusForm->message, $statusForm->getConvertedDeadline());
                        break;
                    case CasesStatus::STATUS_TRASH:
                        $this->casesManageService->trash($case->cs_id, $user->id, $statusForm->message);
                        break;
                    case CasesStatus::STATUS_SOLVED:
                        $this->casesManageService->solved($case->cs_id, $user->id, $statusForm->message);
                        if ($statusForm->isSendFeedback()) {
                            $this->sendFeedbackEmailProcess($case, $statusForm, Auth::user());
                        }
                        break;
                    case CasesStatus::STATUS_PENDING:
                        $this->casesManageService->pending($case->cs_id, $user->id, $statusForm->message);
                        break;
                    case CasesStatus::STATUS_PROCESSING:
                        $this->casesManageService->processing($case->cs_id, $statusForm->userId, $user->id, $statusForm->message);
                        break;
                    default:
                        Yii::$app->session->setFlash('error', 'Undefined status');
                        return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
                }

                Yii::$app->session->addFlash('success', 'Case Status changed successfully ("' . CasesStatus::getName($statusForm->statusId) . '")');
            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', 'Server error');
                Yii::error($e, 'CasesController:actionChangeStatus');
            }

            return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
        }

        if (!$statusForm->isResendFeedbackEnable()) {
            $statusForm->resendFeedbackForm = true;
        }

        return $this->renderAjax('partial/_change_status', [
            'statusForm' => $statusForm,
        ]);
    }

    private function sendFeedbackEmailProcess(Cases $case, CasesChangeStatusForm $form, Employee $user): void
    {
        if (!$project = $case->project) {
            return;
        }
        if (!$params = $project->getParams()) {
            return;
        }

        $content = $this->casesCommunicationService->getEmailData($case, $user);

        try {
            $mailPreview = Yii::$app->communication->mailPreview(
                $case->cs_project_id,
                $customData->object->case->feedbackTemplateTypeKey,
                $customData->object->case->feedbackEmailFrom,
                $form->sendTo,
                $content,
                $form->language
            );

            if ($mailPreview['error'] !== false) {
                throw new \DomainException($mailPreview['error']);
            }

            $this->sendFeedbackEmail(
                $customData,
                $case,
                $form,
                $user,
                $mailPreview['data']['email_subject'],
                $mailPreview['data']['email_body_html']
            );
        } catch (\Throwable $e) {
            Yii::$app->session->addFlash('error', 'Send email error: ' . $e->getMessage());
            return;
        }

        Yii::$app->session->addFlash('success', 'Email has been successfully sent.');
    }

    private function sendFeedbackEmail(
        \sales\model\project\entity\params\Params $params,
        Cases $case,
        CasesChangeStatusForm $form,
        Employee $user,
        $subject,
        $body
    ): void {
        $mail = new Email();
        $mail->e_project_id = $case->cs_project_id;
        $mail->e_case_id = $case->cs_id;
        $templateTypeId = EmailTemplateType::find()
            ->select(['etp_id'])
            ->andWhere(['etp_key' => $customData->object->case->feedbackTemplateTypeKey])
            ->asArray()
            ->one();
        if ($templateTypeId) {
            $mail->e_template_type_id = $templateTypeId['etp_id'];
        }
        $mail->e_type_id = Email::TYPE_OUTBOX;
        $mail->e_status_id = Email::STATUS_PENDING;
        $mail->e_email_subject = $subject;
        $mail->body_html = $body;
        $mail->e_email_from = $customData->object->case->feedbackEmailFrom;
        $mail->e_email_from_name = $customData->object->case->feedbackNameFrom ?: $user->nickname;
        $mail->e_email_to_name = $case->client ? $case->client->full_name : '';
        $mail->e_language_id = $form->language;
        $mail->e_email_to = $form->sendTo;
        $mail->e_created_dt = date('Y-m-d H:i:s');
        $mail->e_created_user_id = $user->id;

        if (!$mail->save()) {
            throw new \DomainException(VarDumper::dumpAsString($mail->getErrors()));
        }

        $mail->e_message_id = $mail->generateMessageId();
        $mail->save();
        $mailResponse = $mail->sendMail();

        if ($mailResponse['error'] !== false) {
            throw new \DomainException('Email(Id: ' . $mail->e_id . ') has not been sent.');
        }
    }

    /**
     * @return string
     */
    public function actionStatusHistory()
    {
        $caseGId = Yii::$app->request->get('gid');
        $case = $this->casesRepository->findByGid($caseGId);
        $searchModel = new CaseStatusLogSearch();

        $params = Yii::$app->request->queryParams;
        $params['CaseStatusLogSearch']['csl_case_id'] = $case->cs_id;

        $dataProvider = $searchModel->searchByCase($params);

        return $this->renderAjax('partial/_status_history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionAddPhone()
    {
        $gid = (string)Yii::$app->request->get('gid');
        $case = $this->findModelByGid($gid);

        $form = new CasesAddPhoneForm($case);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->clientUpdateFromEntityService->addPhoneFromCase($case, $form);
                Yii::$app->session->setFlash('success', 'Added new Phone ("' . $form->phone . '")');
                return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
            } catch (\DomainException $e) {
                $form->addError('phone', $e->getMessage());
            }
        }

        return $this->renderAjax('partial/_add_phone', [
            'model' => $form,
        ]);
    }

//    public function actionAddPhone()
//    {
//        $gid = (string)Yii::$app->request->get('gid');
//        $case = $this->findModelByGid($gid);
//        $form = new CasesAddPhoneForm($case);
//
//        try {
//            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
//
//                if($case->client) {
//                    $existClientPhone = ClientPhone::find()->where(['client_id' => $case->client->id, 'phone' => $form->phone])->exists();
//                    if($existClientPhone) {
//                        Yii::$app->session->setFlash('warning', 'This phone already exists ("' . $form->phone . '"), Client Id: '.$case->client->id);
//                    } else {
//                        $clientPhone = new ClientPhone();
//                        $clientPhone->client_id = $case->client->id;
//                        $clientPhone->phone = $form->phone;
//                        if($clientPhone->save()) {
//                            Yii::$app->session->setFlash('success', 'Added new Phone ("' . $form->phone . '")');
//                        } else {
//                            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($clientPhone->errors));
//                        }
//                    }
//                } else {
//                    Yii::$app->session->setFlash('warning', 'Client not found (Client Id: '.$case->cs_client_id.')');
//                }
//                return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
//            }
//
//        } catch (\Throwable $exception) {
//            $form->addError('phone', $exception->getMessage());
//        }
//
//        return $this->renderAjax('partial/_add_phone', [
//            'model' => $form,
//        ]);
//    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionAddEmail()
    {
        $gid = (string)Yii::$app->request->get('gid');
        $case = $this->findModelByGid($gid);

        $form = new CasesAddEmailForm($case);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->clientUpdateFromEntityService->addEmailFromCase($case, $form);
                Yii::$app->session->setFlash('success', 'Added new Email ("' . $form->email . '")');
                return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
            } catch (\DomainException $e) {
                $form->addError('email', $e->getMessage());
            }
        }

        return $this->renderAjax('partial/_add_email', [
            'model' => $form,
        ]);
    }

//    /**
//     * @return string|Response
//     * @throws NotFoundHttpException
//     */
//    public function actionAddEmail()
//    {
//        $gid = (string)Yii::$app->request->get('gid');
//        $case = $this->findModelByGid($gid);
//
//        $form = new CasesAddEmailForm($case);
//
//        try {
//            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
//
//                if($case->client) {
//                    $existClientEmail = ClientEmail::find()->where(['client_id' => $case->client->id, 'email' => $form->email])->exists();
//                    if($existClientEmail) {
//                        Yii::$app->session->setFlash('warning', 'This email already exists ("' . $form->email . '"), Client Id: '.$case->client->id);
//                    } else {
//                        $clientEmail = new ClientEmail();
//                        $clientEmail->client_id = $case->client->id;
//                        $clientEmail->email = $form->email;
//                        if($clientEmail->save()) {
//                            Yii::$app->session->setFlash('success', 'Added new Email ("' . $form->email . '")');
//                        } else {
//                            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($clientEmail->errors));
//                        }
//                    }
//                } else {
//                    Yii::$app->session->setFlash('warning', 'Client not found (Client Id: '.$case->cs_client_id.')');
//                }
//                return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
//            }
//
//        } catch (\Throwable $exception) {
//            $form->addError('email', $exception->getMessage());
//        }
//
//        return $this->renderAjax('partial/_add_email', [
//            'model' => $form,
//        ]);
//    }

    public function actionClientUpdateValidation(): array
    {
        try {
            $case = $this->findModelByGid((string) Yii::$app->request->get('gid'));
            $form = new CasesClientUpdateForm($case);

            if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($form);
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'CasesController:actionClientUpdateValidation');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionClientUpdate()
    {
        $gid = (string)Yii::$app->request->get('gid');
        $case = $this->findModelByGid($gid);

        if (!$client = $case->client) {
            throw new NotFoundHttpException('The requested client does not exist.');
        }

        $form = new CasesClientUpdateForm($case);

        if (Yii::$app->request->isPost) {
            $response = ['error' => true, 'message' => ''];
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                try {
                    $this->clientUpdateFromEntityService->updateClientFromCase($case, $form);
                    $response['error'] = false;
                    $response['message'] = 'Client information has been updated successfully.';
                } catch (\Throwable $throwable) {
                    $response['message'] = $throwable->getMessage();
                }
            } else {
                $response['message'] = $this->getParsedErrors($form->getErrors());
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }

        return $this->renderAjax('partial/_client_update', [
            'model' => $form,
        ]);
    }

    /**
     * @return string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAjaxUpdate()
    {
        $case = $this->findModelByGid((string)Yii::$app->request->get('gid'));

        if (!Auth::can('cases/update', ['case' => $case])) {
            throw new ForbiddenHttpException('Access denied.');
        }

        $form = new UpdateInfoForm(
            $case,
            ArrayHelper::map($this->caseCategoryRepository->getEnabledByDep($case->cs_dep_id), 'cc_id', 'cc_name')
        );

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->updateHandler->handle($form->getDto());
                Yii::$app->session->setFlash('success', 'Case information has been updated successfully.');
            } catch (\Throwable $exception) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($exception));
            }
            return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
        }

        return $this->renderAjax('partial/_case_update', [
            'model' => $form,
        ]);
    }

    /**
     * @param $caseId
     * @param $caseSaleId
     * @return array|string
     */
    public function actionAjaxSaleListEditInfo($caseId, $caseSaleId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [
            'output' => '',
            'message' => '',
            'caseId' => $caseId,
            'caseSaleId' => $caseSaleId
        ];
        try {
            $user = Yii::$app->user->identity;

            if (
                Yii::$app->request->isAjax &&
                Yii::$app->request->isPost &&
                Yii::$app->request->post('cssSaleData')
            ) {
                $caseSale = $this->casesSaleRepository->getSaleByPrimaryKeys((int)$caseId, (int)$caseSaleId);
                $this->checkAccessToManageCaseSaleInfo($caseSale);

                $form = new CasesSaleForm($caseSale, $this->casesSaleService);

                if ($form->load(Yii::$app->request->post(), 'cssSaleData') && $form->validate()) {
                    $decodedSaleData = JsonHelper::decode($form->caseSale->css_sale_data_updated);

                    $difference = $this->casesSaleService->compareSaleData($decodedSaleData, $form->validatedData);
                    if (!$difference) {
                        throw new \RuntimeException('Cannot save because value has not been changed');
                    }

                    $this->casesSaleRepository->updateSaleData($caseSale, $decodedSaleData, $form->validatedData);

                    $sync = !$this->casesSaleService->isDataBackedUpToOriginal($caseSale);
                    $this->casesSaleRepository->updateSyncWithBOField($caseSale, $sync);

                    if (!$caseSale->save()) {
                        Yii::error(VarDumper::dumpAsString($caseSale->errors), 'CasesController:actionAjaxSaleListEditInfo:CaseSale:save');
                        throw new \RuntimeException('Unsuccessful update');
                    }

                    if ($sync) {
                        $out['success_message'] = 'Sale: ' . $caseSaleId . '; Now, you can sync data with b/o';
                    } else {
                        $out['success_message'] = 'Sale: ' . $caseSaleId . '; The data has been returned to its original form.';
                    }

                    $out['sync'] = $sync;
                } else {
                    $out['message'] = implode("; ", $form->getErrorSummary(false));
                }
            }
        } catch (\RuntimeException $exception) {
            $out['message'] = $exception->getMessage();
        } catch (\Throwable $exception) {
            $out['message'] = $exception->getMessage();
            Yii::error($exception->getMessage() . '; File: ' . $exception->getFile() . '; On Line: ' . $exception->getLine(), 'CasesController:actionAjaxSaleListEditInfo:catch:Throwable');
        }

        return $out;
    }

    /**
     * @param $caseId
     * @param $caseSaleId
     * @return array|mixed
     * @throws BadRequestHttpException
     */
    public function actionAjaxSyncWithBackOffice($caseId, $caseSaleId)
    {
        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $out = [
                'error' => 0,
                'message' => ''
            ];

            $caseSale = $this->casesSaleRepository->getSaleByPrimaryKeys((int)$caseId, (int)$caseSaleId);
            $this->checkAccessToManageCaseSaleInfo($caseSale);

            $updatedData = $this->casesSaleService->prepareSaleData($caseSale);
            $updatedData['sale_id'] = $caseSaleId;

            $response = BackOffice::sendRequest2('cs/update-passengers', $updatedData, 'POST', 90);
            if ($response->isOk) {
                $responseResult = json_decode($response->content, true)['results'];

                $error = [];
                foreach ($responseResult as $key => $result) {
                    if ($result['success'] === false) {
                        $error[$key] = $result;
                    }
                }

                if (!empty($error)) {
                    $out['errorHtml'] =  \yii\bootstrap4\Alert::widget([
                        'options' => [
                            'class' => 'alert-danger'
                        ],
                        'body' => $this->renderAjax('/sale/partial/_sale_info_errors', [
                            'errors' => $error
                        ])
                    ]);
                    $out['message'] = 'Errors occurred while syncing sales data with B/O; See error dump;';
                    $out['error']  = 1;
                } else {
                    $this->casesSaleRepository->updateSyncWithBOField($caseSale, false);
                    $this->casesSaleRepository->updateOriginalSaleData($caseSale);
                    $this->casesSaleRepository->save($caseSale);

                    $out['message'] = 'Sale: ' . $caseSaleId . ' data was successfully synchronized with b/o.';
                }
            } else {
                $out['error'] = 1;
                $out['message'] = 'BO request Error: ' . (json_decode($response->content, true)['message'] ?? '');
            }
        } catch (\Throwable $throwable) {
            $out['error'] = 1;
            $out['message'] = 'An internal Sales error has occurred; Check system logs;';
            if ($throwable->getCode() < 0 && $throwable->getCode() > -4) {
                $out['message'] = $throwable->getMessage();
            }
            Yii::error(
                \yii\helpers\VarDumper::dumpAsString($throwable),
                'CaseController:actionAjaxSyncWithBackOffice:catch:Throwable'
            );
        }

        return $out;
    }

    /**
     * @param CaseSale $caseSale
     * @param bool $isRefresh
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    private function checkAccessToManageCaseSaleInfo(CaseSale $caseSale, bool $isRefresh = false): bool
    {
        $caseGuard = Yii::createObject(CaseManageSaleInfoGuard::class);
        $canManageSaleInfo = $caseGuard->canManageSaleInfo(
            $caseSale,
            Yii::$app->user->identity,
            JsonHelper::decode($caseSale->css_sale_data)['passengers'] ?? [],
            $isRefresh
        );

        if ($canManageSaleInfo) {
            throw new \DomainException($canManageSaleInfo, -3);
        }

        return true;
    }

    /**
     * @param $caseId
     * @param $caseSaleId
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionAjaxRefreshSaleInfo($caseId, $caseSaleId): Response
    {
        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPost) {
            throw new BadRequestHttpException();
        }

        $withFareRules = Yii::$app->request->post('check_fare_rules', 0);

        $out = [
            'error' => 0,
            'message' => '',
            'locale' => '',
            'marketing_country' => '',
        ];

        try {
            $case = $this->casesRepository->find((int)$caseId);
            $caseSale = $this->casesSaleRepository->getSaleByPrimaryKeys((int)$caseId, (int)$caseSaleId);
            $this->checkAccessToManageCaseSaleInfo($caseSale, true);

            $saleData = $this->casesSaleService->detailRequestToBackOffice((int)$caseSale->css_sale_id, $withFareRules, 120, 1);
            $caseSale = $this->casesSaleService->refreshOriginalSaleData($caseSale, $case, $saleData);
            $this->saleTicketService->refreshSaleTicketBySaleData((int)$caseId, $caseSale, $saleData);

            if ($client = $case->client) {
                $out['locale'] = (string) ClientManageService::setLocaleFromSaleDate($client, $saleData);
                $out['marketing_country'] = (string) ClientManageService::setMarketingCountryFromSaleDate($client, $saleData);
            }

            $out['message'] = 'Sale info: ' . $caseSale->css_sale_id . ' successfully refreshed';
        } catch (\Throwable $throwable) {
            $out['error'] = 1;
            $out['message'] = 'An internal Sales error has occurred; Check system logs;';
            if ($throwable->getCode() <= 0 && $throwable->getCode() > -4) {
                $out['message'] = $throwable->getMessage();
            }
            Yii::error(
                \yii\helpers\VarDumper::dumpAsString($throwable->getMessage(), 20),
                'CaseController:actionAjaxRefreshSaleInfo:Throwable'
            );
        }

        return $this->asJson($out);
    }

    /**
     * @param $gid
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionMarkChecked($gid): Response
    {
        $model = $this->findModelByGid((string)$gid);

        if (!Auth::can('cases/view_Checked', ['case' => $model])) {
            throw new ForbiddenHttpException('Access denied.');
        }

        try {
            $this->casesManageService->markChecked($model->cs_id);
            Yii::$app->session->setFlash('success', 'Success');
        } catch (\DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            Yii::error($e, 'CasesController:Mark');
            Yii::$app->session->setFlash('error', 'Server error');
        }

        return $this->redirect(['/cases/view', 'gid' => $model->cs_gid]);
    }
}
