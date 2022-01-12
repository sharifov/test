<?php

namespace modules\offer\controllers;

use common\models\Currency;
use modules\offer\src\entities\offer\events\OfferRecalculateProfitAmountEvent;
use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\offer\src\entities\offerProduct\OfferProductRepository;
use modules\offer\src\services\OfferPriceUpdater;
use modules\product\src\entities\productQuote\ProductQuote;
use src\dispatchers\EventDispatcher;
use src\helpers\app\AppHelper;
use src\model\clientChat\socket\ClientChatSocketCommands;
use src\model\clientChatLead\entity\ClientChatLead;
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
 * @property OfferPriceUpdater $offerPriceUpdater
 */
class OfferProductController extends FController
{
    private $offerProductRepository;
    private $eventDispatcher;
    private OfferPriceUpdater $offerPriceUpdater;

    /**
     * OfferProductController constructor.
     * @param $id
     * @param $module
     * @param OfferProductRepository $offerProductRepository
     * @param EventDispatcher $eventDispatcher
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        OfferProductRepository $offerProductRepository,
        EventDispatcher $eventDispatcher,
        OfferPriceUpdater $offerPriceUpdater,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->offerProductRepository = $offerProductRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->offerPriceUpdater = $offerPriceUpdater;
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
                throw new Exception('Not found Product for Quote ID (' . $productQuoteId . ')', 5);
            }

            if ($offerId) {
                $offer = Offer::findOne($offerId);
                if (!$offer) {
                    throw new Exception('Offer (' . $offerId . ') not found', 5);
                }

                $offerProduct = OfferProduct::find()->where(['op_offer_id' => $offer->of_id, 'op_product_quote_id' => $productQuoteId])->one();

                if ($offerProduct) {
                    if ($offer->isConfirm()) {
                        throw new \DomainException('Quote cannot be deleted because offer in status Confirm');
                    }

                    if (!$offerProduct->delete()) {
//                        throw new Exception('Product Quote ID (' . $productQuoteId . ') is already exist in Offer ID (' . $offerId . ')',
//                            15);
                        throw new Exception(
                            'Product Quote ID (' . $productQuoteId . ') & Offer ID (' . $offerId . ') not deleted',
                            15
                        );
                    }

                    $offer->calculateTotalPrice();
                    $offer->save();
                    $this->offerPriceUpdater->update($offer->of_id);

                    return ['message' => 'Successfully deleted Product Quote ID (' . $productQuoteId . ') from offer: "' . Html::encode($offer->of_name) . '" (' . $offer->of_id . ')'];
                }

                if ($offer->isConfirm()) {
                    throw new \DomainException('Quote cannot be added because offer in status Confirm');
                }
            } else {
                $offer = new Offer();
                $offer->initCreate();
                // $offer->of_gid = Offer::generateGid();
                // $offer->of_uid = Offer::generateUid();
                $offer->of_lead_id = $productQuote->pqProduct->pr_lead_id;
                $offer->of_name = $offer->generateName();
                // $offer->of_status_id = Offer::STATUS_NEW;
                $leadPreferences = $productQuote->pqProduct->prLead->leadPreferences;
                if ($leadPreferences && $leadPreferences->pref_currency) {
                    $offer->of_client_currency = $leadPreferences->pref_currency;
                } else {
                    $defaultCurrency = Currency::find()->select(['cur_code'])->andWhere(['cur_default' => true, 'cur_enabled' => true])->one();
                    if ($defaultCurrency && $defaultCurrency['cur_code']) {
                        $offer->of_client_currency = $defaultCurrency['cur_code'];
                    }
                }
                if ($offer->isAlternative()) {
                    $offer->pending();
                }
                $offer->updateOfferTotalByCurrency();

                if (!$offer->save()) {
                    throw new Exception('Product Quote ID (' . $productQuoteId . '), Offer ID (' . $offerId . '): ' . VarDumper::dumpAsString($offer->errors), 17);
                }
            }

            $offerProduct = OfferProduct::create($offer->of_id, $productQuoteId);
            $this->offerProductRepository->save($offerProduct);

            $offer->calculateTotalPrice();
            $offer->save();

            $this->offerPriceUpdater->update($offer->of_id);

            $chat = ClientChatLead::find()->andWhere(['ccl_lead_id' => $offer->of_lead_id])->one();
            if ($chat) {
                ClientChatSocketCommands::clientChatAddOfferButton($chat->chat, $offer->of_lead_id);
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable), 'OfferProductController:' . __FUNCTION__);
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully added Product Quote ID (' . $productQuoteId . ') to offer: "' . Html::encode($offer->of_name) . '"  (' . $offer->of_id . ')'];
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
            if ($model->opOffer && $model->opOffer->isConfirm()) {
                throw new \DomainException('Product Quote cannot be deleted because related offer is in status Confirm');
            }
            $this->offerProductRepository->remove($model);
            $this->eventDispatcher->dispatchAll([new OfferRecalculateProfitAmountEvent([$model->opOffer])]);
            $transaction->commit();
            $this->offerPriceUpdater->update($offerId);
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable), 'OfferProductController:' . __FUNCTION__);
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }
        return ['message' => 'Successfully removed product quote (' . $productQuoteId . ') from offer (' . $offerId . ')'];
    }
}
