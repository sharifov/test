<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund */

$this->title = 'Create Product Quote Option Refund';
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Option Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-option-refund-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
