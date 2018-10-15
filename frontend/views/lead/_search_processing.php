<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\search\LeadSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-search">

    <?php

$form = ActiveForm::begin([
        // 'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'class' => 'form-inline'
        ]
    ]);
    ?>
    <?=$form->field($model, 'filter')->dropDownList(['snooze' => 'Leads in snooze'], ['prompt' => '']);?>

	<div class="form-group">
    	<?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['class' => 'btn btn-primary']) ?>
	</div>
    <?php ActiveForm::end(); ?>

</div>
