<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\coupon\entity\couponCase\search\CouponCaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupon Cases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-case-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Coupon Case', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cc_coupon_id',
            'cc_case_id',
            'cc_sale_id',
            'cc_created_dt',
            'cc_created_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
