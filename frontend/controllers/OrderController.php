<?php

namespace frontend\controllers;

use common\models\Lead;
use common\models\OrderProduct;
use frontend\models\form\OrderForm;
use Yii;
use common\models\Order;
use common\models\search\OrderSearch;
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
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends FController
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
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->or_id]);
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
        $model = new OrderForm(); //new Product();


        if ($model->load(Yii::$app->request->post())) {

            //Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->validate()) {
                $order = new Order();
                //$order->attributes = $model->attributes;
                $order->initCreate();

                $order->or_lead_id = $model->or_lead_id;
                $order->or_name = $model->or_name;

                if (!$order->or_name && $order->or_lead_id) {
                    $order->or_name = $order->generateName();
                }

                if ($order->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-lead-orders"});</script>';
                }

                //$model->errors = $offer->errors;
                Yii::error(VarDumper::dumpAsString($order->errors), 'OrderController:CreateAjax:Order:save');

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

            $model->or_lead_id = $leadId;
        }

        return $this->renderAjax('forms/create_ajax_form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->or_id]);
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
            $modelOrder = $this->findModel($offerId);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }

        $model = new OrderForm();
        $model->or_lead_id = $modelOrder->or_lead_id;
        $model->or_id = $modelOrder->or_id;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                //$modelOrder->attributes = $model->attributes;

                $modelOrder->or_name = $model->or_name;
                $modelOrder->or_status_id = $model->or_status_id;
                $modelOrder->or_pay_status_id = $model->or_pay_status_id;
                $modelOrder->or_client_currency_rate = $model->or_client_currency_rate;
                $modelOrder->or_client_currency = $model->or_client_currency;
                $modelOrder->or_client_total = $model->or_client_total;
                $modelOrder->or_app_total = $model->or_app_total;
                $modelOrder->or_agent_markup = $model->or_agent_markup;
                $modelOrder->or_app_markup = $model->or_app_markup;

                if ($modelOrder->save()) {
                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-lead-orders"});</script>';
                }

                Yii::error(VarDumper::dumpAsString($modelOrder->errors), 'OrderController:actionUpdateAjax:Order:save');
            }
        } else {
            $model->attributes = $modelOrder->attributes;
        }

        return $this->renderAjax('forms/update_ajax_form', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing Order model.
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
                throw new Exception('Order ('.$id.') not deleted', 2);
            }
        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed order (' . $model->or_id . ')'];
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

            $orders = Order::find()->where(['or_lead_id' => $lead->id])->orderBy(['or_id' => SORT_DESC])->all();

            if ($orders) {
                foreach ($orders as $order) {

                    $exist = OrderProduct::find()->where(['orp_order_id' => $order->or_id, 'orp_product_quote_id' => $productQuoteId])->exists();

                    $offerList[] = Html::a(($exist ? '<i class="fa fa-check-square-o success"></i> ' : '<i class="fa fa-square-o"></i> ') . $order->or_name, null, [
                        'class' => 'dropdown-item btn-add-quote-to-order ', // . ($exist ? 'disabled' : ''),
                        'title' => 'ID: ' . $order->or_id . ', UID: ' . \yii\helpers\Html::encode($order->or_uid),
                        'data-product-quote-id' => $productQuoteId,
                        'data-order-id' => $order->or_id,
                        'data-url' => \yii\helpers\Url::to(['order-product/create-ajax'])
                    ]);
                }
            }

        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        $offerList[] = '<div class="dropdown-divider"></div>';
        $offerList[] = Html::a('<i class="fa fa-plus-circle"></i> new order', null, [
            'class' => 'dropdown-item btn-add-quote-to-order',
            'data-product-quote-id' => $productQuoteId,
            'data-order-id' => 0,
            'data-url' => \yii\helpers\Url::to(['order-product/create-ajax'])
        ]);

        return ['html' => implode('', $offerList)];
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
