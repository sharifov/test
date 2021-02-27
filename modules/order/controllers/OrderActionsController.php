<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\order\OrderStatusAction;
use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerRepository;
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
 * @property OrderProcessManagerRepository $processRepository
 */
class OrderActionsController extends FController
{
    private OrderCancelService $cancelService;
    private OrderCompleteService $completeService;
    private OrderProcessManagerRepository $processRepository;

    public function __construct(
        $id,
        $module,
        OrderCancelService $cancelService,
        OrderCompleteService $completeService,
        OrderProcessManagerRepository $processRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->cancelService = $cancelService;
        $this->completeService = $completeService;
        $this->processRepository = $processRepository;
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

    public function actionStartProcess()
    {
        $orderId = (int)Yii::$app->request->get('orderId');

        $order = $this->findModel($orderId);

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
        $orderId = (int)Yii::$app->request->get('orderId');

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

    public function actionCancel()
    {
        $orderId = (int)Yii::$app->request->get('orderId');

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
        $orderId = (int)Yii::$app->request->get('orderId');

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
