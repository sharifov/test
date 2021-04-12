<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderData\OrderData */

$this->title = $model->od_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->od_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->od_id], [
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
                'od_id',
                'od_order_id:orderId',
                'od_display_uid',
                'od_source_id:source',
                'od_created_by',
                'od_updated_by',
                'od_created_dt:byUserDateTime',
                'od_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
