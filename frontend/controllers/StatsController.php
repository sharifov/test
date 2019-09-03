<?php

namespace frontend\controllers;

use common\models\ApiLog;
use common\models\Call;
use common\models\Employee;
use common\models\Lead;
use common\models\search\CommunicationSearch;
use common\models\search\EmployeeSearch;
use common\models\search\LeadTaskSearch;
use common\models\Sms;
use common\models\Email;
use common\models\UserParams;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
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

        $searchModel->timeStart = date('Y-m-d', strtotime('-0 day'));
        $searchModel->timeEnd = date('Y-m-d');

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

    public function actionCallsGraph()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $chartOptions = Yii::$app->request->post();
            $rangeBy = Yii::$app->request->post('groupBy');
            $date = explode("/", $chartOptions['dateRange']);
            $callsGraphData = Call::getCallStats($date[0], $date[1], $rangeBy, (int)$chartOptions['callType']);

            return $this->renderAjax('calls-report', [
                'callsGraphData' => $callsGraphData
            ]);
        } else {
            $currentDate =  date('Y-m-d', strtotime('-0 day'));
            $callsGraphData = Call::getCallStats($currentDate, $currentDate, null, 0);

            return $this->render('calls-report', [
                'callsGraphData' => $callsGraphData
            ]);
        }
    }

    public function actionSmsGraph()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $chartOptions = Yii::$app->request->post();
            $rangeBy = Yii::$app->request->post('groupBy');
            $date = explode("/", $chartOptions['dateRange']);
            $smsGraphData = Sms::getSmsStats($date[0], $date[1], $rangeBy, (int)$chartOptions['smsType']);

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
            $userApiId = $chartOptions['project'];

            if (date('Y-m-d', strtotime($date[0])) == date('Y-m-d', strtotime($date[1])) && $rangeBy != 'D' && $rangeBy != 'M'){
                $range = 'H';
                $chartTimeFormat = 'H:i';
                $currentDate =  date('Y-m-d H:i:s', strtotime($date[0].' 00:00:00'));
                $lastDate =  date('Y-m-d H:i:s', strtotime($date[1].' 23:59:59'));
            } else if(date('Y-m-d', strtotime($date[0])) != date('Y-m-d', strtotime($date[1])) && $rangeBy == 'D') {
                $range = 'D';
                $chartTimeFormat = 'd M';
                $currentDate =  date('Y-m-d', strtotime($date[0]));
                $lastDate =  date('Y-m-d', strtotime($date[1]));
            } else if(date('Y-m-d', strtotime($date[0])) == date('Y-m-d', strtotime($date[1])) && $rangeBy == 'D') {
                $range = 'D';
                $chartTimeFormat = 'd M';
                $currentDate =  date('Y-m-d', strtotime($date[0]));
                $lastDate =  date('Y-m-d', strtotime($date[1]));
            } else if(date('Y-m-d', strtotime($date[0])) == date('Y-m-d', strtotime($date[1])) && $rangeBy == 'M') {
                $range = 'M';
                $chartTimeFormat = 'Y-m';
                $currentDate =  date('Y-m-01', strtotime($date[0]));
                $lastDate =  date('Y-m-31', strtotime($date[1]));
            } if (date('Y-m-d', strtotime($date[0])) != date('Y-m-d', strtotime($date[1])) && $rangeBy != 'H' && $rangeBy != 'M'){
                $range = 'D';
                $chartTimeFormat = 'd M';
                $currentDate =  date('Y-m-d', strtotime($date[0]));
                $lastDate =  date('Y-m-d', strtotime($date[1]));
            } else if(date('Y-m-d', strtotime($date[0])) != date('Y-m-d', strtotime($date[1])) && $rangeBy == 'M') {
                $range = 'M';
                $chartTimeFormat = 'Y-m';
                $currentDate = date('Y-m-01', strtotime($date[0]));
                $lastDate = date('Y-m-31', strtotime($date[1]));
            } else if (date('Y-m-d', strtotime($date[0])) != date('Y-m-d', strtotime($date[1])) && $rangeBy == 'H') {
                $range = 'HD';
                $chartTimeFormat = 'Y-m-d H:i';
                $currentDate = date('Y-m-d H:i:s', strtotime($date[0] . ' 00:00:00'));
                $lastDate = date('Y-m-d H:i:s', strtotime($date[1] . ' 23:59:59'));
            }

            $apiStats = ApiLog::getApiLogStats($currentDate, $lastDate, $range, $userApiId, $action);

            return $this->renderAjax('api-report', [
                'apiStats' => $apiStats,
                'format' => $chartTimeFormat,
                'actions' => $actionList
            ]);
        } else {
            $currentDate =  date('Y-m-d', strtotime('-0 day'));
            $chartTimeFormat = 'H:i';

            $apiStats = ApiLog::getApiLogStats($currentDate, $currentDate, $range = 'H', '', '');
            return $this->render('api-report', [
                'apiStats' => $apiStats,
                'format' => $chartTimeFormat,
                'actions' => $actionList
            ]);
        }

    }
}
