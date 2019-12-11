<?php

namespace frontend\controllers;

use common\models\Lead;
use common\models\OfferProduct;
use frontend\models\form\OfferForm;
use http\Url;
use Yii;
use common\models\Offer;
use common\models\search\OfferSearch;
use frontend\controllers\FController;
use yii\bootstrap4\Html;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * OfferController implements the CRUD actions for Offer model.
 */
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
                    'delete' => ['POST'],
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all Offer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OfferSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Offer model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Offer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Offer();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->of_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * @return array|string
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
//                $offer->of_gid = Offer::generateGid();
//                $offer->of_uid = Offer::generateUid();
//                $offer->of_status_id = Offer::STATUS_NEW;

                if (!$offer->of_name && $offer->of_lead_id) {
                    $offer->of_name = $offer->generateName();
                }

                if ($offer->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-lead-offers", timout: 8000});</script>';
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
     * Updates an existing Offer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->of_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
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
     * Deletes an existing Offer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
                        'data-url' => \yii\helpers\Url::to(['offer-product/create-ajax'])
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
            'data-url' => \yii\helpers\Url::to(['offer-product/create-ajax'])
        ]);

        return ['html' => implode('', $offerList)];
    }

    /**
     * Finds the Offer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Offer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Offer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
