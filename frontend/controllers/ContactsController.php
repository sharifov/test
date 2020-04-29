<?php

namespace frontend\controllers;

use common\models\ClientPhone;
use common\models\Employee;
use common\models\UserContactList;
use sales\access\ClientInfoAccess;
use sales\access\ContactUpdateAccess;
use sales\auth\Auth;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\app\AppHelper;
use sales\services\client\ClientManageService;
use Yii;
use common\models\Client;
use common\models\search\ContactsSearch;
use yii\helpers\ArrayHelper;
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
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new Client();
        $model->cl_type_id = Client::TYPE_CONTACT;
        $post = Yii::$app->request->post();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $userContactList = new UserContactList();
            $userContactList->ucl_client_id = $model->id;
            $userContactList->ucl_user_id = Auth::id();
            $userContactList->ucl_favorite = (isset($post['ucl_favorite'])) ? (bool)$post['ucl_favorite'] : false;

            if(!$userContactList->save()) {
                Yii::error(VarDumper::dumpAsString($userContactList->errors),
                    'ContactsController:actionCreate:saveUserContactList');
            }

            /*
             $post = Yii::$app->request->post($model->formName());
             if(isset($post['projects'])) {
                foreach ($post['projects'] as $projectId) {
                    $clientProject = new ClientProject();
                    $clientProject->cp_client_id = $model->id;
                    $clientProject->cp_project_id = (int) $projectId;
                    $clientProject->save();
                    if(!$clientProject->save()) {
                        Yii::error(VarDumper::dumpAsString($clientProject->errors),
                            'ContactsController:actionCreate:saveClientProject');
                    }
                }
            }*/

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
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
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($userContactList = UserContactList::getUserContact(Auth::id(), $model->id)) {
                $userContactList->ucl_favorite = (isset($post['ucl_favorite'])) ? (bool)$post['ucl_favorite'] : false;
                if(!$userContactList->save()) {
                    Yii::error(VarDumper::dumpAsString($userContactList->errors),
                        'ContactsController:actionUpdate:saveUserContactList');
                }
            }

            /*if(isset($post['projects'])) {
                ClientProject::deleteAll(['cp_client_id' => $model->id]);
                foreach ($post['projects'] as $projectId) {
                    $clientProject = new ClientProject();
                    $clientProject->cp_client_id = $model->id;
                    $clientProject->cp_project_id = (int) $projectId;
                    $clientProject->save();
                    if(!$clientProject->save()) {
                        Yii::error(VarDumper::dumpAsString($clientProject->errors),
                            'ContactsController:actionUpdate:saveClientProject');
                    }
                }
            }*/

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
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
                    $text = $contact->first_name . ' ' . $contact->last_name;
                    $group = strtoupper($text[0] ?? '');
                    $contactData['id'] = $contact->id;
                    $contactData['name'] = $contact->is_company ? $contact->company_name : $text;
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
}
