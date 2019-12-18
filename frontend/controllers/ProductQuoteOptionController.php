<?php

namespace frontend\controllers;

use common\models\ProductQuote;
use frontend\models\form\ProductQuoteOptionForm;
use Yii;
use common\models\ProductQuoteOption;
use common\models\search\ProductQuoteOptionSearch;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ProductQuoteOptionController implements the CRUD actions for ProductQuoteOption model.
 */
class ProductQuoteOptionController extends FController
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
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all ProductQuoteOption models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductQuoteOptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductQuoteOption model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProductQuoteOption model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProductQuoteOption();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pqo_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * @return array|string|Response
     * @throws BadRequestHttpException
     */
    public function actionCreateAjax()
    {
        $form = new ProductQuoteOptionForm();

        if ($form->load(Yii::$app->request->post())) {

            $form->pqo_status_id = ProductQuoteOption::STATUS_PENDING;

            if ($form->validate()) {
                $model = new ProductQuoteOption();
                $model->attributes = $form->attributes;
                if ($model->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-product-quote-list-' . $model->pqoProductQuote->pq_product_id . '"});</script>';
                }
                Yii::error(VarDumper::dumpAsString($model->errors), 'ProductQuoteOptionController:CreateAjax:ProductQuoteOption:save');
            }
        } else {
            $productQuoteId = (int) Yii::$app->request->get('id');

            if (!$productQuoteId) {
                throw new BadRequestHttpException('Not found Product Quote ID', 1);
            }

            $pQuote = ProductQuote::findOne($productQuoteId);
            if (!$pQuote) {
                throw new BadRequestHttpException('Not found Product Quote', 2);
            }

            $form->pqo_product_quote_id = $productQuoteId;
        }

        return $this->renderAjax('forms/create_ajax_form', [
            'model' => $form,
        ]);
    }

    /**
     * Updates an existing ProductQuoteOption model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
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
     * Updates an existing ProductQuoteOption model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionUpdateAjax(): string
    {
        $productQuoteId = (int) Yii::$app->request->get('id');

        if (!$productQuoteId) {
            throw new BadRequestHttpException('Not found Product Quote ID', 1);
        }

        try {
            $model = $this->findModel($productQuoteId);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }

        $form = new ProductQuoteOptionForm();
        $form->pqo_id = $model->pqo_id;
        $form->pqo_product_quote_id = $model->pqo_product_quote_id;

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                $model->attributes = $form->attributes;
                if ($model->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-product-quote-list-' . $model->pqoProductQuote->pq_product_id . '"});</script>';
                }
                Yii::error(VarDumper::dumpAsString($model->errors), 'ProductQuoteOptionController:UpdateAjax:ProductQuoteOption:save');
            }
        } else {
            $form->attributes = $model->attributes;
            //$form->pqo_product_quote_id = $productQuoteId;
        }

        return $this->renderAjax('forms/update_ajax_form', [
            'model' => $form,
        ]);
    }

    /**
     * Deletes an existing ProductQuoteOption model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $model = $this->findModel($id);
            if (!$model->delete()) {
                throw new Exception('Product Quote Option ('.$id.') not deleted', 2);
            }
        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed Quote Option (' . $model->pqo_id . ')'];
    }

    /**
     * Finds the ProductQuoteOption model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductQuoteOption the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductQuoteOption::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
