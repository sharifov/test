<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CaseEventLog */

$this->title = 'Create Case Event Log';
$this->params['breadcrumbs'][] = ['label' => 'Case Event Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-event-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
