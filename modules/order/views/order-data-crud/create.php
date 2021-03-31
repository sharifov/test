<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderData\OrderData */

$this->title = 'Create Order Data';
$this->params['breadcrumbs'][] = ['label' => 'Order Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
