<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponCase\CouponCase */

$this->title = $model->cc_coupon_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="coupon-case-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'cc_coupon_id' => $model->cc_coupon_id, 'cc_case_id' => $model->cc_case_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'cc_coupon_id' => $model->cc_coupon_id, 'cc_case_id' => $model->cc_case_id], [
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
                'cc_coupon_id',
                'case:case',
                'cc_sale_id',
                'cc_created_dt:byUserDateTime',
                'createdUser:userName',
            ],
        ]) ?>

    </div>

</div>
