<?php

namespace modules\order\controllers;

use common\models\Payment;
use common\models\search\TransactionSearch;
use frontend\controllers\FController;
use modules\order\src\payment\services\PaymentService;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class PaymentActionsController
 *
 * @property PaymentService $paymentService
 */
class PaymentActionsController extends FController
{
    private PaymentService $paymentService;

    public function __construct($id, $module, PaymentService $paymentService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->paymentService = $paymentService;
    }

    public function behaviors(): array
    {
        $behaviors = [
//            'verbs' => [
//                'class' => VerbFilter::class,
//                'actions' => [
//                    'cancel' => ['POST'],
//                ],
//            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionVoid()
    {
        $paymentId = (int) \Yii::$app->request->post('id');

        $payment = $this->findModel($paymentId);

        try {
            $this->paymentService->void([]);
            return $this->asJson([
                'error' => false,
                'message' => 'ok',
            ]);
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function actionCapture()
    {
        $paymentId = (int) \Yii::$app->request->post('id');

        $payment = $this->findModel($paymentId);

        try {
            $this->paymentService->capture([]);
            return $this->asJson([
                'error' => false,
                'message' => 'ok',
            ]);
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function actionRefund()
    {
        $paymentId = (int) \Yii::$app->request->post('id');

        $payment = $this->findModel($paymentId);

        try {
            $this->paymentService->refund([]);
            return $this->asJson([
                'error' => false,
                'message' => 'ok',
            ]);
        } catch (\Throwable $e) {
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function actionUpdate()
    {
        $paymentId = (int) \Yii::$app->request->get('id');

        $payment = Payment::findOne($paymentId);

        if (!$payment) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($payment->load(\Yii::$app->request->post()) && $payment->save()) {
            return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-order-payment-' . $payment->pay_order_id . '"});</script>';
        }

        return $this->renderAjax('update', [
            'model' => $payment,
        ]);
    }

    public function actionDelete()
    {
        $paymentId = (int) \Yii::$app->request->post('id');

        $payment = Payment::findOne($paymentId);

        if (!$payment) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $payment->delete();

        return $this->asJson(['error' => false]);
    }

    public function actionStatusLog()
    {
        $paymentId = (int)\Yii::$app->request->get('id');

        $payment = Payment::findOne($paymentId);

        if (!$payment) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $searchModel = new TransactionSearch();

        $params['TransactionSearch']['tr_payment_id'] = $payment->pay_id;

        $dataProvider = $searchModel->search($params);

        return $this->renderAjax('log', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return Payment
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Payment
    {
        if (($model = Payment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
