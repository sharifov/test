<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation */

$this->title = 'Create Product Quote Change Relation';
$this->params['breadcrumbs'][] = ['label' => 'Product Quote Change Relations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-quote-change-relation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
