<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteRefund\ProductQuoteRefund */

$this->title = 'Update Product Quote Refund: ' . $model->pqr_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pqr_id, 'url' => ['view', 'id' => $model->pqr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-refund-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
