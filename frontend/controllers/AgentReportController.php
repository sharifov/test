<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\DynamicModel;
use common\models\search\AgentActivitySearch;
use common\models\Call;
use common\models\Sms;
use common\models\Email;
use yii\helpers\ArrayHelper;


/**
 * AgentActivityController.
 */
class AgentReportController extends FController
{

    public function validateDateParams($params)
    {
        if(!isset($params['AgentActivitySearch']['date_from'])){
            $params['AgentActivitySearch']['date_from'] = date('Y-m-d 00:00');
        }else{
            $params['AgentActivitySearch']['date_from'] = date('Y-m-d 00:00:00', strtotime($params['AgentActivitySearch']['date_from']));
        }
        if(!isset($params['AgentActivitySearch']['date_to'])){
            $params['AgentActivitySearch']['date_to'] = date('Y-m-d 23:59');
        }else{
            $params['AgentActivitySearch']['date_to'] = date('Y-m-d 23:59', strtotime($params['AgentActivitySearch']['date_to']));
        }

        return $params;
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AgentActivitySearch();
        $params = Yii::$app->request->queryParams;

        if(isset($params['reset'])){
            $params = [];
        }

        if(Yii::$app->user->identity->canRole('supervision')) {
            $params['AgentActivitySearch']['supervision_id'] = Yii::$app->user->id;
        }

        $params = $this->validateDateParams($params);
        $dataProvider = $searchModel->searchAgentLeads($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCalls()
    {
        $searchModel = new AgentActivitySearch();
        $params = Yii::$app->request->queryParams;

        $params = $this->validateDateParams($params);
        $dataProvider = $searchModel->searchCalls($params);

        $title = ($params['AgentActivitySearch']['c_call_type_id'] == Call::CALL_TYPE_OUT)?'Inbound calls':'Outbound calls';

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

        $params = $this->validateDateParams($params);
        $dataProvider = $searchModel->searchSms($params);

        $title = (isset($params['AgentActivitySearch']['s_type_id']) && $params['AgentActivitySearch']['s_type_id'] == Sms::TYPE_OUTBOX)?'SMS Sent':'SMS Received';

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

        $params = $this->validateDateParams($params);
        $dataProvider = $searchModel->searchEmail($params);

        $title = (isset($params['AgentActivitySearch']['e_type_id']) && $params['AgentActivitySearch']['e_type_id'] == Email::TYPE_OUTBOX)?'Emails Sent':'Emails Received';

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

        $params = $this->validateDateParams($params);
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

        $params = $this->validateDateParams($params);
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

        $params = $this->validateDateParams($params);
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

        $params = $this->validateDateParams($params);
        $dataProvider = $searchModel->searchFromToLeads($params);

        $title = $params['title'];

        return $this->render('_leads', [
            'title' => $title,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}