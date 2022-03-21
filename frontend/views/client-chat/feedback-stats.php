<?php

use src\entities\chat\ChatFeedbackGraphSearch;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use src\widgets\UserSelect2Widget;
use src\access\EmployeeDepartmentAccess;
use yii\web\JsExpression;

/**
 * @var ChatFeedbackGraphSearch $model
 */

$this->title = 'Client Chat Feedback Rating ';
$this->params['breadcrumbs'][] = $this->title;
?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <div class="chat-stats">
        <h1><i class=""></i> <?= Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Search</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <?php $form = ActiveForm::begin([
                            'id' => 'feedback-chart-search-form',
                            //'options' => ['data-pjax' => '#client-chat-chart'],
                            //'enableClientValidation' => false,
                        ]) ?>

                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($model, 'timeRange', [
                                    'options' => ['class' => 'form-group'],
                                ])->widget(\kartik\daterange\DateRangePicker::class, [
                                    'presetDropdown' => true,
                                    'hideInput' => true,
                                    'convertFormat' => true,
                                    'containerTemplate' => '<div class="kv-drp-dropdown">
                                                                <span class="left-ind">{pickerIcon}</span>
                                                                <input type="text" readonly class="form-control range-value" value="{value}">                                                                
                                                                <span class="right-ind"><b class="caret"></b></span>
                                                            </div>
                                                            {input}',
                                    'pluginOptions' => [
                                        'timePicker' => true,
                                        'timePickerIncrement' => 1,
                                        'timePicker24Hour' => true,
                                        'locale' => [
                                            'format' => 'Y-m-d H:i:s',
                                            'separator' => ' - '
                                        ],
                                        'opens' => 'right',
                                        'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                                    ],
                                    'pluginEvents' => [
                                        "apply.daterangepicker" => new JsExpression('function(){ 
                                            let range = $("#chatfeedbackgraphsearch-timerange").val().split(" - ")
                                            let start = moment(range[0], "YYYY-MM-DD");
                                            let end = moment(range[1], "YYYY-MM-DD");
                                            let intervalOfDays = parseInt(moment.duration(end.diff(start)).asDays() + 1)
                                                                                        
                                            $("#chatfeedbackgraphsearch-groupby option").each(function(){  
                                                if($(this).val() == 1 && intervalOfDays == 1){                    
                                                    this.disabled = false                                                    
                                                } else if($(this).val() == 1 && intervalOfDays != 1){
                                                    this.disabled = true
                                                    this.selected = false
                                                } else if($(this).val() == 3 && intervalOfDays > 6){
                                                    this.disabled = false
                                                }
                                            })                                            
                                        }'),
                                    ]
                                ])->label('Created From / To'); ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'groupBy')->dropDownList($model::getGroupsList())->label('Group By') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'projectID')->dropDownList(\common\models\Project::getList(), ['prompt' => '-'])->label('Project') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'channelID')->dropDownList(\src\model\clientChatChannel\entity\ClientChatChannel::getList(), ['prompt' => '-'])->label('Channel') ?>
                            </div>

                            <div class="col-md-2">
                                <?= $form->field($model, 'ccfUserID')->widget(UserSelect2Widget::class)->label('User') ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-center">
                                    <?= \yii\helpers\Html::button('<i class="fa fa-search"></i> Search', ['type' => 'submit', 'class' => 'btn btn-default', 'id' => 'chat-chart-from-btn']) ?>
                                </div>
                            </div>
                        </div>

                        <?php ActiveForm::end() ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default">
            <div class="card-header"><i class="fa fa-bar-chart"></i> Analytics of Feedback in Chats</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-sm-6 col-xs-12">
                        <div class="x_panel" id="client-chat-chart">
                            <div id="loading" style="text-align:center;font-size: 40px;">
                                <i class="fa fa-spin fa-spinner"></i> Loading ...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$url = \yii\helpers\Url::to(['/client-chat/ajax-get-feedback-stats-chart']);
$js = <<<JS
$(document).ready( function () {
    let formLoaded = $('#feedback-chart-search-form').serializeArray();
    let submitBtnHtml = $('#chat-chart-from-btn').html();
    let spinner = '<i class="fa fa-spinner fa-spin"></i> Loading...';
    
    $.ajax({
        url: '$url',
        type: 'post',
        dataType: 'json',
        data: formLoaded,
        beforeSend: function () {
          $('#chat-chart-from-btn').html(spinner).prop('disabled', true).toggleClass('disabled');
        },
        success: function (data) {
            if (!data.error) {
                $('#client-chat-chart').html(data.html);
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
            $('#client-chat-chart').html('Internal Server Error. Try again letter.');
        },
        complete: function () {
            $('#chat-chart-from-btn').html(submitBtnHtml).removeAttr('disabled').toggleClass('disabled');
        }
    });
    
    let loading = '<div id="loading" style="text-align:center;font-size: 40px;">'+
                        '<i class="fa fa-spin fa-spinner"></i> Loading ...'+
                    '</div>';
    
    $('#feedback-chart-search-form').off().on('submit', function (e) {
        e.preventDefault();
        
        var form = $(this).serializeArray();
        
        var formData = new FormData(document.getElementById('feedback-chart-search-form'));
               
        formData.delete('_csrf-frontend');
        var params = new URLSearchParams(formData).toString();
        console.log(form)
        $.ajax({
            url: '$url',
            type: 'post',
            data: form,
            dataType: 'json',
            beforeSend: function () {
                $('#client-chat-chart').html(loading);
                $('#chat-chart-from-btn').html(spinner).prop('disabled', true).toggleClass('disabled');
            },
            success: function (data) {
                if (!data.error) {
                    $('#client-chat-chart').html(data.html);
                    
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
                
                $('#client-chat-chart').html('Internal Server Error. Try again letter.');
            },
            complete: function () {
                $('#chat-chart-from-btn').html(submitBtnHtml).removeAttr('disabled').toggleClass('disabled');
                $('#loading').remove();
            }
        })
    });
});
JS;
$this->registerJs($js);
?>
<?php
$dropDownJs = <<<JS

let range = $("#chatfeedbackgraphsearch-timerange").val().split(" - ")
    let start = moment(range[0], "YYYY-MM-DD");
    let end = moment(range[1], "YYYY-MM-DD");
    let intervalOfDays = parseInt(moment.duration(end.diff(start)).asDays() + 1)
                                                                                        
     $("#chatfeedbackgraphsearch-groupby option").each(function(){  
     if($(this).val() == 1 && intervalOfDays == 1){                    
         this.disabled = false
     } else if($(this).val() == 1 && intervalOfDays != 1){
         this.disabled = true
     } else if($(this).val() == 3 && intervalOfDays > 6){
         this.disabled = false
     }
})            

JS;
$this->registerJs($dropDownJs, \yii\web\View::POS_READY);
?>