<?php

/**
 * @var $searchModel UserFeedbackSearch
 * @var $statusData array
 * @var $this \yii\web\View
 */

use frontend\assets\ChartJsAsset;
use modules\user\userFeedback\entity\search\UserFeedbackSearch;
use yii\widgets\ActiveForm;

$this->title = 'User Feedback Statistics';

ChartJsAsset::register($this);
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>

<div class="row">
    <div class="col-md-8">
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
                    <div class="col-md-5">
                        <?= $form->field($searchModel, 'createTimeRange', [
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
                                ],
                                'allowClear' => true
                            ],
                        ])->label('Created From / To'); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-center">
                            <?= \yii\helpers\Html::button('<i class="fa fa-search"></i> Search', ['type' => 'submit', 'class' => 'btn btn-default', 'id' => 'chart-form-btn']) ?>
                        </div>
                    </div>
                </div>

                <?php /* \yii\helpers\Html::checkboxList($searchModel->formName() . '[totalChartColumns]', $searchModel->totalChartColumns, $searchModel::getChartTotalCallTextList(), [
                    'itemOptions' => [
                        'style' => 'display: none',
                        'label' => false
                    ],
                ]) */ ?>

                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-4">
        <div class="statusChartWrapper">
            <?= $this->render('partial/_total_user_feedback_status_chart', [
                'statusData' => $statusData
            ]) ?>
        </div>
    </div>
</div>

<div class="card card-default">
    <div class="card-header"><i class="fa fa-bar-chart"></i> User Feedback Chart</div>
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
$url = \yii\helpers\Url::to(['/stats/ajax-get-user-feedback-chart']);
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
                    $('.statusChartWrapper').html(data.statusHtml);
                    
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
