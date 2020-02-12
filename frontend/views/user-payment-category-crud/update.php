<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\paymentCategory\UserPaymentCategory */

$this->title = 'Update User Payment Category: ' . $model->upc_id;
$this->params['breadcrumbs'][] = ['label' => 'User Payment Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->upc_id, 'url' => ['view', 'id' => $model->upc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-payment-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
