<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit */

$this->title = 'Update Order Tips User Profit: ' . $model->otup_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Tips User Profits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->otup_order_id, 'url' => ['view', 'otup_order_id' => $model->otup_order_id, 'otup_user_id' => $model->otup_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-tips-user-profit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
