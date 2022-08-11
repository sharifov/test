<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentBaggage */

$this->title = 'Create Quote Segment Baggage';
$this->params['breadcrumbs'][] = ['label' => 'Quote Segment Baggages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-segment-baggage-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
