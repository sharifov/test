<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderEmail\OrderEmail */

$this->title = 'Update Order Email: ' . $model->oe_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Emails', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->oe_id, 'url' => ['view', 'id' => $model->oe_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-email-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
