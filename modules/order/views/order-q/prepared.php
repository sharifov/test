<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\grid\columns\OrderPayStatusColumn;
use common\components\grid\UserSelect2Column;
use common\components\grid\DateTimeColumn;

/**
 * @var $searchModel \modules\order\src\entities\order\search\OrderQSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = 'Prepared';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>
    <i class="fa fa-recycle"></i> <?= Html::encode($this->title) ?>
</h1>

<div class="orders-q-new">
    <?php Pjax::begin(['id' => 'orders-q-prepared-pjax-list', 'timeout' => 5000, 'enablePushState' => true]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'or_id',
            ],
            'or_fare_id',
            [
                'label' => 'Booking ID',
                'attribute' => 'or_uid'
            ],
            [
                'attribute' => 'or_project_id',
                'format' => 'projectName',
                'filter' => \common\models\Project::getList()
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'or_created_dt',
            ],
            [
                'label' => 'Status time',
                'value' => static function (Order $order) {
                    $data = $order->orderStatusLogs;
                    return $data ? Yii::$app->formatter->asDuration(time() - strtotime(end($data)->orsl_start_dt)) : ' - ';
                }
            ],

            [
                'label' => 'Status reason',
                'value' => static function (Order $order) {
                    $data = $order->orderStatusLogs;
                    return $data ? end($data)->orsl_description : ' - ';
                }
            ],
            'or_app_total',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'or_owner_user_id',
                'relation' => 'orOwnerUser',
                'placeholder' => 'Select User',
            ],
            [
                'label' => 'Types',
                'value' => static function (Order $order) {
                    $out = '';
                    foreach ($order->productQuotes as $quote) {
                        $out .= '&nbsp;&nbsp;&nbsp;' . Html::a(
                            '<i class="' . $quote->pqProduct->prType->pt_icon_class . '"></i>',
                            ['/product/product-quote-crud/view', 'id' => $quote->pq_id],
                            ['target' => '_blank', 'data-pjax' => 0]
                        );
                    }
                    return $out;
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'or_type_id',
                'value' => static function (Order $model) {
                    return $model->getOrderSourceType();
                },
                'filter' => OrderSourceType::LIST
            ],
            [
                'class' => OrderPayStatusColumn::class,
                'attribute' => 'or_pay_status_id'
            ],
        ]
    ])
?>

    <?php Pjax::end() ?>
</div>
