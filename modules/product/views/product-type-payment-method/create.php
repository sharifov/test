<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\ProductTypePaymentMethod\ProductTypePaymentMethod */

$this->title = 'Create Product Type Payment Method';
$this->params['breadcrumbs'][] = ['label' => 'Product Type Payment Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-type-payment-method-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
