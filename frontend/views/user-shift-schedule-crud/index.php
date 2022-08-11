<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use frontend\widgets\DateTimePickerWidget;
use kartik\select2\Select2;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use src\widgets\UserSelect2Widget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap4\Html;
use yii\bootstrap4\Modal;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var $multipleForm \modules\shiftSchedule\src\forms\UserShiftScheduleMultipleUpdateForm */
/** @var $multipleErrors array */

$this->title = 'User Shift Schedules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-shift-schedule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create User Shift Schedule', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-user-shift-schedule', 'timeout' => 8000, 'enablePushState' => true, 'enableReplaceState' => false, 'scrollTo' => 0]); ?>

    <?= $this->render('_search', ['model' => $searchModel]);?>

    <p>
        <div class="btn-group">
            <?= Html::button('<span class="fa fa-square-o"></span> Check All', ['class' => 'btn btn-default', 'id' => 'btn-check-all']); ?>

            <button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu">
                <?= \yii\helpers\Html::a('<i class="fa fa-edit text-warning"></i> Multiple update', null, ['class' => 'dropdown-item btn-multiple-update', 'data-toggle' => 'modal', 'data-target' => '#modalUpdate' ])?>
                <div class="dropdown-divider"></div>
                <?= \yii\helpers\Html::a('<i class="fa fa-info text-info"></i> Show Checked IDs', null, ['class' => 'dropdown-item btn-show-checked-ids'])?>
            </div>
        </div>
    </p>

    <?= GridView::widget([
        'id' => 'user-shift-schedule-list-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'cssClass' => 'multiple-checkbox',
                'checkboxOptions' => function (UserShiftSchedule $model) {
                    return ['value' => $model->uss_id];
                }
            ],
            [
                'attribute' => 'uss_id',
                'options' => ['style' => 'width:100px']
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'uss_user_id',
                'relation' => 'user',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            //'uss_sst_id',
            [
                'attribute' => 'uss_sst_id',
                'value' => static function (
                    UserShiftSchedule $model
                ) {
                    return $model->getScheduleTypeTitle();
                },
                'filter' => ShiftScheduleType::getList()
            ],
            //'uss_ssr_id',

//            'uss_description',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'uss_start_utc_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'attribute' => 'uss_duration',
                'options' => ['style' => 'width:100px']
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'uss_end_utc_dt',
                'format' => 'byUserDateTime'
            ],
//            'uss_duration',

            [
                'attribute' => 'uss_shift_id',
                'value' => static function (
                    UserShiftSchedule $model
                ) {
                    return $model->getShiftName();
                },
                'filter' => Shift::getList()
            ],

            [
                'attribute' => 'uss_ssr_id',
                'value' => static function (
                    UserShiftSchedule $model
                ) {
                    return $model->getRuleTitle();
                },
                'filter' => ShiftScheduleRule::getList()
            ],
            //'uss_status_id',
            [
                'attribute' => 'uss_status_id',
                'value' => static function (
                    UserShiftSchedule $model
                ) {
                    return $model->getStatusName();
                },
                'filter' => UserShiftSchedule::getStatusList()
            ],
            [
                'attribute' => 'uss_type_id',
                'value' => static function (
                    UserShiftSchedule $model
                ) {
                    return $model->getTypeName();
                },
                'filter' => UserShiftSchedule::getTypeList()
            ],
//            'uss_type_id',
            'uss_customized:boolean',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'uss_created_dt',
                'format' => 'byUserDateTime'
            ],
//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'uss_updated_dt',
//                'format' => 'byUserDateTime'
//            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'uss_created_user_id',
                'relation' => 'createdUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
//            [
//                'class' => UserSelect2Column::class,
//                'attribute' => 'uss_updated_user_id',
//                'relation' => 'updatedUser',
//                'format' => 'username',
//                'placeholder' => 'Select User'
//            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php $form = ActiveForm::begin(['id' => 'user-list-update-form', 'method' => 'post', 'options' => ['data-pjax' => true]]); ?>


    <?= $form->errorSummary($multipleForm) ?>


    <?php if ($multipleErrors || $multipleForm->getErrors()) : ?>
        <div class="card multiple-update-summary" style="margin-bottom: 10px;">
            <div class="card-header">
                <span class="pull-right clickable close-icon"><i class="fa fa-times"> </i></span>
                Errors:
            </div>
            <div class="card-body">
                <?php
                foreach ($multipleErrors as $shiftId => $multipleError) {
                    echo 'ShiftId: ' . $shiftId . ' <br>';
                    echo VarDumper::dumpAsString($multipleError) . ' <br><br>';
                }
                ?>
                <?= $multipleForm->getErrors() ? VarDumper::dumpAsString($multipleForm->getErrorSummary(true)) : '' ?>
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
    <?php endif;?>

        <?php
        Modal::begin([
            'title' => 'Multiple update selected Shifts',
            'id' => 'modalUpdate'
        ]);
        ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">

                                <?= $form->field($multipleForm, 'uss_sst_id')->widget(Select2::class, [
                                    'data' => ShiftScheduleType::getList(),
                                    'size' => Select2::SMALL,
                                    'options' => ['placeholder' => 'Select schedule type'],
                                    'pluginOptions' => ['allowClear' => true],
                                ]) ?>

                                <?= $form->field($multipleForm, 'uss_type_id')->widget(Select2::class, [
                                    'data' => UserShiftSchedule::getTypeList(),
                                    'size' => Select2::SMALL,
                                    'options' => ['placeholder' => 'Select type'],
                                    'pluginOptions' => ['allowClear' => true],
                                ]) ?>

                                <?= $form->field($multipleForm, 'uss_status_id')->widget(Select2::class, [
                                    'data' => UserShiftSchedule::getStatusList(),
                                    'size' => Select2::SMALL,
                                    'options' => ['placeholder' => 'Select status'],
                                    'pluginOptions' => ['allowClear' => true],
                                ]) ?>

                                <?= $form->field($multipleForm, 'uss_shift_id')->widget(Select2::class, [
                                    'data' => Shift::getList(),
                                    'size' => Select2::SMALL,
                                    'options' => ['placeholder' => 'Select shift'],
                                    'pluginOptions' => ['allowClear' => true],
                                ]) ?>
                            </div>

                            <div class="col">
                                <?= $form->field($multipleForm, 'uss_start_utc_dt')->widget(DateTimePickerWidget::class, [
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd hh:ii:ss',
                                        'todayBtn' => true
                                    ]
                                ]) ?>

                                <?= $form->field($multipleForm, 'uss_end_utc_dt')->widget(DateTimePickerWidget::class, [
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd hh:ii:ss',
                                        'todayBtn' => true
                                    ]
                                ]) ?>

                                <?= $form->field($multipleForm, 'uss_ssr_id')->widget(Select2::class, [
                                    'data' => ShiftScheduleRule::getList(),
                                    'size' => Select2::SMALL,
                                    'options' => ['placeholder' => 'Select rule'],
                                    'pluginOptions' => ['allowClear' => true],
                                ]) ?>

                                <?= $form->field($multipleForm, 'uss_user_id')->widget(UserSelect2Widget::class) ?>
                            </div>
                            <?= $form->field($multipleForm, 'shift_list_json')->hiddenInput(['id' => 'shift_list_json'])->label(false) ?>
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <?= Html::submitButton('<i class="fa fa-check-square"></i> Update selected Shifts', ['id' => 'btn-submit-multiple-update', 'class' => 'btn btn-success']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php Modal::end(); ?>

        <?php ActiveForm::end(); ?>
        <?php
        $selectAllUrl = Url::current(['act' => 'select-all']);
        ?>
        <script>
            var selectAllUrl = '<?=$selectAllUrl?>';
        </script>
    <?php Pjax::end(); ?>

</div>

<script>
    sessionStorage.selectedShifts = '{}';
    document.addEventListener('DOMContentLoaded', function() {
        function refreshUserSelectedState() {
            if (sessionStorage.selectedShifts) {
                let data = jQuery.parseJSON(sessionStorage.selectedShifts);
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
                sessionStorage.selectedShifts = '{}';
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
                            sessionStorage.selectedShifts = jsonData;
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
            if (sessionStorage.selectedShifts) {
                data = jQuery.parseJSON(sessionStorage.selectedShifts);
                let arrIds = [];
                if (data) {
                    arrIds = Object.values(data);
                }
                alert('User IDs (' + arrIds.length + ' items): ' + arrIds.join(', '));
            } else {
                alert('sessionStorage.selectedShifts = null');
            }
        }).on('change', '.select-on-check-all', function(e) {
            let checked = $('#user-shift-schedule-list-grid').yiiGridView('getSelectedRows');
            let unchecked = $("input[name='selection[]']:not(:checked)").map(function () { return this.value; }).get();
            let data = [];

            if (sessionStorage.selectedShifts) {
                data = jQuery.parseJSON(sessionStorage.selectedShifts);
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

            sessionStorage.selectedShifts = JSON.stringify(data);
            refreshUserSelectedState();
        });

        $(document).on('click', '.btn-multiple-update', function() {
            let arrIds = [];
            if (sessionStorage.selectedShifts) {
                let data = jQuery.parseJSON(sessionStorage.selectedShifts);
                arrIds = Object.values(data);

                $('#shift_list_json').val(JSON.stringify(arrIds));
            }
        });

        $('#pjax-user-shift-schedule').on('pjax:end', function() {
            refreshUserSelectedState();
        });

        $(document).on('pjax:start', function() {
            $("#modalUpdate .close").click();
        });

        refreshUserSelectedState();
    });
</script>