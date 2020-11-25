<?php

use common\components\grid\BooleanColumn;
use common\components\grid\coupon\CouponStatusColumn;
use common\components\grid\coupon\CouponTypeColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
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
            'c_id',
            'c_code',
            'c_amount',
            'c_currency_code',
            'c_percent',
            ['class' => BooleanColumn::class, 'attribute' => 'c_reusable'],
            'c_reusable_count',
            ['class' => BooleanColumn::class, 'attribute' => 'c_public'],
            ['class' => CouponStatusColumn::class],
            ['class' => BooleanColumn::class, 'attribute' => 'c_disabled'],
            ['class' => CouponTypeColumn::class],
            ['class' => DateTimeColumn::class, 'attribute' => 'c_exp_date'],
            ['class' => DateTimeColumn::class, 'attribute' => 'c_start_date'],
            ['class' => DateTimeColumn::class, 'attribute' => 'c_used_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'c_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'c_updated_dt'],
            ['class' => UserSelect2Column::class, 'attribute' => 'c_created_user_id', 'relation' => 'createdUser'],
            ['class' => UserSelect2Column::class, 'attribute' => 'c_updated_user_id', 'relation' => 'updatedUser'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
