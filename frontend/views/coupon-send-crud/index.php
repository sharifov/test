<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use src\helpers\email\MaskEmailHelper;
use src\model\coupon\entity\couponSend\CouponSend;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\coupon\entity\couponSend\CouponSendSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupon Sends';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-send-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Coupon Send', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-coupon-send', 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [

            'cus_id',
            'cus_coupon_id:coupon',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cus_user_id',
                'relation' => 'cusUser',
                'placeholder' => '',
            ],
            [
                'attribute' => 'cus_type_id',
                'value' => static function (CouponSend $model) {
                    return CouponSend::getTypeName($model->cus_type_id);
                },
                'format' => 'raw',
                'filter' => CouponSend::TYPE_LIST
            ],
            [
                'attribute' => 'cus_send_to',
                'value' => static function ($model) {
                    return MaskEmailHelper::masking($model->cus_send_to);
                }
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'cus_created_dt'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
