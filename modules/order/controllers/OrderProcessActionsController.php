<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\order\Order;
use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerRepository;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class OrderProcessActionsController
 *
 * @property OrderProcessManagerRepository $processRepository
 */
class OrderProcessActionsController extends FController
{
    private OrderProcessManagerRepository $processRepository;

    public function __construct(
        $id,
        $module,
        OrderProcessManagerRepository $processRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->processRepository = $processRepository;
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

        if (OrderProcessManager::find()->andWhere(['opm_id' => $order->or_id])->exists()) {
            return $this->asJson([
                'error' => true,
                'message' => 'Process is already exist.'
            ]);
        }

        try {
            $process = OrderProcessManager::create($order->or_id, new \DateTimeImmutable());
            $this->processRepository->save($process);
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

        $process = OrderProcessManager::findOne($orderId);

        if (!$process) {
            return $this->asJson([
                'error' => true,
                'message' => 'Not found Process'
            ]);
        }

        try {
            $process->cancel(new \DateTimeImmutable());
            $this->processRepository->save($process);
            return $this->asJson([
                'error' => false,
                'message' => 'Success'
            ]);
        } catch (\Throwable $e) {
            Yii::error([
                'message' => 'Cancel Order Process error',
                'error' => $e->getMessage(),
                'orderId' => $process->opm_id,
            ], 'OrderActionsController');
            return $this->asJson([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
