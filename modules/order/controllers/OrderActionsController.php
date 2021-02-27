<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\order\OrderStatusAction;
use modules\order\src\useCase\orderCancel\CancelForm;
use modules\order\src\useCase\orderCancel\OrderCancelService;
use modules\order\src\useCase\orderComplete\CompleteForm;
use modules\order\src\useCase\orderComplete\OrderCompleteService;
use sales\auth\Auth;
use Yii;
use modules\order\src\entities\order\Order;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class OrderActionsController
 *
 * @property OrderCancelService $cancelService
 * @property OrderCompleteService $completeService
 */
class OrderActionsController extends FController
{
    private OrderCancelService $cancelService;
    private OrderCompleteService $completeService;

    public function __construct($id, $module, OrderCancelService $cancelService, OrderCompleteService $completeService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->cancelService = $cancelService;
        $this->completeService = $completeService;
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

    public function actionCancel()
    {
        $orderId = (int) Yii::$app->request->get('orderId');

        $order = $this->findModel($orderId);

        $model = new CancelForm($order->or_id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $this->cancelService->cancel(
                    $model->orderId,
                    $model->description,
                    OrderStatusAction::MANUAL,
                    Auth::id()
                );
                return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-lead-orders", push: false, replace: false, async: false, timeout: 2000});</script>';
            } catch (\Throwable $e) {
                return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-lead-orders", push: false, replace: false, async: false, timeout: 2000}); createNotify(\'Cancel order\', \'' . $e->getMessage() . '\', \'error\')</script>';
            }
        }

        return $this->renderAjax('cancel', [
            'model' => $model,
        ]);
    }

    public function actionComplete()
    {
        $orderId = (int) Yii::$app->request->get('orderId');

        $order = $this->findModel($orderId);

        $model = new CompleteForm($order->or_id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $this->completeService->complete(
                    $model->orderId,
                    $model->description,
                    OrderStatusAction::MANUAL,
                    Auth::id()
                );
                return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-lead-orders", push: false, replace: false, async: false, timeout: 2000});</script>';
            } catch (\Throwable $e) {
                return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-lead-orders", push: false, replace: false, async: false, timeout: 2000}); createNotify(\'Complete order\', \'' . $e->getMessage() . '\', \'error\')</script>';
            }
        }

        return $this->renderAjax('complete', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Order
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Order
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
