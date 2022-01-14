<?php

use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\coupon\entity\couponUse\CouponUseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupon Uses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-use-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Coupon Use', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-coupon-use', 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'cu_id',
            'cu_coupon_id:coupon',
            'cu_ip',
            'cu_user_agent',
            ['class' => DateTimeColumn::class, 'attribute' => 'cu_created_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
