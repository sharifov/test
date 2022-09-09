<?php

use common\models\Employee;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\user\userActivity\entity\search\UserActivitySearch;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $user Employee */

/* @var $startDateTime string */
/* @var $endDateTime string */
/* @var $startDateTimeCalendar string */
/* @var $endDateTimeCalendar string */

/* @var $scheduleEventList array|UserShiftSchedule[] */
/* @var $userActiveEvents array */
/* @var $userOnlineEvents array */
/* @var $userOnCallEvents array */
/* @var $userOnlineData array */
/* @var $summary array */
/* @var $searchModel UserActivitySearch */


//$this->title = 'User Activity' . ' (' . $user->username . ')';
$this->params['breadcrumbs'][] = $this->title;

\frontend\assets\FullCalendarAsset::register($this);
\frontend\assets\TimerAsset::register($this);

$startDateTimeFormat = date('d-M [H:i]', strtotime($startDateTimeCalendar));
$endDateTimeFormat = date('d-M [H:i]', strtotime($endDateTimeCalendar));

?>
<div class="task-list-index">
    <h1><i class="fa fa-clock-o"></i> <?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['id' => 'pjax-user-timeline']); ?>
    <div class="row">
        <div class="col-md-12">
        <?php echo $this->render('partial/_dashboard_search', ['model' => $searchModel]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h5>Summary from <?=$startDateTimeFormat?> to <?=$endDateTimeFormat?>: </h5>
        </div>
        <div class="col-md-6 text-right">
            <button class="btn btn-info" id="btn-show-help"><i class="fa fa-info-circle"></i> Help Info</button>
        </div>

        <?php
            yii\bootstrap4\Modal::begin([
                'title' => '<i class="fa fa-info-circle"></i> Help ',
                'id' => 'modal_help',
                'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
            ]);
            ?>
            <p></p>
            <div class="row">
                <div class="col-md-8">
                    <img src="/img/user-activity/user-activity-tl1.png" title="user-activity 1" style="width: 96%"/>
                </div>
                <div class="col-md-4">
                    <p><h5>Displaying the period when the agent started working earlier and overworked from the planned shift work schedule</h5></p>
                </div>
            </div>
            <p></p>
            <div class="row">
                <div class="col-md-8">
                    <img src="/img/user-activity/user-activity-tl2.png" title="user-activity 2" style="width: 96%"/>
                </div>
                <div class="col-md-4">
                    <p><h5>Displaying the period when the agent started working earlier and left earlier than the scheduled shift work schedule</h5></p>
                </div>
            </div>
        <p></p>
            <div class="row">
                <div class="col-md-8">
                    <img src="/img/user-activity/user-activity-tl3.png" title="user-activity 3" style="width: 96%"/>
                </div>
                <div class="col-md-4">
                    <p><h5>Displaying the period when the agent was late and left earlier than the scheduled work schedule for the shift</h5></p>
                </div>
            </div>

        <?php
            yii\bootstrap4\Modal::end();
        ?>

    </div>
    <div class="row">
        <div class="col-md-12">


            <div class="x_panel">
                <div class="tile_count">
                    <div class="col-md-1 col-sm-4  tile_stats_count dev-tile-adjust">
                        <span class="count_top"><i class="fa fa-clock-o"></i> Online Time</span>
                        <div class="count" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="<?= empty($summary['online']) ? 0 : Yii::$app->formatter->asDuration($summary['online'] * 60)?>">
                            <?= empty($summary['online']) ? 0 : Yii::$app->formatter->asHoursDuration($summary['online'], '%02d:%02d') ?>
                        </div>
                        <span class="count_bottom"> Selected time</span>
                    </div>

                    <div class="col-md-1 col-sm-4  tile_stats_count dev-tile-adjust">
                        <span class="count_top"><i class="fa fa-clock-o"></i> Useful Time </span>
                        <div class="count" title="<?= empty($summary['UsefulTime']) ? 0 : Yii::$app->formatter->asDuration($summary['UsefulTime'] * 60)?>">
                            <?= empty($summary['UsefulTime']) ? 0 : Yii::$app->formatter->asHoursDuration($summary['UsefulTime'], '%02d:%02d') ?>
                        </div>
                        <span class="count_bottom"> by Shift</span>
                    </div>

                    <div class="col-md-2 col-sm-4  tile_stats_count dev-tile-adjust">
                        <span class="count_top"><i class="fa fa-clock-o"></i> Activity Time </span>
                        <div class="count green" title="<?= empty($summary['activity']) ? 0 : Yii::$app->formatter->asDuration($summary['activity'] * 60)?>">
                            <?= empty($summary['activity']) ? 0 : Yii::$app->formatter->asHoursDuration($summary['activity'], '%02d:%02d') ?>
                        </div>
                        <span class="count_bottom"> Selected time</span>
                    </div>

                    <div class="col-md-1 col-sm-4  tile_stats_count dev-tile-adjust">
                        <span class="count_top" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="ES - indicates cumulative time of employee's late arrivals on the assigned shift">
                            <i class="fa fa-clock-o"></i> (ES) Early Start</span>
                        <div class="count warning" title="<?= empty($summary['EarlyStart']) ? 0 : Yii::$app->formatter->asDuration($summary['EarlyStart'] * 60)?>">
                            <?= empty($summary['EarlyStart']) ? 0 : Yii::$app->formatter->asHoursDuration($summary['EarlyStart'], '%02d:%02d') ?>
                        </div>
                        <span class="count_bottom"><i class="green"></i> on Shift</span>
                    </div>

                    <div class="col-md-1 col-sm-4  tile_stats_count dev-tile-adjust">
                        <span class="count_top" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="EF - indicates cumulative time of employee's early arrivals on the assigned shift">
                            <i class="fa fa-clock-o"></i> (EF) Early Finish</span>
                        <div class="count red" title="<?= empty($summary['EarlyFinish']) ? 0 : Yii::$app->formatter->asDuration($summary['EarlyFinish'] * 60)?>">
                            <?= empty($summary['EarlyFinish']) ? 0 : Yii::$app->formatter->asHoursDuration($summary['EarlyFinish'], '%02d:%02d') ?>
                        </div>
                        <span class="count_bottom"><i class="green"></i> on Shift</span>
                    </div>

                    <div class="col-md-1 col-sm-4  tile_stats_count dev-tile-adjust">
                        <span class="count_top" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="LS - indicates cumulative time of employee's late arrivals on the assigned shift">
                            <i class="fa fa-clock-o"></i> (LS) Late Start</span>
                        <div class="count red" title="<?= empty($summary['LateStart']) ? 0 : Yii::$app->formatter->asDuration($summary['LateStart'] * 60)?>">
                            <?= empty($summary['LateStart']) ? 0 : Yii::$app->formatter->asHoursDuration($summary['LateStart'], '%02d:%02d') ?>
                        </div>
                        <span class="count_bottom"> on Shift</span>
                    </div>

                    <div class="col-md-2 col-sm-4  tile_stats_count dev-tile-adjust">
                        <span class="count_top" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="LF - indicates cumulative overtime worked by the employee beyond the assigned shift">
                            <i class="fa fa-clock-o"></i> (LF) Late Finish</span>
                        <div class="count warning" title="<?= empty($summary['LateFinish']) ? 0 : Yii::$app->formatter->asDuration($summary['LateFinish'] * 60)?>">
                            <?= empty($summary['LateFinish']) ? 0 : Yii::$app->formatter->asHoursDuration($summary['LateFinish'], '%02d:%02d') ?>
                        </div>
                        <span class="count_bottom"> on Shift</span>
                    </div>

                    <div class="col-md-1 col-sm-4  tile_stats_count dev-tile-adjust">
                        <span class="count_top"><i class="fa fa-clock-o"></i> On Call Time </span>
                        <div class="count" style="color: #fd6a02" title="<?= empty($summary['on_call']) ? 0 : Yii::$app->formatter->asDuration($summary['on_call'] * 60)?>">
                            <?= empty($summary['on_call']) ? 0 : Yii::$app->formatter->asHoursDuration($summary['on_call'], '%02d:%02d') ?>
                        </div>
                        <span class="count_bottom"> Selected time</span>
                    </div>

                </div>
            </div>



            <?php //\yii\helpers\VarDumper::dump($summary, 10, true) ?>
            <?php /*
            <div class="col-md-6">
                <h3>Total: </h3>
                <table class="table table-bordered table-hover">
                    <tr class="text-center">
                        <th>Metric</th>

                    </tr>
                    <?php foreach($metricData as $key => $item): ?>
                    <tr class="text-right">
                        <td><?= empty($summary[$key]) ? 0 : Yii::$app->formatter->asDuration($summary[$key] * 60)?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
*/ ?>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $this->render('partial/_timeline_events', [
                    'user' => $user,
                    'scheduleEventList' => $scheduleEventList,
                    'userOnlineEvents' => $userOnlineEvents,
                    'userOnCallEvents' => $userOnCallEvents,
                    'userActiveEvents' => $userActiveEvents,
                    'startDateTime' => $startDateTime,
                    'endDateTime' => $endDateTime,
                    'userOnlineData' => $userOnlineData,

                    'startDateTimeCalendar' => $startDateTimeCalendar,
                    'endDateTimeCalendar' => $endDateTimeCalendar,
            ]); ?>
        </div>
    </div>


    <?php Pjax::end(); ?>
</div>

<?php
$jsCode = <<<JS
    $(document).on('click', '#btn-show-help', function(){
        $('#modal_help').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);