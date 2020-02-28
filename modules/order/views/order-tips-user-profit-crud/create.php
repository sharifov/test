<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit */

$this->title = 'Create Order Tips User Profit';
$this->params['breadcrumbs'][] = ['label' => 'Order Tips User Profits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-tips-user-profit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
