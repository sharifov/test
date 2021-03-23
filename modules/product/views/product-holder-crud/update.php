<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productHolder\ProductHolder */

$this->title = 'Update Product Holder: ' . $model->ph_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Holders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ph_id, 'url' => ['view', 'id' => $model->ph_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-holder-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
