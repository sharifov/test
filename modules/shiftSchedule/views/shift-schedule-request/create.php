<?php

/**
 * @var View $this
 * @var ShiftScheduleRequest $model
 */

use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('app', 'Create Shift Schedule Request');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shift Schedule Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
