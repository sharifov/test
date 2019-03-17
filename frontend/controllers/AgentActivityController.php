<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\DynamicModel;
use common\models\search\AgentActivitySearch;


/**
 * AgentActivityController.
 */
class AgentActivityController extends FController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['supervision','admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
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

        if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
            $params['AgentActivitySearch']['supervision_id'] = Yii::$app->user->id;
        }

        if (!empty($params[$searchModel->formName()])) {
            $searchModel->attributes = $params[$searchModel->formName()];
        }else{
            $searchModel->attributes = ['date_from' => date('Y-m-d 00:00'),'date_to' => date('Y-m-d 23:59')];
        }

        $dataProvider = $searchModel->searchAgentLeads($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}