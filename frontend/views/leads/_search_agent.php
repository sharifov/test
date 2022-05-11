<?php

use common\models\Quote;
use kartik\select2\Select2;
use src\model\leadDataKey\entity\LeadDataKey;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="lead-search">
    <p class="text-warning"><i class="fa fa-info-circle"></i> For searching the exact matches of the fields are used</p>

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
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
                    <?= $form->field($model, 'uid')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'quote_pnr')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'gid')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'hybrid_uid')->textInput(['title' => 'Hybrid UID'])->label('Booking ID') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'quoteTypeId')->dropDownList(Quote::TYPE_LIST, ['prompt' => '-']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'excludeExtraQueue')->checkbox(['label' => 'Exclude Extra Queue']); ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'excludeBonusQueue')->checkbox(['label' => 'Exclude Bonus Queue / Follow up Queue']); ?>
                </div>
            </div>

        </div>

        <div class="col-md-3">

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
                        <?= $form->field($model, 'lead_data_key')->dropDownList(LeadDataKey::getListCache(), ['prompt' => '-']) ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $form->field($model, 'lead_data_value') ?>
                    </div>
                </div>


            <div class="row">

                <div class="col-md-6">
                    <?php  //echo $form->field($model, 'trip_type')->dropDownList(\common\models\Lead::TRIP_TYPE_LIST, ['prompt' => '-'])?>
                </div>
                <div class="col-md-6">
                    <?php  //echo $form->field($model, 'cabin')->dropDownList(\common\models\Lead::CABIN_LIST, ['prompt' => '-'])?>
                </div>
            </div>



            <div class="row">
                <div class="col-md-4">
                    <?php  //echo $form->field($model, 'adults')->dropDownList(array_combine(range(0, 9), range(0, 9)), ['prompt' => '-'])?>
                </div>
                <div class="col-md-4">
                    <?php  //echo $form->field($model, 'children')->dropDownList(array_combine(range(0, 9), range(0, 9)), ['prompt' => '-'])?>
                </div>
                <div class="col-md-4">
                    <?php  //echo $form->field($model, 'infants')->dropDownList(array_combine(range(0, 9), range(0, 9)), ['prompt' => '-'])?>
                </div>
            </div>



        </div>

        <div class="col-md-3">


            <?php  //echo $form->field($model, 'project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-'])?>

            <?php  //echo $form->field($model, 'source_id')->dropDownList(\common\models\Source::getList(), ['prompt' => '-'])?>


            <div class="row">
                <div class="col-md-12">

                    <?php
                    echo $form->field($model, 'statuses')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\Lead::STATUS_LIST,
                        'size' => \kartik\select2\Select2::SMALL,
                        'options' => ['placeholder' => 'Select status', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ]);
                    ?>

                </div>
            </div>

            <?php /*
            <div class="row">
                <div class="col-md-6">


                    <?= $form->field($model, 'created_date_from')->widget(
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

                <div class="col-md-6">

                    <?= $form->field($model, 'created_date_to')->widget(
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
            </div>*/ ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'client_email')//->input('email')?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'client_phone') ?>
                </div>
            </div>

            <?php //= $form->field($model, 'employee_id')?>


        </div>

        <div class="col-md-3">
        <?php  // echo $form->field($model, 'request_ip_detail')?>

        <?php  // echo $form->field($model, 'offset_gmt')?>

        <?php //php  echo $form->field($model, 'snooze_for')?>

            <div class="row">
                <div class="col-md-6">
                    <?php  echo $form->field($model, 'bo_flight_id')->label('BO Sale ID') ?>
                </div>


                <?php //php  echo $form->field($model, 'called_expert')?>
                <div class="col-md-6">
                    <?php  echo $form->field($model, 'request_ip') ?>
                    <?php // echo $form->field($model, 'employee_id')->dropDownList([Yii::$app->user->id => Yii::$app->user->identity->username], ['prompt' => '-'])?>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">
                    <?php  //echo $form->field($model, 'rating')->dropDownList(array_combine(range(1, 3), range(1, 3)), ['prompt' => '-'])?>
                </div>

                <div class="col-md-6">
                    <?php  //echo $form->field($model, 'request_ip')?>
                </div>
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

        <?php  //echo $form->field($model, 'notes_for_experts')?>

        <?php //php  echo $form->field($model, 'bo_flight_id')?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2><i class="fa fa-list"></i> Show Additional fields</h2>
            <?php //echo Html::label('Additional fields:', 'showFields', ['class' => 'control-label']);?>
            <?= //Select2::widget([
            // 'name' => 'LeadSearch[show_fields]', //Html::getInputName($filter, 'showFilter'),
            $form->field($model, 'show_fields')->widget(Select2::class, [
                'data' => $model->getViewFields(),
                'size' => Select2::SIZE_SMALL,
                'pluginOptions' => [
                    'closeOnSelect' => false,
                    'allowClear' => true,
                    //'width' => '100%',
                ],
                'options' => [
                    'placeholder' => 'Choose additional fields...',
                    'multiple' => true,
                    'id' => 'showFields',
                ],
                //'value' => $model->show_fields,
            ])->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['class' => 'btn btn-primary search_leads_btn']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset data', ['leads/index'], ['class' => 'btn btn-warning']) ?>
                <?php //= Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-warning'])?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
