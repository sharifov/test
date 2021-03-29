<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderRequest\OrderRequest */

$this->title = 'Create Order Request';
$this->params['breadcrumbs'][] = ['label' => 'Order Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
