<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteChange\ProductQuoteChange */

$this->title = 'Create Product Quote Change';
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-change-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
