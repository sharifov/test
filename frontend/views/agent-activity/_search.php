<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AgentActivitySearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $action string */
?>

<div class="activity-search">

    <?php $form = ActiveForm::begin([
        'action' => [$action],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-4">
		<?= $form->field($model, 'date_from')->widget(
                \dosamigos\datepicker\DatePicker::class, [
                'inline' => false,
                //'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy',
                    'todayBtn' => true
                ]
            ]);?>
		</div>
		<div class="col-md-4">
		 <?= $form->field($model, 'date_to')->widget(
                \dosamigos\datepicker\DatePicker::class, [
                'inline' => false,
                //'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy',
                    'todayBtn' => true
                ]
            ]);?>
		</div>
		<div class="col-md-4">
		<?php if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
                $groups = \common\models\UserGroup::getList();
            }

            if(Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)) {
                $groups = Yii::$app->user->identity->getUserGroupList();                            //exit;
            }?>
		 <?= $form->field($model, 'user_groups')->widget(\kartik\select2\Select2::class, [
		      'data' => $groups,
                'size' => \kartik\select2\Select2::SMALL,
                'options' => ['placeholder' => 'Select user groups', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ]);?>
		</div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['name' => 'search','class' => 'btn btn-primary']) ?>
                <?= Html::submitButton('<i class="fa fa-close"></i> Reset form', ['name' => 'reset','class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
