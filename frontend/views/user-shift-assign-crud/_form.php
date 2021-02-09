<?php

use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\shiftSchedule\entity\userShiftAssign\UserShiftAssign */
/* @var $form ActiveForm */
?>

<div class="user-shift-assign-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'usa_user_id')->widget(UserSelect2Widget::class) ?>

        <?= $form->field($model, 'usa_ssr_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
