<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation */

$this->title = 'Update Product Quote Change Relation: ' . $model->pqcr_pqc_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Change Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pqcr_pqc_id, 'url' => ['view', 'pqcr_pqc_id' => $model->pqcr_pqc_id, 'pqcr_pq_id' => $model->pqcr_pq_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-change-relation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
