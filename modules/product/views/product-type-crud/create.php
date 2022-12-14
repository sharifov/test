<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productType\ProductType */

$this->title = 'Create Product Type';
$this->params['breadcrumbs'][] = ['label' => 'Product Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
