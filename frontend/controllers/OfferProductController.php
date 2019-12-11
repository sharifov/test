<?php

namespace frontend\controllers;

use common\models\Offer;
use common\models\ProductQuote;
use Yii;
use common\models\OfferProduct;
use common\models\search\OfferProductSearch;
use frontend\controllers\FController;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * OfferProductController implements the CRUD actions for OfferProduct model.
 */
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
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all OfferProduct models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OfferProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OfferProduct model.
     * @param integer $op_offer_id
     * @param integer $op_product_quote_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($op_offer_id, $op_product_quote_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($op_offer_id, $op_product_quote_id),
        ]);
    }

    /**
     * Creates a new OfferProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OfferProduct();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'op_offer_id' => $model->op_offer_id, 'op_product_quote_id' => $model->op_product_quote_id]);
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
     * Updates an existing OfferProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $op_offer_id
     * @param integer $op_product_quote_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($op_offer_id, $op_product_quote_id)
    {
        $model = $this->findModel($op_offer_id, $op_product_quote_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'op_offer_id' => $model->op_offer_id, 'op_product_quote_id' => $model->op_product_quote_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing OfferProduct model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $op_offer_id
     * @param integer $op_product_quote_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($op_offer_id, $op_product_quote_id)
    {
        $this->findModel($op_offer_id, $op_product_quote_id)->delete();

        return $this->redirect(['index']);
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
     * Finds the OfferProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $op_offer_id
     * @param integer $op_product_quote_id
     * @return OfferProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($op_offer_id, $op_product_quote_id)
    {
        if (($model = OfferProduct::findOne(['op_offer_id' => $op_offer_id, 'op_product_quote_id' => $op_product_quote_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
