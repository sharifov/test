<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign */

$this->title = 'Create User Shift Assign';
$this->params['breadcrumbs'][] = ['label' => 'User Shift Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-shift-assign-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
