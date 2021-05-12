<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteLead\ProductQuoteLead */

$this->title = 'Update Product Quote Lead: ' . $model->pql_product_quote_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pql_product_quote_id, 'url' => ['view', 'pql_product_quote_id' => $model->pql_product_quote_id, 'pql_lead_id' => $model->pql_lead_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-lead-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
