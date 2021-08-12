<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\cases\src\abac\CasesAbacObject;
use modules\lead\src\services\LeadFailBooking;
use modules\order\src\abac\OrderAbacObject;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\orderData\OrderDataActions;
use modules\order\src\services\CreateOrderDTO;
use modules\order\src\services\OrderManageService;
use modules\order\src\services\OrderPriceUpdater;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use sales\auth\Auth;
use sales\dispatchers\EventDispatcher;
use sales\helpers\app\AppHelper;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use sales\model\leadProduct\entity\LeadProductRepository;

/**
 * @property ProductQuoteRepository $productQuoteRepository
 * @property EventDispatcher $eventDispatcher
 * @property OrderManageService $orderManageService
 * @property OrderPriceUpdater $orderPriceUpdater
 * @property LeadProductRepository $leadProductRepository
 */
class OrderProductController extends FController
{
    private $eventDispatcher;
    /**
     * @var OrderManageService
     */
    private $orderManageService;
    /**
     * @var ProductQuoteRepository
     */
    private $productQuoteRepository;

    private OrderPriceUpdater $orderPriceUpdater;
    private LeadProductRepository $leadProductRepository;

    public function __construct(
        $id,
        $module,
        ProductQuoteRepository $productQuoteRepository,
        OrderManageService $orderManageService,
        EventDispatcher $eventDispatcher,
        OrderPriceUpdater $orderPriceUpdater,
        LeadProductRepository $leadProductRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->eventDispatcher = $eventDispatcher;
        $this->orderManageService = $orderManageService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->orderPriceUpdater = $orderPriceUpdater;
        $this->leadProductRepository = $leadProductRepository;
    }


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
            'access' => [
                'allowActions' => [
                    'delete-ajax'
                ]
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @return array
     */
    public function actionCreateAjax(): array
    {
        $orderId = (int) Yii::$app->request->post('order_id');
        $productQuoteId = (int) Yii::$app->request->post('product_quote_id');
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (!$productQuoteId) {
                throw new Exception('Not found Product Quote ID param', 3);
            }

            $productQuote = ProductQuote::findOne($productQuoteId);

            if (!$productQuote) {
                throw new Exception('Not found Product Quote ', 4);
            }

            if (!$productQuote->pqProduct) {
                throw new Exception('Not found Product for Quote ID (' . $productQuoteId . ')', 5);
            }

            if ($orderId) {
                $order = Order::findOne($orderId);
                if (!$order) {
                    throw new Exception('Order (' . $orderId . ') not found', 5);
                }

                if ($productQuote->isRelatedWithOrder() && $productQuote->isTheSameOrder($orderId)) {
                    $productQuote->removeOrderRelation();
                    $this->productQuoteRepository->save($productQuote);
                    $this->orderPriceUpdater->update($orderId);
                    return ['message' => 'Successfully deleted Product Quote ID (' . $productQuoteId . ') from order: "' . Html::encode($order->or_name) . '" (' . $order->or_id . ')'];
                }
            } else {
                $dto = new CreateOrderDTO(
                    $productQuote->pqProduct->pr_lead_id,
                    $productQuote->pq_client_currency,
                    [],
                    OrderSourceType::MANUAL,
                    null,
                    $productQuote->pqProduct->prLead->project_id,
                    OrderStatus::PENDING,
                    null,
                    $productQuote->pqProduct->prLead->l_client_lang,
                    null
                );
                $order = $this->orderManageService->createOrder($dto, $productQuote->pqProduct->prLead->source_id, OrderDataActions::CREATE_ORDER_WITH_PRODUCT_QUOTE, Auth::id());
            }

            $productQuote->setOrderRelation($order->or_id);
            $this->productQuoteRepository->save($productQuote);
            $this->orderPriceUpdater->update($order->or_id);
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable), 'OrderProductController:' . __FUNCTION__);
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully added Product Quote ID (' . $productQuoteId . ') to order: "' . Html::encode($order->or_name) . '"  (' . $order->or_id . ')'];
    }

    /**
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $orderId = (int) Yii::$app->request->post('order_id');
        $productQuoteId = (int) Yii::$app->request->post('product_quote_id');

        if (!Yii::$app->abac->can(null, CasesAbacObject::ACT_PRODUCT_QUOTE_REMOVE, CasesAbacObject::ACTION_ACCESS)) {
            throw new ForbiddenHttpException('Access denied');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $this->productQuoteRepository->find($productQuoteId);
            $order = $model->pqOrder;
            $model->removeOrderRelation();
            $this->productQuoteRepository->save($model);
            if ($order) {
                $this->eventDispatcher->dispatchAll([new OrderRecalculateProfitAmountEvent([$order])]);
            }
            $transaction->commit();
            $this->orderPriceUpdater->update($order->or_id);
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable), 'OrderProductController:' . __FUNCTION__);
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed product quote (' . $productQuoteId . ') from order (' . $orderId . ')'];
    }

    public function actionCreateLeadBookingFail(): Response
    {
        $productQuoteId = (int) Yii::$app->request->post('product_quote_id');
        $productId = (int) Yii::$app->request->post('product_id');

        try {
            $service = \Yii::createObject(LeadFailBooking::class);
            if ($this->leadProductRepository->exist($productId)) {
                return $this->asJson([
                    'status' => 0,
                    'message' => 'Error for Creating Fail Booking Lead. Lead already Exist for Product Quote ( ' . $productQuoteId . ' )'
                ]);
            }
            $service->create($productQuoteId, Auth::id());
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable), 'OrderProductController:' . __FUNCTION__);
            return $this->asJson(['message' => 'Info: ' . $throwable->getMessage()]);
        }

        return $this->asJson([
            'status' => 1,
            'message' => 'Success: Fail Booking Lead is created'
        ]);
    }
}
