<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderContact\OrderContact */

$this->title = 'Update Order Contact: ' . $model->oc_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->oc_id, 'url' => ['view', 'id' => $model->oc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="order-contact-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
