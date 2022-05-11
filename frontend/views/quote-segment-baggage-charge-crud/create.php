<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentBaggageCharge */

$this->title = 'Create Quote Segment Baggage Charge';
$this->params['breadcrumbs'][] = ['label' => 'Quote Segment Baggage Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-segment-baggage-charge-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
