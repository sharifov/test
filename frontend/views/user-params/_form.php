<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Employee;

/* @var $this yii\web\View */
/* @var $model common\models\UserParams */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-params-form">

    <?php $form = ActiveForm::begin(); ?>
	<div class="col-md-3">
        <?= $form->field($model, 'up_user_id')->dropDownList(\common\models\Employee::getList()) ?>

        <?= $form->field($model, 'up_commission_percent')->input('number') ?>

        <?= $form->field($model, 'up_base_amount')->input('number') ?>

        <?= $form->field($model, 'up_bonus_active')->checkbox() ?>

        <?= $form->field($model, 'up_work_start_tm')->widget(
                            \kartik\time\TimePicker::class, [
                                'pluginOptions' => [
                                    'showSeconds' => false,
                                    'showMeridian' => false,
                            ]])?>

		<?= $form->field($model, 'up_work_minutes')->input('number', ['step' => 10, 'min' => 0])?>

        <?= $form->field($model, 'up_inbox_show_limit_leads')->input('number', ['step' => 1, 'min' => 0, 'max' => 500])?>
        <?= $form->field($model, 'up_default_take_limit_leads')->input('number', ['step' => 1, 'min' => 0, 'max' => 100])?>
        <?= $form->field($model, 'up_min_percent_for_take_leads')->input('number', ['step' => 1, 'min' => 0, 'max' => 100])?>

		<?= $form->field($model, 'up_timezone')->dropDownList(Employee::timezoneList(),['prompt' =>'-'])?>

        <?= $form->field($model, 'up_call_expert_limit')->input('number', ['step' => 1, 'min' => -1, 'max' => 1000])?>


        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
