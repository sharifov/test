<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use src\widgets\UserSelect2Widget;
use src\model\leadUserRating\entity\LeadUserRating;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserRating\entity\LeadUserRating */
/* @var $form ActiveForm */
?>

<div class="lead-user-rating-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'lur_lead_id')->textInput() ?>


        <?= $form->field($model, 'lur_rating')->dropDownList(LeadUserRating::getRatingList()) ?>

        <?= $form->field($model, 'lur_user_id')->widget(UserSelect2Widget::class, [
            'data' => $model->lur_user_id ? [
                $model->lur_user_id => $model->user->username
            ] : [],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>