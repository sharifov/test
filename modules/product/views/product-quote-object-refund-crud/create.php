<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund */

$this->title = 'Create Product Quote Object Refund';
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Object Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-object-refund-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
