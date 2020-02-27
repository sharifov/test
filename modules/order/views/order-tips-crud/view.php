<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderTips\OrderTips */

$this->title = 'Order ' . $model->ot_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Tips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-tips-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ot_order_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ot_order_id], [
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
            'otOrder:order',
            'ot_client_amount',
            'ot_amount',
            'ot_user_profit',
            'ot_user_profit_percent:percentInteger',
            'ot_description:ntext',
            'ot_created_dt:ByUserDateTime',
            'ot_updated_dt:ByUserDateTime',
        ],
    ]) ?>

</div>
