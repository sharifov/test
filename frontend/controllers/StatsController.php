<?php

namespace frontend\controllers;

use common\models\ApiLog;
use common\models\Employee;
use common\models\Lead;
use common\models\search\EmployeeSearch;
use common\models\search\LeadTaskSearch;
use common\models\UserParams;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Stats controller
 */
class StatsController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post', 'GET'],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }




    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $userId = Yii::$app->user->id;

        $searchModel = new EmployeeSearch();
        $params = Yii::$app->request->queryParams;

        if (Yii::$app->authManager->getAssignment('supervision', $userId)) {
            $params['EmployeeSearch']['supervision_id'] = $userId;
            $params['EmployeeSearch']['status'] = Employee::STATUS_ACTIVE;
        }

        $dataProvider = $searchModel->searchByUserGroups($params);

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');

        //$searchModel->date_range = $searchModel->datetime_start.' - '. $searchModel->datetime_end;


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);


    }



}
