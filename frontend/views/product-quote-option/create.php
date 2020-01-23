<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductQuoteOption */

$this->title = 'Create Product Quote Option';
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-option-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
