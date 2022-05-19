<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentStop */

$this->title = 'Update Quote Segment Stop: ' . $model->qss_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Segment Stops', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qss_id, 'url' => ['view', 'qss_id' => $model->qss_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-segment-stop-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
