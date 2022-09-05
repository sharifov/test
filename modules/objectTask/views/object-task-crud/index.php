<?php

use common\components\grid\DateTimeColumn;
use modules\objectTask\src\abac\ObjectTaskObject;
use modules\objectTask\src\entities\ObjectTask;
use modules\objectTask\src\services\ObjectTaskService;
use yii\bootstrap\ActiveForm;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\objectTask\src\entities\ObjectTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var \modules\objectTask\src\forms\ObjectTaskMultipleUpdateForm $multipleUpdateForm */
/** @var array $multipleErrors */
/** @var int $changedTasks */

$this->title = 'Object Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-task-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-object-task-list', 'timeout' => 15000, 'enablePushState' => true, 'enableReplaceState' => false, 'scrollTo' => 0]); ?>

    <?php if ($multipleErrors || $multipleUpdateForm->getErrors()) : ?>
        <div class="card multiple-update-summary" style="margin-bottom: 10px;">
            <div class="card-header">
                <span class="pull-right clickable close-icon"><i class="fa fa-times"> </i></span>
                Errors:
            </div>
            <div class="card-body">
                <?php foreach ($multipleErrors as $uuid => $multipleError) : ?>
                    <?= "ObjectTaskId: {$uuid}<br>"; ?>
                    <div>
                        <?php foreach ($multipleError as $error) : ?>
                            <?= VarDumper::dumpAsString($error) . '<br>'; ?>
                        <?php endforeach; ?>
                    </div><br>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        $js = <<<JS
            $('.close-icon').on('click', function(){    
                $('.multiple-update-summary').slideUp();
            })
        JS;
        $this->registerJs($js);
        ?>
    <?php endif; ?>

    <p>
        <div class="btn-group">
            <?= Html::button('<span class="fa fa-square-o"></span> Check All', ['class' => 'btn btn-default', 'id' => 'btn-check-all']); ?>
            <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu">
                <?= \yii\helpers\Html::a('<i class="fa fa-edit text-warning"></i> Multiple update', null, ['class' => 'dropdown-item btn-multiple-update', 'data-toggle' => 'modal', 'data-target' => '#modalUpdate']) ?>
                <div class="dropdown-divider"></div>
                <?= \yii\helpers\Html::a('<i class="fa fa-info text-info"></i> Show Checked IDs', null, ['class' => 'dropdown-item btn-show-checked-ids']) ?>
            </div>
        </div>
    </p>

    <?= GridView::widget([
        'id' => 'object-task-list',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'cssClass' => 'multiple-checkbox',
            ],
            ['class' => 'yii\grid\SerialColumn'],
            'ot_uuid',
            'ot_q_id',
            [
                'attribute' => 'ot_ots_id',
                'label' => 'Scenario',
                'value' => static function (ObjectTask $model) {
                    return ObjectTaskService::SCENARIO_LIST[$model->objectTaskScenario->ots_key];
                }
            ],
            'ot_group_hash',
            'ot_object',
            [
                'attribute' => 'ot_object_id',
                'format' => 'raw',
                'value' => static function (ObjectTask $model) {
                    if ($model->ot_object === ObjectTaskService::OBJECT_LEAD) {
                        return Html::a($model->ot_object_id, "/lead/view/{$model->lead->gid}", [
                            'target' => '_blank',
                            'data' => [
                                'pjax' => 0
                            ],
                        ]);
                    }

                    return $model->ot_object_id;
                }
            ],
            [
                'attribute' => 'ot_command',
                'value' => static function (ObjectTask $model) {
                    return ObjectTaskService::COMMAND_LIST[$model->ot_command];
                },
                'filter' => ObjectTaskService::COMMAND_LIST,
            ],
            [
                'attribute' => 'ot_status',
                'value' => static function (ObjectTask $model) {
                    return ObjectTask::STATUS_LIST[$model->ot_status] ?? '';
                },
                'filter' => ObjectTask::STATUS_LIST,
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'ot_execution_dt'],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ObjectTask $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ot_uuid' => $model->ot_uuid]);
                },
                'visibleButtons' => [
                    'update' => static function (ObjectTask $model, $key, $index) {
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_LIST, ObjectTaskObject::ACTION_UPDATE, Access to page object-task/object-task-crud/update */
                        return \Yii::$app->abac->can(
                            null,
                            ObjectTaskObject::ACT_OBJECT_TASK_LIST,
                            ObjectTaskObject::ACTION_UPDATE
                        );
                    },
                    'delete' => static function (ObjectTask $model, $key, $index) {
                        /** @abac ObjectTaskObject::ACT_OBJECT_TASK_LIST, ObjectTaskObject::ACTION_UPDATE, Access to page object-task/object-task-crud/delete */
                        return \Yii::$app->abac->can(
                            null,
                            ObjectTaskObject::ACT_OBJECT_TASK_LIST,
                            ObjectTaskObject::ACTION_DELETE
                        );
                    },
                ],
            ],
        ],
    ]); ?>

    <?php $form = ActiveForm::begin(['id' => 'object-task-update-form', 'method' => 'post', 'options' => ['data-pjax' => true]]); ?>

        <?= $form->errorSummary($multipleUpdateForm); ?>
        <?php
        Modal::begin(
            [
                'title' => 'Multiple update object task',
                'id' => 'modalUpdate',
                'size' => 'modal-md'
            ]
        );
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?= $form->field($multipleUpdateForm, 'statusId')->widget(\kartik\select2\Select2::class, [
                                    'data' => ObjectTask::getStatusList([]),
                                    'size' => \kartik\select2\Select2::SMALL,
                                    'options' => ['placeholder' => 'Select status', 'multiple' => false, 'class' => 'input_select2'],
                                    'pluginOptions' => ['allowClear' => true],
                                ]) ?>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <?= Html::submitButton('<i class="fa fa-check-square"></i> Update object tasks', ['id' => 'btn-submit-multiple-update', 'class' => 'btn btn-success']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?= $form->field($multipleUpdateForm, 'element_list_json')->hiddenInput(['id' => 'element_list_json'])->label(false) ?>

        <?php Modal::end(); ?>

    <?php ActiveForm::end(); ?>

    <?php
    $selectAllUrl = \yii\helpers\Url::to(array_merge(['index'], Yii::$app->getRequest()->getQueryParams(), ['act' => 'select-all']));
    ?>
    <script>
        var selectAllUrl = '<?= $selectAllUrl ?>';
    </script>

    <?php
    if ($changedTasks > 0) {
        $this->registerJs(
            "createNotifyByObject({title: 'Success', type: 'success', text: 'Tasks successfully edited: {$changedTasks}', hide: true});"
        );
    }
    ?>
    <?php Pjax::end(); ?>

</div>

<?php
$js = <<<JS
    sessionStorage.selectedElements = '{}';

    $(document).on('click', '.btn-multiple-update', function() {
        
        let arrIds = [];
        if (sessionStorage.selectedElements) {
            let data = jQuery.parseJSON(sessionStorage.selectedElements);
            arrIds = Object.values(data);
            
            $('#element_list_json').val(JSON.stringify(arrIds));
        }
    });

    $(document).on('pjax:start', function() {
        $("#modalUpdate").modal('hide');
        $('.modal-backdrop').remove();
    });
    
    $('#pjax-object-task-list').on('pjax:end', function() {
        refreshElementsSelectedState();
    });
    
    $('body').on('click', '#btn-check-all',  function (e) {
        let btn = $(this);
        
        if ($(this).hasClass('checked')) {
            btn.removeClass(['btn-warning', 'checked']).addClass('btn-default').html('<span class="fa fa-square-o"></span> Check All');
            $('.select-on-check-all').prop('checked', false);
            $("input[name='selection[]']:checked").prop('checked', false);
            sessionStorage.selectedElements = '{}';
        } else {
            btn.html('<span class="fa fa-spinner fa-spin"></span> Loading ...');
            
            $.ajax({
             type: 'post',
             dataType: 'json',
             url: selectAllUrl,
             success: function (data) {
                console.info(data);
                let cnt = Object.keys(data).length
                if (data) {
                    let jsonData = JSON.stringify(data);
                    sessionStorage.selectedElements = jsonData;
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

    
    function refreshElementsSelectedState() {
         if (sessionStorage.selectedElements) {
            let data = jQuery.parseJSON(sessionStorage.selectedElements);
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
        } else {
             sessionStorage.selectedElements = '{}';
        }
    }
    
    $('body').on('click', '.btn-show-checked-ids', function(e) {
       let data = [];
       if (sessionStorage.selectedElements) {
            data = jQuery.parseJSON(sessionStorage.selectedElements);
            let arrIds = [];
            if (data) {
                arrIds = Object.values(data);
            }
            alert('User IDs (' + arrIds.length + ' items): ' + arrIds.join(', '));
       } else {
           alert('sessionStorage.selectedElements = null');
       }
    });
    
    $('body').on('change', '.select-on-check-all', function(e) {
        let checked = $('#object-task-list').yiiGridView('getSelectedRows');
        let unchecked = $("input[name='selection[]']:not(:checked)").map(function () { return this.value; }).get();
        let data = {};
        
        if (sessionStorage.selectedElements) {
            data = jQuery.parseJSON(sessionStorage.selectedElements);
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
       
       sessionStorage.selectedElements = JSON.stringify(data);
       refreshElementsSelectedState();
    });

    refreshElementsSelectedState();
    
    function resetForm() {
        $('#object-task-update-form').trigger('reset');
        $('.input_select2').val('').trigger('change');
    }

    $('body').on('click', '.close', function(e) {
        resetForm();
    });
    $("#pjax-object-task-list").on("pjax:complete", function() {
        resetForm();
    });
JS;

$this->registerJs($js, \yii\web\View::POS_READY);
?>