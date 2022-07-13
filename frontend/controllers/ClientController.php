<?php

namespace frontend\controllers;

use common\models\Call;
use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use common\models\search\lead\LeadSearchByClient;
use common\models\search\LeadSearch;
use modules\lead\src\abac\LeadAbacObject;
use src\access\EmployeeDepartmentAccess;
use src\access\EmployeeProjectAccess;
use src\auth\Auth;
use src\entities\cases\Cases;
use src\entities\cases\CasesSearch;
use src\entities\cases\CasesSearchByClient;
use src\model\call\socket\CallUpdateMessage;
use src\model\client\abac\ClientAbacObject;
use Yii;
use common\models\Client;
use common\models\search\ClientSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends FController
{
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
            'access' => [
                'allowActions' => [
                    'ajax-get-info-json'
                ]
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $model = $this->findModel($id);
        $leadsQuery = Lead::find()->select(['id', 'gid', 'request_ip', 'status'])->where(['client_id' => $id])->asArray();
        $leadsDataProvider = new ActiveDataProvider([
            'query' => $leadsQuery,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        $casesQuery = Cases::find()->select(['cs_id', 'cs_gid', 'cs_status'])->where(['cs_client_id' => $id])->asArray();
        $casesDataProvider = new ActiveDataProvider([
            'query' => $casesQuery,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        return $this->render('view', [
            'model' => $model,
            'leadsDataProvider' => $leadsDataProvider,
            'casesDataProvider' => $casesDataProvider,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Client();
        $model->scenario = Client::SCENARIO_MANUALLY;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->cl_type_create = Client::TYPE_CREATE_MANUALLY;
                $model->save(false);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Client::SCENARIO_MANUALLY;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return Client
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Client
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     * @throws \ReflectionException
     */
    public function actionAjaxGetInfo(): string
    {
        if (!$clientId = Yii::$app->request->post('client_id')) {
            $clientId = Yii::$app->request->get('client_id');
        }
        if (!$callSid = Yii::$app->request->post('callSid')) {
            $callSid = Yii::$app->request->get('callSid');
        }
        $client = $this->findModel((int)$clientId);
        $case = Cases::findOne(Yii::$app->request->post('case_id'));
        $call = Call::findOne(['c_call_sid' => $callSid]);

        $providers = [];

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

//        if (Yii::$app->user->can('leadSection')) {
            $providers['leadsDataProvider'] = $this->getLeadsDataProvider($client->id, $user);
//        }
//        if (Yii::$app->user->can('caseSection')) {
            $providers['casesDataProvider'] = $this->getCasesDataProvider($client->id, Yii::$app->user->id);
//        }

        return $this->renderAjax('ajax_info', ArrayHelper::merge(
            [
                'model' => $client,
                'case' => $case,
                'call' => $call
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
    private function getCasesDataProvider(int $clientId, int $userId): ActiveDataProvider
    {
        $params[CasesSearchByClient::getShortName()]['clientId'] = $clientId;

        $dataProvider = (new CasesSearchByClient())->search($params, $userId);

        $dataProvider->query->orderBy(['cs_last_action_dt' => SORT_DESC]);

        $dataProvider->sort = false;

        $pagination = $dataProvider->pagination;
        $pagination->pageSize = 10;
        $pagination->params = array_merge(Yii::$app->request->get(), ['client_id' => $clientId]);
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
    private function getLeadsDataProvider(int $clientId, Employee $user): ActiveDataProvider
    {
        $params[LeadSearchByClient::getShortName()]['clientId'] = $clientId;

        $dataProvider = (new LeadSearchByClient())->search($params, $user);

        $dataProvider->query->orderBy(['l_last_action_dt' => SORT_DESC]);

        $dataProvider->sort = false;

        $pagination = $dataProvider->getPagination();
        $pagination->pageSize = 10;
        $pagination->params = array_merge(Yii::$app->request->get(), ['client_id' => $clientId]);
        $pagination->pageParam = 'lead-page';
        $pagination->pageSizeParam = 'lead-per-page';
        $dataProvider->setPagination($pagination);

        return $dataProvider;
    }

    public function actionAjaxGetInfoJson(): Response
    {
        $callId = Yii::$app->request->post('callId');
        if (!$call = Call::findOne($callId)) {
            throw new BadRequestHttpException('Call Not found');
        }

        /** @abac ClientAbacObject::ACT_GET_INFO_JSON, ClientAbacObject::ACTION_READ, get client info json for phone widget*/
        if (!(bool)\Yii::$app->abac->can(null, ClientAbacObject::ACT_GET_INFO_JSON, ClientAbacObject::ACTION_READ)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $result = [
            'error' => false,
            'message' => ''
        ];

        try {
            $data = (new CallUpdateMessage())->getContactData($call, Auth::id());
        } catch (\Throwable $e) {
            $result['error'] = true;
            $result['message'] = $e->getMessage();
        }

        return $this->asJson(ArrayHelper::merge($result, $data ?? []));
    }

    public function actionStats()
    {
        $projects = Project::find()->asArray()->all();
        $data = [];

        foreach ($projects as $project) {
            $data[] = [
                'projectId' => $project['id'],
                'projectName' => $project['name'],
                'countClients' => Client::find()->andWhere(['cl_project_id' => $project['id']])->count()
            ];
        }

        $data[] = [
            'projectId' => null,
            'projectName' => 'Without project',
            'countClients' => Client::find()->andWhere(['IS', 'cl_project_id', null])->count()
        ];

        return $this->render('stats', ['data' => $data]);
    }
}
