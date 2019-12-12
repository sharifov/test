<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductQuote */

$this->title = 'Create Product Quote';
$this->params['breadcrumbs'][] = ['label' => 'Product Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
