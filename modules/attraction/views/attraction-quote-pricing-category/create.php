<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuotePricingCategory */

$this->title = 'Create Attraction Quote Pricing Category';
$this->params['breadcrumbs'][] = ['label' => 'Attraction Quote Pricing Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attraction-quote-pricing-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
