<?php

namespace backend\controllers;

use common\controllers\DefaultController;
use common\models\search\LeadFlightSegmentSearch;
use common\models\search\LeadSearch;
use common\models\search\QuoteSearch;
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
class LeadsController extends BController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors ['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Lists all Lead models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LeadSearch();
        $dataProvider = $searchModel->search2(Yii::$app->request->queryParams);

        return $this->render('index', [
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

            return $this->renderAjax('view', $viewParams);
        }

        return $this->render('view', $viewParams);

    }

    /**
     * Creates a new Lead model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Lead();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Lead model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Lead model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Lead model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Lead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Lead::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
