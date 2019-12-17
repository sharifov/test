<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductOption */

$this->title = 'Update Product Option: ' . $model->po_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->po_id, 'url' => ['view', 'id' => $model->po_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-option-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
