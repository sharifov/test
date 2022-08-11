<?php

namespace frontend\controllers;

use common\models\Employee;
use Yii;
use common\models\search\AgentActivitySearch;
use common\models\Call;
use common\models\Sms;
use src\entities\email\helpers\EmailType;

/**
 * AgentActivityController.
 */
class AgentReportController extends FController
{
    public function actionIndex()
    {
        $searchModel = new AgentActivitySearch();
        $params = Yii::$app->request->queryParams;

        if (isset($params['reset'])) {
            $params = [];
        }

        /** @var Employee $user */
        $user = Yii::$app->user->identity;

        $dataProvider = $searchModel->searchAgentLeads($params, $user);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCalls()
    {
        $searchModel = new AgentActivitySearch();
        $params = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->searchCalls($params);

        if ($params['AgentActivitySearch']['c_call_type_id'] == Call::CALL_TYPE_OUT) {
            $title = 'Outbound calls';
        } elseif ($params['AgentActivitySearch']['c_call_type_id'] == Call::CALL_TYPE_IN) {
            $title = 'Inbound calls';
        } elseif ($params['AgentActivitySearch']['c_call_type_id'] == Call::CALL_TYPE_JOIN) {
            $title = 'Join calls';
        }

        return $this->render('_calls', [
            'title' => $title,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSms()
    {
        $searchModel = new AgentActivitySearch();
        $params = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->searchSms($params);

        $title = (isset($params['AgentActivitySearch']['s_type_id']) && $params['AgentActivitySearch']['s_type_id'] == Sms::TYPE_OUTBOX) ? 'SMS Sent' : 'SMS Received';

        return $this->render('_sms', [
            'title' => $title,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEmail()
    {
        $searchModel = new AgentActivitySearch();
        $params = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->searchEmail($params);

        $title = (isset($params['AgentActivitySearch']['e_type_id']) && $params['AgentActivitySearch']['e_type_id'] == EmailType::OUTBOX) ? 'Emails Sent' : 'Emails Received';

        return $this->render('_email', [
            'title' => $title,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCloned()
    {
        $searchModel = new AgentActivitySearch();
        $params = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->searchClonedLeads($params);

        $title = 'Cloned Leads';

        return $this->render('_cloned', [
            'title' => $title,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreated()
    {
        $searchModel = new AgentActivitySearch();
        $params = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->searchCreatedLeads($params);

        $title = 'Created Leads';

        return $this->render('_leads', [
            'title' => $title,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSold()
    {
        $searchModel = new AgentActivitySearch();
        $params = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->searchSoldLeads($params);

        $title = 'Sold Leads';

        return $this->render('_leads', [
            'title' => $title,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFromToLeads()
    {
        $searchModel = new AgentActivitySearch();
        $params = Yii::$app->request->queryParams;

        $dataProvider = $searchModel->searchFromToLeads($params);

        $title = $params['title'];

        return $this->render('_leads', [
            'title' => $title,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
