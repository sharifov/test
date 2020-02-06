<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderStatusLog\OrderStatusLog */

$this->title = 'Update Order Status Log: ' . $model->orsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->orsl_id, 'url' => ['view', 'id' => $model->orsl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-status-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
