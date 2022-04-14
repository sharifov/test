<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule */

$this->title = 'Create User Shift Schedule';
$this->params['breadcrumbs'][] = ['label' => 'User Shift Schedules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-shift-schedule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
