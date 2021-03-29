<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\order\src\entities\orderRequest\search\OrderRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order Request', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-order-request']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'orr_id',
            'orr_request_data_json',
            'orr_response_data_json',
            'orr_source_type_id',
            'orr_response_type_id',
            //'orr_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
