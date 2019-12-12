<?php

use common\models\search\CallGraphsSearch;
use yii\bootstrap4\ActiveForm;

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
										'timePicker' => true,
										'timePickerIncrement' => 1,
										'timePicker24Hour' => true,
										'locale' => [
											'format' => 'd-M-Y H:i',
											'separator' => ' - '
										]
									]
								])->label('Created From / To'); ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'c_project_id', [
                                        'options' => ['class' => 'form-group']
                                ])->dropDownList(\common\models\Project::getList(), [
                                        'prompt' => 'All'
                                ])->label('Project') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'callDepId', [
                                        'options' => ['class' => 'form-group']
                                ])->dropDownList(\common\models\Department::getList(), [
                                        'prompt' => 'All'
                                ])->label('Department') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'c_created_user_id', [
                                        'options' => ['class' => 'form-group']
                                ])->dropDownList(\common\models\Employee::getList(), [
                                        'prompt' => 'All'
                                ])->label('Username') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'userGroupId', [
                                        'options' => ['class' => 'form-group']
                                ])->dropDownList(\common\models\UserGroup::getList(), [
                                        'prompt' => 'All'
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
                                ])->input('number', ['class' => 'form-control'])->label('Recording Duration Seconds From') ?>
                            </div>

                            <div class="col-md-2">
								<?= $form->field($model, 'recordingDurationTo', [
									'options' => ['class' => 'form-group']
								])->input('number', ['class' => 'form-control'])->label('Recording Duration Seconds To') ?>
                            </div>

                            <div class="col-md-2">
								<?= $form->field($model, 'callGraphGroupBy', [
									'options' => ['class' => 'form-group']
								])->dropDownList($model::getDateFormatTextList())->label('Group By') ?>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-center">
                                    <?= \yii\helpers\Html::button('Search', ['type' => 'submit', 'class' => 'btn btn-default', 'id' => 'call-chart-from-btn']) ?>
                                </div>
                            </div>
                        </div>

                        <?= \yii\helpers\Html::checkboxList($model->formName().'[totalChartColumns]', $model->totalChartColumns, $model::getChartTotalCallTextList(), [
                            'itemOptions' => [
                                'style' => 'display: none',
                                'label' => false
                            ],
                        ]) ?>

                        <?= \yii\helpers\Html::dropDownList($model->formName().'[chartTotalCallsVaxis]', $model->chartTotalCallsVaxis, $model::getChartTotalCallsVaxisText(), [
                            'style' => 'display: none',
                            'label' => false
                        ]) ?>

                    <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<div class="card card-default">
    <div class="card-header"><i class="fa fa-bar-chart"></i> Total Calls Chart</div>
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
    
    let spinner = '<i class="fa fa-spinner fa-spin"></i>';
    
    $.ajax({
        url: '$url',
        type: 'post',
        dataType: 'json',
        data: formLoaded,
        beforeSend: function () {
              
        },
        success: function (data) {
            if (!data.error) {
                $('#total-calls-chart').html(data.html);
            } else {
               new PNotify({
                    title: 'Error',
                    text: data.message,
                    type: 'error'                
               }); 
            }
        },
        error: function (error) {
            new PNotify({
                title: 'Error',
                text: 'Internal Server Error. Try again letter.',
                type: 'error'                
            });
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
        
        var submitBtnHtml = $('#call-chart-from-btn').html();
        
        $.ajax({
            url: '$url',
            type: 'post',
            data: form,
            dataType: 'json',
            beforeSend: function () {
                $('#total-calls-chart').html(loading);
                $('#call-chart-from-btn').html(spinner);
            },
            success: function (data) {
                if (!data.error) {
                    $('#total-calls-chart').html(data.html);
                    
                    window.history.replaceState({}, '', location.pathname+'?'+params);
                } else {
                   new PNotify({
                        title: 'Error',
                        text: data.message,
                        type: 'error'                
                   }); 
                }
            },
            error: function (error) {
                new PNotify({
                    title: 'Error',
                    text: 'Internal Server Error. Try again letter.',
                    type: 'error'                
                });
            },
            complete: function () {
                $('#call-chart-from-btn').html(submitBtnHtml);
                $('#loading').remove();
            }
        })
    });
});
JS;
$this->registerJs($js);
?>