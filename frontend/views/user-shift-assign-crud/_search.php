<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\userShiftAssign\search\SearchUserShiftAssign */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="user-shift-assign-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'usa_user_id') ?>

    <?= $form->field($model, 'usa_sh_id') ?>

    <?= $form->field($model, 'usa_created_dt') ?>

    <?= $form->field($model, 'usa_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
