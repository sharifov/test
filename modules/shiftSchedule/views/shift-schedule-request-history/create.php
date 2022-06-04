<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\ShiftScheduleRequestHistory */

$this->title = 'Create Shift Schedule Request History';
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Request Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-request-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
