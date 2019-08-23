<?php

namespace frontend\controllers;

use common\components\BackOffice;
use common\components\CommunicationService;
use common\models\Call;
use common\models\CaseNote;
use common\models\CaseSale;
use common\models\ClientEmail;
use common\models\ClientPhone;
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
use frontend\models\CaseCommunicationForm;
use frontend\models\CasePreviewEmailForm;
use frontend\models\CasePreviewSmsForm;
use frontend\models\CommunicationForm;
use http\Exception\InvalidArgumentException;
use sales\entities\cases\CasesStatus;
use sales\entities\cases\CasesStatusLogSearch;
use sales\forms\cases\CasesAddEmailForm;
use sales\forms\cases\CasesChangeStatusForm;
use sales\forms\cases\CasesClientUpdateForm;
use sales\forms\cases\CasesCreateByWebForm;
use sales\forms\cases\CasesUpdateForm;
use sales\repositories\cases\CasesCategoryRepository;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesCommunicationService;
use sales\repositories\user\UserRepository;
use sales\services\cases\CasesCreateService;
use sales\services\cases\CasesManageService;
use Yii;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesSearch;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;
use function GuzzleHttp\Psr7\str;

/**
 * Class CasesController
 *
 * @property CasesCreateService $casesCreateService
 * @property CasesManageService $casesManageService
 * @property CasesCategoryRepository $casesCategoryRepository
 * @property CasesRepository $casesRepository
 * @property CasesCommunicationService $casesCommunicationService
 * @property UserRepository $userRepository,
 */
class CasesController extends FController
{

    private $casesCreateService;
    private $casesManageService;
    private $casesCommunicationService;
    private $casesCategoryRepository;
    private $casesRepository;
    private $userRepository;

    /**
     * CasesController constructor.
     * @param $id
     * @param $module
     * @param CasesCreateService $casesCreateService
     * @param CasesManageService $casesManageService
     * @param CasesCategoryRepository $casesCategoryRepository
     * @param CasesRepository $casesRepository
     * @param CasesCommunicationService $casesCommunicationService
     * @param UserRepository $userRepository,
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        CasesCreateService $casesCreateService,
        CasesManageService $casesManageService,
        CasesCategoryRepository $casesCategoryRepository,
        CasesRepository $casesRepository,
        CasesCommunicationService $casesCommunicationService,
        UserRepository $userRepository,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->casesCreateService = $casesCreateService;
        $this->casesManageService = $casesManageService;
        $this->casesCategoryRepository = $casesCategoryRepository;
        $this->casesRepository = $casesRepository;
        $this->casesCommunicationService = $casesCommunicationService;
        $this->userRepository = $userRepository;
    }

    /**
     * Lists all Cases models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CasesSearch();
        if(Yii::$app->user->identity->canRole('agent')) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params, $isAgent);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent
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

//                if ($comForm->c_type_id == CommunicationForm::TYPE_VOICE) {
//
//
//
//                }

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
            $saleData = $this->findSale($id);

            $cs = CaseSale::find()->where(['css_cs_id' => $model->cs_id, 'css_sale_id' => $saleData['saleId']])->limit(1)->one();
            if($cs) {
                $data['error'] = 'This sale ('.$saleData['saleId'].') exist in this Case Id '.$model->cs_id;
            } else {
                $cs = new CaseSale();
                $cs->css_cs_id = $model->cs_id;
                $cs->css_sale_id = $saleData['saleId'];
                $cs->css_sale_data = json_encode($saleData);
                $cs->css_sale_pnr = $saleData['pnr'] ?? null;
                $cs->css_sale_created_dt = $saleData['created'] ?? null;
                $cs->css_sale_book_id = $saleData['bookingId'] ?? null;
                $cs->css_sale_pax = isset($saleData['passengers']) && is_array($saleData['passengers']) ? count($saleData['passengers']) : null;

                if(!$cs->save()) {
                    Yii::error(VarDumper::dumpAsString($cs->errors), 'CasesController:actionAddSale:CaseSale:save');
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
        $form = new CasesCreateByWebForm(Yii::$app->user->identity);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $case = $this->casesCreateService->createByWeb($form);
                $this->casesManageService->processing($case, Yii::$app->user->id);
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
        $form = new CasesCreateByWebForm(Yii::$app->user->identity);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @param $id
     * @return string
     */
    public function actionGetCategories($id): string
    {
        $id = (int)$id;
        $str = '';
        if ($categories = $this->casesCategoryRepository->getAllByDep($id)) {
            $str .= '<option>Choose a category</option>';
            foreach ($categories as $category) {
                $str .= '<option value="' . $category->cc_key . '">' . $category->cc_name . '</option>';
            }
        } else {
            $str = '<option>-</option>';
        }
        return $str;
    }

    /**
     * @param $gid
     * @param $uid
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionTake($gid, $uid): Response
    {
        $gId = (string) $gid;
        $userId = (int) $uid;
        $case = $this->findModelByGid($gId);
        try {
            $user = $this->userRepository->find($userId);
            $this->casesManageService->processing($case, $user->id);
            Yii::$app->session->setFlash('success', 'Success');
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            Yii::error($e, 'Cases:CasesController:Take');
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
     * @param int $id
     * @return mixed
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    protected function findSale(int $id)
    {

        try {
            $data['sale_id'] = $id;
            $response = BackOffice::sendRequest2('cs/detail', $data);

            if ($response->isOk) {
                $result = $response->data;
                if ($result && is_array($result)) {
                    return $result;
                }
            } else {
                throw new Exception('BO request Error: ' . VarDumper::dumpAsString($response->content), 10);
            }

        } catch (\Throwable $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }

        throw new NotFoundHttpException('The requested Sale does not exist.');
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionChangeStatus()
    {
        $gid = (string)Yii::$app->request->get('gid');
        $case = $this->findModelByGid($gid);

        $form = new CasesChangeStatusForm($case);

        try {
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {

                switch ((int)$form->status) {
                    case CasesStatus::STATUS_FOLLOW_UP :
                        $this->casesManageService->followUp($case->cs_id, $form->message);
                        break;
                    case CasesStatus::STATUS_TRASH :
                        $this->casesManageService->trash($case->cs_id, $form->message);
                        break;
                    case CasesStatus::STATUS_SOLVED :
                        $this->casesManageService->solved($case->cs_id, $form->message);
                        break;
                    case CasesStatus::STATUS_PENDING :
                        $this->casesManageService->pending($case->cs_id, $form->message);
                        break;
                    default:
                        Yii::$app->session->setFlash('error', 'Undefined status');
                        return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
                }

                Yii::$app->session->setFlash('success', 'Case Status changed successfully ("' . CasesStatus::getName($form->status) . '")');
                return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
            }

        } catch (\Throwable $exception) {
            $form->addError('status', $exception->getMessage());
        }

        return $this->renderAjax('partial/_change_status', [
            'model' => $form,
        ]);
    }

    /**
     * @return string
     */
    public function actionStatusHistory()
    {

        $caseGId = Yii::$app->request->get('gid');
        $case = $this->casesRepository->findByGid($caseGId);
        $searchModel = new CasesStatusLogSearch();

        $params = Yii::$app->request->queryParams;
        $params['CasesStatusLogSearch']['csl_case_id'] = $case->cs_id;

        $dataProvider = $searchModel->searchByCase($params);

        return $this->renderAjax('partial/_status_history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionAddEmail()
    {
        $gid = (string)Yii::$app->request->get('gid');
        $case = $this->findModelByGid($gid);

        $form = new CasesAddEmailForm($case);

        try {
            if ($form->load(Yii::$app->request->post()) && $form->validate()) {

                if($case->client) {
                    $existClientEmail = ClientEmail::find()->where(['client_id' => $case->client->id, 'email' => $form->email])->exists();
                    if($existClientEmail) {
                        Yii::$app->session->setFlash('warning', 'This email already exists ("' . $form->email . '"), Client Id: '.$case->client->id);
                    } else {
                        $clientEmail = new ClientEmail();
                        $clientEmail->client_id = $case->client->id;
                        $clientEmail->email = $form->email;
                        if($clientEmail->save()) {
                            Yii::$app->session->setFlash('success', 'Added new Email ("' . $form->email . '")');
                        } else {
                            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($clientEmail->errors));
                        }
                    }
                } else {
                    Yii::$app->session->setFlash('warning', 'Client not found (Client Id: '.$case->cs_client_id.')');
                }
                return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
            }

        } catch (\Throwable $exception) {
            $form->addError('email', $exception->getMessage());
        }

        return $this->renderAjax('partial/_add_email', [
            'model' => $form,
        ]);
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionClientUpdate()
    {
        $gid = (string)Yii::$app->request->get('gid');
        $case = $this->findModelByGid($gid);

        $form = new CasesClientUpdateForm($case);

        try {
            if ($form->load(Yii::$app->request->post())) {
                if($form->validate()) {
                    if ($client = $case->client) {
                        $client->first_name = $form->first_name;
                        $client->last_name = $form->last_name;
                        $client->middle_name = $form->middle_name;

                        if ($client->save()) {
                            Yii::$app->session->setFlash('success', 'Client information has been updated successfully.');
                        } else {
                            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($client->errors));
                        }

                    } else {
                        Yii::$app->session->setFlash('warning', 'Client not found (Client Id: ' . $case->cs_client_id . ')');
                    }
                    return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
                }
            } else {
                if($client = $case->client) {
                    $form->first_name = $client->first_name;
                    $form->last_name = $client->last_name;
                    $form->middle_name = $client->middle_name;
                }
            }

        } catch (\Throwable $exception) {
            $form->addError('first_name', $exception->getMessage());
        }

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
        $gid = (string)Yii::$app->request->get('gid');
        $case = $this->findModelByGid($gid);
        $form = new CasesUpdateForm($case);

        try {
            if ($form->load(Yii::$app->request->post())) {
                if($form->validate()) {
                    try {
                        $case->updateCategory($form->category);
                        $case->updateSubject($form->subject);
                        $case->updateDescription($form->description);

                        $this->casesRepository->save($case);
                        //$this->casesManageService->updateCategory($case, $form->category);
                        Yii::$app->session->setFlash('success', 'Case information has been updated successfully.');
                    } catch (\Throwable $exception) {
                        Yii::$app->session->setFlash('error', VarDumper::dumpAsString($exception));
                    }
                    return $this->redirect(['cases/view', 'gid' => $case->cs_gid]);
                }
            } else {
                $form->category = $case->cs_category;
                $form->subject = $case->cs_subject;
                $form->description = $case->cs_description;
            }

        } catch (\Throwable $exception) {
            $form->addError('category', $exception->getMessage());
        }

        $categories = $this->casesCategoryRepository->getAllByDep($case->cs_dep_id);
        $categoryList = ArrayHelper::map($categories, 'cc_key', 'cc_name');

        return $this->renderAjax('partial/_case_update', [
            'model' => $form,
            'categoryList' => $categoryList
        ]);
    }

}
