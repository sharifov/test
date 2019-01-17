<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Employee;

/* @var $this yii\web\View */
/* @var $model common\models\KpiHistory */
/* @var $form yii\widgets\ActiveForm */
/* @var $isAgent bool */
?>

<div class="kpi_history-form form-inline">
    <?php $form = ActiveForm::begin(); ?>

		<?php if(!$isAgent):?>
        <?= $form->field($model, 'kh_estimation_profit')->input('number',['readonly'=>true,'style'=>'width:100px;padding-right:0;']) ?>
        <?php endif;?>

        <?= $form->field($model, 'kh_base_amount')->input('number',['readonly'=>true,'style'=>'width:80px;padding-right:0;']) ?>

        <?= $form->field($model, 'kh_commission_percent')->input('number',['readonly'=>true,'style'=>'width:50px;padding-right:0;']) ?>

        <?= $form->field($model, 'kh_profit_bonus')->input('number',['readonly'=>true,'style'=>'width:80px;padding-right:0;']) ?>

		<?php if(!$isAgent):?>
        <?= $form->field($model, 'kh_manual_bonus')->input('number',['style'=>'width:80px;padding-right:0;']) ?>
		<?php endif;?>

		<?php if(!$isAgent && (empty($model->kh_agent_approved_dt) || empty($model->kh_super_approved_dt))):?>
        <?= Html::submitButton('Calculate salary', ['name' => 'calculate_salary','class' => 'btn btn-success']) ?>
		<?php endif;?>

		<?php if($isAgent  && empty($model->kh_agent_approved_dt)):?>
        <?= Html::submitButton('Approve salary', ['name' => 'approved_by_agent','class' => 'btn btn-primary']) ?>
		<?php elseif(!$isAgent && empty($model->kh_super_approved_dt)):?>
        <?= Html::submitButton('Approve salary', ['name' => 'approved_by_super','class' => 'btn btn-primary']) ?>
		<?php endif;?>
		<br/>
		<?php if(!$isAgent && (empty($model->kh_agent_approved_dt) || empty($model->kh_super_approved_dt))):?>
        <?= Html::submitButton('Recalculate Kpi', ['name' => 'recalculate_kpi','class' => 'btn btn-info',
            'data' => [
                'confirm' => 'Are you sure you want to recalculate salary for month?',
            ],]) ?>
		<?php endif;?>
    <?php ActiveForm::end(); ?>

</div>
