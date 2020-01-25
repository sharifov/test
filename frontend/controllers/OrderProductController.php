<?php

namespace frontend\controllers;

use common\models\Offer;
use common\models\OfferProduct;
use common\models\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use Yii;
use common\models\OrderProduct;
use common\models\search\OrderProductSearch;
use frontend\controllers\FController;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * OrderProductController implements the CRUD actions for OrderProduct model.
 */
class OrderProductController extends FController
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
                    'delete' => ['POST'],
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all OrderProduct models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrderProduct model.
     * @param integer $orp_order_id
     * @param integer $orp_product_quote_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($orp_order_id, $orp_product_quote_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($orp_order_id, $orp_product_quote_id),
        ]);
    }

    /**
     * Creates a new OrderProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderProduct();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'orp_order_id' => $model->orp_order_id, 'orp_product_quote_id' => $model->orp_product_quote_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
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

            $orderProduct = new OrderProduct();
            $orderProduct->orp_order_id = $order->or_id;
            $orderProduct->orp_product_quote_id = $productQuoteId;

            if (!$orderProduct->save()) {
                throw new Exception('Product Quote ID ('.$productQuoteId.'), Order ID ('.$orderId.'): ' . VarDumper::dumpAsString($orderProduct->errors), 16);
            }

        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully added Product Quote ID ('.$productQuoteId.') to order: "'.Html::encode($order->or_name).'"  ('.$order->or_id.')'];
    }

    /**
     * Updates an existing OrderProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $orp_order_id
     * @param integer $orp_product_quote_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($orp_order_id, $orp_product_quote_id)
    {
        $model = $this->findModel($orp_order_id, $orp_product_quote_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'orp_order_id' => $model->orp_order_id, 'orp_product_quote_id' => $model->orp_product_quote_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OrderProduct model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $orp_order_id
     * @param integer $orp_product_quote_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($orp_order_id, $orp_product_quote_id)
    {
        $this->findModel($orp_order_id, $orp_product_quote_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $orderId = (int) Yii::$app->request->post('order_id');
        $productQuoteId = (int) Yii::$app->request->post('product_quote_id');

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (!$orderId) {
                throw new Exception('OrderId param is empty', 2);
            }

            if (!$productQuoteId) {
                throw new Exception('ProductQuoteId param is empty', 3);
            }

            $model = $this->findModel($orderId, $productQuoteId);
            if (!$model->delete()) {
                throw new Exception('Order Product (offer: '.$orderId.', quote: '.$productQuoteId.') not deleted', 4);
            }
        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed product quote (' . $productQuoteId . ') from order (' . $orderId . ')'];
    }

    /**
     * Finds the OrderProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $orp_order_id
     * @param integer $orp_product_quote_id
     * @return OrderProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($orp_order_id, $orp_product_quote_id)
    {
        if (($model = OrderProduct::findOne(['orp_order_id' => $orp_order_id, 'orp_product_quote_id' => $orp_product_quote_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
