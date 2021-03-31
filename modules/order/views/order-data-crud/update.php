<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderData\OrderData */

$this->title = 'Update Order Data: ' . $model->od_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->od_id, 'url' => ['view', 'id' => $model->od_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
