<?php

namespace frontend\controllers;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\LeadPreferences;
use common\models\search\ClientSearch;
use frontend\models\LeadForm;
use sales\access\ClientInfoAccess;
use sales\access\EmployeeGroupAccess;
use sales\access\LeadPreferencesAccess;
use sales\forms\lead\ClientCreateForm;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\LeadPreferencesForm;
use sales\forms\lead\PhoneCreateForm;
use sales\services\client\ClientManageService;
use common\models\Quote;
use sales\forms\lead\CloneQuoteByUidForm;
use sales\services\lead\LeadCloneQuoteService;
use sales\services\lead\LeadPreferencesManageService;
use Yii;
use common\models\Lead;
use common\models\search\lead\LeadSearchByIp;
use yii\base\Model;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

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
		$config = [])
	{
		$this->clientManageService = $clientManageService;
		$this->leadCloneQuoteService = $leadCloneQuoteService;
		$this->leadPreferencesManageService = $leadPreferencesManageService;
		parent::__construct($id, $module, $config);
	}

    /**
     * @return string
     * @throws NotFoundHttpException
     * @throws \ReflectionException
     */
    public function actionSearchLeadsByIp(): string
    {
        $lead = $this->findLeadByGid((string)Yii::$app->request->get('gid'));
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
		if (Yii::$app->request->isAjax)
		{
			try {
				$searchModel = new ClientSearch();

				$phone = Yii::$app->request->get('phone');
				$clientId = Yii::$app->request->get('clientId');

				$params['ClientSearch']['client_phone'] = $phone;
				$params['ClientSearch']['not_in_client_id'] = $clientId;

				$dataProvider = $searchModel->searchFromLead($params);

				return $this->renderAjax('_client_same_users_by_phone', [
					'dataProvider' => $dataProvider,
					'phone' => $phone,
					'clientId' => $clientId
				]);

			}catch (\Exception $e) {
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
		if (Yii::$app->request->isAjax)
		{
			try {
				$searchModel = new ClientSearch();

				$email = Yii::$app->request->get('email');
				$clientId = Yii::$app->request->get('clientId');

				$params['ClientSearch']['client_email'] = $email;
				$params['ClientSearch']['not_in_client_id'] = $clientId;

				$dataProvider = $searchModel->searchFromLead($params);

				return $this->renderAjax('_client_same_users_by_email', [
					'dataProvider' => $dataProvider,
					'email' => $email,
					'clientId' => $clientId
				]);
			}catch (\Exception $e) {
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
	public function actionAjaxAddClientPhoneModalContent():string
	{
		try {
			$gid = (string)Yii::$app->request->get('gid');

			$lead = $this->findLeadByGid($gid);

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

		}catch (\Throwable $e) {
			Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxAddClientPhoneValidation:Throwable');
		}
		throw new BadRequestHttpException();
	}

	/**
	 * @throws HttpException
	 */
	public function actionAjaxAddClientPhone()
	{
		$user = Yii::$app->user->identity;
		$gid = (string)Yii::$app->request->get('gid');
		$lead = $this->findLeadByGid($gid);

		if (!$lead->isOwner(Yii::$app->user->id) && !$user->isAnySupervision() && !$user->isAdmin() && !$user->isSuperAdmin()) {
			throw new HttpException(403, 'Access Denied');
		}

		try {

			$form = new PhoneCreateForm();
			$form->client_id = $lead->client_id;
			$form->required = true;

			if ($form->load(Yii::$app->request->post()) && $form->validate()) {
				$this->clientManageService->addPhone($lead->client, $form);

				$response['error'] = false;
				$response['message'] = 'New phone was successfully added: ' . $form->phone;
				$response['html'] = $this->renderAjax('/lead/client-info/_client_manage_phone', [
					'clientPhones' => $lead->client->clientPhones,
					'lead' => $lead,
					'manageClientInfoAccess' => ClientInfoAccess::isUserCanManageLeadClientInfo($lead, Yii::$app->user->id)
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
			try {
				$id = (int)Yii::$app->request->get('pid');
				$gid = (string)Yii::$app->request->get('gid');
				$lead = $this->findLeadByGid($gid);
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
			}catch (\Throwable $e) {
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

		}catch (\Throwable $e) {
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
		$user = Yii::$app->user->identity;
		try {
			$gid = (string)Yii::$app->request->get('gid');
			$lead = $this->findLeadByGid($gid);

			if (!$lead->isOwner(Yii::$app->user->id) && !$user->isAnySupervision() && !$user->isAdmin() && !$user->isSuperAdmin()) {
				throw new HttpException(403, 'Access Denied');
			}

			$form = new PhoneCreateForm();
			$form->scenario = 'update';

			$form->load(Yii::$app->request->post());

			if (!($user->isAdmin() || $user->isSuperAdmin())) {
				$form->phone = null;
			} else {
				$form->required = true;
			}

			if ($form->validate()) {

				$this->clientManageService->updatePhone($form);

				$response['error'] = false;
				$response['message'] = 'Phone was successfully updated: ' . $form->phone;
				$response['html'] = $this->renderAjax('/lead/client-info/_client_manage_phone', [
					'clientPhones' => $lead->client->clientPhones,
					'lead' => $lead,
					'manageClientInfoAccess' => ClientInfoAccess::isUserCanManageLeadClientInfo($lead, Yii::$app->user->id)
				]);
			} else {
				$response['error'] = true;
				$response['message'] = $this->getParsedErrors($form->getErrors());
			}

			Yii::$app->response->format = Response::FORMAT_JSON;
			return $response;
		} catch (\Throwable $e) {
			Yii::error($e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'LeadViewController:actionAjaxEditClientPhone:Throwable');
		}

		throw new BadRequestHttpException();
	}

	/**
	 * @return string
	 * @throws BadRequestHttpException
	 */
	public function actionAjaxAddClientEmailModalContent():string
	{
		try {
			$gid = (string)Yii::$app->request->get('gid');

			$lead = $this->findLeadByGid($gid);

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

		}catch (\Throwable $e) {
			Yii::error($e->getMessage() . '; in File: ' . $e->getFile() . '; on Line: ' . $e->getLine(), 'LeadViewController:actionAjaxAddClientEmailValidation:Throwable');
		}
		throw new BadRequestHttpException();
	}

	/**
	 * @throws HttpException
	 */
	public function actionAjaxAddClientEmail()
	{
		$user = Yii::$app->user->identity;
		$gid = (string)Yii::$app->request->get('gid');
		$lead = $this->findLeadByGid($gid);

		if (!$lead->isOwner(Yii::$app->user->id) && !$user->isAnySupervision() && !$user->isAdmin() && !$user->isSuperAdmin()) {
			throw new HttpException(403, 'Access Denied');
		}

		try {
			$form = new EmailCreateForm();
			$form->client_id = $lead->client_id;
			$form->required = true;

			if ($form->load(Yii::$app->request->post()) && $form->validate()) {
				$this->clientManageService->addEmails($lead->client, [$form]);

				$response['error'] = false;
				$response['message'] = 'New email was successfully added: ' . $form->email;
				$response['html'] = $this->renderAjax('/lead/client-info/_client_manage_email', [
					'clientEmails' => $lead->client->clientEmails,
					'lead' => $lead,
					'manageClientInfoAccess' => ClientInfoAccess::isUserCanManageLeadClientInfo($lead, Yii::$app->user->id)
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
			try {
				$id = (int)Yii::$app->request->get('pid');
				$gid = (string)Yii::$app->request->get('gid');
				$lead = $this->findLeadByGid($gid);
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
			}catch (\Throwable $e) {
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

		}catch (\Throwable $e) {
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
		$user = Yii::$app->user->identity;
		try {
			$gid = (string)Yii::$app->request->get('gid');
			$lead = $this->findLeadByGid($gid);

			if (!$lead->isOwner(Yii::$app->user->id) && !$user->isAnySupervision() && !$user->isAdmin() && !$user->isSuperAdmin()) {
				throw new HttpException(403, 'Access Denied');
			}

			$form = new EmailCreateForm();
			$form->scenario = 'update';

			$form->load(Yii::$app->request->post());

			if (!($user->isAdmin() || $user->isSuperAdmin())) {
				$form->email = null;
			} else {
				$form->required = true;
			}

			if ($form->validate()) {
				$this->clientManageService->updateEmail($form);

				$response['error'] = false;
				$response['message'] = 'Email was successfully updated: ' . $form->email;
				$response['html'] = $this->renderAjax('/lead/client-info/_client_manage_email', [
					'clientEmails' => $lead->client->clientEmails,
					'lead' => $lead,
					'manageClientInfoAccess' => ClientInfoAccess::isUserCanManageLeadClientInfo($lead, Yii::$app->user->id)
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
			try {
				$gid = (string)Yii::$app->request->get('gid');
				$lead = $this->findLeadByGid($gid);

				$form = new ClientCreateForm();
				$form->id = $lead->client->id;
				$form->firstName = $lead->client->first_name;
				$form->lastName = $lead->client->last_name;
				$form->middleName = $lead->client->middle_name;

				return $this->renderAjax('partial/_client_edit_name_modal_content', [
					'editName' => $form,
					'lead' => $lead
				]);
			}catch (\Throwable $e) {
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
		}catch (\Throwable $e) {
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
			$response['message'] = 'User name was successfully updated';
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
                $this->leadCloneQuoteService->cloneByUid($form->uid, $form->leadGid);
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
				$leadPreferencesForm = new LeadPreferencesForm($lead);
				return $this->renderAjax('partial/_lead_preferences_edit_modal_content', [
					'leadPreferencesForm' => $leadPreferencesForm,
					'gid' => $lead->gid
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
		if (Yii::$app->request->isAjax) {
			$gid = Yii::$app->request->get('gid');
			$lead = $this->findLeadByGid($gid);
			if (!LeadPreferencesAccess::isUserCanManageLeadPreference($lead, Yii::$app->user->id)) {
				throw new HttpException(403, 'Access Denied');
			}

			try {
				$form = new LeadPreferencesForm($lead);
				if ($form->load(Yii::$app->request->post()) && $form->validate()) {

					$this->leadPreferencesManageService->edit($form, $lead);

					$lead->refresh();

					$response['error'] = false;
					$response['message'] = 'Lead preferences successfully updated';
					$response['html'] = $this->renderAjax('/lead/partial/_lead_preferences', [
						'lead' => $lead
					]);
				}else {
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
	 * @param array $errors
	 * @return string
	 */
    private function getParsedErrors(array $errors): string
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
