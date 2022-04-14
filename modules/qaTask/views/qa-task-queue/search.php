<?php

use modules\qaTask\src\entities\qaTask\QaTask;
use modules\qaTask\src\grid\columns\QaTaskObjectTypeColumn;
use modules\qaTask\src\grid\columns\QaTaskCreatedTypeColumn;
use modules\qaTask\src\grid\columns\QaTaskQueueActionColumn;
use modules\qaTask\src\grid\columns\QaTaskRatingColumn;
use modules\qaTask\src\grid\columns\QaTaskStatusColumn;
use modules\qaTask\src\grid\columns\QaTaskObjectOwnerColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\department\DepartmentColumn;
use common\components\grid\project\ProjectColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use modules\qaTask\src\abac\QaTaskAbacObject;

/* @var $this yii\web\View */
/* @var $searchModel modules\qaTask\src\entities\qaTask\search\QaTaskCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Tasks Search';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-search">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="btn-group check_uncheck_btns">
        <?php echo Html::button('<span class="fa fa-square-o"></span> Check All', ['class' => 'btn btn-sm btn-default', 'id' => 'btn-check-all']); ?>

        <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu">
            <p>
                <?php /** @abac null, QaTaskAbacObject::ACT_USER_ASSIGN, QaTaskAbacObject::ACTION_ACCESS, Assign Multiple Tasks To QA*/ ?>
                <?php if (Yii::$app->abac->can(null, QaTaskAbacObject::ACT_USER_ASSIGN, QaTaskAbacObject::ACTION_ACCESS)) : ?>
                    <?php echo
                    Html::a(
                        '<i class="fa fa-edit text-warning"></i> Assign user',
                        null,
                        [
                            'class' => 'dropdown-item btn-multiple-update',
                            'data' => [
                                'url' => Url::to(['/qa-task/qa-task-action/user-assign']),
                                'title' => 'Multiple update',
                            ],
                        ]
                    )
                    ?>
                <?php endif; ?>

                <?php /** @abac null, QaTaskAbacObject::ACT_MULTI_CANCEL, QaTaskAbacObject::ACTION_ACCESS, Cancel Multiple Qa Tasks */ ?>
                <?php if (Yii::$app->abac->can(null, QaTaskAbacObject::ACT_MULTI_CANCEL, QaTaskAbacObject::ACTION_ACCESS)) : ?>
                    <?php echo
                    Html::a(
                        '<i class="fa fa-times text-danger"></i> Multiple Cancel',
                        null,
                        [
                            'class' => 'dropdown-item btn-multiple-cancel',
                            'data' => [
                                'url' => Url::to(['/qa-task/qa-task-action/multiple-cancel']),
                                'title' => 'Multiple cancel',
                            ],
                        ]
                    )
                    ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'qa-task-list-grid',
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'cssClass' => 'multiple-checkbox',
                'checkboxOptions' => function ($model) {
                    return ['value' => $model->t_gid];
                }
            ],
            't_id',
            't_gid',
            [
                'class' => ProjectColumn::class,
                'attribute' => 't_project_id',
                'relation' => 'project',
                'filter' => $searchModel->getProjectList(),
            ],
            [
                'class' => QaTaskObjectTypeColumn::class,
                'attribute' => 't_object_type_id',
                'filter' => $searchModel->getObjectTypeList(),
            ],
            't_object_id',
            [
                'attribute' => 't_category_id',
                'value' => static function (QaTask $task) {
                    return $task->t_category_id ? $task->category->tc_name : null;
                },
                'filter' => $searchModel->getCategoryList(),
            ],
            [
                'class' => QaTaskStatusColumn::class,
                'attribute' => 't_status_id',
                'filter' => $searchModel->getStatusList(),
            ],
            [
                'class' => QaTaskRatingColumn::class,
                'attribute' => 't_rating',
                'filter' => $searchModel->getRatingList(),
            ],
            [
                'class' => QaTaskCreatedTypeColumn::class,
                'attribute' => 't_create_type_id',
                'filter' => $searchModel->getCreatedTypeList(),
            ],
            [
                'class' => DepartmentColumn::class,
                'attribute' => 't_department_id',
                'relation' => 'department',
                'filter' => $searchModel->getDepartmentList(),
            ],

            [
                'class' => QaTaskObjectOwnerColumn::class,
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 't_assigned_user_id',
                'relation' => 'assignedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 't_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 't_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 't_deadline_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 't_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 't_updated_dt',
            ],
            [
                'class' => QaTaskQueueActionColumn::class,
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>

<?php
$selectAllUrl = Url::to(['/qa-task/qa-task-queue/search']);
$js = <<<JS
$(document).on('click', '#btn-check-all',  function (e) {
    let btn = $(this);
    
    if ($(this).hasClass('checked')) {
        btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Check All');
        $('.select-on-check-all').prop('checked', false);
        $("input[name='selection[]']:checked").prop('checked', false);
        sessionStorage.selectedTasks = '{}';
        //sessionStorage.removeItem('selectedUsers');
    } else {
        btn.html('<span class="fa fa-spinner fa-spin"></span> Loading ...');
        let params = new URLSearchParams(window.location.search);
        $.ajax({
         type: 'post',
         dataType: 'json',
         //data: {},
         url: '$selectAllUrl' + '?' + params.toString() + '&act=select-all',
         success: function (data) {
            let cnt = Object.keys(data).length
            if (data) {
                let jsonData = JSON.stringify(data);
                sessionStorage.selectedTasks = jsonData;
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

function checkedVerify(title) {
    if (!$("input[name='selection[]']:checked").length) {
        createNotifyByObject({title: title, type: "error", text: 'Not selected rows.', hide: true});
        return false;
    }
    return true;
}

$(document).on('click', '.btn-multiple-update', function(e) {
    e.preventDefault();        
    let arrIds = [];
    
    if (!checkedVerify('Assign user')) return false;
    
    if (sessionStorage.selectedTasks) {
        let data = jQuery.parseJSON( sessionStorage.selectedTasks );
        arrIds = Object.values(data);    
        
        let modal = $('#modal-df');
        let urlAction = $(this).data('url');
        let title = $(this).data('title');
        
        console.log(arrIds);
        
        $.ajax({
            type: 'get',
            url: urlAction,
            dataType: 'html',
            cache: false,
            data: {'gid[]': arrIds.length ? arrIds : []},
            beforeSend: function () {
                modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
                modal.find('.modal-title').html(title);
                modal.modal('show');
            },
            success: function (data) {
                modal.find('.modal-body').html(data);
            },
            error: function (xhr) {
                if (xhr.status != 403) {
                    modal.find('.modal-body').html('Error: ' + xhr.responseText);
                } else {
                    modal.find('.modal-body').html('Access denied.');
                }
                            
            },
        });
    }
});

$(document).on('click', '.btn-multiple-cancel', function(e) {
    e.preventDefault();        
    let arrIds = [];
    
    if (!checkedVerify('Multiple cancel')) return false;
    
    if (sessionStorage.selectedTasks) {
        let data = jQuery.parseJSON( sessionStorage.selectedTasks );
        arrIds = Object.values(data);    
        
        let modal = $('#modal-df');
        let urlAction = $(this).data('url');
        let title = $(this).data('title');
        
        console.log(arrIds);
        
        $.ajax({
            type: 'get',
            url: urlAction,
            dataType: 'html',
            cache: false,
            data: {'gid[]': arrIds.length ? arrIds : []},
            beforeSend: function () {
                modal.find('.modal-body').html('<div><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
                modal.find('.modal-title').html(title);
                modal.modal('show');
            },
            success: function (data) {
                modal.find('.modal-body').html(data);
            },
            error: function (xhr) {
                if (xhr.status != 403) {
                    modal.find('.modal-body').html('Error: ' + xhr.responseText);
                } else {
                    modal.find('.modal-body').html('Access denied.');
                }
                            
            },
        });
    }
});

$(document).on('click', '.multiple-checkbox', function(e) {
    e.stopPropagation();
    let checked = $("input[name='selection[]']:checked").map(function () { return this.value; }).get();
    let unchecked = $("input[name='selection[]']:not(:checked)").map(function () { return this.value; }).get();
    let data = {};
    if (sessionStorage.selectedTasks) {
        data = jQuery.parseJSON( sessionStorage.selectedTasks );
    }
   
    $.each( checked, function( key, value ) {
        if (typeof data[value] === 'undefined') {
            data[value] = value;
            let uncheckedIndex = unchecked[unchecked.findIndex((elem) => elem === value)];
            if (uncheckedIndex !== 'undefined') {
                delete(unchecked[uncheckedIndex]);
            }
        }
    });
    
    $.each( unchecked, function( key, value ) {
        if (typeof data[value] !== 'undefined') {
            delete(data[value]);
        }
    });
   
   sessionStorage.selectedTasks = JSON.stringify(data);
   refreshUserSelectedState();
});

$('body').on('change', '.select-on-check-all', function(e) {
    let checked = $("input[name='selection[]']:checked").map(function () { return this.value; }).get();
    let unchecked = $("input[name='selection[]']:not(:checked)").map(function () { return this.value; }).get();
    let data = {};
    if (sessionStorage.selectedTasks) {
        data = jQuery.parseJSON( sessionStorage.selectedTasks );
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
   
   sessionStorage.selectedTasks = JSON.stringify(data);
   refreshUserSelectedState();
});

function refreshUserSelectedState() {
     if (sessionStorage.selectedTasks) {
        let data = jQuery.parseJSON( sessionStorage.selectedTasks );
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
JS;
$this->registerJs($js);

