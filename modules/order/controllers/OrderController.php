<?php

namespace modules\order\controllers;

use common\models\Lead;
use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\search\OrderCrudSearch;
use modules\order\src\entities\order\search\OrderSearch;
use modules\order\src\forms\OrderForm;
use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\services\CreateOrderDTO;
use modules\order\src\services\OrderManageService;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\auth\Auth;
use Yii;
use modules\order\src\entities\order\Order;
use frontend\controllers\FController;
use yii\bootstrap4\Html;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use modules\fileStorage\src\services\url\UrlGenerator;

/**
 * Class OrderController
 * @package modules\order\controllers
 *
 * @property OrderManageService $orderManageService
 */
class OrderController extends FController
{
    /**
     * @var OrderManageService
     */
    private $orderManageService;

    public function __construct($id, $module, OrderManageService $orderManageService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->orderManageService = $orderManageService;
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

    public function actionSearch()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionCreateAjax()
    {
        $model = new OrderForm(); //new Product();

        $leadId = (int) Yii::$app->request->get('id');

        if (!$leadId) {
            throw new BadRequestHttpException('Not found Lead identity.');
        }

        $lead = Lead::findOne($leadId);
        if (!$lead) {
            throw new BadRequestHttpException('Not found Lead');
        }

        if ($model->load(Yii::$app->request->post())) {
            //Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->validate()) {
                try {
                    $dto = new CreateOrderDTO(
                        $model->or_lead_id,
                        null,
                        [],
                        OrderSourceType::MANUAL,
                        null,
                        $lead->project_id
                    );
                    $this->orderManageService->createOrder($dto);

                    return '<script>$("#modal-df").modal("hide"); $.pjax.reload({container: "#pjax-lead-orders", push: false, replace: false, async: false, timeout: 2000});</script>';
                } catch (\Throwable $e) {
                    Yii::error(VarDumper::dumpAsString($e->getMessage()), 'OrderController:CreateAjax:orderManageService:createOrder');
                }
            }
        }

        $model->or_lead_id = $leadId;

        return $this->renderAjax('forms/create_ajax_form', [
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
                $modelOrder->or_client_currency = $model->or_client_currency;
                $modelOrder->or_app_total = $model->or_app_total;
                $modelOrder->or_agent_markup = $model->or_agent_markup;
                $modelOrder->or_app_markup = $model->or_app_markup;

                $modelOrder->updateOrderTotalByCurrency();

                //$modelOrder->or_client_total = $model->or_client_total;
                //$modelOrder->or_client_currency_rate = $model->or_client_currency_rate;

                if ($modelOrder->save()) {
                    return '<script>
                        $("#modal-df").modal("hide"); 
                        if ($("#pjax-lead-orders").length) {
                            $.pjax.reload({container: "#pjax-lead-orders", push: false, replace: false, async: false, timeout: 2000});
                        }
                        if ($("#pjax-order-view-' . $model->or_id . '").length) {
                            $.pjax.reload({container: "#pjax-order-view-' . $model->or_id . '", push: false, replace: false, async: false, timeout: 2000});
                        }
                    </script>';
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
    * @return array
    */
    public function actionDeleteAjax(): array
    {
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $model = $this->findModel($id);
            if (!$model->delete()) {
                throw new Exception('Order (' . $id . ') not deleted', 2);
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
                throw new Exception('Lead (' . $leadId . ') not found', 4);
            }

            $orders = Order::find()->where(['or_lead_id' => $lead->id])->orderBy(['or_id' => SORT_DESC])->all();

            if ($orders) {
                foreach ($orders as $order) {
                    $exist = ProductQuote::find()->where(['pq_order_id' => $order->or_id, 'pq_id' => $productQuoteId])->exists();

                    $offerList[] = Html::a(($exist ? '<i class="fa fa-check-square-o success"></i> ' : '') . $order->or_name, null, [
                        'class' => 'dropdown-item btn-add-quote-to-order ', // . ($exist ? 'disabled' : ''),
                        'title' => 'ID: ' . $order->or_id . ', UID: ' . \yii\helpers\Html::encode($order->or_uid),
                        'data-product-quote-id' => $productQuoteId,
                        'data-order-id' => $order->or_id,
                        'data-url' => \yii\helpers\Url::to(['/order/order-product/create-ajax'])
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
            'data-url' => \yii\helpers\Url::to(['/order/order-product/create-ajax'])
        ]);

        return ['html' => implode('', $offerList)];
    }

    public function actionView(string $gid, UrlGenerator $urlGenerator)
    {
        if (!$order = Order::findOne(['or_gid' => $gid])) {
            throw new NotFoundHttpException('Order not found by GID(' . $gid . ')');
        }

        return $this->render('view', [
            'order' => $order,
            'orderProcessManage' => OrderProcessManager::findOne($order->or_id),
            'orderFiles' => FileOrder::findAll(['fo_or_id' => $order->or_id]),
            'urlGenerator' => $urlGenerator,
        ]);
    }

    /**
     * @param $id
     * @return Order
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Order
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Order not found by ID(' . $id . ')');
    }
}
