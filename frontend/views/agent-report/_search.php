<?php

use common\models\Employee;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AgentActivitySearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $action string */

/** @var Employee $user */
$user = Yii::$app->user->identity;

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
    		<?= \yii\bootstrap\Html::label('Date Range','date_range')?>
			<?=  \kartik\daterange\DateRangePicker::widget([
                    'model'=> $model,
                    'attribute' => 'date_range',
                    'useWithAddon'=>true,
                    'presetDropdown'=>true,
                    'hideInput'=>true,
                    'convertFormat'=>true,
                    'startAttribute' => 'date_from',
                    'endAttribute' => 'date_to',
                    'pluginOptions'=>[
                        'timePicker'=> true,
                        'timePickerIncrement'=>1,
                        'timePicker24Hour'=> true,
                        'locale'=>[
                                'format'=>'Y-m-d H:i'
                        ]
                    ]
                ]);
                ?>
    	</div>
		<div class="col-md-4">
		<?php if($user->isAdmin()) {
                $groups = \common\models\UserGroup::getList();
            }

            if($user->isSupervision()) {
                $groups = $user->getUserGroupList();                            //exit;
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
