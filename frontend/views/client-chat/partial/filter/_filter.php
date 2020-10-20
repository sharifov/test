<?php

use kartik\select2\Select2;
use sales\model\clientChat\dashboard\FilterForm;
use sales\model\clientChat\dashboard\GroupFilter;
use sales\widgets\UserSelect2Widget;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var FilterForm $filter */
/** @var string $loadChannelsUrl */
/** @var ArrayDataProvider|null $dataProvider */
/** @var int $countFreeToTake */

/*
    <div class="_cc_tabs_wrapper">
        <?php foreach (ClientChat::getTabList() as $key => $item): ?>
            <div class="_cc_tab <?= $key === $filter->status ? 'active' : ''; ?>" data-tab-id="<?= $key; ?>"> <?= $item; ?>
                <?php if (ClientChat::isTabActive($key)): ?>
                    <sup class="_cc_unread_messages label label-danger" ><?= $totalUnreadMessages ?: ''; ?></sup>
                <?php endif; ?>
                <span class="_cc_tab_active"></span>
            </div>
        <?php endforeach; ?>
    </div>
 */

?>

<?= Html::beginForm(Url::to(['/client-chat/index']), 'GET', ['id' => $filter->getId()]); ?>
    <div class="col-md-12" style="margin-top: 10px">

        <?php echo Html::hiddenInput(Html::getInputName($filter, 'resetAdditionalFilter'), 0, ['id' => 'resetAdditionalFilter']); ?>

            <div class="row">

                <?php if ($filter->permissions->canStatus()): ?>
                    <div class="col-md-6">
                        <?= Html::label('Show:', null, ['class' => 'control-label']); ?>
                        <?= Select2::widget([
                            'data' => $filter->getShowFilter(),
                            'name' => Html::getInputName($filter, 'status'),
                            'size' => Select2::SIZE_SMALL,
                            'pluginEvents' => [
                                'change' => new \yii\web\JsExpression('function (e) {
                                        window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                                    }'),
                            ],
                            'pluginOptions' => [
                                'width' => '100%',
                            ],
                            'options' => [
                                'placeholder' => 'Choose the status...',
                                'id' => Html::getInputId($filter, 'status'),
                            ],
                            'value' => $filter->status,
                        ]); ?>
                    </div>
                <?php endif; ?>

                <?php if ($filter->permissions->canChannel()): ?>
                    <div class="col-md-6">
                        <?= Html::label('Channel:', null, ['class' => 'control-label']); ?>
                        <?= Select2::widget([
                            'data' => $filter->getChannels(),
                            'name' => Html::getInputName($filter, 'channelId'),
                            'size' => Select2::SIZE_SMALL,
                            'pluginEvents' => [
                                'change' => new \yii\web\JsExpression('function (e) {
                                    window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                                }'),
                            ],
                            'pluginOptions' => [
                                'width' => '100%',
                            ],
                            'options' => [
                                'placeholder' => 'Choose the channel...',
                                'id' => Html::getInputId($filter, 'channelId'),
                            ],
                            'value' => $filter->channelId,
                        ]); ?>
                    </div>
                <?php endif; ?>
            </div>

        <?php
            if ($filter->permissions->canProject() || $filter->permissions->canUser() || $filter->permissions->canCreatedDate()):
        ?>

            <?php $isAdditionalFilterActive = $filter->isAdditionalFilterActive(); ?>

            <div class="row" style="margin-top: 6px;">
                <div class="col-md-12 text-right">
                    <i class="fa fa-filter"></i> <?= Html::a('Additional filters', null, ['id' => 'btn_additional_filters']) ?>
                    <?php if ($isAdditionalFilterActive): ?>
                        <?php echo Html::a('(reset <i class="fa fa-times"></i>)', null, ['id' => 'reset_additional', 'style' => 'font-weight: bold;']); ?>
                    <?php endif ?>
                </div>
            </div>

            <div
                class="row"
                id="additional_filters_div"
                style="margin-bottom: 20px; display: <?php echo $isAdditionalFilterActive ? '' : 'none' ?>;">

                <?php if ($filter->permissions->canProject()): ?>
                    <div class="col-md-6">
                        <?= Html::label('Project:', null, ['class' => 'control-label']); ?>
                        <?= Select2::widget([
                            'data' => $filter->getProjects(),
                            'name' => Html::getInputName($filter, 'project'),
                            'size' => Select2::SIZE_SMALL,
                            'options' => [
                                'placeholder' => 'Choose the channel...',
                                'id' => Html::getInputId($filter, 'project'),
                            ],
                            'value' => $filter->project,
                            'pluginOptions' => [
                                'width' => '100%',
                            ],
                            'pluginEvents' => [
                                'change' => new \yii\web\JsExpression('function (e) {
                                    window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                                }'),
                            ],
                        ]); ?>
                    </div>
                <?php endif; ?>

                <?php if ($filter->permissions->canUser()): ?>
                    <div class="col-md-6">
                        <?= Html::label('Agent:', null, ['class' => 'control-label']); ?>
                        <?= UserSelect2Widget::widget([
                            'name' => Html::getInputName($filter, 'userId'),
                            'size' => Select2::SIZE_SMALL,
                            'pluginEvents' => [
                                'change' => new \yii\web\JsExpression('function (e) {
                                        window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                                    }'),
                            ],
                            'pluginOptions' => [
                                'width' => '100%',
                            ],
                            'options' => [
                                'placeholder' => 'Choose the agent...',
                                'id' => Html::getInputId($filter, 'userId'),
                            ],
                            'value' => $filter->userId,
                            'initValueText' => $filter->userName,
                        ]); ?>
                    </div>
                <?php endif; ?>

                <?php if ($filter->permissions->canCreatedDate()): ?>
                    <div class="col-md-12">
                        <?= Html::label('Created:', null, ['class' => 'control-label']); ?>
                        <?= \kartik\daterange\DateRangePicker::widget([
                        'model' => $filter,
                            'attribute' => 'rangeDate',
                            'useWithAddon' => true,
                            'presetDropdown' => false,
                            'hideInput' => true,
                            'convertFormat' => true,
                            'startAttribute' => 'fromDate',
                            'endAttribute' => 'toDate',
                            'pluginOptions' => [
                                'timePicker' => false,
                                'locale' => [
                                    'format' => 'Y-m-d',
                                    'separator' => ' / '
                                ]
                            ],
                            'pluginEvents' => [
                                'apply.daterangepicker' => new JsExpression('function() { 
                                    window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                                }')
                            ],
                        ]); ?>
                    </div>
                <?php endif; ?>
            </div>

    <?php endif; ?>

    </div>

    <?php if ($filter->permissions->canOneOfGroup()): ?>
        <div class="_cc_groups_wrapper ">
            <?php foreach ($filter->getGroupFilterUI() as $key => $item): ?>
                <?php if ($key === GroupFilter::FREE_TO_TAKE): ?>
                    <?php
                        $countItems = '';
                        if ($countFreeToTake) {
                            $countItems = ' 
                                <small style="margin-left: 4px;">
                                    <span 
                                        class="label label-default" 
                                        style="font-size: 9px;" 
                                        id="count_free_to_take">
                                            ' . $countFreeToTake . '</span></small>';
                        }
                    ?>
                    <div
                        class="_cc_group cc_btn_group_filter <?php echo($key === $filter->group ? 'active' : '') ?>"
                        data-group-id="<?php echo $key ?>">
                            <?php echo $item . $countItems ?>
                                <span class="_cc_group_active"> </span>
                    </div>
                <?php else: ?>
                    <div class="_cc_group cc_btn_group_filter <?= ($key === $filter->group ? 'active' : ''); ?>" data-group-id="<?= $key; ?>"><?= $item; ?><span class="_cc_group_active"> </span></div>
                <?php endif; ?>
            <?php endforeach; ?>
            <?= $filter->getGroupInput(); ?>
        </div>
    <?php else: ?>
        <div class="_cc_groups_wrapper"><h5>Not found chat permissions</h5></div>
    <?php endif; ?>

    <?php $canReadUnread = (GroupFilter::isMy($filter->group) && $filter->permissions->canReadUnread()); ?>
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <div class="d-flex justify-content-<?php if ($canReadUnread): ?>between<?php else: ?>end<?php endif; ?> align-items-center">
                <?php if ($canReadUnread): ?>
                    <?= $filter->getReadUnreadInput(); ?>
                <?php endif; ?>

                <div class="btn-group">
                    <?php echo Html::button('<span class="fa fa-square-o"></span> Check All', ['class' => 'btn btn-sm btn-default', 'id' => 'btn-check-all']); ?>

                    <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu">
                        <?= \yii\helpers\Html::a('<i class="fa fa-edit text-warning"></i> Multiple update', null, ['class' => 'dropdown-item btn-multiple-update'])?>
<!--                        <div class="dropdown-divider"></div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= Html::endForm(); ?>

<?php
$selectAllUrl = Url::to(array_merge(['client-chat/index'], Yii::$app->getRequest()->getQueryParams(), ['act' => 'select-all']));
$clientChatMultipleUpdate = Url::to(['client-chat/ajax-multiple-update']);
$js = <<<JS
$(document).on('click', '#btn-check-all',  function (e) {
        let btn = $(this);
        
        if ($(this).hasClass('checked')) {
            btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Check All');
            $('.select-on-check-all').prop('checked', false);
            $("input[name='selection[]']:checked").prop('checked', false);
            sessionStorage.selectedChats = '{}';
            //sessionStorage.removeItem('selectedUsers');
        } else {
            btn.html('<span class="fa fa-spinner fa-spin"></span> Loading ...');
            
            $.ajax({
             type: 'post',
             dataType: 'json',
             //data: {},
             url: '$selectAllUrl',
             success: function (data) {
                let cnt = Object.keys(data).length
                if (data) {
                    let jsonData = JSON.stringify(data);
                    sessionStorage.selectedChats = jsonData;
                    btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + cnt + ')');
                   
                    $('.select-on-check-all').prop('checked', true); //.trigger('click');
                    $("input[name='selection[]']").prop('checked', true);
                } else {
                    btn.html('<span class="fa fa-square-o"></span> Check All');
                }
             },
             error: function (error) {
                    btn.html('<span class="fa fa-error text-danger"></span> Error ...');
                    console.error(error);
                    alert('Request Error');
                 }
             });
        }
});

function refreshUserSelectedState() {
     if (sessionStorage.selectedChats) {
        let data = jQuery.parseJSON( sessionStorage.selectedChats );
        let btn = $('#btn-check-all');
        
        let cnt = Object.keys(data).length;
        if (cnt > 0) {
            $.each( data, function( key, value ) {
              $("input[name='selection[]'][value=" + value + "]").prop('checked', true);
            });
            btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + cnt + ')');
            
        } else {
            btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Check All');
            $('.select-on-check-all').prop('checked', false);
        }
    }
}

$(document).on('click', '.multiple-checkbox', function(e) {
    e.stopPropagation();
    let checked = $("input[name='selection[]']:checked").map(function () { return this.value; }).get();
    let unchecked = $("input[name='selection[]']:not(:checked)").map(function () { return this.value; }).get();
    let data = [];
    if (sessionStorage.selectedChats) {
        data = jQuery.parseJSON( sessionStorage.selectedChats );
    }
   
    $.each( checked, function( key, value ) {
        if (typeof data[value] === 'undefined') {
          data[value] = value;
        }
    });
    
   $.each( unchecked, function( key, value ) {
      if (typeof data[value] !== 'undefined') {
            delete(data[value]);
      }
    });
   
   sessionStorage.selectedChats = JSON.stringify(data);
   refreshUserSelectedState();
});

$(document).on('click', '.btn-multiple-update', function(e) {
e.preventDefault();        
let arrIds = [];
if (sessionStorage.selectedChats) {
    let data = jQuery.parseJSON( sessionStorage.selectedChats );
    arrIds = Object.values(data);
    
    console.log(arrIds);
    // $('#user_list_json').val(JSON.stringify(arrIds));
    
    let modal = $('#modal-sm');
    
    $.ajax({
        type: 'post',
        url: '{$clientChatMultipleUpdate}',
        dataType: 'html',
        cache: false,
        data: {chatIds: arrIds.length ? JSON.stringify(arrIds) : ''},
        beforeSend: function () {
            modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
            modal.find('.modal-title').html('Client Chat Multiple Update');
            modal.modal('show');
        },
        success: function (data) {
            modal.find('.modal-body').html(data);
        },
        error: function (xhr) {                  
            modal.find('.modal-body').html('Error: ' + xhr.responseText);
            //createNotify('Error', xhr.responseText, 'error');
        },
    });
}
});

refreshUserSelectedState();
JS;
$this->registerJs($js);
?>
