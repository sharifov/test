<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteRelation\ProductQuoteRelation */

$this->title = 'Update Product Quote Relation: ' . $model->pqr_parent_pq_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pqr_parent_pq_id, 'url' => ['view', 'pqr_parent_pq_id' => $model->pqr_parent_pq_id, 'pqr_related_pq_id' => $model->pqr_related_pq_id, 'pqr_type_id' => $model->pqr_type_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-relation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
