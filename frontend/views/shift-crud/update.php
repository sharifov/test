<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shift\Shift */

$this->title = 'Update Shift: ' . $model->sh_id;
$this->params['breadcrumbs'][] = ['label' => 'Shifts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sh_id, 'url' => ['view', 'id' => $model->sh_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shift-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
