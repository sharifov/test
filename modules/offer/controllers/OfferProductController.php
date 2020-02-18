<?php

namespace modules\offer\controllers;

use modules\offer\src\entities\offer\events\OfferRecalculateProfitAmountEvent;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\offer\src\entities\offerProduct\OfferProductRepository;
use modules\product\src\entities\productQuote\ProductQuote;

use sales\dispatchers\EventDispatcher;
use sales\helpers\app\AppHelper;
use Yii;
use frontend\controllers\FController;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * @property OfferProductRepository $offerProductRepository
 * @property EventDispatcher $eventDispatcher
 */
class OfferProductController extends FController
{
    private $offerProductRepository;
    private $eventDispatcher;

    /**
     * OfferProductController constructor.
     * @param $id
     * @param $module
     * @param OfferProductRepository $offerProductRepository
     * @param EventDispatcher $eventDispatcher
     * @param array $config
     */
    public function __construct($id, $module, OfferProductRepository $offerProductRepository, EventDispatcher $eventDispatcher, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->offerProductRepository = $offerProductRepository;
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

            $offerProduct = OfferProduct::create($offer->of_id, $productQuoteId);
            $this->offerProductRepository->save($offerProduct);

        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable),'OfferProductController:' . __FUNCTION__ );
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
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $this->offerProductRepository->find($offerId, $productQuoteId);
            $this->offerProductRepository->remove($model);
            $this->eventDispatcher->dispatchAll([new OfferRecalculateProfitAmountEvent([$model->opOffer])]);
            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable),'OfferProductController:' . __FUNCTION__  );
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }
        return ['message' => 'Successfully removed product quote (' . $productQuoteId . ') from offer (' . $offerId . ')'];
    }
}
