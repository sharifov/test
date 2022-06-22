<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\Select2Column;
use common\components\grid\UserSelect2Column;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\TaskObject;
use modules\taskList\src\objects\TargetObjectList;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\taskList\src\entities\taskList\search\TaskListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Task Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Task List Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'task-list-pjax']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => static function (TaskList $model) {
            if ($model->tl_enable_type === TaskList::ET_DISABLED) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'tl_id',
                'value' => static function (TaskList $model) {
                    return $model->tl_id;
                },
                'options' => ['style' => 'width:100px']
            ],
            //'tl_id',
            'tl_title',
            [
                'label' => 'Object Segment Assigned',
                'attribute' => 'objectSegmentAssigned',
                'class' => Select2Column::class,
                'value' => function (TaskList $model) {
                    $objectSegments = [];

                    if (!empty($model->objectSegmentTaskAssigns)) {
                        foreach ($model->objectSegmentTaskAssigns as $item) {
                            $objectSegments[] = Html::tag(
                                'span',
                                Html::encode($item->objectSegmentList->osl_title),
                                ['class' => 'label label-default', 'style' => 'font-size: 11px;']
                            );
                        }
                    }

                    return implode(' ', $objectSegments);
                },
                'data' => ObjectSegmentList::getListCache(),
                'filter' => true,
                'id' => 'assign-filter',
                'options' => ['width' => '300px'],
                'pluginOptions' => ['allowClear' => true],
                'format' => 'raw',
            ],
//            'tl_object',
            [
                'attribute' => 'tl_object',
                'filter' => TaskObject::getObjectList()
            ],

            [
                'attribute' => 'tl_target_object_id',
                'filter' => TargetObjectList::getAllTargetObjectList(),
                'value' => static function (TaskList $model) {
                    return $model->getTargetObjectName();
                }
            ],


            'tl_condition',
            //'tl_condition_json',
            //'tl_params_json',
            'tl_duration_min',
            [
                'attribute' => 'tl_enable_type',
                'value' => static function (TaskList $model) {
                    return $model->getEnableTypeLabel();
                },
                'format' => 'raw',
                'filter' => TaskList::getEnableTypeList()
            ],
            [
                'attribute' => 'tl_cron_expression',
                'value' => static function (TaskList $model) {
                    return $model->tl_cron_expression ?
                        Html::tag('pre', Html::encode($model->tl_cron_expression)) : '-';
                },
                'format' => 'raw',
            ],
            // 'tl_cron_expression',
//            'tl_sort_order',
//            'tl_updated_dt',
//            'tl_updated_user_id',

            [
                'attribute' => 'tl_sort_order',
                'options' => ['style' => 'width:100px']
            ],

            ['class' => UserSelect2Column::class, 'attribute' => 'tl_updated_user_id', 'relation' => 'tlUpdatedUser'],
            ['class' => DateTimeColumn::class, 'attribute' => 'tl_updated_dt'],

            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {assign} {delete}',
                'urlCreator' => function ($action, TaskList $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'tl_id' => $model->tl_id]);
                },
                'buttons'  => [
                    'assign' => static function ($url, TaskList $model, $key) {
                        return Html::a(
                            '<span class="fa fa-user-plus"></span>',
                            '#',
                            [
                                'class' => 'js_edit_tl',
                                'title' => 'Edit Task List Assign',
                                'data-url' => Url::to(['assign-form']),
                                'data-id' => $model->tl_id,
                                'data-type-id' => $model->tl_target_object_id,
                            ],
                        );
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
yii\bootstrap4\Modal::begin([
    'title' => '',
    'id' => 'task_list_assign_modal',
    'size' => \yii\bootstrap4\Modal::SIZE_DEFAULT,
]);
yii\bootstrap4\Modal::end();
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('click', '.js_edit_tl', function() {
            let urlAssign = $(this).data('url'),
                taskListId = $(this).data('id'),
                objectTypeId = $(this).data('type-id');

            $.ajax({
                url: urlAssign,
                type: 'POST',
                dataType: 'json',
                data: {taskListId: taskListId, objectTypeId: objectTypeId}
            }).done(function(dataResponse) {
                if (dataResponse.status === 1) {
                    let modalBodyEl = $('#task_list_assign_modal .modal-body');
                    modalBodyEl.html(dataResponse.data);
                    $('#task_list_assign_modal-label').html('Object Segment List Assign');
                    $('#task_list_assign_modal').modal('show');
                } else if (dataResponse.message.length) {
                    createNotify('Error', dataResponse.message, 'error');
                } else {
                    createNotify('Error', 'Error, please check logs', 'error');
                }
            }).fail(function(error) {
                console.error(error);
                alert('Request Error');
            }).always(function() {});
        });
    });
</script>
