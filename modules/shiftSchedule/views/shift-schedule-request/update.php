<?php

/**
 * @var View $this
 * @var ShiftScheduleRequest $model
 */

use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::t('app', 'Update Shift Schedule Request: {name}', [
    'name' => $model->srh_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shift Schedule Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->srh_id, 'url' => ['view', 'srh_id' => $model->srh_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="shift-schedule-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
