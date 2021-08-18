<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteChange\ProductQuoteChange */

$this->title = 'Update Product Quote Change: ' . $model->pqc_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pqc_id, 'url' => ['view', 'id' => $model->pqc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-change-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
