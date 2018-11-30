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
use yii\helpers\VarDumper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class SiteController extends FController
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
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'logout', 'profile', 'get-airport'],
                        'allow' => true,
                        'roles' => ['@'],
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


    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => '@yiister/gentelella/views/error',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex2()
    {
        return $this->render('index2');
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $userId = Yii::$app->user->id;

        if (Yii::$app->authManager->getAssignment('supervision', $userId)) {
            return $this->dashboardSupervision();
        }

        if (Yii::$app->authManager->getAssignment('admin', $userId)) {
            return $this->dashboardAdmin();
        }

        return $this->dashboardAgent();

    }

    public function dashboardSupervision(): string
    {

        $userId = Yii::$app->user->id;

        $searchModel = new EmployeeSearch();
        $params = Yii::$app->request->queryParams;

        //if(Yii::$app->authManager->getAssignment('supervision', $userId)) {
        $params['EmployeeSearch']['supervision_id'] = $userId;
        $params['EmployeeSearch']['status'] = Employee::STATUS_ACTIVE;
        //}


        $dataProvider = $searchModel->searchByUserGroups($params);

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');

        //$searchModel->date_range = $searchModel->datetime_start.' - '. $searchModel->datetime_end;


        return $this->render('index_supervision', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);

    }

    public function dashboardAdmin(): string
    {

        //$userId = Yii::$app->user->id;

        $days = 20;
        $dataStatsDone = Lead::find()->select("COUNT(*) AS done_count, DATE(created) AS created_date")
            ->where(['<>', 'status', Lead::STATUS_TRASH])
            //->andWhere("DATE(created) >= DATE(NOW() - interval '".$days." days')")
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-" . $days . " days"))])
            ->groupBy('DATE(created)')
            ->orderBy('DATE(created) DESC')
            ->limit(30)->asArray()->all();


        $dataStatsPending = Lead::find()->select("COUNT(*) AS pending_count, DATE(created) AS created_date")
            ->where([
                'status' => [
                    Lead::STATUS_PENDING,
                ],
            ])
            //->andWhere("DATE(created) >= DATE(NOW() - interval '".$days." days')")
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-" . $days . " days"))])
            ->groupBy(['DATE(created)'])
            ->orderBy('DATE(created) DESC')
            ->limit(30)->asArray()->all();


        $dataStatsBooked = Lead::find()->select("COUNT(*) AS book_count, DATE(created) AS created_date")
            ->where([
                'status' => [
                    Lead::STATUS_FOLLOW_UP,
                ],
            ])
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-" . $days . " days"))])
            ->groupBy(['DATE(created)'])
            ->orderBy('DATE(created) DESC')
            ->limit(30)->asArray()->all();


        $dataStatsProcessing = Lead::find()->select("COUNT(*) AS proc_count, DATE(created) AS created_date")
            ->where([
                'status' => [
                    Lead::STATUS_PROCESSING,
                    Lead::STATUS_ON_HOLD
                ],
            ])
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-" . $days . " days"))])
            ->groupBy(['DATE(created)'])
            ->orderBy('DATE(created) DESC')
            ->limit(30)->asArray()->all();


        $dataStatsTrash = Lead::find()->select("COUNT(*) AS trash_count, DATE(created) AS created_date")
            ->where([
                'status' => [
                    Lead::STATUS_TRASH,
                ],
            ])
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-" . $days . " days"))])
            ->groupBy(['DATE(created)'])
            ->orderBy('DATE(created) DESC')
            ->limit(30)->asArray()->all();


        $dataStatsSold = Lead::find()->select("COUNT(*) AS sold_count, DATE(created) AS created_date")
            ->where([
                'status' => [
                    Lead::STATUS_SOLD,
                ],
            ])
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-" . $days . " days"))])
            ->groupBy(['DATE(created)'])
            ->orderBy('DATE(created) DESC')
            ->limit(30)->asArray()->all();


        //print_r($dataStatsPending);

        $dataStats = [];

        foreach ($dataStatsDone as $item) {
            $item['pending_count'] = 0;
            $item['book_count'] = 0;
            $item['sold_count'] = 0;
            $item['proc_count'] = 0;
            $item['trash_count'] = 0;
            //$item['done_count']     = 0;

            $dataStats[$item['created_date']] = $item;
        }

        foreach ($dataStatsPending as $item) {
            $item['done_count'] = 0;
            $item['book_count'] = 0;
            $item['sold_count'] = 0;
            $item['proc_count'] = 0;
            $item['trash_count'] = 0;
            if (isset($dataStats[$item['created_date']])) {

                $dataStats[$item['created_date']]['pending_count'] = $item['pending_count'];

            } else {
                $dataStats[$item['created_date']] = $item;
            }
        }


        foreach ($dataStatsBooked as $item) {
            $item['done_count'] = 0;
            $item['pending_count'] = 0;
            $item['sold_count'] = 0;
            $item['proc_count'] = 0;
            $item['trash_count'] = 0;

            if (isset($dataStats[$item['created_date']])) {

                $dataStats[$item['created_date']]['book_count'] = $item['book_count'];

            } else {
                $dataStats[$item['created_date']] = $item;
            }
        }


        foreach ($dataStatsTrash as $item) {
            $item['done_count'] = 0;
            $item['pending_count'] = 0;
            $item['sold_count'] = 0;
            $item['proc_count'] = 0;
            $item['book_count'] = 0;

            if (isset($dataStats[$item['created_date']])) {
                $dataStats[$item['created_date']]['trash_count'] = $item['trash_count'];
            } else {
                $dataStats[$item['created_date']] = $item;
            }
        }


        foreach ($dataStatsProcessing as $item) {
            $item['done_count'] = 0;
            $item['pending_count'] = 0;
            $item['sold_count'] = 0;
            $item['trash_count'] = 0;
            $item['book_count'] = 0;

            if (isset($dataStats[$item['created_date']])) {

                $dataStats[$item['created_date']]['proc_count'] = $item['proc_count'];

            } else {
                $dataStats[$item['created_date']] = $item;
            }
        }


        foreach ($dataStatsSold as $item) {
            $item['done_count'] = 0;
            $item['pending_count'] = 0;
            $item['book_count'] = 0;
            $item['proc_count'] = 0;
            $item['trash_count'] = 0;

            if (isset($dataStats[$item['created_date']])) {

                $dataStats[$item['created_date']]['sold_count'] = $item['sold_count'];

            } else {
                $dataStats[$item['created_date']] = $item;
            }
        }

        ksort($dataStats);


        $days2 = 7;

        $dataSources = ApiLog::find()->select('COUNT(*) AS cnt, al_user_id')
            ->andWhere(['>=', 'DATE(al_request_dt)', date('Y-m-d', strtotime("-" . $days2 . " days"))])
            ->groupBy(['al_user_id'])
            ->orderBy('cnt DESC')
            ->asArray()->all();


        $dataEmployee = Lead::find()->select("COUNT(*) AS cnt, employee_id")//, SUM(tr_total_price) AS sum_price
        ->where([
            'status' => [
                Lead::STATUS_PROCESSING,
                Lead::STATUS_ON_HOLD,
            ],
        ])
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-" . $days2 . " days"))])
            ->groupBy(['employee_id'])
            ->orderBy('cnt DESC')
            ->limit(20)->asArray()->all();


        $dataEmployeeSold = Lead::find()->select("COUNT(*) AS cnt, employee_id")//, SUM(tr_total_price) AS sum_price
        ->where([
            'status' => [
                Lead::STATUS_SOLD,
            ],
        ])
            ->andWhere(['>=', 'DATE(created)', date('Y-m-d', strtotime("-" . $days2 . " days"))])
            ->groupBy(['employee_id'])
            ->orderBy('cnt DESC')
            ->limit(20)->asArray()->all();


        $searchModel = new EmployeeSearch();
        $params = Yii::$app->request->queryParams;

        //if(Yii::$app->authManager->getAssignment('supervision', $userId)) {
        //$params['EmployeeSearch']['supervision_id'] = $userId;

        $params['EmployeeSearch']['status'] = Employee::STATUS_ACTIVE;
        //}


        $dataProvider = $searchModel->searchByUserGroups($params);

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');

        //$searchModel->date_range = $searchModel->datetime_start.' - '. $searchModel->datetime_end;


        return $this->render('index_admin', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'dataStats' => $dataStats,
            'dataSources' => $dataSources,
            'dataEmployee' => $dataEmployee,
            'dataEmployeeSold' => $dataEmployeeSold,
            'days2' => $days2,
        ]);

    }

    public function dashboardAgent(): string
    {
        $userId = Yii::$app->user->id;

        $params = Yii::$app->request->queryParams;
        $params['LeadTaskSearch']['lt_user_id'] = $userId;
        $params['LeadTaskSearch']['status'] = [Lead::STATUS_PROCESSING, Lead::STATUS_ON_HOLD];

        $params['LeadTaskSearch']['status_not_in'] = [Lead::STATUS_TRASH/* , Lead::STATUS_SNOOZE */];

        //VarDumper::dump($params); exit;
        $searchLeadTask = new LeadTaskSearch();

        $params['LeadTaskSearch']['lt_date'] = date('Y-m-d', strtotime("-1 days"));
        $dp1 = $searchLeadTask->searchDashboard($params);

        // $params['LeadTaskSearch']['status'] = [Lead::STATUS_PROCESSING, Lead::STATUS_ON_HOLD];

        $params['LeadTaskSearch']['lt_date'] = date('Y-m-d');
        $dp2 = $searchLeadTask->searchDashboard($params);

        $params['LeadTaskSearch']['lt_date'] = date('Y-m-d', strtotime("+1 days"));
        $dp3 = $searchLeadTask->searchDashboard($params);


        /*$taskList = \common\models\LeadTask::find()->where(['lt_user_id' => $userId])
            ->andWhere(['>=', 'lt_date', date('Y-m-d', strtotime("-1 days"))])
            ->andWhere(['<=', 'lt_date', date('Y-m-d', strtotime("+1 days"))])
            ->orderBy(['lt_date' => SORT_ASC])->all();

        $dateItem = [];
        $myTaskByDate = [];

        if($taskList) {
            foreach ($taskList as $task) {
                $dateItem[$task->lt_date] = $task->lt_date;
                $myTaskByDate[$task->lt_date][$task->lt_user_id][] = $task;
            }
        }*/


        $searchModel = new EmployeeSearch();
        $params = Yii::$app->request->queryParams;

        //if(Yii::$app->authManager->getAssignment('supervision', $userId)) {
        //$params['EmployeeSearch']['supervision_id'] = $userId;
        $params['EmployeeSearch']['id'] = $userId;
        $params['EmployeeSearch']['status'] = Employee::STATUS_ACTIVE;
        //}


        $dataProvider = $searchModel->searchByUserGroups($params);

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');


        return $this->render('index', [
            'searchLeadTask' => $searchLeadTask,
            'dp1' => $dp1,
            'dp2' => $dp2,
            'dp3' => $dp3,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = '@frontend/themes/gentelella/views/layouts/login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login.php', [
            'model' => $model,
        ]);

    }


    /**
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionProfile(): string
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException();
        }

        $model = Employee::findOne(Yii::$app->user->id);
        if (!$model) {
            throw new NotFoundHttpException('The requested User does not exist.');
        }

        $modelUserParams = $model->userParams;
        if (!$modelUserParams) {
            $modelUserParams = new UserParams();
        }


        //Yii::$app->request->isPost
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            //$attr = Yii::$app->request->post($model->formName());

            if (!empty($this->password)) {
                $this->setPassword($this->password);
            }


            if ($modelUserParams->load(Yii::$app->request->post()) && $modelUserParams->validate()) {
                $modelUserParams->save();
            }
            //$attr = Yii::$app->request->post($model->formName());

            if (!empty($model->password)) {
                $model->setPassword($model->password);
            }
            //$model->prepareSave($attr);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Profile successful updated!');
                $model->refresh();
            }

        }

        //new UserParams();


        return $this->render('/employee/update_profile', [
            'model' => $model,
            'modelUserParams' => $modelUserParams
        ]);
    }

    public function actionGetAirport($term)
    {
        $response = file_get_contents(sprintf('%s?term=%s', Yii::$app->params['getAirportUrl'], $term));
        $response = json_decode($response, true);
        if (isset($response['success']) && $response['success']) {
            if (isset($response['data'])) {
                foreach ($response['data'] as $key => $item) {
                    $response['data'][$key]['value'] = sprintf('%s (%s)', $item['city'], $item['iata']);
                }
                return json_encode($response['data']);
            }
        }

        return json_encode([]);
    }
}
