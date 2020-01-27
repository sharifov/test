<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productOption\ProductOption */

$this->title = 'Create Product Option';
$this->params['breadcrumbs'][] = ['label' => 'Product Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-option-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
