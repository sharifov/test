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
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'call-sms', 'calls-graph', 'sms-graph', 'emails-graph'],
                        'allow' => true,
                        'roles' => ['supervision', 'admin', 'qa'],
                    ],
                ],
            ],
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

        if (Yii::$app->authManager->getAssignment('supervision', $userId)) {
            $params['EmployeeSearch']['supervision_id'] = $userId;
            $params['EmployeeSearch']['status'] = Employee::STATUS_ACTIVE;
        }

        $dataProvider = $searchModel->searchByUserGroups($params);

        $searchModel->datetime_start = date('Y-m-d', strtotime('-0 day'));
        $searchModel->datetime_end = date('Y-m-d');*/



        //$datetime_start = date('Y-m-d', strtotime('-0 day'));
        //$datetime_end = date('Y-m-d');


        //$datetime_start = Yii::$app->request->get('datetime_start', date('Y-m-d', strtotime('-0 day')));
        //$datetime_end = Yii::$app->request->get('datetime_end', date('Y-m-d'));



        //$searchModel->date_range = $searchModel->datetime_start.' - '. $searchModel->datetime_end;

        $params = Yii::$app->request->queryParams;

        if (Yii::$app->authManager->getAssignment('supervision', $userId)) {
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
            $dateRange = Yii::$app->request->post('dateRange');
            $rangeBy = Yii::$app->request->post('groupBy');
            $callType = Yii::$app->request->post('callType');

            $date = $pieces = explode("/", $dateRange);
            $callsGraphData = Call::getCallStats($date[0], $date[1], $rangeBy, (int)$callType);

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
            $dateRange = Yii::$app->request->post('dateRange');
            $rangeBy = Yii::$app->request->post('groupBy');
            $smsType = Yii::$app->request->post('smsType');

            $date = $pieces = explode("/", $dateRange);
            $smsGraphData = Sms::getSmsStats($date[0], $date[1], $rangeBy, (int)$smsType);

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
            $dateRange = Yii::$app->request->post('dateRange');
            $rangeBy = Yii::$app->request->post('groupBy');
            $emailsType = Yii::$app->request->post('emailsType');

            $date = $pieces = explode("/", $dateRange);
            $emailsGraphData = Email::getEmailsStats($date[0], $date[1], $rangeBy, (int)$emailsType);

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
}
