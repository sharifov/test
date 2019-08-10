<?php

namespace frontend\controllers;

use common\models\LeadFlow;
use common\models\LeadTask;
use common\models\Reason;
use common\models\search\LeadFlightSegmentSearch;
use common\models\search\LeadSearch;
use common\models\search\QuoteSearch;
use common\models\search\SaleSearch;
use common\models\Task;
use frontend\models\LeadMultipleForm;
use Yii;
use common\models\Lead;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeadsController implements the CRUD actions for Lead model.
 */
class SaleController extends FController
{

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

    /**
     * Lists all Lead models.
     * @return mixed
     */
    public function actionSearch()
    {
        $session = Yii::$app->session;

        $searchModel = new SaleSearch();

        $params = Yii::$app->request->queryParams;
        $params2 = Yii::$app->request->post();


        $params = ArrayHelper::merge($params, $params2);

        $dataProvider = $searchModel->search($params);


        /*if($isAgent) {
            $user = Yii::$app->user->identity;
            $checkShiftTime = $user->checkShiftTime();

        }*/

        $multipleForm = new LeadMultipleForm();



        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single Lead model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        $searchModel = new QuoteSearch();
        $searchModelSegments = new LeadFlightSegmentSearch();

        $params = Yii::$app->request->queryParams;
        $params['QuoteSearch']['lead_id'] = $model->id;
        $dataProvider = $searchModel->search($params);


        $params = Yii::$app->request->queryParams;
        $params['LeadFlightSegmentSearch']['lead_id'] = $model->id;
        $dataProviderSegments = $searchModelSegments->search($params);

        //unset($searchModel);

        // VarDumper::dump($quotes, 10, true);


        $viewParams = [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

            'searchModelSegments' => $searchModelSegments,
            'dataProviderSegments' => $dataProviderSegments,
        ];

        if (Yii::$app->request->isAjax) {
            /*$viewParams['searchModel'] = null;
            $viewParams['dataProvider']->sort = false;
            $viewParams['searchModelSegments'] = null;
            $viewParams['dataProviderSegments']->sort = false;*/

            //return $this->renderAjax('view', $viewParams);
        }

        return $this->render('view', $viewParams);

    }

}
