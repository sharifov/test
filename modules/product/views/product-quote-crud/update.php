<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productQuote\ProductQuote */

$this->title = 'Update Product Quote: ' . $model->pq_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pq_id, 'url' => ['view', 'id' => $model->pq_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
