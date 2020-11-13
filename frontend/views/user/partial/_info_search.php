<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var $datePickerModel \yii\base\DynamicModel
 */
?>

<div class="profile_title">
    <div class="col-md-7">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'options' => [
                'data-pjax' => true
            ],
        ]); ?>

        <div class="row">
            <div class="col-md-8">
                <?php echo $form->field($datePickerModel, 'dateRange', [
                    //'addon'=>['prepend'=>['content'=>'<i class="fa fa-calendar"></i>']],
                    'options' => ['class' => 'form-group']
                ])->widget(\kartik\daterange\DateRangePicker::class, [
                    'useWithAddon' => true,
                    'presetDropdown' => true,
                    'hideInput' => true,
                    'convertFormat' => true,
                    'startAttribute' => 'dateStart',
                    'endAttribute' => 'dateEnd',
                    'startInputOptions' => ['value' => $datePickerModel->dateStart],
                    'endInputOptions' => ['value' => $datePickerModel->dateEnd],
                    'pluginOptions' => [
                        'timePicker' => true,
                        'timePickerIncrement' => 1,
                        'timePicker24Hour' => true,
                        'locale' => [
                            'format' => 'Y-m-d H:i:s',
                            'separator' => ' - '
                        ],
                        'opens' => 'right'
                    ]
                ])->label(false);
                ?>
            </div>
            <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>