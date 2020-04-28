<?php

namespace frontend\controllers;

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
    /* TODO:: add access to actions - ContactUpdateAccess */

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
        $post = Yii::$app->request->post($model->formName());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $userContactList = new UserContactList();
            $userContactList->ucl_client_id = $model->id;
            $userContactList->ucl_user_id = Auth::id();

            if(!$userContactList->save()) {
                Yii::error(VarDumper::dumpAsString($userContactList->errors),
                    'ContactsController:actionCreate:saveUserContactList');
            }

            /*if(isset($post['projects'])) {
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
        $post = Yii::$app->request->post($model->formName());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

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
				$this->clientManageService->addPhone($client, $form);

				$response['error'] = false;
				$response['message'] = 'New phone was successfully added: ' . $form->phone;
				$response['html'] = $this->renderAjax('/lead/client-info/_client_manage_phone', [
					'clientPhones' => $client->clientPhones,
					'lead' => $client,
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
}
