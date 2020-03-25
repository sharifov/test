<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\search\LeadFlightSegmentSearch;
use common\models\search\LeadSearch;
use common\models\search\QuoteSearch;
use frontend\widgets;
use Yii;
use common\models\Lead;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * LeadsController implements the CRUD actions for Lead model.
 *
 * @property widgets\lead\editTool\Service $toolService
 */
class LeadsController extends FController
{
    private $toolService;

    public function __construct($id, $module, widgets\lead\editTool\Service $toolService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->toolService = $toolService;
    }

    public function behaviors()
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
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionEdit(): Response
    {
        $lead = $this->findModel((int)Yii::$app->request->post('id'));
        $form = new widgets\lead\editTool\Form($lead);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->toolService->edit($lead, $form);
                return $this->asJson(['success' => true]);
            } catch (\DomainException $e) {
                return $this->asJson(['success' => false, 'text'=> $e->getMessage()]);
            }
        } else {
            return $this->asJson(['data' => $this->renderAjax('_tool_edit', ['lead' => $lead])]);
        }
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionEditValidation(): array
    {
        $lead = $this->findModel((int)Yii::$app->request->post('id'));
        $form = new widgets\lead\editTool\Form($lead);
        if (Yii::$app->request->isAjax && $form->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($form);
        }
        throw new BadRequestHttpException();
    }

    /**
     * Lists all Lead models.
     * @return mixed
     */
    public function actionIndex()
    {
        $session = Yii::$app->session;

        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();


        $params = ArrayHelper::merge($params, $params2);

        if (isset($params['reset'])){
            $params = [];
            $session->remove('LeadSearch');
        }

        /*if (empty($params) && $session->has('LeadSearch')){
            $params = $session->get('LeadSearch');

            //VarDumper::dump($params, 10, true);

        } elseif (!empty($params)){
            $session->set('LeadSearch', $params);
        }*/

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($user->isAgent()) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if ($user->isSupervision()) {
            $params['LeadSearch']['supervision_id'] = $user->id;
        }

        if (!$params && $isAgent) {
            $params['LeadSearch']['employee_id'] = $user->id;
        }

        if ($isAgent) {
            $dataProvider = $searchModel->searchAgent($params);
        } else {
            $dataProvider = $searchModel->search($params);
        }

        /*if($isAgent) {
            $user = Yii::$app->user->identity;
            $checkShiftTime = $user->checkShiftTime();

        }*/

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isAgent' => $isAgent,
        ]);
    }

    /**
     * Lists all Lead models.
     * @return mixed
     */
    public function actionExport()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;

        if(Yii::$app->user->identity->canRole('supervision')) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchExport($params);

        return $this->render('export', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionExportCsv()
    {
        //set_time_limit(30);
        //ini_set('memory_limit', '512M');
        $searchModel = new LeadSearch();
        $params = Yii::$app->request->queryParams;
        if(Yii::$app->user->identity->canRole('supervision')) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->searchExport($params);
        $totalLeads = $dataProvider->query->count();
        //$totalLeads = Lead::find()->count();

        $limit = 10000;
        $queryIterations = ceil($totalLeads / $limit);

        $fpath = fopen(Yii::getAlias('@runtime'. '/file.csv'), 'w');

        for ($i = 0; $i < $queryIterations; $i++){
            $offset = $i * $limit;
            $dataProvideQuery = $searchModel->searchExportCsv($params, $offset, $limit);

            foreach ($dataProvideQuery as $rowIndex => $row){
                if ($i == 0 && $rowIndex == 0){
                    fputcsv($fpath, array_keys($row));
                }

                if (!empty($row['l_type_create'])){
                    $row['l_type_create'] = Lead::TYPE_CREATE_LIST[$row['l_type_create']];
                }
                if (!empty($row['status'])){
                    $row['status'] = Lead::STATUS_LIST[$row['status']];
                }

                fputcsv($fpath, $row);
            }

    }

        if(fclose($fpath)){
            //return $this->redirect(['leads/download-csv']);
            return 'success';
        }
    }

    public function actionDownloadCsv()
    {
        $path = Yii::getAlias('@runtime');
        $file = $path . '/file.csv';

        if (file_exists($file)) {
            Yii::$app->response->sendFile($file);
        }
    }

    public function actionFileSize()
    {
        $path = Yii::getAlias('@runtime');
        $file = $path . '/file.csv';
        if (file_exists($file)) {
            return filesize($file);
        }
    }

    /**
     * Lists all Lead models.
     * @return mixed
     */
    public function actionDuplicate()
    {
        $searchModel = new LeadSearch();
        $dataProviderEmail = $searchModel->searchEmail(Yii::$app->request->queryParams);
        $dataProviderPhone = $searchModel->searchPhone(Yii::$app->request->queryParams);
        $dataProviderIp = $searchModel->searchIp(Yii::$app->request->queryParams);


        //VarDumper::dump($dataProvider, 10, true); exit;

        return $this->render('duplicate', [
            'searchModel' => $searchModel,
            'dataProviderEmail' => $dataProviderEmail,
            'dataProviderPhone' => $dataProviderPhone,
            'dataProviderIp' => $dataProviderIp,
        ]);
    }


    /**
     * Displays a single Lead model.
     * @param integer $id
     * @param null|string $showInPopUp
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $showInPopUp = null)
    {
        $model = $this->findModel($id);

        $searchModel = new QuoteSearch();
        $searchModelSegments = new LeadFlightSegmentSearch();

        $params = Yii::$app->request->queryParams;
        $params['QuoteSearch']['lead_id'] = $model->id;
        $dataProvider = $searchModel->search($params);


        $params = Yii::$app->request->queryParams;
        $params['LeadFlightSegmentSearch']['lead_id'] = $model->id;
        $dataProviderSegments = $searchModelSegments->search($params);

        //unset($searchModel);

        // VarDumper::dump($quotes, 10, true);


        $viewParams = [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            'searchModelSegments' => $searchModelSegments,
            'dataProviderSegments' => $dataProviderSegments,
        ];

        if (Yii::$app->request->isAjax) {
            /*$viewParams['searchModel'] = null;
            $viewParams['dataProvider']->sort = false;
            $viewParams['searchModelSegments'] = null;
            $viewParams['dataProviderSegments']->sort = false;*/

            //return $this->renderAjax('view', $viewParams);
        }

        if ($showInPopUp === 'modal'){
            return $this->renderAjax('view', $viewParams);
        } else {
            return $this->render('view', $viewParams);
        }
    }

    /**
     * Creates a new Lead model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Lead();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Lead model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
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
     * Deletes an existing Lead model.
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
     * Finds the Lead model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lead::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
