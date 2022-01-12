<?php

use src\model\coupon\entity\couponSend\CouponSend;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponSend\CouponSend */

$this->title = $model->cus_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon Sends', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="coupon-send-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cus_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cus_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'cus_id',
                'cus_coupon_id:coupon',
                'cus_user_id:userName',
                [
                    'attribute' => 'cus_type_id',
                    'value' => static function (CouponSend $model) {
                        return CouponSend::getTypeName($model->cus_type_id);
                    },
                    'format' => 'raw',
                ],
                'cus_send_to',
                'cus_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
