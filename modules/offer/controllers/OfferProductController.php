<?php

namespace modules\offer\controllers;

use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\product\src\entities\productQuote\ProductQuote;
use Yii;
use frontend\controllers\FController;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OfferProductController extends FController
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
        $offerId = (int) Yii::$app->request->post('offer_id');
        $productQuoteId = (int) Yii::$app->request->post('product_quote_id');
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
//            if (!$offerId) {
//                throw new Exception('Not found Offer ID param', 2);
//            }

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

            if ($offerId) {
                $offer = Offer::findOne($offerId);
                if (!$offer) {
                    throw new Exception('Offer (' . $offerId . ') not found', 5);
                }

                $offerProduct = OfferProduct::find()->where(['op_offer_id' => $offer->of_id, 'op_product_quote_id' => $productQuoteId])->one();

                if ($offerProduct) {

                    if (!$offerProduct->delete()) {
//                        throw new Exception('Product Quote ID (' . $productQuoteId . ') is already exist in Offer ID (' . $offerId . ')',
//                            15);
                        throw new Exception('Product Quote ID (' . $productQuoteId . ') & Offer ID (' . $offerId . ') not deleted',
                            15);
                    }

                    return ['message' => 'Successfully deleted Product Quote ID ('.$productQuoteId.') from offer: "'.Html::encode($offer->of_name).'" ('.$offer->of_id.')'];
                }

            } else {

                $offer = new Offer();
                $offer->initCreate();
                // $offer->of_gid = Offer::generateGid();
                // $offer->of_uid = Offer::generateUid();
                $offer->of_lead_id = $productQuote->pqProduct->pr_lead_id;
                $offer->of_name = $offer->generateName();
                // $offer->of_status_id = Offer::STATUS_NEW;

                if (!$offer->save()) {
                    throw new Exception('Product Quote ID ('.$productQuoteId.'), Offer ID ('.$offerId.'): ' . VarDumper::dumpAsString($offer->errors), 17);
                }
            }

            $offerProduct = new OfferProduct();
            $offerProduct->op_offer_id = $offer->of_id;
            $offerProduct->op_product_quote_id = $productQuoteId;

            if (!$offerProduct->save()) {
                throw new Exception('Product Quote ID ('.$productQuoteId.'), Offer ID ('.$offerId.'): ' . VarDumper::dumpAsString($offerProduct->errors), 16);
            }

        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully added Product Quote ID ('.$productQuoteId.') to offer: "'.Html::encode($offer->of_name).'"  ('.$offer->of_id.')'];
    }

    /**
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $offerId = (int) Yii::$app->request->post('offer_id');
        $productQuoteId = (int) Yii::$app->request->post('product_quote_id');

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (!$offerId) {
                throw new Exception('OfferId param is empty', 2);
            }

            if (!$productQuoteId) {
                throw new Exception('ProductQuoteId param is empty', 3);
            }

            $model = $this->findModel($offerId, $productQuoteId);
            if (!$model->delete()) {
                throw new Exception('Offer Product (offer: '.$offerId.', quote: '.$productQuoteId.') not deleted', 4);
            }
        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed product quote (' . $productQuoteId . ') from offer (' . $offerId . ')'];
    }

    /**
     * @param $op_offer_id
     * @param $op_product_quote_id
     * @return OfferProduct
     * @throws NotFoundHttpException
     */
    protected function findModel($op_offer_id, $op_product_quote_id): OfferProduct
    {
        if (($model = OfferProduct::findOne(['op_offer_id' => $op_offer_id, 'op_product_quote_id' => $op_product_quote_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
