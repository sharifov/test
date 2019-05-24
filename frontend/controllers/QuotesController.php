<?php

namespace frontend\controllers;

use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\search\QuotePriceSearch;
use Yii;
use common\models\Quote;
use common\models\search\QuoteSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * QuoteController implements the CRUD actions for Quote model.
 */
class QuotesController extends FController
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'ajax-details'],
                        'allow' => true,
                        'roles' => ['supervision'],
                    ],
                    [
                        'actions' => ['ajax-details'],
                        'allow' => true,
                        'roles' => ['agent'],
                    ],
                ],
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all Quote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QuoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Quote model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        $searchModel = new QuotePriceSearch();

        $params = Yii::$app->request->queryParams;

        $params['QuotePriceSearch']['quote_id'] = $model->id;

        $dataProvider = $searchModel->search($params);

        //unset($searchModel);
        // VarDumper::dump($quotes, 10, true);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);


    }

    public function actionAjaxDetails()
    {
        $id = Yii::$app->request->get('id');

        $model = $this->findModel($id);
        $lead = $model->lead;

        if($lead->status === Lead::STATUS_TRASH && Yii::$app->user->identity->canRole('agent')) {
            throw new ForbiddenHttpException('Access Denied for Agent');
        }

        //unset($searchModel);
        //VarDumper::dump($model, 10, true);

        return $this->renderPartial('view-details', [
            'model' => $model,
        ]);
    }


    /**
     * Creates a new Quote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Quote();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Quote model.
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
     * Deletes an existing Quote model.
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
     * Finds the Quote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Quote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Quote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
