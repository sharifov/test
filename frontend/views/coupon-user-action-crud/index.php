<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\model\coupon\entity\couponUserAction\CouponUserAction;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\coupon\entity\couponUserAction\CouponUserActionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupon User Actions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-user-action-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Coupon User Action', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-coupon-user-action']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'cuu_id',
            'cuu_coupon_id:coupon',
            [
                'attribute' => 'cuu_action_id',
                'format' => 'raw',
                'value' => static function (CouponUserAction $model) {
                    return CouponUserAction::getActionName($model->cuu_action_id);
                },
                'filter' => CouponUserAction::ACTION_LIST
            ],
            'cuu_api_user_id',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cuu_user_id',
                'relation' => 'cuuUser',
                'placeholder' => '',
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'cuu_created_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
