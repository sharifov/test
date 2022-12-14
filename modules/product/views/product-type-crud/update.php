<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productType\ProductType */

$this->title = 'Update Product Type: ' . $model->pt_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pt_id, 'url' => ['view', 'id' => $model->pt_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
