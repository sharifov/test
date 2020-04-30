<?php

namespace frontend\controllers;

use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\UserContactList;
use frontend\models\form\ContactForm;
use sales\access\ContactUpdateAccess;
use sales\auth\Auth;
use sales\forms\CompositeFormHelper;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\app\AppHelper;
use sales\services\client\ClientManageService;
use Yii;
use common\models\Client;
use common\models\search\ContactsSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * ContactsController implements the CRUD actions for Client model.
 * @property  ClientManageService $clientManageService
 */
class ContactsController extends FController
{
    /**
	 * @var ClientManageService
	 */
	private $clientManageService;

	/**
	 * LeadViewController constructor.
	 * @param $id
	 * @param $module
	 * @param ClientManageService $clientManageService
	 * @param array $config
	 */
	public function __construct(
		$id,
		$module,
		ClientManageService $clientManageService,
		$config = [])
	{
		$this->clientManageService = $clientManageService;
		parent::__construct($id, $module, $config);
	}

    /**
     * @return array
     */
    public function behaviors(): array
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
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContactsSearch(Auth::id());

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $client = new Client();
        $client->cl_type_id = Client::TYPE_CONTACT;
        $contactForm = new ContactForm();
        $post = Yii::$app->request->post();

        if ($post) {
            /*\Yii::info(
                \yii\helpers\VarDumper::dumpAsString($post, 10, true),
                'info\Debug:' . self::class . ':' . __FUNCTION__
            );*/
            /* TODO: to remove */
        }

        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'ContactForm',
            ['emails' => 'EmailCreateForm', 'phones' => 'PhoneCreateForm',]
        );
        $form = new ContactForm(count($data['post']['EmailCreateForm']), count($data['post']['PhoneCreateForm']));

        if ($form->load($data['post']) && $form->validate()) {

            try {

                if ($client->load(Yii::$app->request->post(), $contactForm->formName()) && $client->save()) {

                    $userContactList = new UserContactList();
                    $userContactList->ucl_client_id = $client->id;
                    $userContactList->ucl_user_id = Auth::id();
                    $userContactList->ucl_favorite = (isset($post['ucl_favorite'])) ? (bool)$post['ucl_favorite'] : false;

                    if(!$userContactList->save()) {
                        Yii::error(VarDumper::dumpAsString($userContactList->errors),
                            'ContactsController:actionCreate:saveUserContactList');
                    }

                    $this->clientManageService->addEmails($client, $form->emails);
                    $this->clientManageService->addPhones($client, $form->phones);

                    Yii::$app->session->setFlash('success', 'Contact save');
                    return $this->redirect(['view', 'id' => $client->id]);
                }

                if ($client->errors) {
                    Yii::error( VarDumper::dumpAsString($client->errors),
                        'ContactsController:actionCreate:updateClient');
                }
            } catch (\Throwable $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['/contacts/create']);
            }
        }
        return $this->render('create', [
            'contactForm' => $form,
            'model' => $client,
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($id)
    {
        $client = $this->findModel($id);

        if (!(new ContactUpdateAccess())->isUserCanUpdateContact($client, Auth::user())) {
			throw new HttpException(403, 'Access Denied');
		}

        $post = Yii::$app->request->post();

        if ($post) {
            /*\Yii::info(
                \yii\helpers\VarDumper::dumpAsString($post, 10, true),
                'info\Debug:' . self::class . ':' . __FUNCTION__
            );*/
            /* TODO: to remove */
        }

        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'ContactForm',
            ['emails' => 'EmailCreateForm', 'phones' => 'PhoneCreateForm',]
        );
        $form = new ContactForm(count($data['post']['EmailCreateForm']), count($data['post']['PhoneCreateForm']));

        if ($form->load($data['post']) && $form->validate()) {

            try {

                if ($client->load(Yii::$app->request->post(), (new ContactForm())->formName()) && $client->save()) {

                    if ($userContactList = UserContactList::getUserContact(Auth::id(), $client->id)) {
                        $userContactList->ucl_favorite = (isset($post['ucl_favorite'])) ? (bool)$post['ucl_favorite'] : false;
                        if(!$userContactList->save()) {
                            Yii::error(VarDumper::dumpAsString($userContactList->errors),
                                'ContactsController:actionUpdate:saveUserContactList');
                        }
                    }

                    ClientEmail::deleteAll(['client_id' => $client->id]);
                    ClientPhone::deleteAll(['client_id' => $client->id]);

                    if (isset($post[$form->formName()]['emails']['email'])) {
                        foreach ($post[$form->formName()]['emails']['email'] as $key => $value) {
                            $emailCreateForm = new EmailCreateForm();
                            $emailCreateForm->scenario = 'update';
			                $emailCreateForm->required = true;
			                $emailCreateForm->email = $value;
			                $emailCreateForm->client_id = $client->id;

                            $this->clientManageService->addEmail($client, $emailCreateForm);
                        }
                    }

                    if (isset($post[$form->formName()]['phones']['phone'])) {
                        foreach ($post[$form->formName()]['phones']['phone'] as $key => $value) {
                            $phoneCreateForm = new PhoneCreateForm();
                            $phoneCreateForm->scenario = 'update';
			                $phoneCreateForm->required = true;
			                $phoneCreateForm->phone = $value;
			                $phoneCreateForm->client_id = $client->id;

                            $this->clientManageService->addPhone($client, $phoneCreateForm);
                        }
                    }

                    Yii::$app->session->setFlash('success', 'Contact save');
                    return $this->redirect(['view', 'id' => $client->id]);
                }

                if ($client->errors) {
                    Yii::error( VarDumper::dumpAsString($client->errors),
                        'ContactsController:actionCreate:updateClient');
                }
            } catch (\Throwable $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        foreach ($client->getAttributes() AS $name => $value) {
            $form->{$name} = $value;
        }

        $form->emails = $client->clientEmails;
        $form->phones = $client->clientPhones;

        $favorite = false;
        if ($contact = UserContactList::getUserContact(Auth::id(), $client->id)) {
            $favorite = $contact->ucl_favorite;
        }

        return $this->render('update', [
            'model' => $client,
            'contactForm' => $form,
            'favorite' => $favorite,
        ]);
    }


    /**
     * @return array
     */
    public function actionValidateContact(): array
    {
        $post = Yii::$app->request->post();
        if ($post) {
            /*\Yii::info(
                \yii\helpers\VarDumper::dumpAsString($post, 10, true),
                'info\Debug:' . self::class . ':' . __FUNCTION__
            );*/
            /* TODO: to remove */
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = CompositeFormHelper::prepareDataForMultiInput(
            Yii::$app->request->post(),
            'ContactForm',
            ['emails' => 'EmailCreateForm', 'phones' => 'PhoneCreateForm',]
        );
        $form = new ContactForm(count($data['post']['EmailCreateForm']), count($data['post']['PhoneCreateForm']));
        $form->load($data['post']);
        return CompositeFormHelper::ajaxValidate($form, $data['keys']);
    }

    /**
     * Deletes an existing Client model.
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
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxAddContactPhoneModalContent():string
	{
		try {
			$clientId = (int)Yii::$app->request->get('client_id');

			return $this->renderAjax('partial/_contact_add_phone_modal_content', [
				'addPhone' => new PhoneCreateForm(),
				'client' => Client::findOne($clientId),
			]);
		} catch (\Throwable $throwable) {
			Yii::error(AppHelper::throwableFormatter($throwable), 'ContactsController:actionAjaxAddClientPhoneModalContent:Throwable');
		}
		throw new BadRequestHttpException();
	}

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionAjaxAddContactPhoneValidation(): array
	{
		$clientId = (int)Yii::$app->request->get('client_id');

		try {
			$form = new PhoneCreateForm();
			$form->required = true;

			if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
				$form->client_id = $clientId;
				Yii::$app->response->format = Response::FORMAT_JSON;
				return ActiveForm::validate($form);
			}

		}catch (\Throwable $throwable) {
			Yii::error(AppHelper::throwableFormatter($throwable), 'ContactsController:actionAjaxAddContactPhoneValidation:Throwable');
		}
		throw new BadRequestHttpException();
	}

    /**
     * @return mixed
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function actionAjaxAddContactPhone()
	{
		$clientId = (int)Yii::$app->request->get('client_id');
        $client = Client::findOne($clientId);

        if (!$client) {
			throw new HttpException(403, 'Client not found');
		}
		if (!(new ContactUpdateAccess())->isUserCanUpdateContact($client, Auth::user())) {
			throw new HttpException(403, 'Access Denied');
		}

		try {
			$form = new PhoneCreateForm();
			$form->client_id = $clientId;
			$form->required = true;

			if ($form->load(Yii::$app->request->post()) && $form->validate()) {
				$phone = $this->clientManageService->addPhone($client, $form);

				$response['error'] = false;
				$response['message'] = 'New phone was successfully added: ' . $form->phone;
				$response['html'] = $this->renderAjax('partial/_phone_row', [
					'phone' => $phone,
				]);
			} else {
				$response['error'] = true;
				$response['message'] = $this->getParsedErrors($form->getErrors());
			}

			Yii::$app->response->format = Response::FORMAT_JSON;
			return $response;
		} catch (\Throwable $throwable) {
			Yii::error(AppHelper::throwableFormatter($throwable), 'ContactsController:actionAjaxAddClientPhone:Throwable');
		}

		throw new BadRequestHttpException();
	}

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionAjaxEditContactPhoneModalContent(): string
    {
		if (Yii::$app->request->isAjax) {
			try {
				$phoneId = (int)Yii::$app->request->get('phone_id');

				if ($phone = ClientPhone::findOne($phoneId)) {

					$phoneForm = new PhoneCreateForm();
					$phoneForm->id = $phone->id;
					$phoneForm->phone = $phone->phone;
					$phoneForm->type = $phone->type;
					$phoneForm->client_id = $phone->client_id;

					return $this->renderAjax('partial/_contact_edit_phone_modal_content', [
						'editPhone' => $phoneForm,
						'client' => Client::findOne($phone->client_id),
					]);
				}
			} catch (\Throwable $throwable) {
				Yii::error(AppHelper::throwableFormatter($throwable), 'ContactsController:actionAjaxEditContactPhoneModalContent:Throwable');
			}
		}
		throw new BadRequestHttpException();
	}

	/**
	 * @return array
	 * @throws BadRequestHttpException
     */
	public function actionAjaxEditContactPhoneValidation(): array
	{
		$clientId = (int)Yii::$app->request->get('client_id');

		try {
			$form = new PhoneCreateForm();
			$form->scenario = 'update';
			$form->required = true;

			if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())){

			    if (!$client = Client::findOne($clientId)) {
                    throw new HttpException(403, 'Client not found');
                }

				$form->client_id = $client->id;
				Yii::$app->response->format = Response::FORMAT_JSON;
				return ActiveForm::validate($form);
			}

		}catch (\Throwable $throwable) {
			Yii::error(AppHelper::throwableFormatter($throwable), 'ContactsController:actionAjaxEditContactPhoneValidation:Throwable');
		}
		throw new BadRequestHttpException();
	}

	public function actionAjaxEditContactPhone()
	{
		try {

            $clientId = (int)Yii::$app->request->get('client_id');
            $client = Client::findOne($clientId);

            if (!$client) {
                throw new HttpException(403, 'Client not found');
            }
            if (!(new ContactUpdateAccess())->isUserCanUpdateContact($client, Auth::user())) {
                throw new HttpException(403, 'Access Denied');
            }

			$form = new PhoneCreateForm();
			$form->scenario = 'update';
            $form->required = true;

			$form->load(Yii::$app->request->post());

			if ($form->validate()) {

				$phone = $this->clientManageService->updatePhone($form);

				$response['error'] = false;
				$response['message'] = 'Phone was successfully updated: ' . $form->phone;
				$response['html'] = $this->renderAjax('partial/_phone_row', [
					'phone' => $phone,
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

    public function actionListAjax(?string $q = null): Response
    {
        $out = ['results' => []];

        if ($q !== null) {

            if (strlen($q) < 2) {
                return $this->asJson($out);
            }

            /** @var ContactsSearch[] $contacts */
            $contacts = (new ContactsSearch(Auth::id()))->searchByWidget($q)->getModels();
//sleep(4);
            $data = [];
            if ($contacts) {
                foreach ($contacts as $n => $contact) {
                    $contactData = [];
                    if ($contact->is_company) {
                        $name = $contact->company_name ?: $contact->first_name . ' ' . $contact->last_name;
                    } else {
                        $name = $contact->first_name . ' ' . $contact->last_name;
                    }
                    $group = strtoupper($name[0] ?? 'A');
                    $contactData['id'] = $contact->id;
                    $contactData['name'] = $name;
                    $contactData['description'] = $contact->description;
                    $contactData['avatar'] = $group;
                    $contactData['is_company'] = $contact->is_company;
                    if ($contact->clientPhones) {
                        foreach ($contact->clientPhones as $phone) {
                            $contactData['phones'][] = $phone->phone;
                        }
                    }
                    if ($contact->clientEmails) {
                        foreach ($contact->clientEmails as $email) {
                            $contactData['emails'][] = $email->email;
                        }
                    }
                    //$data[$n]['selection'] = $item['text'];
                    $data[$group][$n] = $contactData;
                }
            }

            $out['results'] = $data;
        }

        return $this->asJson($out);
    }

    public function actionListCallsAjax(?string $q = null): Response
    {
        $out = ['results' => []];

        if ($q !== null) {

            if (strlen($q) < 3) {
                return $this->asJson($out);
            }

            /** @var ContactsSearch[] $contacts */
            $contacts = (new ContactsSearch(Auth::id()))->searchByWidget($q, $limit = 3)->getModels();
//sleep(4);
            $data = [];
            if ($contacts) {
                foreach ($contacts as $n => $contact) {
                    if ($contact->is_company) {
                        $name = $contact->company_name ?: $contact->first_name . ' ' . $contact->last_name;
                    } else {
                        $name = $contact->first_name . ' ' . $contact->last_name;
                    }
                    if ($contact->clientPhones) {
                        foreach ($contact->clientPhones as $phone) {
                            $contactData = [];
                            $contactData['id'] = $contact->id;
                            $contactData['name'] = StringHelper::truncate($name, 18, '...') . ' ' . $phone->phone;
                            $contactData['phone'] = $phone->phone;
                            $data[] = $contactData;
                            if (count($data) === 3) {
                                break 2;
                            }
                        }
                    }
                }
            }

            $out['results'] = $data;
        }

        return $this->asJson($out);
    }
}
