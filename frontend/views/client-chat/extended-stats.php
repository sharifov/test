<?php

use src\entities\chat\ChatExtendedGraphsSearch;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use src\widgets\UserSelect2Widget;
use kartik\select2\Select2;
use common\models\UserGroup;
use common\models\Employee;

/**
 * @var ChatExtendedGraphsSearch $model
 */

$this->title = 'Client Chat';
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
                        'id' => 'chat-chart-search-form',
                        'options' => ['data-pjax' => '#client-chat-chart'],
                        //'action' => \yii\helpers\Url::to('/stats/ajax-get-total-chart'),
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
                                        'format' => 'Y-m-d H:i:s',
                                        'separator' => ' - '
                                    ]
                                ]
                            ])->label('Created From / To'); ?>
                        </div>

                        <div class="col-md-2">
                            <?= $form->field($model, 'timeZone')->widget(Select2::class, [
                                'data' => Employee::timezoneList(true),
                                'size' => Select2::SMALL,
                                'options' => [
                                    'placeholder' => 'Select TimeZone',
                                    'multiple' => false,
                                    'value' => $model->defaultUserTz
                                ],
                                'pluginOptions' => ['allowClear' => true],
                            ]);
?>
                        </div>

                        <div class="col-md-2">
                            <?= $form->field($model, 'graphGroupBy', [
                                'options' => ['class' => 'form-group']
                            ])->dropDownList($model::getDateFormatTextList())->label('Group By') ?>
                        </div>

                        <div class="col-md-2">
                            <?= $form->field($model, 'cch_owner_user_id')->widget(UserSelect2Widget::class, [
                                'data' => $model->cch_owner_user_id ? [
                                    $model->cch_owner_user_id => $model->cchOwnerUser->username
                                ] : [],
                            ])
?>
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
                            <?= $form->field($model, 'cch_project_id')->dropDownList(
                                \common\models\Project::getList(),
                                ['prompt' => '-']
                            ) ?>
                        </div>

                        <div class="col-md-2">
                            <?= $form->field($model, 'cch_channel_id', [
                                'options' => ['class' => 'form-group']
                            ])->dropDownList(\src\model\clientChatChannel\entity\ClientChatChannel::getList(), [
                                'prompt' => 'All'
                            ])->label('Channel') ?>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-center">
                                <?= \yii\helpers\Html::button('<i class="fa fa-search"></i> Search', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-default',
                                    'id' => 'chat-chart-from-btn'
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-default">
        <div class="card-header"><i class="fa fa-bar-chart"></i> Chats state Chart</div>
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
$url = \yii\helpers\Url::to(['/client-chat/ajax-get-extended-stats-chart']);
$js = <<<JS
$(document).ready( function () {
    let formLoaded = $('#chat-chart-search-form').serializeArray();
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
    
    $('#chat-chart-search-form').off().on('submit', function (e) {
        e.preventDefault();
        
        var form = $(this).serializeArray();
        
        var formData = new FormData(document.getElementById('chat-chart-search-form'));
               
       formData.delete('_csrf-frontend');
        var params = new URLSearchParams(formData).toString();
        
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
