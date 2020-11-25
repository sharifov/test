<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\model\coupon\entity\couponCase\CouponCase;
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
            'cc_coupon_id',
            [
                 'attribute' => 'cc_case_id',
                 'value' => static function (CouponCase $model) {
                    return $model->case ?: null;
                 },
                 'format' => 'case'
            ],
            'cc_sale_id',
            ['class' => DateTimeColumn::class, 'attribute' => 'cc_created_dt'],
            ['class' => UserSelect2Column::class, 'attribute' => 'cc_created_user_id', 'relation' => 'createdUser'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
