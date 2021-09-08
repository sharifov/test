<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteData\ProductQuoteData */

$this->title = 'Update Product Quote Data: ' . $model->pqd_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pqd_id, 'url' => ['view', 'id' => $model->pqd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-quote-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
