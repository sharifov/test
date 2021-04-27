<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteOrigin\ProductQuoteOrigin */

$this->title = 'Create Product Quote Origin';
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Origins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-origin-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
