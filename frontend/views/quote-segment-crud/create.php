<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegment */

$this->title = 'Create Quote Segment';
$this->params['breadcrumbs'][] = ['label' => 'Quote Segments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-segment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
