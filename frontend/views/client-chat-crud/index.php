<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use src\model\clientChat\entity\ClientChat;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatMessage\entity\ClientChatMessage;
use yii\helpers\Url;
use yii\helpers\Inflector;
use src\auth\Auth;

/* @var $this yii\web\View */
/* @var $searchModel src\model\clientChat\entity\search\ClientChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chats';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-index" xmlns="http://www.w3.org/1999/html">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'client_chat_crud', 'scrollTo' => 650]); ?>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php if (Auth::user()->isAdmin()) : ?>
    <p>
    <div class="btn-group">
        <?php echo Html::button('<span class="fa fa-square-o"></span> Check All', ['class' => 'btn btn-default', 'id' => 'btn-check-all']); ?>

        <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu">
            <?= \yii\helpers\Html::a('<i class="fa fa-remove text-danger"></i>  Delete Selected', null, ['id' => 'btn-act-delete-selected', 'class' => 'dropdown-item btn-multiple-update' ])?>
            <div class="dropdown-divider"></div>
            <?= \yii\helpers\Html::a('<i class="fa fa-info text-info"></i> Show Checked IDs', null, ['class' => 'dropdown-item btn-show-checked-ids'])?>
        </div>
    </div>
    </p>
    <?php endif ?>

    <?= GridView::widget([
            'id' => 'chats-list-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'cssClass' => 'multiple-checkbox'
            ],
            [
                'attribute' => 'cch_id',
                'options' => ['style' => 'width:100px']
            ],
            [
                'attribute' => 'cch_parent_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_parent_id ?
                        Html::a(
                            '<i class="fa fa-link"></i> ' . $model->cch_parent_id,
                            ['client-chat-crud/view', 'id' => $model->cch_parent_id],
                            ['target' => '_blank', 'data-pjax' => 0]
                        ) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:100px'],
            ],
            'cch_rid',
            [
                'label' => 'Messages',
                'value' => static function (ClientChat $model) {
                    $count = ClientChatMessage::countByChatId($model->cch_id);
                    return Html::a(
                        '<span class="glyphicon glyphicon-comment"></span> <sup>' . $count . '</sup>',
                        ['/client-chat-qa/view', 'id' => $model->cch_id, '#' => 'messages'],
                        [
                            'target' => '_blank',
                            'data-pjax' => 0,
                            'title' => 'Messages',
                        ]
                    );
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'cch_ccr_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_ccr_id ?
                        Html::a(
                            '<i class="fa fa-link"></i> ' . $model->cch_ccr_id,
                            ['client-chat-request-crud/view', 'id' => $model->cch_ccr_id],
                            ['target' => '_blank', 'data-pjax' => 0]
                        ) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:100px'],
            ],
            [
                'attribute' => 'cch_status_id',
                'value' => static function (ClientChat $model) {
                    return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-' . $model->getStatusClass()]);
                },
                'format' => 'raw',
                'filter' => ClientChat::getStatusList()
            ],
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'cch_project_id',
                'relation' => 'cchProject',
            ],
            [
                'attribute' => 'cch_dep_id',
                'format' => 'department',
                'filter' => \common\models\Department::getList()
            ],
            [
                'attribute' => 'cch_channel_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_channel_id ? Html::a(
                        Html::encode($model->cchChannel->ccc_name),
                        ['client-chat-channel-crud/view', 'id' => $model->cch_channel_id],
                        ['target' => '_blank', 'data-pjax' => 0]
                    ) : '-';
                },
                'format' => 'raw',
                'filter' => ClientChatChannel::getList()
            ],
            [
                'attribute' => 'cch_client_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_client_id ? Html::a(
                        '<i class="fa fa-link"></i> ' . $model->cch_client_id,
                        ['client/view', 'id' => $model->cch_client_id],
                        ['target' => '_blank', 'data-pjax' => 0]
                    ) : '-';
                },
                'format' => 'raw',
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cch_owner_user_id',
                'relation' => 'cchOwnerUser',
                'placeholder' => 'Select User'
            ],
            [
                'attribute' => 'cch_source_type_id',
                'options' => ['style' => 'width:100px'],
                'filter' => ClientChat::getSourceTypeList(),
                'value' => static function (ClientChat $model) {
                    return $model->getSourceTypeName();
                }
            ],
            [
                'attribute' => 'caseId',
                'label' => 'Case',
                'value' => static function (ClientChat $chat) {
                    $out = '';
                    foreach ($chat->cases as $case) {
                        $out .= Yii::$app->formatter->format($case, 'case') . '<br />';
                    }
                    return $out;
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width:120px; white-space: normal;'],
            ],
            [
                'attribute' => 'leadId',
                'label' => 'Lead',
                'value' => static function (ClientChat $chat) {
                    $out = '';
                    foreach ($chat->leads as $lead) {
                        $out .= Yii::$app->formatter->format($lead, 'lead') . '<br />';
                    }
                    return $out;
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width:120px; white-space: normal;'],
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cch_created_dt',
                'format' => 'byUserDateTime',
            ],

            ['class' => 'yii\grid\ActionColumn'],
            ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
$storageName = Inflector::variablize($this->title);
//$selectAllUrl = Url::to(array_merge(['/client-chat-crud/select-all'], Yii::$app->getRequest()->getQueryParams()));
$selectAllUrl = Url::to(['/client-chat-crud/select-all']);
$deleteSelectedUrl = Url::to(array_merge(['/client-chat-crud/delete-selected'], Yii::$app->getRequest()->getQueryParams()));
$pjaxContainer = '#client_chat_crud' ;

$script = <<< JS

    let selectAllUrl = '$selectAllUrl';
    let deleteSelectedUrl = '$deleteSelectedUrl';
    let storageName = '$storageName';
    let pjaxContainer = '$pjaxContainer';
    
    let loadingInner = '<span class="fa fa-spinner fa-spin"></span> Loading ...';    
    let checkAllInner = '<span class="fa fa-square-o"></span> Check All';
        
    function refreshSelectedState() {
        let btn = $('#btn-check-all');      
        if (sessionStorage.getItem(storageName)) {
            let data = jQuery.parseJSON(sessionStorage.getItem(storageName));
            let cnt = Object.keys(data).length;
            
            if (cnt > 0) {
                $.each(data, function(key, value) {
                    $("input[name='selection[]'][value='" + value + "']").prop('checked', true);
                });
                btnUncheckAll(btn, cnt);                
            } else {
                btnCheckAll(btn);
                $('.select-on-check-all').prop('checked', false);
            }
        } else {
            btnCheckAll(btn);
            $('.select-on-check-all').prop('checked', false);
        }
    }
    
    function btnUncheckAll(btn, cnt) {
        btn.removeClass('btn-default').
            addClass(['btn-warning', 'checked']).
            html('<span class="fa fa-check-square-o"></span> Uncheck All (' + cnt + ')'); 
    }
    
    function btnCheckAll(btn) {
        btn.removeClass(['btn-warning', 'checked']).
            addClass('btn-default').
            html(checkAllInner);
    }
    
    function notifyAlert(text, type = 'success') {
        createNotifyByObject({
            title: type,
            type: type,
            text: text,
            hide: true
        });  
    }
    
    $(document).on('click', '#btn-check-all',  function (e) {
        let btn = $(this);
        
        if (btn.hasClass('checked')) {
            btnCheckAll(btn);
            $('.select-on-check-all').prop('checked', false);
            $("input[name='selection[]']:checked").prop('checked', false);
            sessionStorage.removeItem(storageName);
            
        } else {    
            btn.html(loadingInner).prop('disabled', true);
                let queryParams = ''
                if (window.location.href.indexOf('?') > 0) {
                    queryParams = window.location.href.slice(window.location.href.indexOf('?'))
                }                
            $.ajax({
                url: selectAllUrl + queryParams,
                type: 'POST',
                dataType: 'json'    
            })
            .done(function(dataResponse) {
                
                let cnt = Object.keys(dataResponse).length;
                if (dataResponse) {
                    sessionStorage.setItem(storageName, JSON.stringify(dataResponse));
                    btnUncheckAll(btn, cnt);                        
                    $('.select-on-check-all').prop('checked', true); 
                    $("input[name='selection[]']").prop('checked', true);
                } else {
                    btn.html(checkAllInner);
                }
            })
            .fail(function(error) {
                console.error(error);
                alert('Request Error');
                btn.html('<span class="fa fa-error text-danger"></span> Error ...');                
            })
            .always(function() {
                btn.prop('disabled', false);
            }); 
        }
    });
    
    $(document).on('click', '#btn-act-delete-selected', function() {
        
        if (!sessionStorage.getItem(storageName)) {
            notifyAlert('Please select items', 'error');
            return false; 
        }          
        
        let data = jQuery.parseJSON(sessionStorage.getItem(storageName));
        let cnt = Object.keys(data).length;
                              
        if(!confirm('Are you sure you want to delete (' + cnt + ') ?')) {
            return false;
        }
                    
        $.ajax({
            url: deleteSelectedUrl,
            type: 'POST',
            dataType: 'json',
            data: {selection : data}
        })
        .done(function(dataResponse) {
            if (dataResponse) {
                sessionStorage.removeItem(storageName);
                $.pjax.reload({container: pjaxContainer});
                notifyAlert('Items (' + cnt + ') deleted successfully');
            }                
        })
        .fail(function(error) {
            console.error(error);
            alert('Request Error');
        })
        .always(function() {});
        
    });
    
    $(document).on('change', '.select-on-check-all', function(e) {
        let checked = $('#chats-list-grid').yiiGridView('getSelectedRows');
        let unchecked = $("input[name='selection[]']:not(:checked)").map(function () { return this.value; }).get();
        let data = [];        
        if (sessionStorage.getItem(storageName)) {
            data = JSON.parse(sessionStorage.getItem(storageName));
        }
                        
        if (checked) {
            $.each(checked, function(key, value) {
            
                let searchValue = parseInt(value, 10);
                if (isNaN(parseInt(value, 10))) {                    
                    if (typeof value === 'string' || value instanceof String) {
                        searchValue = value; 
                    } else {
                        searchValue = JSON.stringify(value);
                    }
                }
                
                let keyForAdd = data.indexOf(searchValue);                
                if (keyForAdd === -1) {
                    data.push(searchValue);
                }
            });
        }         
        if (unchecked) {
            $.each(unchecked, function(key, value) { 
                
                let searchValue = parseInt(value, 10);
                if (isNaN(parseInt(value, 10))) {                    
                    if (typeof value === 'string' || value instanceof String) {
                        searchValue = value; 
                    } else {
                        searchValue = JSON.stringify(value);
                    }
                }
                           
                let keyForDelete = data.indexOf(searchValue);                
                if (keyForDelete !== -1) {
                    data.splice(keyForDelete, 1);
                }                
            });
        }
        
        if (data.length) {
            sessionStorage.setItem(storageName, JSON.stringify(data));
        } else {
            sessionStorage.removeItem(storageName);
        }
        refreshSelectedState();
    });        
    
    $(document).ready(function() {        
        refreshSelectedState();
    });
        
    $(pjaxContainer).on('pjax:end', function() { 
       refreshSelectedState();
    });
    
    $('body').on('click', '.btn-show-checked-ids', function(e) {
       let data = [];
       if (sessionStorage.getItem(storageName)) {
            data = jQuery.parseJSON( sessionStorage.getItem(storageName));
            let arrIds = [];
            if (data) {
                arrIds = Object.values(data);                 
            }
            alert('Client Chat IDs (' + arrIds.length + ' items): ' + arrIds.join(', '));
       } else {
           alert('No Any Chats Selected');
       }
    });
JS;

$this->registerJs($script);

?>
