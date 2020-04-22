<?php

use sales\widgets\UserSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Notifications */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notifications-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
    <?php /*= $form->field($model, 'n_user_id')->dropDownList(\common\models\Employee::getList())*/ ?>

    <?= $form->field($model, 'n_user_id')->widget(UserSelect2Widget::class, [
        'data' => $model->n_user_id ? [
            $model->n_user_id => $model->nUser->username
        ] : [],
    ]) ?>

    <?= $form->field($model, 'n_type_id')->dropDownList(\common\models\Notifications::getTypeList()) ?>

    <?= $form->field($model, 'n_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'n_message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'n_new')->checkbox() ?>

    <?= $form->field($model, 'n_deleted')->checkbox() ?>

    <?= $form->field($model, 'n_popup')->checkbox() ?>

    <?= $form->field($model, 'n_popup_show')->checkbox() ?>

    <?php //= $form->field($model, 'n_read_dt')->textInput() ?>

    <?php //= $form->field($model, 'n_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('notifications', 'Create') : Yii::t('notifications', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
