<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuotePricingCategory */

$this->title = 'Update Attraction Quote Pricing Category: ' . $model->atqpc_id;
$this->params['breadcrumbs'][] = ['label' => 'Attraction Quote Pricing Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->atqpc_id, 'url' => ['view', 'id' => $model->atqpc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="attraction-quote-pricing-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
