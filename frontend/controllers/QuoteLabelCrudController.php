<?php

namespace frontend\controllers;

use Yii;
use sales\model\quoteLabel\entity\QuoteLabel;
use sales\model\quoteLabel\entity\QuoteLabelSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class QuoteLabelCrudController
 */
class QuoteLabelCrudController extends FController
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
        $searchModel = new QuoteLabelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $ql_quote_id
     * @param string $ql_label_key
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ql_quote_id, $ql_label_key): string
    {
        return $this->render('view', [
            'model' => $this->findModel($ql_quote_id, $ql_label_key),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new QuoteLabel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ql_quote_id' => $model->ql_quote_id, 'ql_label_key' => $model->ql_label_key]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $ql_quote_id
     * @param string $ql_label_key
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($ql_quote_id, $ql_label_key)
    {
        $model = $this->findModel($ql_quote_id, $ql_label_key);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ql_quote_id' => $model->ql_quote_id, 'ql_label_key' => $model->ql_label_key]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $ql_quote_id
     * @param string $ql_label_key
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($ql_quote_id, $ql_label_key): Response
    {
        $this->findModel($ql_quote_id, $ql_label_key)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $ql_quote_id
     * @param string $ql_label_key
     * @return QuoteLabel
     * @throws NotFoundHttpException
     */
    protected function findModel($ql_quote_id, $ql_label_key): QuoteLabel
    {
        if (($model = QuoteLabel::findOne(['ql_quote_id' => $ql_quote_id, 'ql_label_key' => $ql_label_key])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
