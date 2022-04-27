<?php

use common\models\query\EmployeeQuery;
use yii\helpers\Url;
use yii\grid\CheckboxColumn;
use common\components\grid\Select2Column;
use common\models\Employee;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use src\access\ListsAccess;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Inflector;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var \modules\shiftSchedule\src\entities\userShiftAssign\search\SearchUserShiftAssign $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var ListsAccess $listsAccess */

$this->title = 'User Shift Assigns';
$this->params['breadcrumbs'][] = $this->title;
$pjaxContainerId = 'pjax-user-shift-assign';
?>
<div class="user-shift-assign-index">

    <h1><i class="fa fa-user-plus"></i> <?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => $pjaxContainerId, 'timeout' => 7000, 'scrollTo' => 0]); ?>

    <p>
    <div class="btn-group">
        <?php echo Html::button('<span class="fa fa-square-o"></span> Check All', ['class' => 'btn btn-default', 'id' => 'btn-check-all']); ?>

        <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu">
            <?php echo Html::a('<i class="fa fa-user-plus success"></i>  Assign Selected', null, ['id' => 'js-assign-selected', 'class' => 'dropdown-item btn-multiple-update' ])?>
        </div>
    </div>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            [
                'class' => CheckboxColumn::class,
                'cssClass' => 'multiple-checkbox'
            ],
            [
                'label' => 'User',
                'class' => Select2Column::class,

                'attribute' => 'userId',
                'format' => 'raw',
                'value' => static function (Employee $model) {
                    return '<span style="white-space: nowrap;"><i class="fa fa-user"></i> ' .
                        Html::encode($model->username) . ' (' . $model->id . ')' .
                    '</span>';
                },
                'data' => EmployeeQuery::getList(\src\auth\Auth::id()) ?: [],
                'filter' => true,
                'id' => 'employee-filter',
                'options' => ['min-width' => '280px'],
                'pluginOptions' => ['allowClear' => true],
            ],
            [
                'label' => 'Shift',
                'attribute' => 'shiftId',
                'value' => static function (Employee $model) {
                    if (!$model->userShiftAssigns) {
                        return '';
                    }
                    $shifts = [];
                    foreach ($model->userShiftAssigns as $item) {
                        $shifts[] = Html::tag('span', Html::encode($item->shift->sh_name), ['class' => 'label label-default']);
                    }
                    return implode(' ', $shifts);
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-left', 'style' => 'min-width: 320px'],
                'filter' => \modules\shiftSchedule\src\entities\shift\Shift::getList(),
            ],
            [
                'label' => 'User Groups',
                'attribute' => 'userGroupId',
                'class' => Select2Column::class,
                'value' => static function (Employee $model) {
                    $groups = $model->getUserGroupList();
                    $groupsValueArr = [];
                    foreach ($groups as $group) {
                        $groupsValueArr[] = Html::tag('span', Html::encode($group), ['class' => 'label label-success']);
                    }
                    return implode(' ', $groupsValueArr);
                },
                'data' => \common\models\UserGroup::getList(),
                'filter' => true,
                'id' => 'group-filter',
                'options' => ['min-width' => '320px'],
                'pluginOptions' => ['allowClear' => true],
                'format' => 'raw',
            ],
            [
                'attribute' => 'role',
                'label' => 'Role',
                'value' => static function (Employee $model) {
                    $items = $model->getRoles();
                    $itemsData = [];
                    foreach ($items as $item) {
                        $itemsData[] = Html::tag('span', Html::encode($item), ['class' => 'label bg-light text-dark shadow']);
                    }
                    return implode(' ', $itemsData);
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getAllRoles(),
                'contentOptions' => ['style' => 'width: 10%; white-space: pre-wrap']
            ],
            [
                'label' => 'Project',
                'attribute' => 'projectId',
                'class' => Select2Column::class,
                'value' => static function (Employee $model) {
                    if (!$model->projects) {
                        return '';
                    }
                    $projects = [];
                    foreach ($model->projects as $item) {
                        $projects[] = Html::tag('span', Html::encode($item->name), ['class' => 'label label-info']);
                    }
                    return implode(' ', $projects);
                },
                'data' => \common\models\Project::getList(),
                'filter' => true,
                'id' => 'project-filter',
                'options' => ['min-width' => '320px'],
                'pluginOptions' => ['allowClear' => true],
                'format' => 'raw',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{assign}',
                'buttons' => [
                    'assign' => static function ($url, Employee $model, $key) {
                        return Html::a(
                            '<span class="fa fa-user-plus"></span>',
                            ['assign', 'id' => $model->id],
                            ['title' => 'Assign to Shift', 'target' => '_blank', 'data-pjax' => 0,]
                        );
                    },
                ],
                'visibleButtons' => [
                    'assign' => static function ($model, $key, $index) {
                        /** @abac ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE, Access to button UserShiftAssign */
                        return \Yii::$app->abac->can(null, ShiftAbacObject::ACT_USER_SHIFT_ASSIGN, ShiftAbacObject::ACTION_UPDATE);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
$css = <<<CSS
    .shadow {
        -webkit-box-shadow: 1px 1px 1px 2px #000000; 
        box-shadow: 1px 1px 1px 2px #000000;
    }
CSS;
$this->registerCss($css);
?>

<?php yii\bootstrap4\Modal::begin([
    'title' => '',
    'id' => 'multiple_assign_modal',
    'size' => \yii\bootstrap4\Modal::SIZE_SMALL,
]);
yii\bootstrap4\Modal::end() ?>

<?php
$storageName = Inflector::variablize($this->title);
$selectAllUrl = Url::to(['/shift/user-shift-assign/select-all']);
$multipleAssignFormUrl = Url::to(array_merge(['/shift/user-shift-assign/multiple-assign-form'], Yii::$app->getRequest()->getQueryParams()));
$pjaxContainer = '#' . $pjaxContainerId;

$script = <<< JS
    let selectAllUrl = '$selectAllUrl';
    let multipleAssignFormUrl = '$multipleAssignFormUrl';
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
                setTimeout(function () {
                    btn.html(checkAllInner);
                }, 2000);
            })
            .always(function() {
                btn.prop('disabled', false);
            }); 
        }
    });
    
    $(document).on('click', '#js-assign-selected', function() {
        if (!sessionStorage.getItem(storageName)) {
            notifyAlert('Please select items', 'error');
            return false; 
        }

        let data = jQuery.parseJSON(sessionStorage.getItem(storageName));
        let cnt = Object.keys(data).length;
    
        $.ajax({
            url: multipleAssignFormUrl,
            type: 'POST',
            dataType: 'json',
            data: {userIds : data}
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {
                let modalBodyEl = $('#multiple_assign_modal .modal-body');
                modalBodyEl.html(dataResponse.data);
                $('#multiple_assign_modal-label').html('Assign users to shift'); 
                $('#multiple_assign_modal').modal('show');
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
        })
        .fail(function(error) {
            console.error(error);
            alert('Request Error');
        })
        .always(function() {});
    });

    $(document).on('change', '.select-on-check-all', function(e) {
        e.stopPropagation();
        recalculeteStorageData(storageName);
        refreshSelectedState();
    });
    
    $(document).on('click', '.multiple-checkbox', function(e) {
        e.stopPropagation();
        recalculeteStorageData(storageName);
        refreshSelectedState();
    });

    $(document).ready(function() {
        refreshSelectedState();
    });

    $(pjaxContainer).on('pjax:end', function() { 
       refreshSelectedState();
    });
    
    function recalculeteStorageData(storageName) {
        let checked = $("input[name='selection[]']:checked").map(function () { return this.value; }).get();
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
    }   
JS;

$this->registerJs($script);
