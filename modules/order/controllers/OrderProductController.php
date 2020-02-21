<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\Order;
use modules\order\src\services\CreateOrderDTO;
use modules\order\src\services\OrderManageService;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use sales\dispatchers\EventDispatcher;
use sales\helpers\app\AppHelper;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * @property ProductQuoteRepository $productQuoteRepository
 * @property EventDispatcher $eventDispatcher
 * @property OrderManageService $orderManageService
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

	/**
	 * OrderProductController constructor.
	 * @param $id
	 * @param $module
	 * @param ProductQuoteRepository $productQuoteRepository
	 * @param OrderManageService $orderManageService
	 * @param EventDispatcher $eventDispatcher
	 * @param array $config
	 */
    public function __construct($id, $module, ProductQuoteRepository $productQuoteRepository, OrderManageService $orderManageService, EventDispatcher $eventDispatcher, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->eventDispatcher = $eventDispatcher;
		$this->orderManageService = $orderManageService;
		$this->productQuoteRepository = $productQuoteRepository;
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
                throw new Exception('Not found Product for Quote ID ('. $productQuoteId .')', 5);
            }

            if ($orderId) {
                $order = Order::findOne($orderId);
                if (!$order) {
                    throw new Exception('Order (' . $orderId . ') not found', 5);
                }

                if ($productQuote->isRelatedWithOrder() && $productQuote->isTheSameOrder($orderId)) {
                	$productQuote->removeOrderRelation();
                	$this->productQuoteRepository->save($productQuote);
					return ['message' => 'Successfully deleted Product Quote ID ('.$productQuoteId.') from order: "'.Html::encode($order->or_name).'" ('.$order->or_id.')'];
				}

            } else {
				$order = $this->orderManageService->createOrder((new CreateOrderDTO($productQuote->pqProduct->pr_lead_id)));
            }

            $productQuote->setOrderRelation($order->or_id);
            $this->productQuoteRepository->save($productQuote);


        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),'OrderProductController:' . __FUNCTION__  );
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully added Product Quote ID ('.$productQuoteId.') to order: "'.Html::encode($order->or_name).'"  ('.$order->or_id.')'];
    }

    /**
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $orderId = (int) Yii::$app->request->post('order_id');
        $productQuoteId = (int) Yii::$app->request->post('product_quote_id');

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
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable),'OrderProductController:' . __FUNCTION__  );
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed product quote (' . $productQuoteId . ') from order (' . $orderId . ')'];
    }
}
