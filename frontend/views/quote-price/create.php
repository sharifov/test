<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\QuotePrice */

$this->title = 'Create Quote Price';
$this->params['breadcrumbs'][] = ['label' => 'Quote Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-price-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
