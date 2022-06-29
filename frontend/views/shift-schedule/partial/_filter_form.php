<?php

/**
 * @var $this \yii\web\View
 * @var $timelineCalendarFilter TimelineCalendarFilter
 * @var $userGroups array
 **/

use common\models\query\UserGroupQuery;
use kartik\select2\Select2;
use kartik\time\TimePicker;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\TimelineCalendarFilter;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\widget\ShiftSelectWidget;
use src\auth\Auth;
use src\widgets\DateTimePicker;
use src\widgets\UserSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="x_panel">
  <div class="x_title">
    <h2><i class="fa fa-search"></i> Filter form</h2>
    <ul class="nav navbar-right panel_toolbox">
      <li>
        <a class="collapse-link"><i class="fa fa-chevron-<?= !$timelineCalendarFilter->appliedFilter ? 'down' : 'up' ?>"></i></a>
      </li>
    </ul>
    <div class="clearfix"></div>
  </div>
  <div class="x_content" <?= !$timelineCalendarFilter->appliedFilter ? 'style="display: none;"' : '' ?>>
      <div class="row">
          <div class="col-md-12">
              <?php $form = ActiveForm::begin(['id' => 'filter-calendar-form']); ?>
                  <?= $form->errorSummary($timelineCalendarFilter) ?>
                  <?= $form->field($timelineCalendarFilter, 'userId')->hiddenInput()->label(false) ?>
                  <?= $form->field($timelineCalendarFilter, 'startDate')->hiddenInput(['id' => 'startDate'])->label(false) ?>
                  <?= $form->field($timelineCalendarFilter, 'endDate')->hiddenInput(['id' => 'endDate'])->label(false) ?>
                  <?= $form->field($timelineCalendarFilter, 'collapsedResources')->hiddenInput(['id' => 'collapsedResources'])->label(false) ?>
                  <div class="row">
                      <div class="col-md-3">
                          <div class="row">
                              <div class="col-md-12">
                                  <?= $form->field($timelineCalendarFilter, 'startDateTime')->widget(DateTimePicker::class, [
                                      'clientOptions' => [
                                          'format' => 'yyyy-mm-dd hh:ii'
                                      ]
                                  ]) ?>
                                  <div class="row">
                                    <div class="col-md-12">
                                        <?= $form->field($timelineCalendarFilter, 'startDateTimeCondition')->radioList(TimelineCalendarFilter::getConditionNameList(), [
                                            'style' => 'font-weight: bold'
                                        ])->label(false) ?>
                                    </div>
                                  </div>
                              </div>
                              <div class="col-md-12">
                                  <?= $form->field($timelineCalendarFilter, 'endDateTime')->widget(DateTimePicker::class, [
                                      'clientOptions' => [
                                          'format' => 'yyyy-mm-dd hh:ii'
                                      ]
                                  ]) ?>
                                  <div class="row">
                                    <div class="col-md-12">
                                        <?= $form->field($timelineCalendarFilter, 'endDateTimeCondition')->radioList(TimelineCalendarFilter::getConditionNameList(), [
                                            'style' => 'font-weight: bold'
                                        ])->label(false) ?>
                                    </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-2">
                          <div class="row">
                              <div class="col-md-12">
                                  <?= $form->field($timelineCalendarFilter, 'usersIds')->widget(UserSelect2Widget::class, [
                                      'options' => [
                                          'multiple' => true,
                                      ],
                                      'size' => Select2::SMALL,
                                      'pluginOptions' => [
                                          'allowClear' => true,
                                      ],
                                      'id' => 'filter-users'
                                  ]) ?>

                                  <?= $form->field($timelineCalendarFilter, 'statuses')->widget(Select2::class, [
                                      'data' => UserShiftSchedule::getStatusList(),
                                      'options' => [
                                          'multiple' => true,
                                      ],
                                      'size' => Select2::SMALL,
                                  ]) ?>

                                  <?= $form->field($timelineCalendarFilter, 'scheduleTypes')->widget(Select2::class, [
                                      'data' => ShiftScheduleType::getList(true),
                                      'options' => [
                                          'multiple' => true,
                                      ],
                                      'size' => Select2::SMALL,
                                  ]) ?>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-2">
                          <?= $form->field($timelineCalendarFilter, 'shift')->widget(Select2::class, [
                              'data' => Shift::getList(true),
                              'options' => [
                                  'multiple' => true,
                              ],
                              'size' => Select2::SMALL,
                          ]) ?>

                          <?= $form->field($timelineCalendarFilter, 'duration')->widget(TimePicker::class, [
                              'pluginOptions' => [
                                  'showSeconds' => false,
                                  'showMeridian' => false,
                                  'minuteStep' => 1,
                                  'secondStep' => 5,
                                  'defaultTime' => false
                              ],
                          ]) ?>
                      </div>
                      <div class="col-md-5">
                          <?= $form->field($timelineCalendarFilter, 'userGroups')->widget(Select2::class, [
                              'data' => $userGroups,
                              'options' => [
                                  'multiple' => true
                              ],
                              'size' => Select2::SMALL,
                          ]) ?>
                      </div>
                      <input type="hidden" id="shift_calendar_tab_state" name="tab_state" value="">
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-md-12 text-center">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Submit', ['class' => 'btn btn-success btn-sm', 'id' => 'filter-calendar-form-btn']) ?>
                    </div>
                  </div>
              <?php ActiveForm::end(); ?>
          </div>
      </div>
  </div>
</div>
