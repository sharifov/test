<?php

use common\models\Quote;
use kartik\select2\Select2;
use src\access\ListsAccess;
use src\model\flightQuoteLabelList\service\FlightQuoteLabelListService;
use src\model\leadDataKey\entity\LeadDataKey;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Lead;
use frontend\extensions\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $action string */
/** @var \src\access\ListsAccess $lists */

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
        <div class="col-md-12 col-sm-12 profile_details">
            <div class="well profile_view">
                <div class="col-sm-12">
                    <h4 class="brief"><i>Lead</i></h4>
                    <div class="row">
                        <div class="col-md-1">
                            <?= $form->field($model, 'id')->input('number', ['min' => 1]) ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'uid') ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'gid')->textInput(['maxlength' => true]) ?>
                        </div>
                        <?php /*<div class="col-md-4">
                    <?= $form->field($model, 'discount_id')->input('number', ['min' => 1]) ?>
                </div>*/ ?>
                   <!-- </div>
                    <div class="row">-->
                        <div class="col-md-1">
                            <?= $form->field($model, 'client_id')->input('number', ['min' => 1]) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'client_name') ?>
                        </div>
                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-2">
                            <?= $form->field($model, 'client_email')//->input('email')?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'client_phone') ?>
                        </div>
                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-1">
                            <?= $form->field($model, 'createdType')->dropDownList(Lead::TYPE_CREATE_LIST, ['prompt' => '-']) ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'lead_type')->dropDownList(Lead::TYPE_LIST, ['prompt' => '-']) ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'excludeExtraQueue')->checkbox([])->label('Exclude Extra Queue'); ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'excludeBonusQueue')->checkbox([['label' => 'Exclude Bonus Queue / Follow up Queue']])->label('Exclude Bonus Queue / Follow up Queue'); ?>
                        </div>
                    </div>
                </div>
                <div class=" profile-bottom text-center">
                </div>
            </div>
        </div>

        <div class="col-md-12 col-sm-12  profile_details">
            <div class="well profile_view">
                <div class="col-sm-12">
                    <h4 class="brief"><i>Common</i></h4>
                    <div class="row">
                        <div class="col-md-1">
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
                    <!--</div>
                    <div class="row">-->

                         <div class="col-md-1">
                            <?php echo $form->field($model, 'projectId')->widget(Select2::class, [
                                'data' => $lists->getProjects(),
                                'size' => Select2::SMALL,
                                'options' => ['placeholder' => 'Select Project', 'multiple' => false],
                                'pluginOptions' => ['allowClear' => true],
                            ]); ?>
                        </div>

                        <div class="col-md-1">
                            <?php echo $form->field($model, 'source_id')->widget(Select2::class, [
                                'data' => $lists->getSources(true),
                                'size' => Select2::SMALL,
                                'options' => ['placeholder' => 'Select SourceID', 'multiple' => false],
                                'pluginOptions' => ['allowClear' => true],
                            ]); ?>
                        </div>

                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-1">
                            <?php echo $form->field($model, 'employee_id')->widget(Select2::class, [
                                'data' => $lists->getEmployees(true),
                                'size' => Select2::SMALL,
                                'options' => ['placeholder' => 'Select user', 'multiple' => false],
                                'pluginOptions' => ['allowClear' => true],
                            ]); ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'bo_flight_id')->input('number', ['min' => 0])->label('Sale ID') ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field(
                                $model,
                                'hybrid_uid'
                            )->textInput(['title' => 'Hybrid UID'])->label('Booking ID') ?>
                        </div>
                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-1"><?php echo $form->field($model, 'quote_pnr')->label('PNR') ?></div>
                        <div class="col-md-1">
                            <?php echo $form->field($model, 'rating')->dropDownList(array_combine(range(1, 3), range(1, 3)), ['prompt' => '-']) ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'l_answered')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '-']) ?>
                        </div>
                        <!--</div>
                    <div class="row">-->
                        <div class="col-md-1">
                            <?= $form->field($model, 'discount_id')->input('number', ['min' => 1]) ?>
                        </div>
                        <div class="col-md-1">
                            <?php echo $form->field($model, 'request_ip') ?>
                        </div>
                        <!--</div>
                        <div class="row">-->
                        <div class="col-md-1">
                            <?= $form->field($model, 'quoteTypeId')->dropDownList(Quote::TYPE_LIST, ['prompt' => '-']) ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'l_is_test')->dropDownList([0 => 'False', 1 => 'True'], ['prompt' => '-'])->label('Is Test') ?>
                        </div>
                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-2">
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
                            ])->label('Created Date From / To');
                            ?>
                        </div>
                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-2">
                            <?= $form->field($model, 'statusRangeTime', [
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
                            ])->label('Status Date From / To');
                            ?>
                        </div>
                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-2">
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
                            ])->label('Updated Date From / To');
                            ?>
                        </div>
                   <!-- </div>
                    <div class="row">-->
                        <div class="col-md-2">
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
                        <div class="col-md-2">
                            <?php echo $form->field($model, 'expiration_dt', [
                                'options' => ['class' => 'form-group']
                            ])->widget(DatePicker::class, [
                                'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'clearBtn' => true,
                                ],
                                'options' => [
                                    'autocomplete' => 'off',
                                    'placeholder' => 'Choose Date',
                                    'readonly' => '1',
                                ],
                                'clientEvents' => [
                                    'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                                ],
                            ])->label('Expiration');
                            ?>
                        </div>

                        <div class="col-md-1">
                            <?= $form->field($model, 'is_conversion')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '-']) ?>
                        </div>

                        <div class="row" style="padding-left: 10px;">
                            <div class="col-md-6">
                                <?= $form->field($model, 'lead_data_key')->dropDownList(LeadDataKey::getListCache(), ['prompt' => '-']) ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo $form->field($model, 'lead_data_value') ?>
                            </div>
                        </div>

                        <div class="row" style="padding-left: 10px;">
                            <div class="col-md-12">
                                <?php
                                    echo $form->field($model, 'quote_labels')->widget(Select2::class, [
                                        'data' => FlightQuoteLabelListService::getListKeyDescription(),
                                        'size' => Select2::SMALL,
                                        'options' => ['placeholder' => 'Select quote labels', 'multiple' => true],
                                        'pluginOptions' => ['allowClear' => true],
                                    ]);
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" profile-bottom text-center">
                </div>
            </div>
        </div>

        <div class="col-md-12 col-sm-12  profile_details">
            <div class="well profile_view">
                <div class="col-sm-12">
                    <h4 class="brief"><i>Trip</i></h4>
                    <div class="row">
                        <div class="col-md-1">
                            <?php echo $form->field($model, 'trip_type')->dropDownList(Lead::TRIP_TYPE_LIST, ['prompt' => '-']) ?>
                        </div>
                        <div class="col-md-1">
                            <?php echo $form->field($model, 'cabin')->dropDownList(Lead::CABIN_LIST, ['prompt' => '-']) ?>
                        </div>
                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-1">
                            <?php echo $form->field($model, 'adults')->dropDownList(array_combine(range(0, 9), range(0, 9)), ['prompt' => '-']) ?>
                        </div>
                        <div class="col-md-1">
                            <?php echo $form->field($model, 'children')->dropDownList(array_combine(range(0, 9), range(0, 9)), ['prompt' => '-']) ?>
                        </div>
                        <div class="col-md-1">
                            <?php echo $form->field($model, 'infants')->dropDownList(array_combine(range(0, 9), range(0, 9)), ['prompt' => '-']) ?>
                        </div>
                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-2">
                            <?php echo $form->field($model, 'origin_airport') ?>
                        </div>
                        <div class="col-md-2">
                            <?php echo $form->field($model, 'destination_airport') ?>
                        </div>
                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-2">
                            <?php echo $form->field($model, 'origin_country') ?>
                        </div>
                        <div class="col-md-2">
                            <?php echo $form->field($model, 'destination_country') ?>
                        </div>
                    <!--</div>
                    <div class="row">-->
                        <div class="col-md-2">
                            <?= $form->field($model, 'departRangeTime', ['options' => ['class' => 'form-group']])->widget(\kartik\daterange\DateRangePicker::class, [
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
                    <!--<div class="row">
                        <div class="col-md-12">
                            <?php /*= $form->field($model, 'soldRangeTime', [
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
                            ])->label('Lead flow Status date From / To');
                            */?>
                        </div>
                    </div>-->
                </div>
                <div class=" profile-bottom text-center">
                </div>
            </div>
        </div>

        <div class="col-md-12 col-sm-12  profile_details">
            <div class="well profile_view">
                <div class="col-sm-12">
                    <h4 class="brief"><i>Communication</i></h4>
                    <div class="row">
                        <div class="col-md-1">
                            <?= $form->field($model, 'callsQtyFrom')->textInput() ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'callsQtyTo')->textInput() ?>
                        </div>
                        <!--</div>
                        <div class="row">-->
                        <div class="col-md-1">
                            <?= $form->field($model, 'smsQtyFrom')->textInput() ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'smsQtyTo')->textInput() ?>
                        </div>
                        <!--</div>
                        <div class="row">-->
                        <div class="col-md-1">
                            <?= $form->field($model, 'emailsQtyFrom')->textInput() ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'emailsQtyTo')->textInput() ?>
                        </div>
                        <!--</div>
                        <div class="row">-->
                        <div class="col-md-1">
                            <?= $form->field($model, 'chatsQtyFrom')->textInput() ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'chatsQtyTo')->textInput() ?>
                        </div>
                        <div class="col-md-1">
                            <?= $form->field($model, 'includedFiles')->dropDownList([0 => 'No', 1 => 'Yes'], ['prompt' => '-']) ?>
                        </div>
                    </div>
                    <?php // echo $form->field($model, 'request_ip_detail')?>

                    <?php // echo $form->field($model, 'offset_gmt')?>

                    <?php //php  echo $form->field($model, 'snooze_for')?>

                    <!--<div class="row">
                        <?php //php  echo $form->field($model, 'called_expert')?>
                    </div>-->
                    <!--<div class="row">
                        <div class="col-md-6"><?php /*echo $form->field($model, 'notes_for_experts') */?></div>
                    </div>-->

                    <?php //php  echo $form->field($model, 'bo_flight_id')?>
                </div>
                <div class=" profile-bottom text-center">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
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
                <?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['name' => 'search', 'class' => 'btn btn-primary search_leads_btn']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset data', ['leads/index'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
