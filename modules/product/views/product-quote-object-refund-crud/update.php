<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund */

$this->title = 'Update Product Quote Object Refund: ' . $model->pqor_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Object Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pqor_id, 'url' => ['view', 'id' => $model->pqor_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-object-refund-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
