<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shiftCategory\ShiftCategory */

$this->title = 'Create Shift Category';
$this->params['breadcrumbs'][] = ['label' => 'Shift Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
