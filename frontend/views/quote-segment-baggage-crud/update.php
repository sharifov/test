<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentBaggage */

$this->title = 'Update Quote Segment Baggage: ' . $model->qsb_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Segment Baggages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qsb_id, 'url' => ['view', 'qsb_id' => $model->qsb_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-segment-baggage-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
