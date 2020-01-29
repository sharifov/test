<?php

namespace modules\invoice\controllers;

use modules\order\src\entities\order\Order;
use modules\invoice\src\forms\InvoiceForm;
use Yii;
use modules\invoice\src\entities\invoice\Invoice;
use frontend\controllers\FController;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

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
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
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
     * @param $id
     * @return Invoice
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Invoice
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested Invoice does not exist.');
    }
}
