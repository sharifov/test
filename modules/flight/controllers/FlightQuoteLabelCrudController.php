<?php

namespace modules\flight\controllers;

use frontend\controllers\FController;
use Yii;
use modules\flight\src\entities\flightQuoteLabel\FlightQuoteLabel;
use modules\flight\src\entities\flightQuoteLabel\FlightQuoteLabelSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteLabelCrudController
 */
class FlightQuoteLabelCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function behaviors(): array
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new FlightQuoteLabelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $fql_quote_id
     * @param string $fql_label_key
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($fql_quote_id, $fql_label_key): string
    {
        return $this->render('view', [
            'model' => $this->findModel($fql_quote_id, $fql_label_key),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new FlightQuoteLabel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fql_quote_id' => $model->fql_quote_id, 'fql_label_key' => $model->fql_label_key]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fql_quote_id
     * @param string $fql_label_key
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($fql_quote_id, $fql_label_key)
    {
        $model = $this->findModel($fql_quote_id, $fql_label_key);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fql_quote_id' => $model->fql_quote_id, 'fql_label_key' => $model->fql_label_key]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fql_quote_id
     * @param string $fql_label_key
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($fql_quote_id, $fql_label_key): Response
    {
        $this->findModel($fql_quote_id, $fql_label_key)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $fql_quote_id
     * @param string $fql_label_key
     * @return FlightQuoteLabel
     * @throws NotFoundHttpException
     */
    protected function findModel($fql_quote_id, $fql_label_key): FlightQuoteLabel
    {
        if (($model = FlightQuoteLabel::findOne(['fql_quote_id' => $fql_quote_id, 'fql_label_key' => $fql_label_key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
