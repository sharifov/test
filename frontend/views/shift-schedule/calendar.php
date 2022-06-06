<?php

use frontend\assets\UserShiftCalendarAsset;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\TimelineCalendarFilter;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $timelineCalendarFilter TimelineCalendarFilter */
/* @var $userGroups array */

$this->title = 'Users Shift Calendar';
$this->params['breadcrumbs'][] = $this->title;
$bundle = UserShiftCalendarAsset::register($this);
?>
<div class="shift-schedule-calendar">
    <h1><i class="fa fa-calendar"></i> <?= Html::encode($this->title) ?></h1>

    <?= $this->render('partial/_filter_form', [
        'timelineCalendarFilter' => $timelineCalendarFilter,
        'userGroups' => $userGroups
    ]) ?>

    <?php if (!empty($timelineCalendarFilter->userGroups)) : ?>
        <div id="calendar-multiple-module-wrapper"></div>

        <div class="row">
            <div class="col-md-12" id="calendar-wrapper">
              <div id="calendar" class="ssc"></div>
            </div>
        </div>

        <div id="calendar-tooltip-wrapper"></div>
    <?php else : ?>
        <?= \yii\bootstrap4\Alert::widget([
            'options' => [
                'class' => 'alert-warning',
            ],
            'body' => 'You dont have an associated group. In this case, you cannot view calendar events',
        ]) ?>
    <?php endif; ?>
</div>

<?php
$userTimeZone = \src\auth\Auth::user()->timezone;
$today = date('Y-m-d', strtotime('+1 day'));

$getEventsAjaxUrl           = Url::to(['shift-schedule/calendar-events-ajax']);
$modalUrl                   = Url::to(['/shift-schedule/add-event']);
$formCreateSingleEventUrl   = Url::to(['/shift-schedule/add-single-event']);
$formUpdateSingleEvent      = Url::to(['/shift-schedule/update-single-event']);
$deleteEventUrl             = Url::to(['/shift-schedule/delete-event']);
$openModalEventUrl          = Url::to(['shift-schedule/get-event']);
$viewLogsUrl                = Url::to(['shift-schedule/ajax-get-logs']);
$multipleDeleteUrl          = Url::to(['shift-schedule/ajax-multiple-delete']);
$multipleUpdateUrl          = Url::to(['/shift-schedule/ajax-multiple-update']);
$editEventUrl               = Url::to(['shift-schedule/ajax-edit-event-form']);

/** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE, Create user shift schedule event */
$canMultipleAdd = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE_ON_DOUBLE_CLICK, Access to create event on double click */
$canCreateOnDoubleClick = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE_ON_DOUBLE_CLICK);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_PERMANENTLY_DELETE, Access to permanently delete event in calendar widget */
$canPermanentlyDeleteEvent = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_PERMANENTLY_DELETE);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_PERMANENTLY_DELETE_EVENTS, Access to delete multiple events permanently */
$canMultiplePermanentlyDeleteEvents = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_PERMANENTLY_DELETE_EVENTS);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_UPDATE_EVENTS, Access to multiple update events */
$canMultipleUpdate = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_UPDATE_EVENTS);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_DELETE_EVENTS, Access to multiple delete events */
$canMultipleDelete = Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_DELETE_EVENTS);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_DELETE_EVENTS, Access to update event */
$canUpdate = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_UPDATE);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_DELETE_EVENTS, Access to view event logs event */
$canViewLogs = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_VIEW_EVENT_LOG);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_CALENDAR, ShiftAbacObject::ACTION_MULTIPLE_DELETE_EVENTS, Access to delete event event */
$canDeleteEvent = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE);


$js = <<<JS
$(document).ready( function () {
    var App = window.App;
    var multipleModule = new App.MultipleManageModule(
        Boolean('$canMultipleAdd'), 
        Boolean('$canMultipleDelete'), 
        Boolean('$canMultipleDelete'), 
        Boolean('$canMultiplePermanentlyDeleteEvents'), 
        '$modalUrl',
        '$multipleUpdateUrl',
        '$multipleDeleteUrl'
    );
    var formFilter = new App.TimelineFormFilter('filter-calendar-form');
    var tooltip = new App.TimelineTooltip(
        Boolean('$canUpdate'), 
        Boolean('$canViewLogs'), 
        Boolean('$canDeleteEvent'),
        Boolean('$canPermanentlyDeleteEvent'),
        '$openModalEventUrl',
        '$viewLogsUrl',
        '$editEventUrl',
        '$deleteEventUrl'
    );
    
    window._timeline = new App.ShiftTimeline(
        'calendar',
        'calendar-wrapper',
        formFilter,
        multipleModule,
        '$getEventsAjaxUrl',
        '$formCreateSingleEventUrl',
        '$formUpdateSingleEvent',
        tooltip,
        '$today'
    );
    window._timeline.init({
        userTimeZone: '$userTimeZone',
        canCreate: Boolean('$canCreateOnDoubleClick'),
        dragToCreate: Boolean('$canCreateOnDoubleClick'),
        canUpdate: Boolean('$canUpdate'),
        dragToResize: Boolean('$canUpdate'),
        tooltipElementSelectorId: 'calendar-tooltip-wrapper',
        multipleModuleElementSelectorId: 'calendar-multiple-module-wrapper',
    });
});
JS;
$this->registerJs($js);
