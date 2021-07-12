<?php

use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\coupon\entity\couponClient\CouponClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupon Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-client-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Coupon Client', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-coupon-client']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'cuc_coupon_id:coupon',
            'cuc_client_id:client',
            ['class' => DateTimeColumn::class, 'attribute' => 'cuc_created_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
