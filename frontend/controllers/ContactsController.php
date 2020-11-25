<?php

namespace frontend\controllers;

use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\UserContactList;
use frontend\models\form\ContactForm;
use frontend\widgets\newWebPhone\contacts\helper\ContactsHelper;
use sales\access\ContactUpdateAccess;
use sales\auth\Auth;
use sales\forms\CompositeFormHelper;
use sales\helpers\app\AppHelper;
use sales\services\client\ClientManageService;
use Throwable;
use Yii;
use common\models\Client;
use common\models\search\ContactsSearch;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

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
        $config = []
    ) {
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

//    /**
//     * Lists all Client models.
//     * @return mixed
//     */
//    public function actionIndex()
//    {
//        $searchModel = new ContactsSearch(Auth::id());
//
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

    /**
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContactsSearch(Auth::id());

        $dataProvider = $searchModel->searchUnion(Yii::$app->request->queryParams);

//        $models = $dataProvider->getModels();
//        VarDumper::dump($models);die;

        $contacts = [];
        foreach ($dataProvider->getModels() as $item) {
            $item['type'] = (int)$item['type'];
            $item['disabled'] = (int)$item['disabled'];
            if ($item['type'] === Client::TYPE_INTERNAL) {
                $item['model'] = Employee::find()->andWhere(['id' => $item['id']])->with(['userProjectParams.phoneList', 'userProjectParams.emailList'])->one();
            } else {
                $item['model'] = Client::find()->andWhere(['id' => $item['id']])->with(['clientPhones', 'clientEmails'])->one();
            }
            $contacts[] = $item;
        }
        $dataProvider->setModels($contacts);

        return $this->render('index_union', [
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
        $client->cl_type_create = Client::TYPE_CREATE_MANUALLY;
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

                    if (!$userContactList->save()) {
                        Yii::error(
                            VarDumper::dumpAsString($userContactList->errors),
                            'ContactsController:actionCreate:saveUserContactList'
                        );
                    }

                    $this->clientManageService->addEmails($client, $form->emails);
                    $this->clientManageService->addPhones($client, $form->phones);

                    Yii::$app->session->setFlash('success', 'Contact ' . $client->getNameByType() . ' save');
                    return $this->redirect(['view', 'id' => $client->id]);
                }

                if ($client->errors) {
                    Yii::error(
                        VarDumper::dumpAsString($client->errors),
                        'ContactsController:actionCreate:updateClient'
                    );
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
                    if ($userContactList = UserContactList::findOne(['ucl_client_id' => $client->id])) {
                        $userContactList->ucl_favorite = (isset($post['ucl_favorite'])) ? (bool)$post['ucl_favorite'] : false;
                        if (!$userContactList->save()) {
                            Yii::error(
                                VarDumper::dumpAsString($userContactList->errors),
                                'ContactsController:actionUpdate:saveUserContactList'
                            );
                        }
                    }

                    ClientEmail::deleteAll(['client_id' => $client->id]);
                    ClientPhone::deleteAll(['client_id' => $client->id]);

                    $this->clientManageService->addEmails($client, $form->emails);
                    $this->clientManageService->addPhones($client, $form->phones);

                    Yii::$app->session->setFlash('success', 'Contact ' . $client->getNameByType() . ' updated');
                    return $this->redirect(['view', 'id' => $client->id]);
                }

                if ($client->errors) {
                    Yii::error(
                        VarDumper::dumpAsString($client->errors),
                        'ContactsController:actionCreate:updateClient'
                    );
                }
            } catch (\Throwable $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        foreach ($client->getAttributes() as $name => $value) {
            if ($name !== 'cl_type_create') {
                $form->{$name} = $value;
            }
        }

        $form->emails = $client->clientEmails;
        $form->phones = $client->clientPhones;

        $favorite = false;
        if ($contact = UserContactList::findOne(['ucl_client_id' => $client->id])) {
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
     * @param int $id
     * @return Response
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete(int $id): Response
    {
        $client = $this->findModel($id);

        if (!(new ContactUpdateAccess())->isUserCanUpdateContact($client, Auth::user())) {
            throw new HttpException(403, 'Access Denied');
        }

        $this->clientManageService->removeContact($client);

        return $this->redirect(['index']);
    }

    /**
     * @return array
     */
    public function actionSetFavoriteAjax(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['message' => '', 'status' => 0, 'favorite' => 0];

        if (Yii::$app->request->isAjax) {
            $clientId = (int) Yii::$app->request->post('client_id');
            $isFavorite = (bool) Yii::$app->request->post('is_favorite');
            $ucl_favorite = $isFavorite ? false : true;

            if ($userContactList = UserContactList::findOne(['ucl_client_id' => $clientId])) {
                $userContactList->ucl_favorite = $ucl_favorite;

                if ($userContactList->save()) {
                    $result['status'] = 1;
                    $result['favorite'] = (int)$ucl_favorite;
                } else {
                    $result['message'] = $userContactList->getErrorSummary(false)[0];
                    Yii::error(
                        VarDumper::dumpAsString($userContactList->errors),
                        'ContactsController:actionSetFavoriteAjax:saveUserContactList'
                    );
                }
            } else {
                $result['message'] = 'Client not found';
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function actionSetDisabledAjax(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['message' => '', 'status' => 0, 'disabled' => 0];

        if (Yii::$app->request->isAjax) {
            try {
                $clientId = (int) Yii::$app->request->post('client_id');
                $isDisabled = (bool) Yii::$app->request->post('is_disabled');
                $disabled = $isDisabled ? false : true;

                if ($client = $this->findModel($clientId)) {
                    $client->disabled = $disabled;

                    if ($client->save()) {
                        $result['status'] = 1;
                        $result['disabled'] = (int)$disabled;
                    } else {
                        throw new \DomainException($client->getErrorSummary(false)[0]);
                    }
                } else {
                    throw new \DomainException('Client not found');
                }
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'ContactsController:actionSetDisabledAjax:save');
                $result['message'] = $throwable->getMessage();
            }
        }
        return $result;
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Client
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * From Contacts section full list on Phone Widget
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionFullListAjax(): Response
    {
        if (!(Yii::$app->request->isAjax && Yii::$app->request->isPost)) {
            throw new BadRequestHttpException();
        }

        $page = (int)Yii::$app->request->post('page', 0);

        $provider = (new ContactsSearch(Auth::id()))->searchByWidgetContactsSection();
        $provider->getPagination()->setPage($page);
        $rows = $provider->getModels();

        $out = [
            'results' => ContactsHelper::processContactsList($rows),
            'page' => $page + 1,
            'rows' => empty($rows)
        ];

//        VarDumper::dump($out);die;

        return $this->asJson($out);
    }

    /**
     * From Contacts section with search on Phone Widget
     *
     * @param string|null $q
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionSearchListAjax(?string $q = null): Response
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        $out = ['results' => []];

        if ($q !== null) {
            if (strlen($q) < 2) {
                return $this->asJson($out);
            }

            $provider = (new ContactsSearch(Auth::id()))->searchByWidgetContactsSection($q);
            ($provider->getPagination())->setPageSize(null);

            $rows = $provider->getModels();

            $out = [
                'results' => ContactsHelper::processContactsList($rows),
                'rows' => empty($rows)
            ];
        }

//        VarDumper::dump($out);die;

        return $this->asJson($out);
    }

    /**
     * From Call section on Phone Widget
     *
     * @param string|null $q
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionListCallsAjax(?string $q = null): Response
    {
//        if (!(Yii::$app->request->isAjax && Yii::$app->request->isPost)) {
//            throw new BadRequestHttpException();
//        }

        if (!Auth::can('PhoneWidget_DialpadSearch')) {
            throw new ForbiddenHttpException('Access denied');
        }

        $out = ['results' => []];

        if ($q !== null) {
            if (strlen($q) < 3) {
                return $this->asJson($out);
            }

            $contacts = (new ContactsSearch(Auth::id()))->searchByWidgetCallSection($q, $limit = 3);

//            VarDumper::dump($contacts);die;

            $data = [];
            if ($contacts) {
                foreach ($contacts as $n => $contact) {
                    if ($contact['is_company']) {
                        $name = $contact['company_name'] ?: $contact['full_name'];
                    } else {
                        $name = $contact['full_name'];
                    }
                    $contactData = [];
                    $contactData['id'] = $contact['id'];
                    $contactData['name'] = StringHelper::truncate($name, 18, '...') . ' ' . $contact['phone'];
                    $contactData['phone'] = $contact['phone'];
                    $contactData['type'] = (int)$contact['type'];
                    $contactData['title'] = StringHelper::truncate($name, 18, '...');

                    if ($contactData['type'] === Client::TYPE_INTERNAL) {
                        $isOnline = (int)$contact['user_is_online'] ? true : false;
                        $isCallFree = (int)$contact['user_is_on_call'] ? false : true;
                        $isCallStatusReady = (int)$contact['user_call_phone_status'] ? true : false;
                        if ($isOnline && $isCallFree && $isCallStatusReady) {
                            $class = 'text-success';
                        } elseif ($isOnline && $isCallStatusReady) {
                            $class = 'text-warning';
                        } else {
                            $class = 'text-danger';
                        }
                        $contactData['user_status_class'] = $class;
                    }

                    $data[] = $contactData;
                }
            }

            $out['results'] = $data;
        }

        return $this->asJson($out);
    }
}
