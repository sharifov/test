<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteData\ProductQuoteData */

$this->title = 'Create Product Quote Data';
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
