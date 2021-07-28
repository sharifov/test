<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund */

$this->title = 'Update Product Quote Option Refund: ' . $model->pqor_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Option Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pqor_id, 'url' => ['view', 'id' => $model->pqor_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-option-refund-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
