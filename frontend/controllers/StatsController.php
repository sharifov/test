<?php

namespace frontend\controllers;

use common\models\ApiLog;
use common\models\Email;
use common\models\Employee;
use common\models\search\ApiLogSearch;
use common\models\search\CallSearch;
use common\models\search\CommunicationSearch;
use common\models\search\EmployeeSearch;
use common\models\search\LeadSearch;
use common\models\Setting;
use common\models\Sms;
use modules\user\userFeedback\entity\search\UserFeedbackSearch;
use src\entities\call\CallGraphsSearch;
use src\viewModel\call\ViewModelTotalCallGraph;
use src\viewModel\userFeedback\ViewModelUserFeedbackGraph;
use Yii;
use yii\base\Model;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
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

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['EmployeeSearch']['supervision_id'] = $userId;
            $params['EmployeeSearch']['status'] = Employee::STATUS_ACTIVE;
        }

        $dataProvider = $searchModel->searchByUserGroups($params);

        $searchModel->timeStart = date('Y-m-d H:i', strtotime('-0 day'));
        $searchModel->timeEnd = date('Y-m-d H:i');

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Call-Sms.
     *
     * @return string
     */
    public function actionCallSms(): string
    {
        $searchModel = new CommunicationSearch();
        $userId = Yii::$app->user->id;

        //$searchModel = new EmployeeSearch();
        /*$params = Yii::$app->request->queryParams;

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['EmployeeSearch']['supervision_id'] = $userId;
            $params['EmployeeSearch']['status'] = Employee::STATUS_ACTIVE;
        }

        $dataProvider = $searchModel->searchByUserGroups($params);

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');*/

        $params = Yii::$app->request->queryParams;

        if (Yii::$app->user->identity->canRole('supervision')) {
            $params['CommunicationSearch']['supervision_id'] = $userId;
        }

        $dataProviderCommunication = $searchModel->search($params);

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');

        /*$query1 = (new \yii\db\Query())
            ->select(['s_id AS id', new Expression('"sms" AS type'), 's_lead_id AS lead_id', 's_created_dt AS created_dt'])
            ->from('sms')
            ->orderBy(['s_id' => SORT_DESC]);
            //->where(['s_lead_id' => $lead->id]);


        $query2 = (new \yii\db\Query())
            ->select(['c_id AS id', new Expression('"voice" AS type'), 'c_lead_id AS lead_id', 'c_created_dt AS created_dt'])
            ->from('call')
            ->orderBy(['c_id' => SORT_DESC]);
            //->where(['c_lead_id' => $lead->id]);

        $query3 = (new \yii\db\Query())
            ->select(['e_id AS id', new Expression('"email" AS type'), 'e_lead_id AS lead_id', 'e_created_dt AS created_dt'])
            ->from('email')
            ->orderBy(['e_id' => SORT_DESC]);
        //->where(['e_lead_id' => $lead->id]);

        $unionQuery = (new \yii\db\Query())
            ->from(['union_table' => $query1->union($query2)]) //->union($query3)
            ->orderBy(['created_dt' => SORT_DESC, 'id' => SORT_DESC]);

        //$datetime_start = '2019-02-11';
        //$datetime_end = '2019-02-25';

        $unionQuery->andFilterWhere(['>=', 'DATE(created_dt)', $datetime_start])
            ->andFilterWhere(['<=', 'DATE(created_dt)', $datetime_end]);


        //echo $unionQuery->createCommand()->rawSql; exit;

        //echo $query1->count(); exit;

        $dataProviderCommunication = new ActiveDataProvider([
            'query' => $unionQuery,
            'pagination' => [
                'pageSize' => 30,
                //'page' => 0
            ],
        ]);*/

        return $this->render('call-sms', [
            //'datetime_start' => $datetime_start,
            //'datetime_end' => $datetime_end,
            //'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'dataProviderCommunication' => $dataProviderCommunication,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function actionCallsGraph()
    {
        $params = Yii::$app->request->queryParams;
        $model = new CallGraphsSearch();
        $model->load($params);

        if (Yii::$app->request->post('export_type') && $model->validate()) {
            return $this->render('partial/_call_graph_export', [
                'viewModel' => new ViewModelTotalCallGraph($model->getCallLogStats(), $model),
            ]);
        } else {
            if (!$model->validate()) {
                $model->createTimeRange = null;
            }
            return $this->render('calls-stats', [
                'model' => $model
            ]);
        }
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAjaxGetTotalChart(): \yii\web\Response
    {
        $callSearch = new CallGraphsSearch();
        $callSearch->load(Yii::$app->request->post());
        if ($callSearch->validate()) {
            $html = $this->renderAjax('partial/_total_calls_chart', [
                'viewModel' => new ViewModelTotalCallGraph($callSearch->getCallLogStats(), $callSearch),
            ]);
        }

        $response = [
            'html' => $html ?? '',
            'error' => $callSearch->hasErrors(),
            'message' => $callSearch->getErrorSummary(true)
        ];

        return $this->asJson($response);
    }

    public function actionSmsGraph()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $chartOptions = Yii::$app->request->post();
            $rangeBy = Yii::$app->request->post('groupBy');
            $date = explode("/", $chartOptions['dateRange']);
            if ($chartOptions['dateRange'] == '') {
                $date[0] = $date[1] = date('Y-m-d', strtotime('-0 day'));
            }
            $smsGraphData = Sms::getSmsStats($date[0], $date[1], $rangeBy, (int)$chartOptions['smsType']);
            if ($chartOptions['dateRange'] == '') {
                $date[0] = $date[1] = date('Y-m-d', strtotime('-0 day'));
            }
            return $this->renderAjax('sms-report', [
                'smsGraphData' => $smsGraphData
            ]);
        } else {
            $currentDate =  date('Y-m-d', strtotime('-0 day'));
            $smsGraphData = Sms::getSmsStats($currentDate, $currentDate, null, 0);

            return $this->render('sms-report', [
                'smsGraphData' => $smsGraphData
            ]);
        }
    }

    public function actionEmailsGraph()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $chartOptions = Yii::$app->request->post();
            $rangeBy = Yii::$app->request->post('groupBy');
            $date = explode("/", $chartOptions['dateRange']);
            if ($chartOptions['dateRange'] == '') {
                $date[0] = $date[1] = date('Y-m-d', strtotime('-0 day'));
            }
            $emailsGraphData = Email::getEmailsStats($date[0], $date[1], $rangeBy, (int)$chartOptions['emailsType']);

            return $this->renderAjax('emails-report', [
                'emailsGraphData' => $emailsGraphData
            ]);
        } else {
            $currentDate =  date('Y-m-d', strtotime('-0 day'));
            $emailsGraphData = Email::getEmailsStats($currentDate, $currentDate, null, 0);

            return $this->render('emails-report', [
                'emailsGraphData' => $emailsGraphData
            ]);
        }
    }

    public function actionApiGraph()
    {
        $actionList = ApiLog::getActionsList();

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $chartOptions = Yii::$app->request->post();
            $rangeBy = Yii::$app->request->post('groupBy');
            $action = Yii::$app->request->post('action');
            $date = explode("/", $chartOptions['dateRange']);
            if ($chartOptions['dateRange'] == '') {
                $date[0] = $date[1] = date('Y-m-d', strtotime('-0 day'));
            }
            $userApiId = $chartOptions['project'];

            if (date('Y-m-d', strtotime($date[0])) == date('Y-m-d', strtotime($date[1])) && $rangeBy != 'D' && $rangeBy != 'M') {
                $range = 'H';
                $chartTimeFormat = 'H:i';
                $currentDate =  date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
                $lastDate =  date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
            } elseif (date('Y-m-d', strtotime($date[0])) != date('Y-m-d', strtotime($date[1])) && $rangeBy == 'D') {
                $range = 'D';
                $chartTimeFormat = 'd M';
                $currentDate =  date('Y-m-d', strtotime($date[0]));
                $lastDate =  date('Y-m-d', strtotime($date[1]));
            } elseif (date('Y-m-d', strtotime($date[0])) == date('Y-m-d', strtotime($date[1])) && $rangeBy == 'D') {
                $range = 'D';
                $chartTimeFormat = 'd M';
                $currentDate =  date('Y-m-d', strtotime($date[0]));
                $lastDate =  date('Y-m-d', strtotime($date[1]));
            } elseif (date('Y-m-d', strtotime($date[0])) == date('Y-m-d', strtotime($date[1])) && $rangeBy == 'M') {
                $range = 'M';
                $chartTimeFormat = 'Y-m';
                $currentDate =  date('Y-m-01', strtotime($date[0]));
                $lastDate =  date('Y-m-t', strtotime($date[1]));
            }
            if (date('Y-m-d', strtotime($date[0])) != date('Y-m-d', strtotime($date[1])) && $rangeBy != 'H' && $rangeBy != 'M') {
                $range = 'D';
                $chartTimeFormat = 'd M';
                $currentDate =  date('Y-m-d', strtotime($date[0]));
                $lastDate =  date('Y-m-d', strtotime($date[1]));
            } elseif (date('Y-m-d', strtotime($date[0])) != date('Y-m-d', strtotime($date[1])) && $rangeBy == 'M') {
                $range = 'M';
                $chartTimeFormat = 'Y-m';
                $currentDate = date('Y-m-01', strtotime($date[0]));
                $lastDate = date('Y-m-t', strtotime($date[1]));
            } elseif (date('Y-m-d', strtotime($date[0])) != date('Y-m-d', strtotime($date[1])) && $rangeBy == 'H') {
                $range = 'HD';
                $chartTimeFormat = 'Y-m-d H:i';
                $currentDate = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
                $lastDate = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
            }

            $apiStats = ApiLogSearch::getApiLogStats($currentDate, $lastDate, $range, $userApiId, $action);

            return $this->renderAjax('api-report', [
                'apiStats' => $apiStats,
                'format' => $chartTimeFormat,
                'actions' => $actionList
            ]);
        } else {
            $currentDate =  date('Y-m-d', strtotime('-0 day'));
            $chartTimeFormat = 'H:i';

            $apiStats = ApiLogSearch::getApiLogStats($currentDate, $currentDate, $range = 'H', '', '');
            return $this->render('api-report', [
                'apiStats' => $apiStats,
                'format' => $chartTimeFormat,
                'actions' => $actionList
            ]);
        }
    }

    public function actionAgentRatings()
    {
        $this->layout = '@frontend/themes/gentelella_v2/views/layouts/main_tv';
        $searchLeader = new LeadSearch();

        $agentsSettings = Setting::find()->where(['s_key' => 'agents_ratings'])->asArray()->one();
        $teamsSettings = Setting::find()->where(['s_key' => 'teams_ratings'])->asArray()->one();
        $teamsSettingsSkill = Setting::find()->where(['s_key' => 'exclude_agent_skill'])->asArray()->one();

        $agentsBoardsSettings = json_decode($agentsSettings['s_value'], true);
        $teamsBoardsSettings = json_decode($teamsSettings['s_value'], true);
        $teamsSkill = json_decode($teamsSettingsSkill['s_value'], true);

        if (Yii::$app->request->isPost) {
            $period = Yii::$app->request->post('period');
        } else {
            $period = 'currentWeek';
        }

        $profitDataProvider = $searchLeader->searchTopAgents('finalProfit', $period);
        $soldDataProvider = $searchLeader->searchTopAgents('soldLeads', $period);
        $profitPerPaxDataProvider = $searchLeader->searchTopAgents('profitPerPax', $period);
        $tipsDataProvider = $searchLeader->searchTopAgents('tips', $period);
        $conversionDataProvider = $searchLeader->searchTopAgents('leadConversion', $period);

        $teamsProfitDataProvider = $searchLeader->searchTopTeams('teamsProfit', $period, $teamsSkill);
        $avgSoldLeadsDataProvider = $searchLeader->searchTopTeams('teamsSoldLeads', $period, $teamsSkill);
        $avgProfitPerPax = $searchLeader->searchTopTeams('teamsProfitPerPax', $period, $teamsSkill);
        $avgProfitPerAgent = $searchLeader->searchTopTeams('teamsProfitPerAgent', $period, $teamsSkill);
        $teamConversion = $searchLeader->searchTopTeams('teamsConversion', $period, $teamsSkill);

        $params = [
            'profitDataProvider' => $profitDataProvider,
            'soldDataProvider' => $soldDataProvider,
            'profitPerPaxDataProvider' => $profitPerPaxDataProvider,
            'tipsDataProvider' => $tipsDataProvider,
            'agentsBoardsSettings' => $agentsBoardsSettings,
            'teamsBoardsSettings' => $teamsBoardsSettings,
            'conversionDataProvider' => $conversionDataProvider,
            'teamsProfitDataProvider' => $teamsProfitDataProvider,
            'avgSoldLeadsDataProvider' => $avgSoldLeadsDataProvider,
            'avgProfitPerPax' => $avgProfitPerPax,
            'avgProfitPerAgent' => $avgProfitPerAgent,
            'teamConversion' => $teamConversion
        ];

        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('agent-ratings', $params);
        } else {
            return $this->render('agent-ratings', $params);
        }
    }

    public function actionCallsStats()
    {
        $searchModel = new CallSearch();
        $params = Yii::$app->request->queryParams;

        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $dataProvider = $searchModel->searchCallsStats($params, $user);

        return $this->render('calls', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLeadsStats()
    {
        $searchModel = new LeadSearch();
        $params = Yii::$app->request->queryParams;

        /** @var Employee $user */
        $user = Yii::$app->user->identity;
        $dataProvider = $searchModel->leadFlowStats($params, $user);

        return $this->render('leads-stats', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionUserFeedback()
    {
        $searchModel = new UserFeedbackSearch();
        $searchModel->load(Yii::$app->request->queryParams);
        if ($searchModel->validate()) {
            $statusData = ($searchModel->getUserFeedbackStatusStats())->getModels()[0] ?? [];
        } else {
            $statusData = [];
        }

        return $this->render('user-feedback-stats', [
            'searchModel' => $searchModel,
            'statusData' => $statusData
        ]);
    }

    public function actionAjaxGetUserFeedbackChart(): \yii\web\Response
    {
        $userFeedbackSearch = new UserFeedbackSearch();
        $userFeedbackSearch->load(Yii::$app->request->post());
        if ($userFeedbackSearch->validate()) {
            $html = $this->renderAjax('partial/_total_user_feedback_chart', [
                'viewModel' => new ViewModelUserFeedbackGraph($userFeedbackSearch->graphSearch(), $userFeedbackSearch),
            ]);
            $statusHtml = $this->renderAjax('partial/_total_user_feedback_status_chart', [
                'statusData' => ($userFeedbackSearch->getUserFeedbackStatusStats())->getModels()[0] ?? []
            ]);
        }

        $response = [
            'html' => $html ?? '',
            'error' => $userFeedbackSearch->hasErrors(),
            'message' => $userFeedbackSearch->getErrorSummary(true),
            'statusHtml' => $statusHtml ?? ''
        ];

        return $this->asJson($response);
    }
}
