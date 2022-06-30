<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\components\grid\Select2Column;
use modules\taskList\abac\TaskListAbacObject;
use yii\web\JsExpression;
use modules\objectSegment\src\entities\ObjectSegmentList;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\ActionColumn;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \modules\objectSegment\src\entities\search\ObjectSegmentListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $importCount int */
/* @var $objectTypeList array */

$this->title                   = 'Object Segment List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abac-policy-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(
            '<i class="fa fa-plus"></i>
 Create Object Segment',
            ['create'],
            ['class' => 'btn btn-success']
        ) ?>

    </p>

    <p>
        <?= Html::a('<i class="fa fa-remove"></i> Reset Cache', ['invalidate-cache'], ['class' => 'btn btn-warning']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0, 'id' => 'object-segment-list-pjax']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions'   => static function (ObjectSegmentList $model) {
            if (!$model->osl_enabled) {
                return ['class' => 'danger'];
            }
        },
        'columns'      => [
            [
                'attribute' => 'osl_id',
                'options'   => [
                    'style' => 'width:80px'
                ],
            ],
            [
                'attribute' => 'osl_title',
            ],
            [
                'label' => 'Task List Assigned',
                'attribute' => 'taskAssigned',
                'class' => Select2Column::class,
                'value' => function (ObjectSegmentList $model) {
                    $tasks = [];

                    if (!empty($model->objectSegmentTaskAssigns)) {
                        foreach ($model->objectSegmentTaskAssigns as $item) {
                            $tasks[] = Html::tag(
                                'span',
                                Html::encode($item->taskList->tl_title),
                                ['class' => 'label label-default', 'style' => 'font-size: 11px;']
                            );
                        }
                    }

                    return implode(' ', $tasks);
                },
                'data' => \modules\taskList\src\services\TaskListService::getTaskObjectList(),
                'filter' => true,
                'id' => 'shift-filter',
                'options' => ['width' => '300px'],
                'pluginOptions' => ['allowClear' => true],
                'format' => 'raw',
            ],
            [
                'label'  => 'Status',
                'value'  => static function (ObjectSegmentList $model) use ($objectTypeList) {
                    $exist = in_array($model->osl_ost_id, array_flip($objectTypeList));
                    if (!$exist) {
                        return '<span class="badge badge-danger" title="Invalid object (not exists)">Error</span>';
                    }
                    return '';
                },
                'format' => 'raw',
            ],
            [
                'class'     => Select2Column::class,
                'label'     => 'Object Type',
                'attribute' => 'osl_ost_id',
                'value'     => static function (ObjectSegmentList $model) {
                    return $model->oslObjectSegmentType->ost_key;
                },
                'id'        => 'object-segment-list-filter',
                'data'      => $objectTypeList,
                'format'    => 'raw',
            ],
            'osl_enabled:boolean',
            [
                'attribute' => 'osl_is_system',
                'format' => 'booleanByLabel',
                'filter' =>  [1 => 'Yes', 0 => 'No']
            ],
            [
                'class'       => UserSelect2Column::class,
                'attribute'   => 'osl_updated_user_id',
                'relation'    => 'oslUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class'     => DateTimeColumn::class,
                'attribute' => 'osl_updated_dt',
            ],
            [
                'class'    => ActionColumn::class,
                'template' => '{view} {update} {viewSegmentRules} {assign} {delete}',
                'buttons'  => [
                    'viewSegmentRules' => function ($action, $model, $key) {
                        $url = Url::toRoute(['/object-segment/object-segment-rule/?ObjectSegmentRuleSearch[osr_osl_id]=' . $model->osl_id]);
                        return Html::a('<i class="fa fa-toggle-down"></i>', $url, ['title' => 'View Rules', 'data-pjax' => 0, 'target' => '_blank']);
                    },
                    'assign' => static function ($url, ObjectSegmentList $model, $key) {
                        return Html::a(
                            '<span class="fa fa-user-plus"></span>',
                            '#',
                            [
                                'class' => 'js_edit_usha',
                                'title' => 'Edit User Shift Assign',
                                'data-url' => Url::to(['assign-form', 'id' => $model->osl_id]),
                                'data-id' => $model->osl_id,
                                'data-type-id' => $model->oslObjectSegmentType->ost_id,
                            ],
                        );
                    },
                ],
                'visibleButtons' => [
                    'assign' => static function ($model, $key, $index) {
                        /** @abac TaskListAbacObject::UI_ASSIGN, TaskListAbacObject::ACTION_ACCESS, Access to button Task List assign */
                        return \Yii::$app->abac->can(
                            null,
                            TaskListAbacObject::UI_ASSIGN,
                            TaskListAbacObject::ACTION_ACCESS
                        );
                    },
                    'delete' => static function (ObjectSegmentList $model) {
                        return !$model->osl_is_system;
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<?php
yii\bootstrap4\Modal::begin([
    'title' => '',
    'id' => 'object_segment_list_assign_modal',
    'size' => \yii\bootstrap4\Modal::SIZE_DEFAULT,
]);
yii\bootstrap4\Modal::end();
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('click', '.js_edit_usha', function() {
            let urlAssign = $(this).data('url'),
                objectSegmentId = $(this).data('id'),
                taskIds = $(this).data('tasks'),
                objectTypeId = $(this).data('type-id');

            $.ajax({
                url: urlAssign,
                type: 'POST',
                dataType: 'json',
                data: {objectSegmentId: objectSegmentId, objectTypeId: objectTypeId, taskIds: taskIds}
            }).done(function(dataResponse) {
                if (dataResponse.status === 1) {
                    let modalBodyEl = $('#object_segment_list_assign_modal .modal-body');
                    modalBodyEl.html(dataResponse.data);
                    $('#object_segment_list_assign_modal-label').html('Object Segment List Assign');
                    $('#object_segment_list_assign_modal').modal('show');
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
