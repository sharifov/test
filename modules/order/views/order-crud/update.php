<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\order\src\entities\order\Order */

$this->title = 'Update Order: ' . $model->or_id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->or_id, 'url' => ['view', 'id' => $model->or_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
