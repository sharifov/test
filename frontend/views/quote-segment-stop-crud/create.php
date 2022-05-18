<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentStop */

$this->title = 'Create Quote Segment Stop';
$this->params['breadcrumbs'][] = ['label' => 'Quote Segment Stops', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-segment-stop-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
