<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\ProductTypePaymentMethod\ProductTypePaymentMethod */

$this->title = 'Update Product Type Payment Method: ' . $model->ptpm_produt_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Type Payment Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ptpm_produt_type_id, 'url' => ['view', 'ptpm_produt_type_id' => $model->ptpm_produt_type_id, 'ptpm_payment_method_id' => $model->ptpm_payment_method_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-type-payment-method-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
