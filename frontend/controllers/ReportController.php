<?php

namespace frontend\controllers;

use common\models\search\CallSearch;
use common\models\search\EmployeeSearch;
use common\models\search\LeadFlowSearch;
use common\models\search\LeadSearch;
use frontend\models\SoldReportForm;
use common\models\Employee;
use common\models\Lead;
use src\auth\Auth;
use src\model\callLog\entity\callLog\search\CallLogSearch;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class ReportController extends FController
{
    public function beforeAction($action)
    {
        $this->view->title = ucwords(str_replace('-', ' ', $action->id));
        return parent::beforeAction($action);
    }

    public function actionViewSold($ids)
    {
        $leadIds = explode(',', $ids);
        if (is_array($leadIds)) {
            $leads = Lead::findAll(['id' => $leadIds]);
            return $this->renderAjax('sold/_viewSold', [
                'leads' => $leads
            ]);
        }
        return null;
    }

    public function actionSold()
    {
        $model = new SoldReportForm();
        $employees = Employee::getAllEmployees();

        $isSupervision = true;
        if (Auth::user()->isAgent()) {
            $model->employee = Yii::$app->user->identity->getId();
            $isSupervision = false;
        }

        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $attr = Yii::$app->request->post($model->formName());

            if (!empty($attr)) {
                $model->attributes = $attr;
            }

            if (Yii::$app->request->isAjax) {
                $dataProvider = new ArrayDataProvider([
                    'allModels' => $model->search(),
                    'pagination' => [
                        'pageSize' => $model->limit,
                        'totalCount' => $model->totalCount
                    ]
                ]);

                return [
                    'grid' => $this->renderAjax('sold/_grid', [
                        'dataProvider' => $dataProvider,
                        'model' => $model
                    ]),
                    'totalCount' => $model->totalCount
                ];
            }
        } else {
            $dataProvider = new ArrayDataProvider([
                'allModels' => $model->search(),
                'pagination' => [
                    'pageSize' => $model->limit,
                    'totalCount' => $model->totalCount
                ]
            ]);
        }

        return $this->render('sold/index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'employees' => $employees,
            'isSupervision' => $isSupervision
        ]);
    }

    public function actionAgents()
    {
        $searchModel = new LeadSearch();
        $dataProvider = $searchModel->searchAgentLeads(Yii::$app->request->queryParams);


        //VarDumper::dump($dataProvider, 10, true); exit;

        return $this->render('agents', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCallsReport()
    {
        $searchModel = new CallLogSearch();
        $params = Yii::$app->request->queryParams;

        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $dataProvider = $searchModel->searchCallsReport($params, $user);

        return $this->render('calls-report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLeadsReport()
    {
        $searchModel = new LeadSearch();
        $params = Yii::$app->request->queryParams;

        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $dataProvider = $searchModel->leadFlowReport($params, $user);

        return $this->render('leads-report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
}
