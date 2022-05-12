<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegment */

$this->title = 'Update Quote Segment: ' . $model->qs_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Segments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qs_id, 'url' => ['view', 'qs_id' => $model->qs_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-segment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
