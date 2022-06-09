<?php

use frontend\assets\UserShiftCalendarAsset;
use modules\shiftSchedule\src\abac\ShiftAbacObject;
use modules\shiftSchedule\src\entities\userShiftSchedule\search\TimelineCalendarFilter;
use src\auth\Auth;
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
$userTimeZone = Auth::user()->timezone;
$today = date('Y-m-d', strtotime('+1 day'));

$getEventsAjaxUrl           = Url::to(['/shift-schedule/ajax-get-events']);
$addMultipleEventsUrl       = Url::to(['/shift-schedule/add-multiple-events']);
$addEventUrl                = Url::to(['/shift-schedule/add-event']);
$updateEventUrl             = Url::to(['/shift-schedule/ajax-update-event']);
$deleteEventUrl             = Url::to(['/shift-schedule/delete-event']);
$eventDetailsUrl            = Url::to(['/shift-schedule/ajax-event-details']);
$viewLogsUrl                = Url::to(['/shift-schedule/ajax-get-logs']);
$multipleDeleteUrl          = Url::to(['/shift-schedule/ajax-multiple-delete']);
$multipleUpdateUrl          = Url::to(['/shift-schedule/ajax-multiple-update']);
$editEventUrl               = Url::to(['/shift-schedule/ajax-edit-event-form']);

/** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE, Create user shift schedule event */
$canCreate = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_CREATE);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE, Access to delete event in calendar widget */
$canDelete = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_DELETE);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_UPDATE, Access to update event */
$canUpdate = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_UPDATE);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_VIEW_EVENT_LOG, Access to view event logs event */
$canViewLogs = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_VIEW_EVENT_LOG);
/** @abac ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_SOFT_DELETE, Access to soft delete event event */
$canSoftDelete = \Yii::$app->abac->can(null, ShiftAbacObject::OBJ_USER_SHIFT_EVENT, ShiftAbacObject::ACTION_SOFT_DELETE);

$js = <<<JS
$(document).ready( function () {
    var App = window.App;
    var multipleModule = new App.MultipleManageModule(
        Boolean('$canCreate'), 
        Boolean('$canUpdate'), 
        Boolean('$canSoftDelete'), 
        Boolean('$canDelete'), 
        '$addMultipleEventsUrl',
        '$multipleUpdateUrl',
        '$multipleDeleteUrl'
    );
    var formFilter = new App.TimelineFormFilter('filter-calendar-form');
    var tooltip = new App.TimelineTooltip(
        Boolean('$canUpdate'), 
        Boolean('$canViewLogs'), 
        Boolean('$canSoftDelete'),
        Boolean('$canDelete'),
        '$eventDetailsUrl',
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
        '$addEventUrl',
        '$updateEventUrl',
        tooltip,
        '$today'
    );
    window._timeline.init({
        userTimeZone: '$userTimeZone',
        canCreate: Boolean('$canCreate'),
        dragToCreate: Boolean('$canCreate'),
        canUpdate: Boolean('$canUpdate'),
        dragToResize: Boolean('$canUpdate'),
        tooltipElementSelectorId: 'calendar-tooltip-wrapper',
        multipleModuleElementSelectorId: 'calendar-multiple-module-wrapper',
    });
});
JS;
$this->registerJs($js);
