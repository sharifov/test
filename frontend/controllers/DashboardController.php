<?php

namespace frontend\controllers;

use common\models\ApiLog;
use common\models\Employee;
use common\models\Lead;
use common\models\search\EmployeeSearch;
use common\models\search\LeadTaskSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

/**
 * Dashboard controller
 */
class DashboardController extends FController
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
                    'logout' => ['POST', 'GET'],
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
        //return $this->dashboardAgent();
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        if ($user->isSupervision() || $user->isExSuper() || $user->isSupSuper()) {
            return $this->dashboardSupervision();
        }

        /**
         * Original dashboard disabled for presentation
         */
        if ($user->isAdmin() && false) {
            return $this->dashboardAdmin();
        }

        /**
         * New dashboard for presentation
         */
        if ($user->isAdmin()) {
            return $this->presentationDashboardAdmin();
        }

        if ($user->isQa()) {
            return $this->dashboardQa();
        }

        if ($user->isUserManager()) {
            return $this->dashboardUM();
        }

        if ($user->isSuperAdmin()) {
            return $this->dashboardAdmin();
        }

        return $this->dashboardAgent();
    }

    public function presentationDashboardAdmin()
    {
        return $this->render('index2');
    }

    public function dashboardSupervision(): string
    {
        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $searchModel = new EmployeeSearch();
        $params = Yii::$app->request->queryParams;

        $params['EmployeeSearch']['supervision_id'] = $user->id;
        $params['EmployeeSearch']['status'] = Employee::STATUS_ACTIVE;

        $dataProvider = $searchModel->searchByUserGroupsForSupervision($params);

        $searchModel->timeStart = date('Y-m-d H:i', strtotime('-0 day'));
        $searchModel->timeEnd = date('Y-m-d H:i');

        return $this->render('index_supervision', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function dashboardAdmin(): string
    {
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

        /*$searchModel = new EmployeeSearch();
        $params = Yii::$app->request->queryParams;
        $params['EmployeeSearch']['status'] = Employee::STATUS_ACTIVE;
        $dataProvider = $searchModel->searchByUserGroups($params);
        $searchModel->timeStart = date('Y-m-d H:i', strtotime('-0 day'));
        $searchModel->timeEnd = date('Y-m-d H:i');*/


        $crontabJobList = [];
        $processList = [];

        @exec('cat /var/spool/cron/crontabs/root', $outCron);
        if (isset($outCron) && count($outCron)) {
            foreach ($outCron as $lineCron) {
                if (!preg_match('/(#|SHELL|PATH)/', $lineCron) && strlen($lineCron) > 2) {
                    $crontabJobList[] = $lineCron;
                }
            }
        }

        return $this->render('index_admin', [
            /*'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,*/
            'dataStats' => $dataStats,
            'dataSources' => $dataSources,
            'dataEmployee' => $dataEmployee,
            'dataEmployeeSold' => $dataEmployeeSold,
            'days2' => $days2,
            'crontabJobList' => $crontabJobList,
            'processList' => $processList,
        ]);
    }

    public function dashboardAgent(): string
    {
        $userId = Yii::$app->user->id;

        $params = Yii::$app->request->queryParams;
        $params['LeadTaskSearch']['lt_user_id'] = $userId;
        $params['LeadTaskSearch']['status'] = [Lead::STATUS_PROCESSING, Lead::STATUS_ON_HOLD];

        //$params['LeadTaskSearch']['status_not_in'] = [Lead::STATUS_TRASH , Lead::STATUS_SNOOZE ];

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


        //$params['EmployeeSearch']['supervision_id'] = $userId;
        $params['EmployeeSearch']['id'] = $userId;
        $params['EmployeeSearch']['status'] = Employee::STATUS_ACTIVE;



        $dataProvider = $searchModel->searchByUserGroups($params);

        $searchModel->timeStart = date('Y-m-d H:i', strtotime('-0 day'));
        $searchModel->timeEnd = date('Y-m-d H:i');


        return $this->render('index', [
            'searchLeadTask' => $searchLeadTask,
            'dp1' => $dp1,
            'dp2' => $dp2,
            'dp3' => $dp3,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function dashboardQa(): string
    {
        return $this->render('index_qa');
    }

    public function dashboardUM(): string
    {
        return $this->render('index_um');
    }
}
