<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\components\CommunicationService;
use common\models\CaseNote;
use common\models\CaseSale;
use common\models\ClientEmail;
use common\models\ClientPhone;
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
use frontend\models\CaseCommunicationForm;
use frontend\models\CasePreviewEmailForm;
use frontend\models\CasePreviewSmsForm;
use sales\auth\Auth;
use sales\entities\cases\CasesStatus;
use sales\entities\cases\CaseStatusLogSearch;
use sales\forms\cases\CasesAddEmailForm;
use sales\forms\cases\CasesAddPhoneForm;
use sales\forms\cases\CasesChangeStatusForm;
use sales\forms\cases\CasesClientUpdateForm;
use sales\forms\cases\CasesCreateByWebForm;
use sales\forms\cases\CasesSaleForm;
use sales\model\cases\useCases\cases\updateInfo\UpdateInfoForm;
use sales\guards\cases\CaseManageSaleInfoGuard;
use sales\guards\cases\CaseTakeGuard;
use sales\model\cases\useCases\cases\updateInfo\Handler;
use sales\repositories\cases\CaseCategoryRepository;
use sales\repositories\cases\CasesRepository;
use sales\repositories\cases\CasesSaleRepository;
use sales\repositories\client\ClientEmailRepository;
use sales\services\cases\CasesSaleService;
use sales\services\cases\CasesCommunicationService;
use sales\repositories\user\UserRepository;
use sales\services\cases\CasesCreateService;
use sales\services\cases\CasesManageService;
use sales\services\client\ClientUpdateFromEntityService;
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
 * @property CaseTakeGuard $caseTakeGuard
 * @property Handler $updateHandler
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
    private $caseTakeGuard;
    private $updateHandler;

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
        CaseTakeGuard $caseTakeGuard,
        Handler $updateHandler,
        $config = []
    )
    {
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
        $this->caseTakeGuard = $caseTakeGuard;
        $this->updateHandler = $updateHandler;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'mark-checked',
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

        $dataProvider = $searchModel->search($params, $user);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user' => $user
        ]);
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

        $model = $this->findModelByGid($gid);
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

                    $this->refresh(); //'#communication-form'

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

            $isTypeSMS = (int)$comForm->c_type_id === CaseCommunicationForm::TYPE_SMS;

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
//						$mailFrom = $departmentEmail->dep_email;
						$mailFrom = $departmentEmail->getEmail();
					} else if ($model->cs_project_id) {
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
                        $content_data = $this->casesCommunicationService->getEmailData($model, $userModel);
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
                                $previewEmailForm->e_email_from_name = $userModel->username;
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
                        $previewEmailForm->e_email_from_name = $userModel->username;
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

//						$phoneFrom = $departmentPhone->dpp_phone_number;
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

        if (!Yii::$app->request->isAjax || !Yii::$app->request->get('page')) {
            $pageCount = ceil($dataProviderCommunication->totalCount / $dataProviderCommunication->pagination->pageSize) - 1;
            if ($pageCount < 0) {
                $pageCount = 0;
            }
            $dataProviderCommunication->pagination->page = $pageCount;
        }

        // Sale Search
        $saleSearchModel = new SaleSearch();
        $params = Yii::$app->request->queryParams;

        try {
            $saleDataProvider = $saleSearchModel->search($params);
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


        $modelNote = new CaseNote();
        if ($modelNote->load(Yii::$app->request->post())) {
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

        //VarDumper::dump($dataProvider->allModels); exit;



        $enableCommunication = true;
        $isAdmin = true;

        return $this->render('view', [
            'model' => $model,
            'previewEmailForm' => $previewEmailForm,
            'previewSmsForm' => $previewSmsForm,
            'comForm' => $comForm,
            'enableCommunication' => $enableCommunication,
            'dataProviderCommunication' => $dataProviderCommunication,
            'isAdmin' => $isAdmin,

            'saleSearchModel' => $saleSearchModel,
            'saleDataProvider' => $saleDataProvider,

            'csSearchModel' => $csSearchModel,
            'csDataProvider' => $csDataProvider,

            'leadSearchModel' => $leadSearchModel,
            'leadDataProvider' => $leadDataProvider,

            'modelNote' => $modelNote,
            'dataProviderNotes' => $dataProviderNotes,
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

        return $dataProviderCommunication;
    }

    /**
     * @return array
     */
    public function actionAddSale()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['error' => '', 'data' => []];

        $gid = Yii::$app->request->post('gid');
        $hash = Yii::$app->request->post('h');

        try {
            $model = $this->findModelByGid($gid);

            $arr = explode('|', base64_decode($hash));
            $id = (int)($arr[1] ?? 0);
            $saleData = $this->casesSaleService->detailRequestToBackOffice($id);

            $cs = CaseSale::find()->where(['css_cs_id' => $model->cs_id, 'css_sale_id' => $saleData['saleId']])->limit(1)->one();
            if($cs) {
                $out['error'] = 'This sale ('.$saleData['saleId'].') exist in this Case Id '.$model->cs_id;
            } else {
                $cs = new CaseSale();
                $cs->css_cs_id = $model->cs_id;
                $cs->css_sale_id = $saleData['saleId'];
                $cs->css_sale_data = json_encode($saleData);
                $cs->css_sale_pnr = $saleData['pnr'] ?? null;
                $cs->css_sale_created_dt = $saleData['created'] ?? null;
                $cs->css_sale_book_id = $saleData['bookingId'] ?? null;
                $cs->css_sale_pax = isset($saleData['passengers']) && is_array($saleData['passengers']) ? count($saleData['passengers']) : null;
                $cs->css_sale_data_updated = $cs->css_sale_data;

                $cs = $this->casesSaleService->prepareAdditionalData($cs, $saleData);

                if(!$cs->save()) {
                    Yii::error(VarDumper::dumpAsString($cs->errors). ' Data: ' . VarDumper::dumpAsString($saleData), 'CasesController:actionAddSale:CaseSale:save');
                } else {
                    $model->updateLastAction();
                }
            }

            $out['data'] = ['sale_id' => $saleData['saleId'], 'gid' => $gid, 'h' => $hash];

        } catch (\Throwable $exception) {
            $out['error'] = $exception->getMessage();
        }

        return $out;
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

            if($model->cs_lead_id != $lead->id) {
                $model->cs_lead_id = $lead->id;
                if(!$model->update()) {
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
                $case = $this->casesCreateService->createByWeb($form, $user->id);
                $this->casesManageService->processing($case->cs_id, Yii::$app->user->id, Yii::$app->user->id);
                Yii::$app->session->setFlash('success', 'Case created');
                return $this->redirect(['view', 'gid' => $case->cs_gid]);
            } catch (\Throwable $e){
                Yii::$app->session->setFlash('error', $e->getMessage());
                Yii::error($e, 'Case:Create:Web');
            }
        }
        return $this->render('create', [
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
        if ($categories = $this->caseCategoryRepository->getAllByDep($id)) {
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
     * @throws NotFoundHttpException
     */
    public function actionTake($gid): Response
    {
        $gId = (string) $gid;
        $userId = Yii::$app->user->id;
        $case = $this->findModelByGid($gId);
        try {
            $this->caseTakeGuard->guard($case);
            $user = $this->userRepository->find($userId);
            $this->casesManageService->take($case->cs_id, $user->id, $user->id);
            Yii::$app->session->setFlash('success', 'Success');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            //Yii::error($e, 'Cases:CasesController:Take');
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

                switch ((int)$statusForm->statusId) {
                    case CasesStatus::STATUS_FOLLOW_UP :
                        $this->casesManageService->followUp($case->cs_id, $user->id, $statusForm->message, $statusForm->getConvertedDeadline());
                        break;
                    case CasesStatus::STATUS_TRASH :
                        $this->casesManageService->trash($case->cs_id, $user->id, $statusForm->message);
                        break;
                    case CasesStatus::STATUS_SOLVED :
                        $this->casesManageService->solved($case->cs_id, $user->id, $statusForm->message);
                        break;
                    case CasesStatus::STATUS_PENDING :
                        $this->casesManageService->pending($case->cs_id, $user->id, $statusForm->message);
                        break;
                    case CasesStatus::STATUS_PROCESSING :
                        $this->casesManageService->processing($case->cs_id, $statusForm->userId, $user->id, $statusForm->message);
                        break;
                    default:
                        Yii::$app->session->setFlash('error', 'Undefined status');
                        return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
                }

                Yii::$app->session->setFlash('success', 'Case Status changed successfully ("' . CasesStatus::getName($statusForm->statusId) . '")');

            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', 'Server error');
                Yii::error($e, 'CasesController:actionChangeStatus');
            }

            return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
        }

        return $this->renderAjax('partial/_change_status', [
            'statusForm' => $statusForm,
        ]);
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

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionClientUpdate()
    {
        $gid = (string)Yii::$app->request->get('gid');
        $case = $this->findModelByGid($gid);

        if (!$client = $case->client) {
            throw new NotFoundHttpException('The requested client does not exist.');
        }

        $form = new CasesClientUpdateForm($case);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->clientUpdateFromEntityService->updateClientFromCase($case, $form);
                Yii::$app->session->setFlash('success', 'Client information has been updated successfully.');
            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
            return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
        }


//        try {
//            if ($form->load(Yii::$app->request->post())) {
//                if($form->validate()) {
//                    if ($client = $case->client) {
//                        $client->first_name = $form->first_name;
//                        $client->last_name = $form->last_name;
//                        $client->middle_name = $form->middle_name;
//
//                        if ($client->save()) {
//                            Yii::$app->session->setFlash('success', 'Client information has been updated successfully.');
//                        } else {
//                            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($client->errors));
//                        }
//
//                    } else {
//                        Yii::$app->session->setFlash('warning', 'Client not found (Client Id: ' . $case->cs_client_id . ')');
//                    }
//                    return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
//                }
//            } else {
//                if($client = $case->client) {
//                    $form->first_name = $client->first_name;
//                    $form->last_name = $client->last_name;
//                    $form->middle_name = $client->middle_name;
//                }
//            }
//
//        } catch (\Throwable $exception) {
//            $form->addError('first_name', $exception->getMessage());
//        }

        return $this->renderAjax('partial/_client_update', [
            'model' => $form,
        ]);
    }



    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionAjaxUpdate()
    {
        $case = $this->findModelByGid((string)Yii::$app->request->get('gid'));

        $form = new UpdateInfoForm(
            $case,
            ArrayHelper::map($this->caseCategoryRepository->getAllByDep($case->cs_dep_id), 'cc_id', 'cc_name')
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

			if (Yii::$app->request->isAjax &&
				Yii::$app->request->isPost &&
				Yii::$app->request->post('cssSaleData')) {

				$caseSale = $this->casesSaleRepository->getSaleByPrimaryKeys((int)$caseId, (int)$caseSaleId);
				$this->checkAccessToManageCaseSaleInfo($caseSale);

				$form = new CasesSaleForm($caseSale, $this->casesSaleService);

				if ($form->load(Yii::$app->request->post(), 'cssSaleData') && $form->validate()) {
					$decodedSaleData = json_decode( (string)($form->caseSale->css_sale_data_updated), true );

					$difference = $this->casesSaleService->compareSaleData($decodedSaleData, $form->validatedData);
					if (!$difference) {
						throw new \Exception('Cannot save because value has not been changed');
					}

					$this->casesSaleRepository->updateSaleData($caseSale, $decodedSaleData, $form->validatedData);

					$sync = !$this->casesSaleService->isDataBackedUpToOriginal($caseSale);
					$this->casesSaleRepository->updateSyncWithBOField($caseSale, $sync);

					if (!$caseSale->save()) {
						Yii::error(VarDumper::dumpAsString($caseSale->errors), 'CasesController:actionAjaxSaleListEditInfo:CaseSale:save');
						throw new \Exception('Unsuccessful update');
					}

					if ($sync) {
						$out['success_message'] = 'Sale: '. $caseSaleId .'; Now, you can sync data with b/o';
					}else {
						$out['success_message'] = 'Sale: '. $caseSaleId .'; The data has been returned to its original form.';
					}

					$out['sync'] = $sync;
				} else {
					$out['message'] = implode("; ", $form->getErrorSummary(false));
				}
			}

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

					$out['message'] = 'Sale: '. $caseSaleId .' data was successfully synchronized with b/o.';
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
			    \yii\helpers\VarDumper::dumpAsString($throwable, 10, true),
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
			json_decode((string)$caseSale->css_sale_data, true)['passengers'] ?? [], $isRefresh);

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

		try {
			$out = [
				'error' => 0,
				'message' => ''
			];

			$case = $this->casesRepository->find((int)$caseId);
			$caseSale = $this->casesSaleRepository->getSaleByPrimaryKeys((int)$caseId, (int)$caseSaleId);
			$this->checkAccessToManageCaseSaleInfo($caseSale, true);

			$saleData = $this->casesSaleService->detailRequestToBackOffice((int)$caseSale->css_sale_id, $withFareRules);
			$caseSale = $this->casesSaleService->refreshOriginalSaleData($caseSale, $case, $saleData);

			$out['message'] = 'Sale info: ' . $caseSale->css_sale_id . ' successfully refreshed';

		} catch (\Throwable $throwable) {
			$out['error'] = 1;
			$out['message'] = 'An internal Sales error has occurred; Check system logs;';
			if ($throwable->getCode() <= 0 && $throwable->getCode() > -4) {
				$out['message'] = $throwable->getMessage();
			}
			Yii::error(
			    \yii\helpers\VarDumper::dumpAsString($throwable, 10, true),
			    'CaseController:actionAjaxSyncWithBackOffice:catch:Throwable'
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
