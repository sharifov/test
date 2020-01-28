<?php

namespace frontend\controllers;

use modules\order\src\entities\order\Order;
use frontend\models\form\InvoiceForm;
use Yii;
use common\models\Invoice;
use common\models\search\InvoiceSearch;
use frontend\controllers\FController;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends FController
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
     * Lists all Invoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoice model.
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
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Invoice();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->inv_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionCreateAjax()
    {
        $model = new InvoiceForm(); //new Product();


        if ($model->load(Yii::$app->request->post())) {

            //Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->validate()) {

                $order = Order::findOne($model->inv_order_id);

                $invoice = new Invoice();
                //$order->attributes = $model->attributes;
                $invoice->initCreate();

                $invoice->inv_order_id = $model->inv_order_id;
                $invoice->inv_sum = $model->inv_sum;
                $invoice->inv_client_currency = $order ? $order->or_client_currency : null;
                $invoice->calculateClientAmount();
                $invoice->inv_description = $model->inv_description;

                if (!$invoice->inv_description) {
                    $invoice->inv_description = $invoice->generateDescription();
                }

                if ($invoice->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-order-invoice-' . $invoice->inv_order_id . '"});</script>';
                }

                //$model->errors = $offer->errors;
                Yii::error(VarDumper::dumpAsString($invoice->errors), 'InvoiceController:CreateAjax:Invoice:save');

            }
            //return ['errors' => \yii\widgets\ActiveForm::validate($model)];
        } else {

            $orderId = (int) Yii::$app->request->get('id');
            $invoiceAmount = (float) Yii::$app->request->get('amount', 0);

            if (!$orderId) {
                throw new BadRequestHttpException('Not found Order identity.');
            }

            $order = Order::findOne($orderId);
            if (!$order) {
                throw new BadRequestHttpException('Not found Order');
            }

            $model->inv_order_id = $orderId;
            $model->inv_sum = $invoiceAmount;
        }

        return $this->renderAjax('forms/create_ajax_form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Invoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->inv_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @return string
     */
    public function actionUpdateAjax(): string
    {
        $invoiceId = (int) Yii::$app->request->get('id');

        try {
            $modelInvoice = $this->findModel($invoiceId);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }

        $model = new InvoiceForm();
        $model->inv_order_id = $modelInvoice->inv_order_id; //$modelInvoice->inv_order_id;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $modelInvoice->inv_description = $model->inv_description;
                $modelInvoice->inv_status_id = $model->inv_status_id;
                $modelInvoice->inv_sum = $model->inv_sum;
                $modelInvoice->calculateClientAmount();

                if ($modelInvoice->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-order-invoice-'.$modelInvoice->inv_order_id.'"});</script>';
                }

                Yii::error(VarDumper::dumpAsString($modelInvoice->errors), 'InvoiceController:actionUpdateAjax:Invoice:save');
            }
        } else {
            $model->attributes = $modelInvoice->attributes;

            $model->inv_id = $modelInvoice->inv_id;
            $model->inv_sum = $modelInvoice->inv_sum;
            $model->inv_status_id = $modelInvoice->inv_status_id;
        }

        return $this->renderAjax('forms/update_ajax_form', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing Invoice model.
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
                throw new Exception('Invoice ('.$id.') not deleted', 2);
            }
        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed invoice (' . $model->inv_id . ')'];
    }


    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested Invoice does not exist.');
    }
}
