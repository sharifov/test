<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LeadFlightSegment */

$this->title = 'Create Lead Flight Segment';
$this->params['breadcrumbs'][] = ['label' => 'Lead Flight Segments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-flight-segment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
