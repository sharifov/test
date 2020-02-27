<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderUserProfit\OrderUserProfit */

$this->title = 'Update Order User Profit: ' . $model->oup_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Order User Profits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->oup_order_id, 'url' => ['view', 'oup_order_id' => $model->oup_order_id, 'oup_user_id' => $model->oup_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-user-profit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
