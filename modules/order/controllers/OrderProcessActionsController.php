<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\abac\dto\OrderAbacDto;
use modules\order\src\abac\OrderAbacObject;
use modules\order\src\entities\order\Order;
use modules\order\src\processManager\OrderProcessManagerCanceler;
use modules\order\src\processManager\OrderProcessManagerFactory;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class OrderProcessActionsController
 *
 * @property OrderProcessManagerCanceler $orderProcessManagerCanceler
 * @property OrderProcessManagerFactory $orderProcessManagerFactory
 */
class OrderProcessActionsController extends FController
{
    private OrderProcessManagerCanceler $orderProcessManagerCanceler;
    private OrderProcessManagerFactory $orderProcessManagerFactory;

    public function __construct(
        $id,
        $module,
        OrderProcessManagerCanceler $orderProcessManagerCanceler,
        OrderProcessManagerFactory $orderProcessManagerFactory,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->orderProcessManagerCanceler = $orderProcessManagerCanceler;
        $this->orderProcessManagerFactory = $orderProcessManagerFactory;
    }

    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'start-process' => ['POST'],
                    'cancel-process' => ['POST'],
                ],
            ],
            'access' => [
                'allowActions' => [
                    'cancel-process',
                    'start-process'
                ]
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionStartProcess()
    {
        $orderId = (int)Yii::$app->request->post('id');

        $order = Order::findOne($orderId);

        if (!$order) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $orderAbacDto = new OrderAbacDto($order);
        if (!Yii::$app->abac->can($orderAbacDto, OrderAbacObject::ACT_START_AUTO_PROCESSING, OrderAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access denied');
        }

        try {
            $this->orderProcessManagerFactory->create($order->or_id, $order->or_type_id);
            return $this->asJson([
                'error' => false,
                'message' => 'Success'
            ]);
        } catch (\Throwable $e) {
            Yii::error([
                'message' => 'Create Order process manager error',
                'error' => $e->getMessage(),
                'orderId' => $order->or_id,
            ], 'OrderActionsController');
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function actionCancelProcess()
    {
        $orderId = (int)Yii::$app->request->post('id');

        if (!Yii::$app->abac->can(null, OrderAbacObject::ACT_CANCEL_AUTO_PROCESSING, OrderAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access denied');
        }
        try {
            $this->orderProcessManagerCanceler->stop($orderId);
            return $this->asJson([
                'error' => false,
                'message' => 'Success'
            ]);
        } catch (\Throwable $e) {
            Yii::error([
                'message' => 'Cancel Order Process error',
                'error' => $e->getMessage(),
                'orderId' => $orderId,
            ], 'OrderActionsController');
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
