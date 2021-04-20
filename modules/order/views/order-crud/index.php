<?php

use modules\lead\src\grid\columns\LeadColumn;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\search\OrderCrudSearch;
use modules\order\src\grid\columns\OrderPayStatusColumn;
use modules\order\src\grid\columns\OrderStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\order\src\entities\order\Order;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel OrderCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'or_id',
            'or_gid',
            'or_uid',
            'or_fare_id',
            'or_name',
            /*[
                'class' => LeadColumn::class,
                'attribute' => 'or_lead_id',
                'relation' => 'orLead',
            ],*/
            [
                'label' => 'Leads',
                'value' => static function (Order $order) {
                    $data = [];
                    foreach ($order->leadOrder as $lead) {
                        $data[] = Yii::$app->formatter->format($lead->lead, 'lead');
                    }
                    return !empty($data) ? implode('</br>', $data) : ' - ';
                },
                'format' => 'raw'
            ],
            [
                'label' => 'Cases',
                'value' => static function (Order $order) {
                    $data = [];
                    foreach ($order->caseOrder as $case) {
                        $data[] = Yii::$app->formatter->format($case->cases, 'case');
                    }
                    return !empty($data) ? implode('</br>', $data) : ' - ';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'or_project_id',
                'label' => 'Project',
                'format' => 'projectName',
                'filter' => \common\models\Project::getList()
            ],
            'or_description:ntext',
            [
                'class' => OrderStatusColumn::class,
                'attribute' => 'or_status_id'
            ],
            [
                'class' => OrderPayStatusColumn::class,
                'attribute' => 'or_pay_status_id'
            ],
            'or_app_total',
            'or_app_markup',
            'or_agent_markup',
            'or_client_total',
            'or_client_currency',
            'or_client_currency_rate',
            'or_profit_amount',
            [
                'attribute' => 'or_type_id',
                'value' => static function (OrderCrudSearch $model) {
                    return $model->getOrderSourceType();
                },
                'filter' => OrderSourceType::LIST
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'or_owner_user_id',
                'relation' => 'orOwnerUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'or_created_user_id',
                'relation' => 'orCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'or_updated_user_id',
                'relation' => 'orUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'or_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'or_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>
