<?php

use kartik\select2\Select2;
use sales\access\ListsAccess;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Lead;
use frontend\extensions\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $action string */
/** @var \sales\access\ListsAccess $lists */

?>

<div class="lead-search">

    <?php $form = ActiveForm::begin([
        'action' => [$action],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
        'id' => 'lead_form',
    ]); ?>

    <div class="row">
        <div class="col-md-3">

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'id')->input('number', ['min' => 1]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'uid') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'bo_flight_id')->input('number', ['min' => 0]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'gid')->textInput(['maxlength' => true]) ?>
                </div>
                <?php /*<div class="col-md-3">
                    <?= $form->field($model, 'discount_id')->input('number', ['min' => 1]) ?>
                </div>*/ ?>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'client_id')->input('number', ['min' => 1]) ?>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'client_name') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'client_email')//->input('email')?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'client_phone') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'createdType')->dropDownList(Lead::TYPE_CREATE_LIST, ['prompt' => '-']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'l_answered')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '-']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'hybrid_uid')->textInput(['title' => 'Hybrid UID'])->label('Booking ID') ?>
                </div>
            </div>


        </div>

        <div class="col-md-3">

            <div class="row">
                <div class="col-md-6">
                    <?php echo $form->field($model, 'trip_type')->dropDownList(Lead::TRIP_TYPE_LIST, ['prompt' => '-']) ?>
                </div>
                <div class="col-md-6">
                    <?php echo $form->field($model, 'cabin')->dropDownList(Lead::CABIN_LIST, ['prompt' => '-']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php //= $form->field($model, 'status')->dropDownList(Lead::STATUS_LIST, ['prompt' => '-'])?>
                    <?php
                    echo $form->field($model, 'statuses')->widget(Select2::class, [
                        'data' => Lead::STATUS_LIST,
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select status', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ]);
                    ?>

                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?php echo $form->field($model, 'adults')->dropDownList(array_combine(range(0, 9), range(0, 9)), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-4">
                    <?php echo $form->field($model, 'children')->dropDownList(array_combine(range(0, 9), range(0, 9)), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-4">
                    <?php echo $form->field($model, 'infants')->dropDownList(array_combine(range(0, 9), range(0, 9)), ['prompt' => '-']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?php echo $form->field($model, 'origin_airport') ?>
                </div>
                <div class="col-md-6">
                    <?php echo $form->field($model, 'destination_airport') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?php echo $form->field($model, 'origin_country') ?>
                </div>
                <div class="col-md-6">
                    <?php echo $form->field($model, 'destination_country') ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6"><?php echo $form->field($model, 'project_id')->dropDownList($lists->getProjects(), ['prompt' => '-']) ?></div>
                <div class="col-md-6"><?php echo $form->field($model, 'source_id')->dropDownList($lists->getSources(true), ['prompt' => '-']) ?></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'departRangeTime', [
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
                                'format' => 'd-M-Y',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Depart From / To');
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'createdRangeTime', [
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
                                'format' => 'd-M-Y H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Created From / To');
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'updatedRangeTime', [
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
                                'format' => 'd-M-Y H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Updated From / To');
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'soldRangeTime', [
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
                                'format' => 'd-M-Y H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Status date From / To');
                    ?>
                </div>
            </div>

            <?php //= $form->field($model, 'employee_id')?>


        </div>

        <div class="col-md-3">
            <?php // echo $form->field($model, 'request_ip_detail')?>

            <?php // echo $form->field($model, 'offset_gmt')?>

            <?php //php  echo $form->field($model, 'snooze_for')?>

            <div class="row">
                <div class="col-md-6">
                    <?php echo $form->field($model, 'rating')->dropDownList(array_combine(range(1, 3), range(1, 3)), ['prompt' => '-']) ?>
                </div>

                <?php //php  echo $form->field($model, 'called_expert')?>
                <div class="col-md-6">
                    <?php echo $form->field($model, 'employee_id')->dropDownList($lists->getEmployees(true), ['prompt' => '-']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'discount_id')->input('number', ['min' => 1]) ?>
                </div>
                <div class="col-md-6">
                    <?php echo $form->field($model, 'request_ip') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6"><?php echo $form->field($model, 'notes_for_experts') ?></div>
                <div class="col-md-6"><?php echo $form->field($model, 'quote_pnr')->label('PNR') ?></div>
            </div>

            <?php //php  echo $form->field($model, 'bo_flight_id')?>

            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'lastActionRangeTime', [
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
                                'format' => 'd-M-Y H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Last Action From / To');
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'l_is_test')->dropDownList([0 => 'False',1 => 'True'], ['prompt' => '-'])->label('Is Test') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-1">
            <?= $form->field($model, 'callsQtyFrom')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'callsQtyTo')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'smsQtyFrom')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'smsQtyTo')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'emailsQtyFrom')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'emailsQtyTo')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'chatsQtyFrom')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'chatsQtyTo')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['name' => 'search', 'class' => 'btn btn-primary search_leads_btn']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset data', ['leads/index'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
