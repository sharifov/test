<?php

use src\model\leadUserData\entity\LeadUserDataDictionary;
use src\widgets\DateTimePicker;
use src\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserData\entity\LeadUserData */
/* @var $form ActiveForm */
?>

<div class="lead-user-data-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'lud_type_id')->dropDownList(LeadUserDataDictionary::TYPE_LIST) ?>

        <?= $form->field($model, 'lud_lead_id')->textInput() ?>

        <?= $form->field($model, 'lud_user_id')->widget(UserSelect2Widget::class, [
            'data' => $model->lud_user_id ? [
                $model->lud_user_id => $model->ludUser->username
            ] : [],
        ]) ?>

        <?= $form->field($model, 'lud_created_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
