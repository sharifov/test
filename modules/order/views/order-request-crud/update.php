<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderRequest\OrderRequest */

$this->title = 'Update Order Request: ' . $model->orr_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->orr_id, 'url' => ['view', 'id' => $model->orr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
