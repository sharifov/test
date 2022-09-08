<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\search\LeadFlightSegmentSearch;
use common\models\search\LeadSearch;
use common\models\search\QuoteSearch;
use frontend\widgets;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileLead\FileLeadQuery;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use modules\lead\src\abac\LeadSearchAbacObject;
use src\auth\Auth;
use Yii;
use common\models\Lead;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
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
            'access' => [
                'allowActions' => [
                    'view'
                ],
            ],
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
                return $this->asJson(['success' => false, 'text' => $e->getMessage()]);
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

        if (isset($params['reset'])) {
            $params = [];
            $session->remove('LeadSearch');
        }

        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        /** @abac null, LeadSearchAbacObject::ADVANCED_SEARCH, LeadSearchAbacObject::ACTION_ACCESS, Access to Advanced Search Lead  */
        $accessAdvancedSearch = Yii::$app->abac->can(null, LeadSearchAbacObject::ADVANCED_SEARCH, LeadSearchAbacObject::ACTION_ACCESS);

        /** @fflag FFlag::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH, Remove User Group limitation for Search Leads for Sale Supervisor role */
        if (!\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH)) {
            if ($user->isSupervision()) {
                $params['LeadSearch']['supervision_id'] = $user->id;
            }
        }


        if (!$params && !$accessAdvancedSearch) {
            $params['LeadSearch']['employee_id'] = $user->id;
        }

        if (!isset($params['LeadSearch']['l_is_test'])) {
            $params['LeadSearch']['l_is_test'] = '0';
        }

        if (!$accessAdvancedSearch) {
            $dataProvider = $searchModel->searchAgent($params, Auth::user());
        } else {
            $dataProvider = $searchModel->search($params, Auth::user());
            if (
                FileStorageSettings::isEnabled()
                && is_array($searchModel->show_fields)
                && in_array('count_files', $searchModel->show_fields, false)
            ) {
                $models = $dataProvider->getModels();
                $ids = ArrayHelper::getColumn($models, 'id');
                $files = FileLead::find()
                    ->select('count(*), fld_lead_id')
                    ->andWhere(['fld_lead_id' => $ids])
                    ->groupBy(['fld_lead_id'])
                    ->asArray()
                    ->indexBy('fld_lead_id')
                    ->all();
                /** @var LeadSearch $model */
                foreach ($models as $model) {
                    if (array_key_exists($model->id, $files)) {
                        $model->count_files = $files[$model->id]['count'];
                    } else {
                        $model->count_files = 0;
                    }
                }
                $dataProvider->setModels($models);
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'accessAdvancedSearch' => $accessAdvancedSearch,
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

        /** @fflag FFlag::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH, Remove User Group limitation for Search Leads for Sale Supervisor role */
        if (!\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH)) {
            if (Yii::$app->user->identity->canRole('supervision')) {
                $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
            }
        }

        $dataProvider = $searchModel->searchExport($params);

        return $this->render('export', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionExportCsv()
    {
        $searchModel = new LeadSearch();
        $params = Yii::$app->request->queryParams;
        /** @fflag FFlag::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH, Remove User Group limitation for Search Leads for Sale Supervisor role */
        if (!\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_REMOVE_LIMITATION_SUPERVISION_IN_LEAD_SEARCH)) {
            if (Yii::$app->user->identity->canRole('supervision')) {
                $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
            }
        }

        $dataProvider = $searchModel->searchExport($params);
        $totalLeads = $dataProvider->query->count();
        //$totalLeads = Lead::find()->count();

        $limit = 10000;
        //$queryIterations = ceil($totalLeads / $limit);
        $queryIterations = 5;

        $fpath = fopen(Yii::getAlias('@runtime' . '/file.csv'), 'w');

        for ($i = 0; $i < $queryIterations; $i++) {
            $offset = $i * $limit;
            $dataProvideQuery = $searchModel->searchExportCsv($params, $offset, $limit);

            foreach ($dataProvideQuery as $rowIndex => $row) {
                if ($i == 0 && $rowIndex == 0) {
                    fputcsv($fpath, array_keys($row));
                }

                if (!empty($row['l_type_create'])) {
                    $row['l_type_create'] = Lead::TYPE_CREATE_LIST[$row['l_type_create']];
                }
                if (!empty($row['status'])) {
                    $row['status'] = Lead::STATUS_LIST[$row['status']];
                }

                fputcsv($fpath, $row);
            }
        }

        if (fclose($fpath)) {
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

        /** @abac $abacDto, LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_ACCESS, Access to view lead  */
        if (!Yii::$app->abac->can(new LeadAbacDto($model, Auth::id()), LeadAbacObject::OBJ_LEAD, LeadAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access Denied.');
        }

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

        if ($showInPopUp === 'modal') {
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
