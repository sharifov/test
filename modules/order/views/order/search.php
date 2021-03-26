<?php

use frontend\widgets\multipleUpdate\button\MultipleUpdateButtonWidget;
use modules\lead\src\grid\columns\LeadColumn;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\search\OrderSearch;
use modules\order\src\grid\columns\OrderPayStatusColumn;
use modules\order\src\grid\columns\OrderStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\access\ListsAccess;
use sales\auth\Auth;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-search">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card multiple-update-summary" style="margin-bottom: 10px; display: none">
        <div class="card-header">
            <span class="pull-right clickable close-icon"><i class="fa fa-times"></i></span>
            Processing result log:
        </div>
        <div class="card-body"></div>
    </div>

    <?php
    $js = <<<JS
    $('.close-icon').on('click', function(){    
        $('.multiple-update-summary').slideUp();
    })
JS;
    $this->registerJs($js);
    ?>

    <?php Pjax::begin(['id' => 'order-pjax-list', 'timeout' => 7000, 'enablePushState' => true]); ?>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: <?=(Yii::$app->request->isPjax || Yii::$app->request->get('OrderSearch')) ? 'block' : 'none'?>">
            <?= $this->render('_search_block', [
                'model' => $searchModel,
                'lists' => new ListsAccess(Auth::id())
            ]) ?>
        </div>
    </div>

    <?php $gridId = 'orders-grid-id'; ?>

    <?= MultipleUpdateButtonWidget::widget([
        'modalId' => 'modal-df',
        'showUrl' => Url::to(['/order/order-multiple-update/show']),
        'gridId' => $gridId,
        'buttonClass' => 'multiple-update-btn',
        'buttonClassAdditional' => 'btn btn-info btn-warning',
        'buttonText' => 'Multiple update',
    ]) ?>

    <?= GridView::widget([
        'id' => $gridId,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => '\kartik\grid\CheckboxColumn',
                'name' => 'OrderMultipleForm[order_list]',
                'pageSummary' => true,
                'rowSelectedClass' => \kartik\grid\GridView::TYPE_INFO,
            ],
            'or_id',
            'or_gid',
            'or_uid',
            [
                'class' => LeadColumn::class,
                'attribute' => 'or_lead_id',
                'relation' => 'orLead',
            ],
            [
                'class' => OrderStatusColumn::class,
                'attribute' => 'or_status_id'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'or_created_dt',
            ],
            'or_app_total',
            'or_client_total',
            'or_profit_amount',
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
                'class' => OrderPayStatusColumn::class,
                'attribute' => 'or_pay_status_id'
            ],
            [
                'attribute' => 'or_name',
                'visible' => $searchModel->show_fields && in_array('or_name', $searchModel->show_fields, true),
            ],
            [
                'attribute' => 'or_description',
                'visible' => $searchModel->show_fields && in_array('or_description', $searchModel->show_fields, true),
            ],
            [
                'attribute' => 'or_app_total',
                'visible' => $searchModel->show_fields && in_array('or_app_total', $searchModel->show_fields, true),
            ],
            [
                'attribute' => 'or_app_markup',
                'visible' => $searchModel->show_fields && in_array('or_app_markup', $searchModel->show_fields, true),
            ],
            [
                'attribute' => 'or_agent_markup',
                'visible' => $searchModel->show_fields && in_array('or_agent_markup', $searchModel->show_fields, true),
            ],
            [
                'attribute' => 'or_client_total',
                'visible' => $searchModel->show_fields && in_array('or_client_total', $searchModel->show_fields, true),
            ],
            [
                'attribute' => 'or_client_currency',
                'visible' => $searchModel->show_fields && in_array('or_client_currency', $searchModel->show_fields, true),
            ],
            [
                'attribute' => 'or_client_currency_rate',
                'visible' => $searchModel->show_fields && in_array('or_client_currency_rate', $searchModel->show_fields, true),
            ],
            [
                'attribute' => 'or_profit_amount',
                'visible' => $searchModel->show_fields && in_array('or_profit_amount', $searchModel->show_fields, true),
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'or_created_user_id',
                'relation' => 'orCreatedUser',
                'placeholder' => 'Select User',
                'visible' => $searchModel->show_fields && in_array('or_created_user_id', $searchModel->show_fields, true),
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'or_updated_user_id',
                'relation' => 'orUpdatedUser',
                'placeholder' => 'Select User',
                'visible' => $searchModel->show_fields && in_array('or_updated_user_id', $searchModel->show_fields, true),
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'or_updated_dt',
                'visible' => $searchModel->show_fields && in_array('or_updated_dt', $searchModel->show_fields, true),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => static function ($url, Order $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['/order/order/view', 'gid' => $model->or_gid], [
                            'target' => '_blank',
                            'data-pjax' => 0,
                            'title' => 'View',
                        ]);
                    },
                ],
            ]
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
