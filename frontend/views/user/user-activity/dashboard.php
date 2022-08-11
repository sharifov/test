<?php

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $userTimeZone string */
/* @var $user Employee */
/* @var $startDateTime string */
/* @var $endDateTime string */
/* @var $scheduleEventList array|UserShiftSchedule[] */
/* @var $userActiveEvents array */
/* @var $userOnlineEvents array */
/* @var $userOnlineData array */


$this->title = 'My Activity' . ' (' . $user->username . ')';
$this->params['breadcrumbs'][] = $this->title;

\frontend\assets\FullCalendarAsset::register($this);
\frontend\assets\TimerAsset::register($this);
?>
<div class="task-list-index">
    <h1><i class="fa fa-flash"></i> <?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-12">
            <?= $this->render('partial/_timeline_events', [
                    'user' => $user,
                    'scheduleEventList' => $scheduleEventList,
                    'userOnlineEvents' => $userOnlineEvents,
                    'userActiveEvents' => $userActiveEvents,
                    'startDateTime' => $startDateTime,
                    'endDateTime' => $endDateTime,
                    'userOnlineData' => $userOnlineData,
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <?php Pjax::begin(['id' => 'pjax-user-timeline']); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

