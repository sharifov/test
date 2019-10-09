<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="calls-search">

    <?php $form = ActiveForm::begin([
        'action' => ['report/calls-report'],
        'method' => 'get',
        /*'options' => [
            'data-pjax' => 1
        ],*/
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'createTimeRange', [
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'presetDropdown' => false,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'timePicker' => true,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Calls Updated Date');
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['name' => 'search', 'class' => 'btn btn-primary']) ?>
                <?= Html::submitButton('<i class="fa fa-close"></i> Reset form', ['name' => 'reset', 'class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>