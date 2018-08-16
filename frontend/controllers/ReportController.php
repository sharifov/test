<?php

namespace frontend\controllers;

use common\controllers\DefaultController;
use frontend\models\SoldReportForm;
use common\models\Employee;
use common\models\Lead;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class ReportController extends DefaultController
{
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'sold', 'view-sold'
                        ],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function beforeAction($action)
    {
        $this->view->title = ucwords(str_replace('-', ' ', $action->id));
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return parent::actions();
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
        if (Yii::$app->user->identity->role == 'agent') {
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
}