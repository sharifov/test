<?php

use common\components\grid\coupon\CouponStatusColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\coupon\entity\coupon\search\CouponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Coupon', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'c_id',
            'c_code',
            'c_amount',
            'c_currency_code',
            'c_percent',
            //'c_exp_date',
            //'c_start_date',
            //'c_reusable',
            //'c_reusable_count',
            //'c_public',
            ['class' => CouponStatusColumn::class, 'attribute' => 'c_status_id'],
            //'c_used_dt',
            //'c_disabled',
            //'c_type_id',
            //'c_created_dt',
            //'c_updated_dt',
            //'c_created_user_id',
            //'c_updated_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
