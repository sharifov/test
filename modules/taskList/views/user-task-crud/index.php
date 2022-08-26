<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\taskList\src\entities\userTask\UserTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-task-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Task', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-user-task-index', 'timeout' => 5000, 'enablePushState' => true, 'enableReplaceState' => false, 'scrollTo' => 0]); ?>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
    <div class="btn-group">
        <?= Html::button('<span class="fa fa-square-o"></span> Check All', ['class' => 'btn btn-default', 'id' => 'btn-check-all']); ?>

        <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu">
            <?= \yii\helpers\Html::a('<i class="fa fa-trash text-danger"></i> Multiple delete', null, ['class' => 'dropdown-item btn-multiple-delete', 'data-toggle' => 'modal', 'data-target' => '#modalUpdate' ])?>
            <div class="dropdown-divider"></div>
            <?= \yii\helpers\Html::a('<i class="fa fa-info text-info"></i> Show Checked IDs', null, ['class' => 'dropdown-item btn-show-checked-ids'])?>
        </div>
    </div>
    </p>

    <?= GridView::widget([
        'id' => 'user-task-list-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'cssClass' => 'multiple-checkbox',
                'checkboxOptions' => function (UserTask $model) {
                    return ['value' => $model->ut_id];
                }
            ],
            'ut_id',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ut_user_id',
                'relation' => 'user',
                'placeholder' => 'Employee',
                'format' => 'userNameWithId',
            ],
            [
                'attribute' => 'ut_target_object',
                'value' => static function (UserTask $model) {
                    if (!$model->ut_target_object) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    return $model->ut_target_object;
                },
                'filter' => TargetObject::TARGET_OBJ_LIST,
                'format' => 'raw',
            ],
            'ut_target_object_id',
            [
                'attribute' => 'ut_task_list_id',
                'value' => static function (UserTask $model) {
                    if (!$model->taskList) {
                        return Yii::$app->formatter->nullDisplay;
                    }

                    return Html::a(
                        $model->taskList->tl_title . ' (' . $model->ut_task_list_id . ')' ?? '-',
                        [
                            'task-list/view',
                            'tl_id' => $model->ut_task_list_id
                        ],
                        ['target' => '_blank', 'data-pjax' => 0]
                    );
                },
                'format' => 'raw',
            ],
            [
                'class' => DateTimeColumn::class,
                'limitEndDay' => false,
                'attribute' => 'ut_start_dt',
                'format' => 'byUserDateTimeAndUTC',
            ],
            [
                'class' => DateTimeColumn::class,
                'limitEndDay' => false,
                'attribute' => 'ut_end_dt',
                'format' => 'byUserDateTimeAndUTC',
            ],
            [
                'attribute' => 'ut_priority',
                'value' => static function (UserTask $model) {
                    return UserTaskHelper::priorityLabel($model->ut_priority);
                },
                'format' => 'raw',
                'filter' => UserTask::PRIORITY_LIST,
            ],
            [
                'attribute' => 'ut_status_id',
                'value' => static function (UserTask $model) {
                    return UserTaskHelper::statusLabel($model->ut_status_id);
                },
                'format' => 'raw',
                'filter' => UserTask::STATUS_LIST,
            ],
            [
                'class' => DateTimeColumn::class,
                'limitEndDay' => false,
                'attribute' => 'ut_created_dt',
                'format' => 'byUserDateTimeAndUTC',
            ],
            //'ut_year',
            //'ut_month',
            [
                'class' => ActionColumn::class,
                'urlCreator' => static function ($action, UserTask $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ut_id' => $model->ut_id, 'ut_year' => $model->ut_year, 'ut_month' => $model->ut_month]);
                }
            ],
        ],
    ]); ?>

    <?php
    $selectAllUrl = Url::current(['act' => 'select-all']);
    $multipleDeleteUrl = Url::to(['/task/user-task-crud/multiple-delete']);
    ?>
    <script>
        var selectAllUrl = '<?=$selectAllUrl?>';
        var multipleDeleteUrl = '<?=$multipleDeleteUrl?>';
    </script>

    <?php Pjax::end(); ?>

</div>


<script>
    sessionStorage.selectedUserTasks = '{}';
    document.addEventListener('DOMContentLoaded', function() {
        function refreshUserTaskSelectedState() {
            if (sessionStorage.selectedUserTasks) {
                let data = jQuery.parseJSON(sessionStorage.selectedUserTasks);
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

        $('body').on('click', '#btn-check-all',  function (e) {
            let btn = $(this);

            if ($(this).hasClass('checked')) {
                btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Check All');
                $('.select-on-check-all').prop('checked', false);
                $("input[name='selection[]']:checked").prop('checked', false);
                sessionStorage.selectedUserTasks = '{}';
                //sessionStorage.removeItem('selectedUsers');
            } else {
                btn.html('<span class="fa fa-spinner fa-spin"></span> Loading ...');

                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    //data: {},
                    url: selectAllUrl,
                    success: function (data) {
                        let cnt = Object.keys(data).length
                        if (data) {
                            let jsonData = JSON.stringify(data);
                            sessionStorage.selectedUserTasks = jsonData;
                            btn.removeClass('btn-default').addClass(['btn-warning', 'checked']).html('<span class="fa fa-check-square-o"></span> Uncheck All (' + cnt + ')');

                            $('.select-on-check-all').prop('checked', true); //.trigger('click');
                            $("input[name='selection[]']").prop('checked', true);
                        } else {
                            btn.html('<span class="fa fa-square-o"></span> Check All');
                        }
                    },
                    error: function (error) {
                        btn.html('<span class="fa fa-error text-danger"></span> Error ...');
                        alert('Request Error');
                    }
                });
            }
        }).on('click', '.btn-show-checked-ids', function(e) {
            let data = [];
            if (sessionStorage.selectedUserTasks) {
                data = jQuery.parseJSON(sessionStorage.selectedUserTasks);
                let arrIds = [];
                if (data) {
                    arrIds = Object.values(data);
                }
                alert('UserTask IDs (' + arrIds.length + ' items): ' + arrIds.join(', '));
            } else {
                alert('sessionStorage.selectedUserTasks = null');
            }
        }).on('change', '.select-on-check-all', function(e) {
            let checked = $('#user-task-list-grid').yiiGridView('getSelectedRows');
            let unchecked = $("input[name='selection[]']:not(:checked)").map(function () { return this.value; }).get();
            let data = [];

            if (sessionStorage.selectedUserTasks) {
                data = jQuery.parseJSON(sessionStorage.selectedUserTasks);
            }

            $.each( checked, function( key, value ) {
                if (typeof data[value.ut_id] === 'undefined') {
                    data[value.ut_id] = value.ut_id;
                }
            });

            $.each( unchecked, function( key, value ) {
                if (typeof data[value] !== 'undefined') {
                    delete(data[value]);
                }
            });

            sessionStorage.selectedUserTasks = JSON.stringify(data);
            refreshUserTaskSelectedState();
        });

        $(document).on('click', '.btn-multiple-delete', function() {
            let arrIds = [];
            if (confirm('Are you sure you want to delete selected UserTasks?')) {
                if (sessionStorage.selectedUserTasks) {
                    let data = jQuery.parseJSON(sessionStorage.selectedUserTasks);
                    arrIds = Object.values(data);

                    $.ajax({
                        url: multipleDeleteUrl,
                        type: 'post',
                        dataType: 'json',
                        cache: false,
                        data: {selectedUserTask: arrIds},
                        success: function (data) {
                            if (data.error) {
                                createNotify('Error', data.message, 'error');
                            }
                        }
                    });
                }
            }
        });

        $('#pjax-user-task-index').on('pjax:end', function() {
            refreshUserTaskSelectedState();
        });

        refreshUserTaskSelectedState();
    });
</script>
