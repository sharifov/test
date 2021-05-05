<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteOrigin\ProductQuoteOrigin */

$this->title = 'Update Product Quote Origin: ' . $model->pqo_product_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Origins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pqo_product_id, 'url' => ['view', 'pqo_product_id' => $model->pqo_product_id, 'pqo_quote_id' => $model->pqo_quote_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-origin-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
