<?php

namespace frontend\controllers;

use common\models\Employee;
use common\models\Reason;
use common\models\search\LeadFlightSegmentSearch;
use common\models\search\LeadSearch;
use common\models\search\QuoteSearch;
use frontend\models\LeadMultipleForm;
use sales\services\lead\LeadMultiUpdateService;
use Yii;
use common\models\Lead;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;

/**
 * LeadsController implements the CRUD actions for Lead model.
 */
class LeadsController extends FController
{

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

        $multipleForm = new LeadMultipleForm();

        $report = [];

        if ($user->isAdmin() || $user->isSupervision()) {
            if ($multipleForm->load(Yii::$app->request->post())) {
                if ($multipleForm->validate()) {
                    /** @var LeadMultiUpdateService $service */
                    $service = Yii::createObject(LeadMultiUpdateService::class);
                    $report = $service->update($multipleForm, $user->id);
                }
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'multipleForm' => $multipleForm,
            'isAgent' => $isAgent,
            'report' => $report
        ]);
    }

    /**
     * @return string
     */
    public function actionAjaxReasonList()
    {
        $status_id = Yii::$app->request->post('status_id');
        $data = Reason::getReasonListByStatus($status_id);

        $str = '<option value="">-</option>';
        if($data) {
            foreach ($data as $reasonId => $reasonName) {
                $str .= '<option value="' . $reasonId . '">' . $reasonName . '</option>';
            }
        }

        return $str;
    }


    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAjaxActivityLogs() : string
    {
        $lead_id = Yii::$app->request->get('id');
        $lead = $this->findModel($lead_id);
        $logs = $lead->leadLogs;

        return $this->renderPartial('activity-logs', [
            'logs' => $logs
            //'searchModel' => $searchModel,
            //'dataProvider' => $dataProvider,
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
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
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

        return $this->render('view', $viewParams);

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
