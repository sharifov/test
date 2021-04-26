<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteOrigin\ProductQuoteOrigin */

$this->title = 'Update Product Quote Origin: ' . $model->pqa_product_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Origins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pqa_product_id, 'url' => ['view', 'pqa_product_id' => $model->pqa_product_id, 'pqa_quote_id' => $model->pqa_quote_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-origin-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
