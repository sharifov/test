<?php

namespace frontend\controllers;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientEmailQuery;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Lead;
use common\models\query\ClientPhoneQuery;
use common\models\Quote;
use common\models\search\ClientSearch;
use common\models\search\lead\LeadSearchByClient;
use common\models\search\lead\LeadSearchByIp;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\access\LeadPreferencesAccess;
use src\auth\Auth;
use src\entities\cases\CasesSearchByClient;
use src\forms\lead\CloneQuoteByUidForm;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\LeadPreferencesForm;
use src\forms\lead\LeadQuoteExtraMarkUpForm;
use src\forms\lead\PhoneCreateForm;
use src\helpers\app\AppHelper;
use src\model\clientChat\socket\ClientChatSocketCommands;
use src\model\clientChatLead\entity\ClientChatLead;
use src\model\quote\abac\dto\QuoteFlightExtraMarkupAbacDto;
use src\model\quote\abac\QuoteFlightAbacObject;
use src\repositories\quote\QuotePriceRepository;
use src\repositories\quote\QuoteRepository;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use src\services\lead\LeadCloneQuoteService;
use src\services\lead\LeadPreferencesManageService;
use src\services\quote\quotePriceService\ClientQuotePriceService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\db\Transaction;

/**
 * Class LeadViewController
 *
 * @property  LeadCloneQuoteService $leadCloneQuoteService
 * @property  ClientManageService $clientManageService
 * @property  LeadPreferencesManageService $leadPreferencesManageService
 */
class LeadViewController extends FController
{
    /**
     * @var ClientManageService
     */
    private $clientManageService;

    /**
     * @var LeadCloneQuoteService
     */
    private $leadCloneQuoteService;

    /**
     * @var LeadPreferencesManageService
     */
    private $leadPreferencesManageService;

    private QuoteRepository $quoteRepository;

    /**
     * LeadViewController constructor.
     * @param $id
     * @param $module
     * @param ClientManageService $clientManageService
     * @param LeadCloneQuoteService $leadCloneQuoteService
     * @param LeadPreferencesManageService $leadPreferencesManageService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ClientManageService $clientManageService,
        LeadCloneQuoteService $leadCloneQuoteService,
        LeadPreferencesManageService $leadPreferencesManageService,
        QuoteRepository $quoteRepository,
        $config = []
    ) {
        $this->clientManageService = $clientManageService;
        $this->leadCloneQuoteService = $leadCloneQuoteService;
        $this->leadPreferencesManageService = $leadPreferencesManageService;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'allowActions' => [
                    'ajax-get-info',
                    'ajax-add-client-phone-modal-content',
                    'ajax-add-client-phone-validation',
                    'ajax-add-client-phone',
                    'ajax-add-client-email-modal-content',
                    'ajax-add-client-email-validation',
                    'ajax-add-client-email',
                    'ajax-edit-client-name-modal-content',
                    'ajax-edit-client-name-validation',
                    'ajax-edit-client-name',
                    'ajax-get-users-same-phone-info',
                    'ajax-get-users-same-email-info',
                    'search-leads-by-ip',
                    'ajax-edit-client-phone-modal-content',
                    'ajax-edit-client-phone-validation',
                    'ajax-edit-client-phone',
                    'ajax-edit-client-email-modal-content',
                    'ajax-edit-client-email-validation',
                    'ajax-edit-client-email',
                    'ajax-edit-lead-quote-extra-mark-up-modal-content',
                    'ajax-edit-lead-quote-extra-mark-up'
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     * @throws \ReflectionException
     */
    public function actionSearchLeadsByIp(): string
    {
        $lead = $this->findLeadByGid((string)Yii::$app->request->get('gid'));

        /** @abac new LeadAbacDto($lead), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_SHOW_LEADS_BY_IP, Restrict access to action search leads by ip*/
        if (!Yii::$app->abac->can(new LeadAbacDto($lead, Auth::id()), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_SHOW_LEADS_BY_IP)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        if (!$lead->request_ip) {
            throw new NotFoundHttpException('Not found Lead with request ip');
        }

        $params[LeadSearchByIp::getShortName()]['requestIp'] = $lead->request_ip;

        $dataProvider = (new LeadSearchByIp())->search($params, Yii::$app->user->id);

        return $this->renderAjax('search_leads_by_ip', [
            'dataProvider' => $dataProvider,
            'lead' => $lead
        ]);
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxGetUsersSamePhoneInfo(): string
    {
        if (Yii::$app->request->isAjax) {
            try {
                $searchModel = new ClientSearch();

                $phone = Yii::$app->request->get('phone');
                $clientId = Yii::$app->request->get('clientId');

                $params['ClientSearch']['client_phone'] = $phone;
                $params['ClientSearch']['not_in_client_id'] = $clientId;

                $dataProvider = $searchModel->searchFromLead($params);

                return $this->renderAjax('_client_same_users_by_phone_or_email', [
                    'dataProvider' => $dataProvider,
                    'clientId' => $clientId
                ]);
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . '; On Line: ' . $e->getLine() . '; In File: ' . $e->getFile());
                throw new BadRequestHttpException();
            }
        }

        throw new BadRequestHttpException();
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxGetUsersSameEmailInfo(): string
    {
        if (Yii::$app->request->isAjax) {
            try {
                $searchModel = new ClientSearch();

                $email = Yii::$app->request->get('email');
                $clientId = Yii::$app->request->get('clientId');

                $params['ClientSearch']['client_email'] = $email;
                $params['ClientSearch']['not_in_client_id'] = $clientId;

                $dataProvider = $searchModel->searchFromLead($params);

                return $this->renderAjax('_client_same_users_by_phone_or_email', [
                    'dataProvider' => $dataProvider,
                    'clientId' => $clientId
                ]);
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . '; On Line: ' . $e->getLine() . '; In File: ' . $e->getFile());
                throw new BadRequestHttpException();
            }
        }

        throw new BadRequestHttpException();
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxAddClientPhoneModalContent(): string
    {
        $gid = (string)Yii::$app->request->get('gid');
        $lead = $this->findLeadByGid($gid);

        /** @abac new LeadAbacDto($lead), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_ADD_PHONE, Restrict access to action client details on lead*/
        if (!Yii::$app->abac->can(new LeadAbacDto($lead, Auth::id()), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_ADD_PHONE)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        try {
            $form = new PhoneCreateForm();

            return $this->renderAjax('partial/_client_add_phone_modal_content', [
                'addPhone' => $form,
                'lead' => $lead
            ]);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxAddClientPhoneModalContent:Throwable');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionAjaxAddClientPhoneValidation(): array
    {
        $gid = (string)Yii::$app->request->get('gid');
        $lead = $this->findLeadByGid($gid);

        try {
            $form = new PhoneCreateForm();
            $form->required = true;

            if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
                $form->client_id = $lead->client_id;

                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($form);
            }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxAddClientPhoneValidation:Throwable');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @throws HttpException
     */
    public function actionAjaxAddClientPhone()
    {
        $gid = (string)Yii::$app->request->get('gid');
        $lead = $this->findLeadByGid($gid);
        $leadAbacDto = new LeadAbacDto($lead, Auth::id());

        /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ADD_PHONE, Restrict access to action client add phone on lead*/
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ADD_PHONE)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        /** @abac new $leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK, Disable mask client data on Lead view*/
        $disableMasking = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK);

        try {
            $form = new PhoneCreateForm();
            $form->client_id = $lead->client_id;
            $form->required = true;

            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $this->clientManageService->addPhone($lead->client, $form);

                $response['error'] = false;
                $response['message'] = 'New phone was successfully added: ' . $form->phone;
                $response['html'] = $this->renderAjax('/lead/client-info/_client_manage_phone', [
                    //'clientPhones' => $lead->client->clientPhones,
                    'clientPhones' => ClientPhoneQuery::getWithSameClientsPhonesCount($lead->client_id),
                    'lead' => $lead,
                    'disableMasking' => $disableMasking,
                    'leadAbacDto' => $leadAbacDto
                ]);
            } else {
                $response['error'] = true;
                $response['message'] = $this->getParsedErrors($form->getErrors());
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'LeadViewController:actionAjaxAddClientPhone:Throwable');
        }

        throw new BadRequestHttpException();
    }

    /**
     * @throws BadRequestHttpException
     */
    public function actionAjaxEditClientPhoneModalContent()
    {
        if (Yii::$app->request->isAjax) {
            $gid = (string)Yii::$app->request->get('gid');
            $lead = $this->findLeadByGid($gid);

            /** @abac new LeadAbacDto($lead), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_EDIT_PHONE, Access to action client edit phone on lead*/
            if (!Yii::$app->abac->can(new LeadAbacDto($lead, Auth::id()), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_EDIT_PHONE)) {
                throw new ForbiddenHttpException('Access denied.');
            }

            try {
                $id = (int)Yii::$app->request->get('pid');
                if ($phone = ClientPhone::findOne($id)) {
                    $phoneForm = new PhoneCreateForm();
                    $phoneForm->id = $phone->id;
                    $phoneForm->phone = $phone->phone;
                    $phoneForm->type = $phone->type;
                    $phoneForm->client_id = $phone->client_id;

                    return $this->renderAjax('partial/_client_edit_phone_modal_content', [
                        'editPhone' => $phoneForm,
                        'lead' => $lead
                    ]);
                }

                throw new BadRequestHttpException();
            } catch (\Throwable $e) {
                Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxAddClientPhone:Throwable');
            }
        }

        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionAjaxEditClientPhoneValidation(): array
    {
        $gid = (string)Yii::$app->request->get('gid');
        $lead = $this->findLeadByGid($gid);

        try {
            $form = new PhoneCreateForm();
            $form->scenario = 'update';
            $form->required = true;

            if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
                $form->client_id = $lead->client_id;

                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($form);
            }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxEditClientPhoneValidation:Throwable');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionAjaxEditClientPhone()
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $gid = (string)Yii::$app->request->get('gid');
        $lead = $this->findLeadByGid($gid);
        $leadAbacDto = new LeadAbacDto($lead, $user->id);

        /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS, Restrict access to action edit phone on lead*/
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_EDIT_PHONE)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        /** @abac new $leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK, Disable mask client data on Lead view*/
        $disableMasking = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK);

        try {
            $form = new PhoneCreateForm();
            $form->scenario = 'update';

            $form->load(Yii::$app->request->post());

            $leadAbacDto->formAttribute = 'phone';
            $leadAbacDto->isNewRecord = false;

            if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::PHONE_CREATE_FORM, LeadAbacObject::ACTION_EDIT)) {
                $form->required = true;
            } else {
                $form->phone = null;
            }

            if ($form->validate()) {
                $this->clientManageService->updatePhone($form);

                $response['error'] = false;
                $response['message'] = 'Phone was successfully updated: ' . $form->phone;
                $response['html'] = $this->renderAjax('/lead/client-info/_client_manage_phone', [
                    //'clientPhones' => $lead->client->clientPhones,
                    'clientPhones' => ClientPhoneQuery::getWithSameClientsPhonesCount($lead->client_id),
                    'lead' => $lead,
                    'disableMasking' => $disableMasking,
                    'leadAbacDto' => $leadAbacDto
                ]);
            } else {
                $response['error'] = true;
                $response['message'] = $this->getParsedErrors($form->getErrors());
            }


            return $this->asJson($response);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'LeadViewController:actionAjaxEditClientPhone:Throwable');
        }

        throw new BadRequestHttpException();
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxAddClientEmailModalContent(): string
    {
        $gid = (string)Yii::$app->request->get('gid');
        $lead = $this->findLeadByGid($gid);

        /** @abac new LeadAbacDto($lead), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_ADD_EMAIL, Restrict access to action add email on lead*/
        if (!Yii::$app->abac->can(new LeadAbacDto($lead, Auth::id()), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_ADD_EMAIL)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        try {
            $form = new EmailCreateForm();

            return $this->renderAjax('partial/_client_add_email_modal_content', [
                'addEmail' => $form,
                'lead' => $lead
            ]);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxAddClientEmailModalContent:Throwable');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionAjaxAddClientEmailValidation(): array
    {
        $gid = (string)Yii::$app->request->get('gid');
        $lead = $this->findLeadByGid($gid);

        try {
            $form = new EmailCreateForm();
            $form->required = true;

            if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
                $form->client_id = $lead->client_id;

                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($form);
            }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxAddClientEmailValidation:Throwable');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @throws HttpException
     */
    public function actionAjaxAddClientEmail()
    {
        $gid = (string)Yii::$app->request->get('gid');
        $lead = $this->findLeadByGid($gid);
        $leadAbacDto = new LeadAbacDto($lead, Auth::id());
        /*if (!$lead->isOwner($user->id) && !$user->isAnySupervision() && !$user->isAdmin() && !$user->isSuperAdmin()) {
            throw new HttpException(403, 'Access Denied');
        }*/

        /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ADD_EMAIL, Restrict access to action client add email on lead*/
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ADD_EMAIL)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        /** @abac new $leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK, Disable mask client data on Lead view*/
        $disableMasking = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK);

        try {
            $form = new EmailCreateForm();
            $form->client_id = $lead->client_id;
            $form->required = true;

            if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                $this->clientManageService->addEmails($lead->client, [$form]);

                $response['error'] = false;
                $response['message'] = 'New email was successfully added: ' . $form->email;
                $response['html'] = $this->renderAjax('/lead/client-info/_client_manage_email', [
                    //'clientEmails' => $lead->client->clientEmails,
                    'clientEmails' => ClientEmailQuery::getWithSameClientsEmailsCount($lead->client_id),
                    'lead' => $lead,
                    'leadAbacDto' => $leadAbacDto,
                    'disableMasking' => $disableMasking
                ]);
            } else {
                $response['error'] = true;
                $response['message'] = $this->getParsedErrors($form->getErrors());
            }

            return $this->asJson($response);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'LeadViewController:actionAjaxAddClientEmail:Throwable');
        }

        throw new BadRequestHttpException();
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxEditClientEmailModalContent()
    {
        if (Yii::$app->request->isAjax) {
            $gid = (string)Yii::$app->request->get('gid');
            $lead = $this->findLeadByGid($gid);

            /** @abac new LeadAbacDto($lead), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_EDIT_EMAIL, Access to action client edit email on lead*/
            if (!Yii::$app->abac->can(new LeadAbacDto($lead, Auth::id()), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_EDIT_EMAIL)) {
                throw new ForbiddenHttpException('Access denied.');
            }

            try {
                $id = (int)Yii::$app->request->get('pid');
                if ($email = ClientEmail::findOne($id)) {
                    $emailForm = new EmailCreateForm();
                    $emailForm->id = $email->id;
                    $emailForm->email = $email->email;
                    $emailForm->type = $email->type;
                    $emailForm->client_id = $email->client_id;

                    return $this->renderAjax('partial/_client_edit_email_modal_content', [
                        'editEmail' => $emailForm,
                        'lead' => $lead
                    ]);
                }

                throw new BadRequestHttpException();
            } catch (\Throwable $e) {
                Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxEditClientEmailModalContent:Throwable');
            }
        }

        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionAjaxEditClientEmailValidation(): array
    {
        $gid = (string)Yii::$app->request->get('gid');
        $lead = $this->findLeadByGid($gid);

        try {
            $form = new EmailCreateForm();
            $form->scenario = 'update';
            $form->required = true;

            if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
                $form->client_id = $lead->client_id;

                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($form);
            }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxEditClientEmailValidation:Throwable');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionAjaxEditClientEmail()
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $gid = (string)Yii::$app->request->get('gid');
        $lead = $this->findLeadByGid($gid);
        $leadAbacDto = new LeadAbacDto($lead, $user->id);

        /** @abac new LeadAbacDto($lead), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_EDIT_EMAIL, Restrict access to action edit phone on lead*/
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_EDIT_EMAIL)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        /** @abac new $leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK, Disable mask client data on Lead view*/
        $disableMasking = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK);

        try {
            $form = new EmailCreateForm();
            $form->scenario = 'update';

            $form->load(Yii::$app->request->post());

            $leadAbacDto->formAttribute = 'email';
            $leadAbacDto->isNewRecord = false;
            if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::EMAIL_CREATE_FORM, LeadAbacObject::ACTION_EDIT)) {
                $form->required = true;
            } else {
                $form->email = null;
            }

            if ($form->validate()) {
                $this->clientManageService->updateEmail($form);

                $response['error'] = false;
                $response['message'] = 'Email was successfully updated: ' . $form->email;
                $response['html'] = $this->renderAjax('/lead/client-info/_client_manage_email', [
                    //'clientEmails' => $lead->client->clientEmails,
                    'clientEmails' => ClientEmailQuery::getWithSameClientsEmailsCount($lead->client_id),
                    'lead' => $lead,
                    'leadAbacDto' => $leadAbacDto,
                    'disableMasking' => $disableMasking
                ]);
            } else {
                $response['error'] = true;
                $response['message'] = $this->getParsedErrors($form->getErrors());
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'LeadViewController:actionAjaxEditClientEmail:Throwable');
        }

        throw new BadRequestHttpException();
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxEditClientNameModalContent(): string
    {
        if (Yii::$app->request->isAjax) {
            $gid = (string)Yii::$app->request->get('gid');
            $lead = $this->findLeadByGid($gid);

            /** @abac new LeadAbacDto($lead), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_UPDATE_CLIENT, Restrict access to action client update on lead*/
            if (!Yii::$app->abac->can(new LeadAbacDto($lead, Auth::id()), LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_UPDATE_CLIENT)) {
                throw new ForbiddenHttpException('Access denied.');
            }

            try {
                $form = new ClientCreateForm();
                $form->id = $lead->client->id;
                $form->firstName = $lead->client->first_name;
                $form->lastName = $lead->client->last_name;
                $form->middleName = $lead->client->middle_name;
                $form->locale = $lead->client->cl_locale;
                $form->marketingCountry = $lead->client->cl_marketing_country;

                return $this->renderAjax('partial/_client_edit_name_modal_content', [
                    'editName' => $form,
                    'lead' => $lead
                ]);
            } catch (\Throwable $e) {
                Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxEditClientNameModalContent:Throwable');
            }
        }

        throw new BadRequestHttpException();
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionAjaxEditClientNameValidation(): array
    {

        try {
            $form = new ClientCreateForm();

            if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($form);
            }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxEditClientNameValidation:Throwable');
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionAjaxEditClientName()
    {
        $form = new ClientCreateForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            if (!$client = Client::findOne($form->id)) {
                throw new NotFoundHttpException('The requested client does not exist.');
            }

            $this->clientManageService->updateClient($client, $form);

            $response['error'] = false;
            $response['message'] = 'Client information has been updated successfully';
            $response['html'] = $this->renderAjax('/lead/client-info/_client_manage_name', [
                'client' => $client
            ]);
        } else {
            $response['error'] = true;
            $response['message'] = $this->getParsedErrors($form->getErrors());
        }

        return $this->asJson($response);
    }

    /**
     * @return array
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCloneQuoteByUid(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new CloneQuoteByUidForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->leadCloneQuoteService->cloneByUid($form->uid, $form->leadGid, Auth::id());
                $chat = ClientChatLead::find()->andWhere(['ccl_lead_id' => $form->getLeadId()])->one();
                if ($chat) {
                    ClientChatSocketCommands::clientChatAddQuotesButton($chat->chat, $form->getLeadId());
                }
            } catch (\DomainException $e) {
                Yii::warning($e->getMessage());
                return ['success' => false, 'message' => $e->getMessage()];
            }
            if (Yii::$app->getSession()->hasFlash('warning')) {
                $message = [];
                foreach (Yii::$app->getSession()->getFlash('warning') as $flash) {
                    $message[] = $flash;
                }
                return ['success' => true, 'message' => implode(PHP_EOL, $message)];
            }
            return ['success' => true];
        }
        return ['success' => false, 'message' =>  VarDumper::dumpAsString($form->errors)];
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionCloneQuoteByUidValidate(): array
    {
        $form = new CloneQuoteByUidForm();
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionAjaxEditLeadPreferencesModalContent(): string
    {
        if (Yii::$app->request->isAjax) {
            $gid = Yii::$app->request->get('gid');
            if ($lead = $this->findLeadByGid($gid)) {
                $dto = new LeadAbacDto($lead, Auth::id());
                /** @abac new LeadAbacDto($lead), LeadAbacObject::OBJ_LEAD_PREFERENCES, LeadAbacObject::ACTION_SET_DELAY_CHARGE, Lead preferences update Delay Charge access */
                $delayChargeAccess = Yii::$app->abac->can($dto, LeadAbacObject::OBJ_LEAD_PREFERENCES, LeadAbacObject::ACTION_SET_DELAY_CHARGE);
                /** @abac new LeadAbacDto($lead), LeadAbacObject::OBJ_LEAD_PREFERENCES, LeadAbacObject::ACTION_MANAGE_LEAD_PREF_CURRENCY, Access to manage lead preferences currency */
                $modifyCurrencyAccess = Yii::$app->abac->can($dto, LeadAbacObject::OBJ_LEAD_PREFERENCES, LeadAbacObject::ACTION_MANAGE_LEAD_PREF_CURRENCY);

                $leadPreferencesForm = new LeadPreferencesForm($lead, $modifyCurrencyAccess);
                return $this->renderAjax('partial/_lead_preferences_edit_modal_content', [
                    'leadPreferencesForm' => $leadPreferencesForm,
                    'gid' => $lead->gid,
                    'delayChargeAccess' => $delayChargeAccess
                ]);
            }
        }
        throw new BadRequestHttpException();
    }

    public function actionLeadData(): string
    {
        if (Yii::$app->request->isAjax) {
            $gid = Yii::$app->request->get('gid');
            if ($lead = $this->findLeadByGid($gid)) {
                return $this->renderAjax('partial/_lead_data', [
                    'lead' => $lead,
                ]);
            }
        }
        throw new BadRequestHttpException();
    }

    /**
     * @throws BadRequestHttpException
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionAjaxEditLeadPreferences(): Response
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if (Yii::$app->request->isAjax) {
            $gid = Yii::$app->request->get('gid');
            $lead = $this->findLeadByGid($gid);
            if (!LeadPreferencesAccess::isUserCanManageLeadPreference($lead, $user)) {
                throw new HttpException(403, 'Access Denied');
            }

            try {
                $dto = new LeadAbacDto($lead, Auth::id());
                /** @abac new LeadAbacDto($lead), LeadAbacObject::OBJ_LEAD_PREFERENCES, LeadAbacObject::ACTION_MANAGE_LEAD_PREF_CURRENCY, Access to manage lead preferences currency */
                $canManageCurrency = Yii::$app->abac->can($dto, LeadAbacObject::OBJ_LEAD_PREFERENCES, LeadAbacObject::ACTION_MANAGE_LEAD_PREF_CURRENCY);
                $form = new LeadPreferencesForm($lead, $canManageCurrency);
                $oldCurrency = $lead->leadPreferences->prefCurrency->cur_code ?? '';
                if ($form->load(Yii::$app->request->post()) && $form->validate()) {
                    $this->leadPreferencesManageService->edit($form, $lead, Auth::id());

                    $lead->refresh();

                    if ($oldCurrency !== $form->currency) {
                        $keyCache = sprintf('quick-search-new-%d-%s-%s', $lead->id, '', $lead->generateLeadKey());
                        if (Yii::$app->cacheFile->get($keyCache) !== false) {
                            Yii::$app->cacheFile->delete($keyCache);
                        }
                    }

                    $response['error'] = false;
                    $response['message'] = 'Lead preferences successfully updated';
                    $response['html'] = $this->renderAjax('/lead/partial/_lead_preferences', [
                        'lead' => $lead
                    ]);
                } else {
                    $response['error'] = true;
                    $response['message'] = $this->getParsedErrors($form->getErrors());
                }
            } catch (\Throwable $e) {
                Yii::error($e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'LeadViewController:actionAjaxEditLeadPreferences:Throwable');
                $response['message'] = 'Internal Server error; Try again letter';
                $response['error'] = true;
            }

            return $this->asJson($response);
        }

        throw new BadRequestHttpException();
    }

    public function actionAjaxEditLeadQuoteExtraMarkUpModalContent()
    {
        try {
            if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
                throw new \RuntimeException('Method is not allowed');
            }
            $quoteId = (int)Yii::$app->request->get('quoteId');
            $paxCode = (string)Yii::$app->request->get('paxCode');
            $quote   = Quote::findOne($quoteId);
            if (empty($quote)) {
                throw new \RuntimeException('Quote not Founded');
            }
            $lead = $quote->lead;
            $isOwner = $lead->employee_id = Auth::id();
            $quoteFlightExtraMarkUpAbacDto = new QuoteFlightExtraMarkupAbacDto($lead, $quote, $isOwner);
            /** @abac quoteFlightExtraMarkUpAbacDto, QuoteFlightAbacObject::OBJ_EXTRA_MARKUP, QuoteExtraMarkUpChangeAbacObject::ACTION_UPDATE, Access to edit Quote Extra mark-up */
            $canUpdateExtraMarkUp = Yii::$app->abac->can(
                $quoteFlightExtraMarkUpAbacDto,
                QuoteFlightAbacObject::OBJ_EXTRA_MARKUP,
                QuoteFlightAbacObject::ACTION_UPDATE
            );
            if (!$canUpdateExtraMarkUp) {
                throw new \RuntimeException('Access Denied');
            }
            $quotePrices = QuotePriceRepository::getAllByQuoteIdAndPaxCode($quoteId, $paxCode);
            if (empty($quotePrices)) {
                throw new \RuntimeException('Quote Price not founded');
            }
            $clientCurrency = $quote->clientCurrency;
            if (empty($clientCurrency)) {
                throw new \RuntimeException('Currency not Founded');
            }
            $quotePrice = QuotePriceRepository::findByQuoteIdAndPaxCode($quoteId, $paxCode)->toArray();
            $form = new LeadQuoteExtraMarkUpForm($quote->q_client_currency_rate);
            $form->load($quotePrice);
            return $this->renderAjax('partial/_quote_edit_extra_mark_up_content', [
                'quote'                    => $quote,
                'paxCode'                  => $paxCode,
                'leadQuoteExtraMarkUpForm' => $form,
            ]);
        } catch (\RuntimeException | \DomainException $e) {
            Yii::warning(
                AppHelper::throwableFormatter($e),
                'LeadViewController::actionAjaxEditQuoteExtraMarkUpModalContent:exception'
            );
            return $this->renderAjax('_error', [
                'error' => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            Yii::error(
                AppHelper::throwableLog($e),
                'LeadViewController:actionAjaxEditQuoteExtraMarkUpModalContent:Throwable'
            );
            return $this->renderAjax('_error', [
                'error' => 'ServerError'
            ]);
        }
    }

    public function actionAjaxEditLeadQuoteExtraMarkUp()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction = new Transaction(['db' => Yii::$app->db]);
        try {
            if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
                throw new \RuntimeException('Wrong method');
            }
            $quoteId = (int)Yii::$app->request->get('quoteId');
            $paxCode = (string)Yii::$app->request->get('paxCode');
            $quote = Quote::findOne($quoteId);
            if (empty($quote)) {
                throw new \RuntimeException('Quote not founded');
            }
            $lead = $quote->lead;
            $currentUserId = Auth::id();
            $isOwner = $lead->isOwner($currentUserId);
            $quoteFlightExtraMarkUpAbacDto = new QuoteFlightExtraMarkupAbacDto($lead, $quote, $isOwner);
            /** @abac quoteFlightExtraMarkUpAbacDto, QuoteFlightAbacObject::OBJ_EXTRA_MARKUP, QuoteExtraMarkUpChangeAbacObject::ACTION_UPDATE, Access to edit Quote Extra mark-up */
            $canUpdateExtraMarkUp = Yii::$app->abac->can(
                $quoteFlightExtraMarkUpAbacDto,
                QuoteFlightAbacObject::OBJ_EXTRA_MARKUP,
                QuoteFlightAbacObject::ACTION_UPDATE
            );
            if (!$canUpdateExtraMarkUp) {
                throw new \RuntimeException('Access Denied');
            }
            $quotePrices = QuotePriceRepository::getAllByQuoteIdAndPaxCode($quoteId, $paxCode);
            if (empty($quotePrices)) {
                throw new \RuntimeException('Quote Prices not founded');
            }
            $clientCurrency = $quote->clientCurrency;
            if (empty($clientCurrency)) {
                throw new \RuntimeException('Currency not Founded');
            }
            $form = new LeadQuoteExtraMarkUpForm($quote->q_client_currency_rate);
            $form->load(Yii::$app->request->post());
            if (!$form->validate()) {
                throw new \RuntimeException(implode(', ', $form->getErrorSummary(true)));
            }
            $clientQuotePriceService = new ClientQuotePriceService($quote);
            $priceData = $clientQuotePriceService->getClientPricesData();
            $sellingOld = $priceData['total']['selling'];
            $transaction->begin();
            foreach ($quotePrices as $quotePrice) {
                $quotePrice->extra_mark_up = $form->extra_mark_up;
                $quotePrice->qp_client_extra_mark_up = $form->qp_client_extra_mark_up;
                $quotePrice->update();
            }
            $quote->changeExtraMarkUp($currentUserId, $sellingOld);
            $this->quoteRepository->save($quote);
            $transaction->commit();
            return [
                'message' => 'Quote Extra mark-up Updated Successfully'
            ];
        } catch (\RuntimeException | \DomainException $e) {
            $transaction->rollBack();
            Yii::warning(
                AppHelper::throwableFormatter($e),
                'LeadViewController::actionAjaxEditQuoteExtraMarkUp:exception'
            );
            return [
                'error' => $e->getMessage()
            ];
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error(
                AppHelper::throwableLog($e),
                'LeadViewController:actionAjaxEditQuoteExtraMarkUp:Throwable'
            );
            return [
                'error' => 'Server Error'
            ];
        }
    }



    /**
     * @return string
     * @throws NotFoundHttpException
     * @throws \ReflectionException
     */
    public function actionAjaxGetInfo(): string
    {
        $user = Auth::user();

        if (!$leadId = Yii::$app->request->post('lead_id')) {
            $leadId = Yii::$app->request->get('client_id');
        }

        $model = $this->findLeadById($leadId);
        $leadAbacDto = new LeadAbacDto($model, $user->id);

        /** @abac $leadAbacDto, LeadAbacObject::ACT_CLIENT_DETAILS, LeadAbacObject::UI_BLOCK_CLIENT_INFO, Restrict access to action client details on lead*/
        if (!Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_DETAILS)) {
            throw new ForbiddenHttpException('Access denied.');
        }

        /** @abac new $leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK, Disable mask client data on Lead view*/
        $disableMasking = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK);

        if (!$clientId = Yii::$app->request->post('client_id')) {
            $clientId = Yii::$app->request->get('client_id');
        }
        $client = Client::findOne((int)$clientId);

        $providers = [];
        $providers['leadsDataProvider'] = $this->getLeadsDataProvider($client->id, $leadId, $user);
        $providers['casesDataProvider'] = $this->getCasesDataProvider($client->id, $leadId, $user->id);

        return $this->renderAjax('ajax_info', ArrayHelper::merge(
            [
                'model' => $client,
                'disableMasking' => $disableMasking
            ],
            $providers
        ));
    }

    /**
     * @param int $clientId
     * @param int $userId
     * @return ActiveDataProvider
     * @throws \ReflectionException
     */
    private function getCasesDataProvider(int $clientId, int $leadId, int $userId): ActiveDataProvider
    {
        $params[CasesSearchByClient::getShortName()]['clientId'] = $clientId;

        $dataProvider = (new CasesSearchByClient())->search($params, $userId);

        $dataProvider->query->orderBy(['cs_last_action_dt' => SORT_DESC]);

        $dataProvider->sort = false;

        $pagination = $dataProvider->pagination;
        $pagination->pageSize = 10;
        $pagination->params = array_merge(Yii::$app->request->get(), ['client_id' => $clientId, 'lead_id' => $leadId]);
        $pagination->pageParam = 'case-page';
        $pagination->pageSizeParam = 'case-per-page';
        $dataProvider->pagination = $pagination;

        return $dataProvider;
    }

    /**
     * @param int $clientId
     * @param Employee $user
     * @return ActiveDataProvider
     * @throws \ReflectionException
     */
    private function getLeadsDataProvider(int $clientId, int $leadId, Employee $user): ActiveDataProvider
    {
        $params[LeadSearchByClient::getShortName()]['clientId'] = $clientId;

        $dataProvider = (new LeadSearchByClient())->search($params, $user);

        $dataProvider->query->orderBy(['l_last_action_dt' => SORT_DESC]);

        $dataProvider->sort = false;

        $pagination = $dataProvider->getPagination();
        $pagination->pageSize = 10;
        $pagination->params = array_merge(Yii::$app->request->get(), ['client_id' => $clientId, 'lead_id' => $leadId]);
        $pagination->pageParam = 'lead-page';
        $pagination->pageSizeParam = 'lead-per-page';
        $dataProvider->setPagination($pagination);

        return $dataProvider;
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
     * @param array $errors
     * @return string
     */
    public function getParsedErrors(array $errors): string
    {
        return implode('<br>', array_map(static function ($errors) {
            return implode('<br>', $errors);
        }, $errors));
    }

    /**
     * @param $uid
     * @return Quote
     * @throws NotFoundHttpException
     */
    protected function findQuoteByUid($uid): Quote
    {
        if ($model = Quote::findOne(['uid' => $uid])) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
