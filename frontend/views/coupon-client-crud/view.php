<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponClient\CouponClient */

$this->title = $model->cuc_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="coupon-client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cuc_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cuc_id], [
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
                'cuc_id',
                'cuc_coupon_id',
                'cuc_client_id:client',
                'cuc_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
