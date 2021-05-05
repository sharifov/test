<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderContact\OrderContact */

$this->title = 'Create Order Contact';
$this->params['breadcrumbs'][] = ['label' => 'Order Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-contact-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
