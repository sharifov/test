<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="calls-search">
    <?php $form = ActiveForm::begin([
        'action' => ['client-chat/report'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'timeRange', [
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'presetDropdown' => false,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            //'minDate' => date("Y-m-d 00:00", strtotime("- 61 days")),
                            //'maxDate' => date("Y-m-d 23:59"),
                            'timePicker' => true,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'Y-m-d H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Chat Created Date / Message Send Date');
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
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['client-chat/report'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>