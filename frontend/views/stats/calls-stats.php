<?php

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use kartik\export\ExportMenu;
use src\entities\call\CallGraphsSearch;
use common\models\UserGroup;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2;

/**
 * @var CallGraphsSearch $model
 */

$this->title = 'Calls Stats';
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Search</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                    <?php $form = ActiveForm::begin([
                        'id' => 'call-chart-search-form',
                        'options' => ['data-pjax' => '#total-calls-chart'],
                        'action' => \yii\helpers\Url::to('/stats/ajax-get-total-chart'),
                        'enableClientValidation' => false,
                    ]) ?>

                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'createTimeRange', [
                                    'options' => ['class' => 'form-group'],
                                ])->widget(\kartik\daterange\DateRangePicker::class, [
                                    'presetDropdown' => false,
                                    'hideInput' => true,
                                    'convertFormat' => true,
                                    'pluginOptions' => [
                                        'minDate' => "2018-01-01 00:00",
                                        'maxDate' => date("Y-m-d 23:59"),
                                        'timePicker' => true,
                                        'timePickerIncrement' => 1,
                                        'timePicker24Hour' => true,
                                        'locale' => [
                                            'format' => 'Y-m-d H:i:s',
                                            'separator' => ' - '
                                        ]
                                    ]
                                ])->label('Created From / To'); ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'timeZone')->widget(\kartik\select2\Select2::class, [
                                    'data' => Employee::timezoneList(true),
                                    'size' => \kartik\select2\Select2::SMALL,
                                    'options' => [
                                        'placeholder' => 'Select TimeZone',
                                        'multiple' => false,
                                        'value' =>  $model->timeZone ?? Yii::$app->user->identity->timezone
                                    ],
                                    'pluginOptions' => ['allowClear' => true],
                                ]) ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'projectIds', [
                                        'options' => ['class' => 'form-group']
                                ])->widget(Select2::class, [
                                    'data' => Project::getList(),
                                    'size' => Select2::SMALL,
                                    'options' => ['placeholder' => 'Select Project', 'multiple' => true],
                                    'pluginOptions' => ['allowClear' => true],
                                ])->label('Project') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'dep_ids', [
                                    'options' => ['class' => 'form-group']
                                ])->widget(Select2::class, [
                                    'data' => Department::getList(),
                                    'size' => Select2::SMALL,
                                    'options' => ['placeholder' => 'Select Department', 'multiple' => true],
                                    'pluginOptions' => ['allowClear' => true],
                                ])->label('Department') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'callGraphGroupBy', [
                                    'options' => ['class' => 'form-group']
                                ])->dropDownList($model::getDateFormatTextList())->label('Group By') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'cl_user_id', [
                                        'options' => ['class' => 'form-group']
                                ])->dropDownList(\common\models\Employee::getList(), [
                                        'prompt' => 'All'
                                ])->label('Username') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'userGroupIds', [
                                    'options' => ['class' => 'form-group']
                                ])->widget(Select2::class, [
                                    'data' => UserGroup::getList(),
                                    'size' => Select2::SMALL,
                                    'options' => ['placeholder' => 'Select User Group', 'multiple' => true],
                                    'pluginOptions' => ['allowClear' => true],
                                ])->label('User Groups') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'betweenHoursFrom', [
                                        'options' => ['class' => 'form-group']
                                ])->input('number', ['class' => 'form-control'])->label('Between Hours From') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'betweenHoursTo', [
                                        'options' => ['class' => 'form-group']
                                ])->input('number', ['class' => 'form-control'])->label('Between Hours To') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'recordingDurationFrom', [
                                        'options' => ['class' => 'form-group']
                                ])->input('number', ['class' => 'form-control'])->label('Duration Seconds From') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'recordingDurationTo', [
                                    'options' => ['class' => 'form-group']
                                ])->input('number', ['class' => 'form-control'])->label('Duration Seconds To') ?>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-center">
                                    <?= \yii\helpers\Html::button('<i class="fa fa-search"></i> Search', ['type' => 'submit', 'class' => 'btn btn-default', 'id' => 'call-chart-from-btn']) ?>
                                </div>
                            </div>
                        </div>

                        <?= \yii\helpers\Html::checkboxList($model->formName() . '[totalChartColumns]', $model->totalChartColumns, $model::getChartTotalCallTextList(), [
                            'itemOptions' => [
                                'style' => 'display: none',
                                'label' => false
                            ],
                        ]) ?>

                        <?= \yii\helpers\Html::dropDownList($model->formName() . '[chartTotalCallsVaxis]', $model->chartTotalCallsVaxis, $model::getChartTotalCallsVaxisTextList(), [
                            'style' => 'display: none',
                            'label' => false
                        ]) ?>

                    <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<div class="card card-default">
    <div class="card-header"><i class="fa fa-bar-chart"></i> All Calls Chart</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 col-sm-6 col-xs-12">
                <div class="x_panel" id="total-calls-chart">
                    <div id="loading" style="text-align:center;font-size: 40px;">
                        <i class="fa fa-spin fa-spinner"></i> Loading ...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$url = \yii\helpers\Url::to(['/stats/ajax-get-total-chart']);
$js = <<<JS
$(document).ready( function () {
    let formLoaded = $('#call-chart-search-form').serializeArray();
    let submitBtnHtml = $('#call-chart-from-btn').html();
    let spinner = '<i class="fa fa-spinner fa-spin"></i> Loading...';
    
    $.ajax({
        url: '$url',
        type: 'post',
        dataType: 'json',
        data: formLoaded,
        beforeSend: function () {
          $('#call-chart-from-btn').html(spinner).prop('disabled', true).toggleClass('disabled');
        },
        success: function (data) {
            if (!data.error) {
                $('#total-calls-chart').html(data.html);
            } else {
               createNotifyByObject({
                    title: 'Attention',
                    text: data.message,
                    type: 'warning'                
               }); 
            }
        },
        error: function (error) {
            createNotifyByObject({
                title: 'Error',
                text: 'Internal Server Error. Try again letter.',
                type: 'error'                
            });
            $('#total-calls-chart').html('Internal Server Error. Try again letter.');
        },
        complete: function () {
            $('#call-chart-from-btn').html(submitBtnHtml).removeAttr('disabled').toggleClass('disabled');
        }
    });
    
    let loading = '<div id="loading" style="text-align:center;font-size: 40px;">'+
                        '<i class="fa fa-spin fa-spinner"></i> Loading ...'+
                    '</div>';
    
    $('#call-chart-search-form').off().on('submit', function (e) {
        e.preventDefault();
        
        var form = $(this).serializeArray();
        
        var formData = new FormData(document.getElementById('call-chart-search-form'));
        $('.total-calls-chart-column:checked').each( function (e, elem) {
            let name = $(elem).attr('name');
            let val = $(elem).val();
            form.push({name: name, value: val});
            formData.append(name, val);
        });
        
       formData.delete('_csrf-frontend');
        var params = new URLSearchParams(formData).toString();
        
        $.ajax({
            url: '$url',
            type: 'post',
            data: form,
            dataType: 'json',
            beforeSend: function () {
                $('#total-calls-chart').html(loading);
                $('#call-chart-from-btn').html(spinner).prop('disabled', true).toggleClass('disabled');
            },
            success: function (data) {
                if (!data.error) {
                    $('#total-calls-chart').html(data.html);
                    
                    window.history.replaceState({}, '', location.pathname+'?'+params);
                } else {
                   createNotifyByObject({
                        title: 'Attention',
                        text: data.message,
                        type: 'warning'                
                   }); 
                }
            },
            error: function (error) {
                createNotifyByObject({
                    title: 'Error',
                    text: 'Internal Server Error. Try again letter.',
                    type: 'error'                
                });
                
                $('#total-calls-chart').html('Internal Server Error. Try again letter.');
            },
            complete: function () {
                $('#call-chart-from-btn').html(submitBtnHtml).removeAttr('disabled').toggleClass('disabled');
                $('#loading').remove();
            }
        })
    });
});
JS;
$this->registerJs($js);
?>