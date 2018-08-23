<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuotePrice */

$this->title = 'Update Quote Price: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-price-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
