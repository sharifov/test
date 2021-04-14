<?php

use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\order\src\entities\orderEmail\search\OrderEmailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Emails';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-email-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order Email', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-order-email']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'oe_id',
            'oe_order_id:orderId',
            'oe_email_id:email',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'oe_created_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
