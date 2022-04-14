<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign */

$this->title = 'Update User Shift Assign: ' . $model->usa_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Shift Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->usa_user_id, 'url' => ['view', 'usa_user_id' => $model->usa_user_id, 'usa_sh_id' => $model->usa_sh_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-shift-assign-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
