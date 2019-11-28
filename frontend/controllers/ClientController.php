<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\search\lead\LeadSearchByClient;
use common\models\search\LeadSearch;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use sales\entities\cases\CasesSearch;
use sales\entities\cases\CasesSearchByClient;
use Yii;
use common\models\Client;
use common\models\search\ClientSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
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
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
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
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Client();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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
        $client = $this->findModel((int)$clientId);

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
            ['model' => $client],
            $providers)
        );
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

}
