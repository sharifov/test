<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderStatusLog\OrderStatusLog */

$this->title = 'Create Order Status Log';
$this->params['breadcrumbs'][] = ['label' => 'Order Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-status-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
