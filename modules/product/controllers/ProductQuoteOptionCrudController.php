<?php

namespace modules\product\controllers;

use frontend\controllers\FController;
use sales\auth\Auth;
use Yii;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\search\ProductQuoteOptionCrudSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ProductQuoteOptionController implements the CRUD actions for ProductQuoteOption model.
 */
class ProductQuoteOptionCrudController extends FController
{
    /**
     * @return array
     */
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
        $searchModel = new ProductQuoteOptionCrudSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ProductQuoteOption();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $productQuote = $model->pqoProductQuote;
            $productQuote->profitAmount();
            $productQuote->save(); /* TODO::  */

            return $this->redirect(['view', 'id' => $model->pqo_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pqo_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return ProductQuoteOption
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ProductQuoteOption
    {
        if (($model = ProductQuoteOption::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
