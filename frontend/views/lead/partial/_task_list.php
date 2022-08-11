<?php

/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 */

use common\models\Employee;
use kartik\editable\Editable;
use common\models\UserParams;
use yii\helpers\Url;

$taskList = \common\models\LeadTask::find()->where(['lt_lead_id' => $lead->id])->orderBy(['lt_date' => SORT_ASC])->all();

$dateItem = [];
$taskByDate = [];
$dateItemShift = [];

/** @var Employee $user */
$user = Yii::$app->user->identity;

if ($taskList) {
    $usersParams = [];
    foreach ($taskList as $task) {
        if (!in_array($task->lt_user_id, array_keys($usersParams))) {
            $userParamsEntry = UserParams::find()->where(['up_user_id' => $task->lt_user_id])->one();
            if ($userParamsEntry) {
                $usersParams[$task->lt_user_id] = $userParamsEntry->toArray();
            } else {
                $usersParams[$task->lt_user_id] = [
                    'up_work_start_tm' => '18:00',
                    'up_work_minutes' => 480,
                    'up_timezone' => 'Europe/Chisinau',
                ];
            }
        }
    }
    foreach ($taskList as $task) {
        $taskDate = $task->lt_date;
        $taskDate .= ' ' . $usersParams[$task->lt_user_id]['up_work_start_tm'];

        $usersParams[$task->lt_user_id]['up_timezone'] = $usersParams[$task->lt_user_id]['up_timezone'] ?: 'UTC';

        $taskDateUTC = new DateTime($taskDate, new DateTimeZone($usersParams[$task->lt_user_id]['up_timezone']));
        $taskDateUTC->setTimezone(new DateTimeZone('UTC'));
        $taskDateUTCstr = $taskDateUTC->format('Y-m-d H:i');
        $taskDateUTCShiftEnd = clone $taskDateUTC;
        $taskDateUTCShiftEnd->add(new DateInterval('PT' . ($usersParams[$task->lt_user_id]['up_work_minutes'] * 60) . 'S'));

        if (!isset($dateItemShift[$task->lt_date]) || $task->lt_user_id == $user->id) {
            $dateItemShift[$task->lt_date] = ['start' => $taskDateUTC, 'end' => $taskDateUTCShiftEnd];
        }

        $dateItem[$task->lt_date] = $task->lt_date;
        $taskByDate[$task->lt_date][$task->lt_user_id][] = $task;
    }
}

$is_manager = false;
if ($user->isAdmin() || $user->isSupervision()) {
    $is_manager = true;
}

$call2DelayTime = Yii::$app->params['lead']['call2DelayTime']; //(2 * 60 * 60);

?>

<?php //\yii\helpers\VarDumper::dump($dateItemShift, 10, true); ?>

<div class="x_panel" id="task-list">
    <div class="x_title">
        <h2><i class="fa fa-list-ul"></i> Task List</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <?php /*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>
            <li><a class="close-link"><i class="fa fa-close"></i></a>
            </li>*/?>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block;">




            <?php
                $now = new DateTime();
                $currentTS = $now->getTimestamp();
            ?>


            <ul class="nav nav-tabs">
                <?php

                    $activeShown = false;
                    $active = false;

                foreach ($dateItem as $date) :
                    $dayTS = $dateItemShift[$date]['start']->getTimestamp();
                    $shiftEndTS = $dateItemShift[$date]['end']->getTimestamp();

                    $active = (($dayTS < $currentTS && $shiftEndTS > $currentTS) || ($dayTS > $currentTS && !$active && !$activeShown)) ? true : false;

                    if (!$activeShown) {
                        $activeShown = $active ? true : false;
                    }


                    if ($shiftEndTS < $currentTS) {
                        //$icon = 'fa-calendar-times-o';
                        //$bg = 'lavenderblush';

                        $icon = '';
//                            $bg = '';
                    } elseif (!$active) {
                        $icon = 'fa-calendar-minus-o';
//                            $bg = '';
                    } else {
                        $icon = 'fa-calendar';
//                            $bg = '#dff0d8';
                    }

                    ?>

                    <li class="<?=($active ? 'active' : '')?> nav-item">
                    <?php /*<div class="hidden">
                        Active: <?=($active ? 'true' : 'false')?><br>
                        activeShown: <?=($activeShown ? 'true' : 'false')?><br>
                        dayTS: <?=($dayTS)?><br>
                        shiftEndTS: <?=($shiftEndTS)?><br>
                        currentTS: <?=($currentTS)?><br>
                    </div>*/?>

                        <a data-toggle="tab" href="#tab-<?=\yii\helpers\Html::encode(str_replace([' ',':'], '-', $date))?>" class="nav-link <?=($active ? 'active' : '')?>" >
                            <i class="fa <?=$icon?>"></i> <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(strtotime($date), 'php: j M'))?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>




            <div class="tab-content">
                <?php
                    $activeShown = false;
                    $active = false;
                foreach ($dateItem as $date) :
                    $dayTS = $dateItemShift[$date]['start']->getTimestamp();
                    $shiftEndTS = $dateItemShift[$date]['end']->getTimestamp();

                    $active = (($dayTS < $currentTS && $shiftEndTS >= $currentTS) || ($dayTS > $currentTS && !$active && !$activeShown)) ? true : false;
                    if (!$activeShown) {
                        $activeShown = $active ? true : false;
                    }

                    ?>
                <div id="tab-<?=\yii\helpers\Html::encode(str_replace([' ',':'], '-', $date))?>" class="tab-pane fade in <?=($active ? 'active show' : '')?>">

                    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-tl-' . $date, 'enablePushState' => false, 'enableReplaceState' => false]); ?>

                    <?php
                    $dStart = new DateTime(date('Y-m-d', strtotime($lead->created)));
                    $dEnd  = new DateTime($date);
                    $dDiff = $dStart->diff($dEnd);
                    //echo $dDiff->format('%R'); // use for point out relation: smaller/greater
                    //echo $dDiff->days;
                    ?>


                    <?php /*<h4><i class="fa fa-calendar"></i> <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(strtotime($date)))?> - Day #<?=$dDiff->days+1?> </h4>*/?>

                    <?php //=\yii\helpers\Html::a('Refresh', '/lead/processing/12001', ['class' => 'btn btn-warning']) ?>
                    <p>
                    <?php foreach ($taskByDate[$date] as $user_id => $userTasks) :
                            $leadFlow = \common\models\LeadFlow::find()->where(['lead_id' => $lead->id, 'employee_id' => $user_id, 'status' => \common\models\Lead::STATUS_PROCESSING])->orderBy(['id' => SORT_DESC])->one();

                            $userNrDay = null;

                        if ($leadFlow && $leadFlow->created) {
                            $userStartDate = date('Y-m-d', strtotime($leadFlow->created));

                            $dStartUser = new DateTime($userStartDate);
                            $dEndUser  = new DateTime($date);
                            $dDiffUser = $dStartUser->diff($dEndUser);
                            $userNrDay = ($dDiffUser->days + 1);
                        }

                        if ($user_id != $user->id) {
                            $agentName = 'Other agent';
                            if (isset($userTasks[0]) && $userTasks[0]->ltUser && $is_manager) {
                                $agentName = $userTasks[0]->ltUser->username . ' (Id: ' . $userTasks[0]->ltUser->id . ')';
                            }
                        } else {
                            $agentName = $user->username . ' (I am: ' . $user->id . ')';
                        }
                        ?>

                        <br>
                        <p> <i class="fa fa-user"></i>
                        <?=\yii\helpers\Html::encode($agentName)?> ...... <i class="fa fa-clock-o"></i> <b><?=$userNrDay ? $userNrDay : 1?></b> day "processing", started <?=$leadFlow ? ' ....... <i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($leadFlow->created)) : '-' ?>
                        </p>

                        <table class="table table-bordered table-hover table-condensed">
                        <thead>
                            <tr>
                                <th style="width: 50px">#</th>
                                <th style="width: 30px"> </th>
                                <th class="text-center" style="width: 130px">Task</th>
                                <th class="text-center" style="width: 170px">Completed Time</th>
                                <th class="text-center">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php /** @var \common\models\LeadTask $taskItem */


                            $call2TaskEnable = false;

                        foreach ($userTasks as $nr => $taskItem) :
                            $taskName = \yii\helpers\Html::encode($taskItem->ltTask->t_name);

                            $taskIcon = '';
                            $checked = $taskItem->lt_completed_dt ? true : false;
                            $disabledTask = false;

                            if ($taskItem->ltTask->t_key == 'call1') {
                                if ($checked) {
                                    if ((strtotime($taskItem->lt_completed_dt) + $call2DelayTime) <= time()) {
                                        $call2TaskEnable = true;
                                    } else {
                                        $taskIcon = '<i class="fa fa-clock-o" title="Next call ' . Yii::$app->formatter->asDatetime(strtotime($taskItem->lt_completed_dt) + $call2DelayTime) . '"></i>';
                                    }
                                }
                                //$taskIcon = '<i class="fa fa-clock-o"></i>';
                            }


                            if ($taskItem->ltTask->t_key == 'call2') {
                                $disabledTask = !$call2TaskEnable;
                            }

                            if ($checked && !$is_manager) {
                                $disabledTask = true;
                            }

                            if ((!$active) && !$is_manager) {
                                $disabledTask = true;
                            }

                            if ($user->id != $taskItem->lt_user_id) {
                                $disabledTask = true;
                            }

                            $taskCheckbox = \yii\helpers\Html::checkbox('task[]', $checked, ['class' => 'ch_task', 'disabled' => $disabledTask, 'data-pjax-id' => $date, 'data-lead-id' => $taskItem->lt_lead_id, 'data-user-id' => $taskItem->lt_user_id, 'value' => $taskItem->lt_task_id]);

                            if ($checked && !$is_manager) {
                                $taskCheckbox = '<i class="fa fa-check-square-o text-success"></i>';
                            }

                            if (!$checked && !$is_manager && $disabledTask) {
                                $taskCheckbox = '<i class="fa fa-circle-o text-warning"></i>';
                            }

                            if (!$checked && $currentTS > $shiftEndTS && !$is_manager) {
                                $taskCheckbox = '<i class="fa fa-times text-danger" ></i>';
                            }



                            if ($lead->l_answered && $taskItem->lt_task_id == 4 && !$checked) {
                                $disabledTask = true;
                                $taskName = '<s>' . $taskName . '</s>';
                                $taskCheckbox = '<i class="fa fa-delicious"></i>';
                            }

                            ?>
                            <tr class="<?=$taskItem->lt_completed_dt ? 'success' : '' ?>">
                                <td><?=($nr + 1)?></td>
                                <td><?=$taskCheckbox?></td>
                                <td><b><?=$taskIcon?> <?=$taskName?></b></td>
                                <td><?=($taskItem->lt_completed_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($taskItem->lt_completed_dt)) : '' )?> </td>
                                <td>
                                <?php

                                if ($user->id == $taskItem->lt_user_id) {
                                    $name = 'task_notes[' . $taskItem->lt_task_id . '_' . $date . '_' . $taskItem->lt_user_id . ']';

                                    echo Editable::widget([
                                        'name' => $name,

                                        'asPopover' => false,
                                        //'asPopover' => true,
                                        'displayValue' => nl2br(\yii\helpers\Html::encode($taskItem->lt_notes)),
                                        //'format' => Editable::FORMAT_BUTTON,
                                        'inputType' => Editable::INPUT_TEXTAREA,
                                        'value' => $taskItem->lt_notes,
                                        'header' => 'Notes',
                                        'submitOnEnter' => false,
                                        /*'formOptions'=>[
                                            'action'=>\yii\helpers\Url::to(['member/usercp', 'id'=>$model->id]),

                                        ],*/
                                        'size' => \kartik\popover\PopoverX::SIZE_LARGE,
                                        'options' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Enter notes...']
                                    ]);
                                } else {
                                    echo nl2br(\yii\helpers\Html::encode($taskItem->lt_notes));
                                }
                                ?>

                                    </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        </table>

                    <?php endforeach; ?>

                    </p>
                    <?php \yii\widgets\Pjax::end(); ?>
                </div>
                <?php endforeach; ?>
            </div>


    </div>
</div>

<?php
$this->registerJs(
    '
        $(document).on("change",".ch_task", function() {
            var pjaxId = $(this).data("pjax-id");
            var containerId = "#pjax-tl-" + pjaxId;
            var taskId = $(this).val();
            var taskDate = pjaxId;
            var taskLeadId = $(this).data("lead-id");
            var taskUserId = $(this).data("user-id");

            $.pjax.reload({container: containerId, push: false, replace: false, timeout: 5000, data: {date: taskDate, task_id: taskId, lead_id: taskLeadId, user_id: taskUserId}});
        });

        $(document).on("pjax:start", function () {
            //$("#pjax-container").fadeOut("fast");
        });

        $(document).on("pjax:end", function () {
            //$("#pjax-container").fadeIn("fast");
            //alert("end");
        });
    '
);
?>