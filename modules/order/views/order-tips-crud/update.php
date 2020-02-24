<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderTips\OrderTips */

$this->title = 'Update Order Tips: ' . $model->ot_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Tips', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ot_id, 'url' => ['view', 'id' => $model->ot_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-tips-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
