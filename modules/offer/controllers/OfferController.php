<?php

namespace modules\offer\controllers;

use modules\offer\src\entities\offerProduct\OfferProduct;
use Yii;
use common\models\Lead;
use frontend\controllers\FController;
use modules\offer\src\forms\OfferForm;
use modules\offer\src\entities\offer\Offer;
use yii\bootstrap4\Html;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OfferController extends FController
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
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionCreateAjax()
    {
        $model = new OfferForm(); //new Product();


        if ($model->load(Yii::$app->request->post())) {

            //Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->validate()) {
                $offer = new Offer();
                $offer->initCreate();
                $offer->of_lead_id = $model->of_lead_id;
                $offer->of_name = $model->of_name;

                $offer->of_client_currency = $model->of_client_currency ?: null;
                $offer->of_client_currency_rate = $model->of_client_currency_rate ?: null;
                $offer->of_app_total = $model->of_app_total;

//                $offer->of_client_total = $model->of_client_total;



//                $offer->of_gid = Offer::generateGid();
//                $offer->of_uid = Offer::generateUid();
//                $offer->of_status_id = Offer::STATUS_NEW;

                if (!$offer->of_name && $offer->of_lead_id) {
                    $offer->of_name = $offer->generateName();
                }

                $offer->updateOfferTotalByCurrency();

                if ($offer->save()) {
                    return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#pjax-lead-offers"})</script>';
                }

                //$model->errors = $offer->errors;
                Yii::error(VarDumper::dumpAsString($offer->errors), 'OfferController:CreateAjax:Offer:save');

            }
            //return ['errors' => \yii\widgets\ActiveForm::validate($model)];
        } else {

            $leadId = (int) Yii::$app->request->get('id');

            if (!$leadId) {
                throw new BadRequestHttpException('Not found Lead identity.');
            }

            $lead = Lead::findOne($leadId);
            if (!$lead) {
                throw new BadRequestHttpException('Not found Lead');
            }

            $model->of_lead_id = $leadId;
        }

        return $this->renderAjax('forms/create_ajax_form', [
            'model' => $model,
        ]);
    }

    /**
     * @return array
     */
    public function actionListMenuAjax(): array
    {
        $leadId = (int) Yii::$app->request->post('lead_id');
        $productQuoteId = (int) Yii::$app->request->post('product_quote_id');
        $offerList = [];

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {

            if (!$leadId) {
                throw new Exception('Not found Lead ID params', 2);
            }

            if (!$productQuoteId) {
                throw new Exception('Not found Product Quote ID params', 3);
            }

            $lead = Lead::findOne($leadId);
            if (!$lead) {
                throw new Exception('Lead ('.$leadId.') not found', 4);
            }

            $offers = Offer::find()->where(['of_lead_id' => $lead->id])->orderBy(['of_id' => SORT_DESC])->all();

            if ($offers) {
                foreach ($offers as $offer) {

                    $exist = OfferProduct::find()->where(['op_offer_id' => $offer->of_id, 'op_product_quote_id' => $productQuoteId])->exists();

                    $offerList[] = Html::a(($exist ? '<i class="fa fa-check-square-o success"></i> ' : '<i class="fa fa-square-o"></i> ') . $offer->of_name, null, [
                        'class' => 'dropdown-item btn-add-quote-to-offer ', // . ($exist ? 'disabled' : ''),
                        'title' => 'ID: ' . $offer->of_id . ', UID: ' . \yii\helpers\Html::encode($offer->of_uid),
                        'data-product-quote-id' => $productQuoteId,
                        'data-offer-id' => $offer->of_id,
                        'data-url' => \yii\helpers\Url::to(['/offer/offer-product/create-ajax'])
                    ]);
                }
            }

        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        $offerList[] = '<div class="dropdown-divider"></div>';
        $offerList[] = Html::a('<i class="fa fa-plus-circle"></i> new offer', null, [
            'class' => 'dropdown-item btn-add-quote-to-offer',
            'data-product-quote-id' => $productQuoteId,
            'data-offer-id' => 0,
            'data-url' => \yii\helpers\Url::to(['/offer/offer-product/create-ajax'])
        ]);

        return ['html' => implode('', $offerList)];
    }

    /**
     * @return string
     */
    public function actionUpdateAjax(): string
    {
        $offerId = (int) Yii::$app->request->get('id');

        try {
            $modelOffer = $this->findModel($offerId);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }

        $model = new OfferForm();
        $model->of_lead_id  = $modelOffer->of_lead_id;
        $model->of_id       = $modelOffer->of_id;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $modelOffer->of_name = $model->of_name;
                $modelOffer->of_status_id = $model->of_status_id;

                $modelOffer->of_client_currency = $model->of_client_currency;
                $modelOffer->of_client_currency_rate = $model->of_client_currency_rate;
                $modelOffer->of_app_total = $model->of_app_total;

                $modelOffer->updateOfferTotalByCurrency();

                if ($modelOffer->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-lead-offers", timout: 8000});</script>';
                }

                Yii::error(VarDumper::dumpAsString($modelOffer->errors), 'OfferController:actionUpdateAjax:Offer:save');
            }
        } else {
            $model->attributes = $modelOffer->attributes;
        }

        return $this->renderAjax('forms/update_ajax_form', [
            'model' => $model,
        ]);

    }

    /**
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $model = $this->findModel($id);
            if (!$model->delete()) {
                throw new Exception('Offer ('.$id.') not deleted', 2);
            }
        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed offer (' . $model->of_id . ')'];
    }

    /**
     * @param $id
     * @return Offer
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Offer
    {
        if (($model = Offer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
