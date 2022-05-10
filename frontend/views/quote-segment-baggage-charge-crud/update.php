<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentBaggageCharge */

$this->title = 'Update Quote Segment Baggage Charge: ' . $model->qsbc_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Segment Baggage Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qsbc_id, 'url' => ['view', 'qsbc_id' => $model->qsbc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-segment-baggage-charge-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
