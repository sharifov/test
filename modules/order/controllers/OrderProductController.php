<?php

namespace modules\order\controllers;

use frontend\controllers\FController;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderProduct\OrderProductRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\dispatchers\EventDispatcher;
use sales\helpers\app\AppHelper;
use Yii;
use modules\order\src\entities\orderProduct\OrderProduct;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * @property OrderProductRepository $orderProductRepository
 * @property EventDispatcher $eventDispatcher
 */
class OrderProductController extends FController
{
    private $orderProductRepository;
    private $eventDispatcher;

    /**
     * OrderProductController constructor.
     * @param $id
     * @param $module
     * @param OrderProductRepository $orderProductRepository
     * @param EventDispatcher $eventDispatcher
     * @param array $config
     */
    public function __construct($id, $module, OrderProductRepository $orderProductRepository, EventDispatcher $eventDispatcher, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->orderProductRepository = $orderProductRepository;
        $this->eventDispatcher = $eventDispatcher;
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

                $orderProduct = OrderProduct::find()->where(['orp_order_id' => $order->or_id, 'orp_product_quote_id' => $productQuoteId])->one();

                if ($orderProduct) {

                    if (!$orderProduct->delete()) {
//                        throw new Exception('Product Quote ID (' . $productQuoteId . ') is already exist in Offer ID (' . $offerId . ')',
//                            15);
                        throw new Exception('Product Quote ID (' . $productQuoteId . ') & Order ID (' . $orderId . ') not deleted',
                            15);
                    }

                    return ['message' => 'Successfully deleted Product Quote ID ('.$productQuoteId.') from order: "'.Html::encode($order->or_name).'" ('.$order->or_id.')'];
                }

            } else {

                $order = new Order();
                $order->initCreate();
                // $offer->of_gid = Offer::generateGid();
                // $offer->of_uid = Offer::generateUid();
                $order->or_lead_id = $productQuote->pqProduct->pr_lead_id;
                $order->or_name = $order->generateName();
                // $offer->of_status_id = Offer::STATUS_NEW;

                if (!$order->save()) {
                    throw new Exception('Product Quote ID ('.$productQuoteId.'), Order ID ('.$orderId.'): ' . VarDumper::dumpAsString($order->errors), 17);
                }
            }

            $orderProduct = OrderProduct::create($order->or_id, $productQuoteId);
            $this->orderProductRepository->save($orderProduct);

            if (!$orderProduct->save()) {
                throw new Exception('Product Quote ID ('.$productQuoteId.'), Order ID ('.$orderId.'): ' . VarDumper::dumpAsString($orderProduct->errors), 16);
            }

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
            $model = $this->orderProductRepository->find($orderId, $productQuoteId);
            $this->orderProductRepository->remove($model);
            $this->eventDispatcher->dispatchAll([new OrderRecalculateProfitAmountEvent([$model->orpOrder])]);
            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable),'OrderProductController:' . __FUNCTION__  );
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed product quote (' . $productQuoteId . ') from order (' . $orderId . ')'];
    }

    /**
     * @param $orp_order_id
     * @param $orp_product_quote_id
     * @return OrderProduct
     * @throws NotFoundHttpException
     */
    protected function findModel($orp_order_id, $orp_product_quote_id): OrderProduct
    {
        if (($model = OrderProduct::findOne(['orp_order_id' => $orp_order_id, 'orp_product_quote_id' => $orp_product_quote_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
