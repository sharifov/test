<?php

namespace modules\order\controllers;

use common\models\Payment;
use common\models\search\TransactionSearch;
use common\models\Transaction;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class PaymentActionsController
 *
 */
class PaymentActionsController extends Controller
{
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
        $paymentId = (int) \Yii::$app->request->get('id');

        $payment = $this->findModel($paymentId);

        //todo
    }

    public function actionCapture()
    {
        $paymentId = (int) \Yii::$app->request->get('id');

        $payment = $this->findModel($paymentId);

        //todo
    }

    public function actionRefund()
    {
        $paymentId = (int) \Yii::$app->request->get('id');

        $payment = $this->findModel($paymentId);

        //todo
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
