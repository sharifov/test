<?php

namespace modules\product\controllers;

use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\offer\src\services\OfferPriceUpdater;
use modules\order\src\services\OrderPriceUpdater;
use sales\dispatchers\EventDispatcher;
use sales\helpers\app\AppHelper;
use Yii;
use frontend\controllers\FController;
use modules\product\src\forms\ProductQuoteOptionForm;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ProductQuoteOptionController
 * @property EventDispatcher $eventDispatcher
 * @property OfferPriceUpdater $offerPriceUpdater
 * @property OrderPriceUpdater $orderPriceUpdater
 */
class ProductQuoteOptionController extends FController
{
    private $eventDispatcher;

    private OfferPriceUpdater $offerPriceUpdater;

    private OrderPriceUpdater $orderPriceUpdater;

    public function __construct(
        $id,
        $module,
        EventDispatcher $eventDispatcher,
        OfferPriceUpdater $offerPriceUpdater,
        OrderPriceUpdater $orderPriceUpdater,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->eventDispatcher = $eventDispatcher;
        $this->offerPriceUpdater = $offerPriceUpdater;
        $this->orderPriceUpdater = $orderPriceUpdater;
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
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionCreateAjax(): string
    {
        $form = new ProductQuoteOptionForm();

        $productTypeId = null;

        if ($form->load(Yii::$app->request->post())) {
            $form->pqo_status_id = ProductQuoteOptionStatus::PENDING;

            if ($form->validate()) {
                $model = new ProductQuoteOption();
                $model->attributes = $form->attributes;
                $model->calculateClientPrice();

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save()) {
                        Yii::error(VarDumper::dumpAsString($model->errors), 'ProductQuoteOptionController:CreateAjax:ProductQuoteOption:save');
                        throw new \RuntimeException('ProductQuoteOption not saved');
                    }
                    $productQuote = $model->pqoProductQuote;
                    $productQuote->recalculateProfitAmount();
                    $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());

                    $transaction->commit();

                    if ($productQuote->pq_order_id) {
                        $this->orderPriceUpdater->update($productQuote->pq_order_id);
                    }

                    $offers = OfferProduct::find()->select(['op_offer_id'])->andWhere(['op_product_quote_id' => $productQuote->pq_id])->column();
                    foreach ($offers as $offerId) {
                        $this->offerPriceUpdater->update($offerId);
                    }
                } catch (\Throwable $throwable) {
                    $transaction->rollBack();
                    Yii::error(AppHelper::throwableFormatter($throwable), 'ProductQuoteOptionController:' . __FUNCTION__);
                }
                return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#pjax-product-quote-list-' . $model->pqoProductQuote->pq_product_id . '"});</script>';
            }
        } else {
            $productQuoteId = (int) Yii::$app->request->get('id');

            if (!$productQuoteId) {
                throw new BadRequestHttpException('Not found Product Quote ID', 1);
            }

            $pQuote = ProductQuote::findOne($productQuoteId);
            if (!$pQuote) {
                throw new BadRequestHttpException('Not found Product Quote', 2);
            }

            $form->pqo_product_quote_id = $productQuoteId;

            $productTypeId = $pQuote->pqProduct ? $pQuote->pqProduct->pr_type_id : 0;
        }

        return $this->renderAjax('forms/create_ajax_form', [
            'model' => $form,
            'productTypeId' => $productTypeId
        ]);
    }

    /**
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionUpdateAjax(): string
    {
        $productQuoteId = (int) Yii::$app->request->get('id');

        if (!$productQuoteId) {
            throw new BadRequestHttpException('Not found Product Quote ID', 1);
        }

        try {
            $model = $this->findModel($productQuoteId);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }

        $form = new ProductQuoteOptionForm();
        $form->pqo_id = $model->pqo_id;
        $form->pqo_product_quote_id = $model->pqo_product_quote_id;

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                $model->attributes = $form->attributes;
                $model->calculateClientPrice();

                $checkProfit = ($model->isAttributeChanged('pqo_extra_markup') || $model->isAttributeChanged('pqo_status_id'));

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save()) {
                        Yii::error(VarDumper::dumpAsString($model->errors), 'ProductQuoteOptionController:UpdateAjax:ProductQuoteOption:save');
                        throw new \RuntimeException('ProductQuoteOption not saved');
                    }
                    if ($checkProfit) {
                        $productQuote = $model->pqoProductQuote;
                        $productQuote->recalculateProfitAmount();
                        $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());
                    }

                    $transaction->commit();

                    if (isset($model->pqoProductQuote->pq_order_id)) {
                        $this->orderPriceUpdater->update($model->pqoProductQuote->pq_order_id);
                    }

                    $offers = OfferProduct::find()->select(['op_offer_id'])->andWhere(['op_product_quote_id' => $model->pqo_product_quote_id])->column();
                    foreach ($offers as $offerId) {
                        $this->offerPriceUpdater->update($offerId);
                    }
                } catch (\Throwable $throwable) {
                    $transaction->rollBack();
                    Yii::error(AppHelper::throwableFormatter($throwable), 'ProductQuoteOptionController:' . __FUNCTION__);
                }
                return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#pjax-product-quote-list-' . $model->pqoProductQuote->pq_product_id . '"});</script>';
            }
        } else {
            $form->attributes = $model->attributes;
            //$form->pqo_product_quote_id = $productQuoteId;
        }

        return $this->renderAjax('forms/update_ajax_form', [
            'model' => $form,
            'productTypeId' => $model->pqoProductQuote->pqProduct ? $model->pqoProductQuote->pqProduct->pr_type_id : 0
        ]);
    }

    /**
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model = $this->findModel($id);
            $productQuote = $model->pqoProductQuote;
            $orderId = $model->pqoProductQuote->pq_order_id ?? null;
            $productQuoteId = $model->pqo_product_quote_id;
            if (!$model->delete()) {
                throw new Exception('Product Quote Option (' . $id . ') not deleted', 2);
            }
            $productQuote->recalculateProfitAmount();
            $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());

            $transaction->commit();

            if ($orderId) {
                $this->orderPriceUpdater->update($orderId);
            }

            $offers = OfferProduct::find()->select(['op_offer_id'])->andWhere(['op_product_quote_id' => $productQuoteId])->column();
            foreach ($offers as $offerId) {
                $this->offerPriceUpdater->update($offerId);
            }
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable), 'ProductQuoteOptionController:' . __FUNCTION__);

            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed Quote Option (' . $model->pqo_id . ')'];
    }

    /**
     * @param $id
     * @return ProductQuoteOption
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ProductQuoteOption
    {
        if (($model = ProductQuoteOption::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
