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

                    Yii::$app->session->setFlash('success', 'Contact ' . $client->getNameByType() . ' save');
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
     * @throws HttpException
     */
    public function actionUpdate($id)
    {
        $client = $this->findModel($id);

        if (!(new ContactUpdateAccess())->isUserCanUpdateContact($client, Auth::user())) {
			throw new HttpException(403, 'Access Denied');
		}

        $post = Yii::$app->request->post();
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

                    if (isset($data['post']['EmailCreateForm'])) {
                        foreach ($data['post']['EmailCreateForm'] as $key => $value) {
                            $emailCreateForm = new EmailCreateForm();
			                $emailCreateForm->required = true;
			                $emailCreateForm->email = $value['email'];
			                $emailCreateForm->type = $value['type'] ?? null;
			                $emailCreateForm->ce_title = $value['ce_title'];
			                $emailCreateForm->client_id = $client->id;

                            $this->clientManageService->addEmail($client, $emailCreateForm);
                        }
                    }
                    if (isset($data['post']['PhoneCreateForm'])) {
                        foreach ($data['post']['PhoneCreateForm'] as $key => $value) {
                            $phoneCreateForm = new PhoneCreateForm();
			                $phoneCreateForm->required = true;
			                $phoneCreateForm->phone = $value['phone'];
			                $phoneCreateForm->type = $value['type'] ?? null;
			                $phoneCreateForm->cp_title = $value['cp_title'];
			                $phoneCreateForm->client_id = $client->id;

                            $this->clientManageService->addPhone($client, $phoneCreateForm);
                        }
                    }

                    Yii::$app->session->setFlash('success', 'Contact ' . $client->getNameByType() . ' updated');
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
        $client = $this->findModel($id);

        if (!(new ContactUpdateAccess())->isUserCanUpdateContact($client, Auth::user())) {
			throw new HttpException(403, 'Access Denied');
		}

        $client->delete();

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
