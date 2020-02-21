<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderUserProfit\OrderUserProfit */

$this->title = 'Create Order User Profit';
$this->params['breadcrumbs'][] = ['label' => 'Order User Profits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-user-profit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
