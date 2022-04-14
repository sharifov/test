<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shiftCategory\ShiftCategory */

$this->title = 'Update Shift Category: ' . $model->sc_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sc_id, 'url' => ['view', 'sc_id' => $model->sc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shift-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
