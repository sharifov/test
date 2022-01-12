<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadRedial\entity\CallRedialUserAccess */
/* @var $form ActiveForm */
?>

<div class="call-redial-user-access-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'crua_lead_id')->textInput() ?>

        <?= $form->field($model, 'crua_user_id')->textInput() ?>

        <?= $form->field($model, 'crua_created_dt')->textInput() ?>

        <?= $form->field($model, 'crua_updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
