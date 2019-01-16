<?php

namespace frontend\controllers;

use common\models\LeadFlow;
use common\models\LeadTask;
use common\models\Reason;
use common\models\search\LeadFlightSegmentSearch;
use common\models\search\LeadSearch;
use common\models\search\QuoteSearch;
use common\models\Task;
use frontend\models\LeadMultipleForm;
use Yii;
use common\models\Lead;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadsController implements the CRUD actions for Lead model.
 */
class LeadsController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'delete', 'create', 'export', 'duplicate'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                    [
                        'actions' => ['index', 'ajax-reason-list'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
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

        $params = array_merge($params, $params2);

        if(isset($params['reset'])){
            $params = [];
            $session->remove('LeadSearch');
        }

        if ($session->has('LeadSearch') && empty($params)){
            $params = $session->get('LeadSearch');
        }elseif (!empty($params)){
            $session->set('LeadSearch', $params);
        }

        if(Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id)) {
            $isAgent = true;
        } else {
            $isAgent = false;
        }

        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        if(!$params && $isAgent) {
            $params['LeadSearch']['employee_id'] = Yii::$app->user->id;
        }

        if($isAgent) {
            $dataProvider = $searchModel->searchAgent($params);
        } else {
            $dataProvider = $searchModel->search2($params);
        }

        /*if($isAgent) {
            $user = Yii::$app->user->identity;
            $checkShiftTime = $user->checkShiftTime();

        }*/

        $multipleForm = new LeadMultipleForm();

        if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            if ($multipleForm->load(Yii::$app->request->post()) && $multipleForm->lead_list) {
                if ($multipleForm->validate()) {

                    if (\is_array($multipleForm->lead_list)) {
                        foreach ($multipleForm->lead_list as $lead_id) {
                            $lead = Lead::findOne($lead_id);


                            if ($lead) {
                                //$lead->scenario = Lead::SCENARIO_MULTIPLE_UPDATE;
                                $is_save = false;

                                if ($multipleForm->employee_id) {
                                    if($multipleForm->employee_id == -1) {
                                        $lead->employee_id = null;
                                    } else {
                                        $lead->employee_id = $multipleForm->employee_id;
                                    }
                                    $is_save = true;
                                }

                                if ($multipleForm->status_id) {
                                    $lead->status = $multipleForm->status_id;
                                    $is_save = true;
                                }

                                if ($multipleForm->rating) {
                                    $lead->rating = $multipleForm->rating;
                                    $is_save = true;
                                }


                                $reasonValue = null;

                                if ($multipleForm->status_id && is_numeric($multipleForm->reason_id)) {


                                    if($multipleForm->reason_id > 0) {
                                        $reasonValue = Reason::getReasonByStatus($multipleForm->status_id, $multipleForm->reason_id);
                                    } else {
                                        $reasonValue = $multipleForm->reason_description;
                                    }

                                    if($reasonValue) {
                                        $reason = new Reason();
                                        $reason->employee_id = Yii::$app->user->id;
                                        $reason->lead_id = $lead->id;
                                        $reason->reason = $reasonValue;
                                        $reason->created = date('Y-m-d H:i:s');

                                        if(!$reason->save()) {
                                            Yii::error($reason->errors, 'Leads/Index:Reason:save');
                                        }
                                    }

                                }

                                if($multipleForm->status_id == Lead::STATUS_PROCESSING && $multipleForm->employee_id > 0 ) {

                                    if($lead->l_answered) {
                                        $taskType = Task::CAT_ANSWERED_PROCESS;
                                    } else {
                                        $taskType = Task::CAT_NOT_ANSWERED_PROCESS;
                                    }

                                    LeadTask::createTaskList($lead->id, $multipleForm->employee_id, 1, '', $taskType);
                                    LeadTask::createTaskList($lead->id, $multipleForm->employee_id, 2, '', $taskType);
                                    LeadTask::createTaskList($lead->id, $multipleForm->employee_id, 3, '', $taskType);
                                }


                                if ($is_save) {
                                    $lead->save();
                                }
                            }
                        }
                    }

                }
            }
        }


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'multipleForm' => $multipleForm,
            'isAgent' => $isAgent
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
     * Lists all Lead models.
     * @return mixed
     */
    public function actionExport()
    {
        $searchModel = new LeadSearch();

        $params = Yii::$app->request->queryParams;

        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['LeadSearch']['supervision_id'] = Yii::$app->user->id;
        }

        $dataProvider = $searchModel->search2($params);

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

            return $this->renderAjax('view', $viewParams);
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
