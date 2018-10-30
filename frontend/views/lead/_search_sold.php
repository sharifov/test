<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-search">
    <?php $form = ActiveForm::begin([
        //'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'class' => 'form-inline',
        ],
    ]); ?>
    <?= $form->field($model, 'sold_date_from',['template'=>'{label}{input}'])->widget(
            \dosamigos\datepicker\DatePicker::class, [
            'inline' => false,
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'dd-M-yyyy',
                'todayBtn' => true
            ]
        ]);?>
    <?= $form->field($model, 'sold_date_to',['template'=>'{label}{input}'])->widget(
            \dosamigos\datepicker\DatePicker::class, [
            'inline' => false,
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'dd-M-yyyy',
                'todayBtn' => true
            ]
        ]);?>

	<div class="form-group" style="margin-bottom: 16px;">
    	<?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['class' => 'btn btn-primary']) ?>
    	<?= Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-warning']) ?>
	</div>
    <?php ActiveForm::end(); ?>

</div>
