<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 */


use kartik\editable\Editable;

$taskList = \common\models\LeadTask::find()->where(['lt_lead_id' => $lead->id])->orderBy(['lt_date' => SORT_ASC])->all();

$dateItem = [];
$taskByDate = [];

if($taskList) {
    foreach ($taskList as $task) {
        $dateItem[$task->lt_date] = $task->lt_date;
        $taskByDate[$task->lt_date][$task->lt_user_id][] = $task;
    }
}

$userId = Yii::$app->user->id;

$is_manager = false;
if(Yii::$app->authManager->getAssignment('admin', $userId) || Yii::$app->authManager->getAssignment('supervision', $userId)) {
    $is_manager = true;
}

$call2DelayTime = (2 * 60 * 60);

?>


<?php //\yii\helpers\VarDumper::dump($dateItem); ?>




<div class="panel panel-neutral panel-wrapper agents-notes-block">
    <div class="panel-heading collapsing-heading">
        <a data-toggle="collapse" href="#agents-notes" aria-expanded="true"
           class="collapsing-heading__collapse-link">
            TODO Task List
            <i class="collapsing-heading__arrow"></i>
        </a>
    </div>
    <div class="collapse in" id="agents-notes" aria-expanded="true" style="">
        <div class="panel-body">


            <?php
                $currentTS = strtotime(date('Y-m-d'));
            ?>


            <ul class="nav nav-tabs">
                <?php foreach ($dateItem as $date):
                    $dayTS = strtotime($date);

                    if($dayTS < $currentTS ) {
                        $icon = 'fa-calendar-times-o';
                        $bg = 'lavenderblush';
                    } elseif($dayTS > $currentTS ) {
                        $icon = 'fa-calendar-minus-o';
                        $bg = '';
                    } else {
                        $icon = 'fa-calendar';
                        $bg = '#dff0d8';
                    }

                    ?>

                    <li class="<?=($date == date('Y-m-d') ? 'active' : '')?>">

                        <a data-toggle="tab" href="#tab-<?=\yii\helpers\Html::encode($date)?>" style="background-color: <?=$bg?>">
                            <i class="fa <?=$icon?>"></i> <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(strtotime($date)))?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>




            <div class="tab-content">
                <?php foreach ($dateItem as $date):

                    $dayTS = strtotime($date);

                    ?>
                <div id="tab-<?=\yii\helpers\Html::encode($date)?>" class="tab-pane fade in <?=($date == date('Y-m-d') ? 'active' : '')?>">

                    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-tl-'.$date, 'enablePushState' => false, 'enableReplaceState' => false]); ?>

                    <?php
                        $dStart = new DateTime(date('Y-m-d', strtotime($lead->created)));
                        $dEnd  = new DateTime($date);
                        $dDiff = $dStart->diff($dEnd);
                        //echo $dDiff->format('%R'); // use for point out relation: smaller/greater
                        //echo $dDiff->days;
                    ?>


                    <h4><i class="fa fa-calendar"></i> <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(strtotime($date)))?> - Day #<?=$dDiff->days?> </h4>
                    <?//=\yii\helpers\Html::a('Refresh', '/lead/processing/12001', ['class' => 'btn btn-warning']) ?>
                    <p>
                        <?php foreach ($taskByDate[$date] as $user_id => $userTasks):


                            $leadFlow = \common\models\LeadFlow::find()->where(['lead_id' => $lead->id, 'employee_id' => $user_id, 'status' => \common\models\Lead::STATUS_PROCESSING])->orderBy(['id' => SORT_DESC])->one();

                            $userNrDay = null;

                            if($leadFlow && $leadFlow->created) {
                                $userStartDate = date('Y-m-d', strtotime($leadFlow->created));

                                $dStartUser = new DateTime($userStartDate);
                                $dEndUser  = new DateTime($date);
                                $dDiffUser = $dStartUser->diff($dEndUser);
                                $userNrDay = ($dDiffUser->days + 1);
                            }

                            $user = \common\models\Employee::findOne($user_id);
                            if($user_id != Yii::$app->user->id) {
                                $agentName = 'Other agent';
                                if($user && $is_manager) {
                                    $agentName = $user->username . ' (Id: '.$user->id.')';
                                }
                            } else {
                                $agentName = $user->username . ' (I am: '.$user->id.')';
                            }
                        ?>

                        <br>
                        <p> <i class="fa fa-user"></i>
                    <?=\yii\helpers\Html::encode($agentName)?> ...... <i class="fa fa-clock-o"></i> <b><?=$userNrDay ? $userNrDay : 1?></b> day "processing", started <?=$leadFlow ? ' ....... <i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($leadFlow->created)) : '-' ?>
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

                            foreach ($userTasks as $nr => $taskItem):

                                $taskName = \yii\helpers\Html::encode($taskItem->ltTask->t_name);

                                $taskIcon = '';
                                $checked = $taskItem->lt_completed_dt ? true : false;
                                $disabledTask = false;

                                if($taskItem->ltTask->t_key == 'call1') {
                                    if($checked) {
                                        if((strtotime($taskItem->lt_completed_dt) + $call2DelayTime) <= time()) {
                                            $call2TaskEnable = true;
                                        } else {
                                            $taskIcon = '<i class="fa fa-clock-o" title="Next call '.Yii::$app->formatter->asDatetime(strtotime($taskItem->lt_completed_dt) + $call2DelayTime).'"></i>';
                                        }
                                    }
                                    //$taskIcon = '<i class="fa fa-clock-o"></i>';
                                }


                                if($taskItem->ltTask->t_key == 'call2') {
                                    $disabledTask = !$call2TaskEnable;
                                }

                                if($checked && !$is_manager) {
                                    $disabledTask = true;
                                }

                                if(($dayTS < $currentTS || $dayTS > $currentTS) && !$is_manager) {
                                    $disabledTask = true;
                                }

                                if(Yii::$app->user->id != $taskItem->lt_user_id) {
                                    $disabledTask = true;
                                }

                                $taskCheckbox = \yii\helpers\Html::checkbox('task[]', $checked, ['class' => 'ch_task', 'disabled' => $disabledTask, 'data-pjax-id' => $date, 'data-lead-id' => $taskItem->lt_lead_id, 'data-user-id' => $taskItem->lt_user_id, 'value' => $taskItem->lt_task_id]);

                                if($checked && !$is_manager) {
                                    //$taskCheckbox = '<i class="fa fa-thumbs-o-up text-success"></i>';
                                }

                                if(!$checked && $dayTS < $currentTS && !$is_manager) {
                                    $taskCheckbox = '<i class="fa fa-times text-danger" ></i>';
                                }



                                if($lead->l_answered && $taskItem->lt_task_id == 4 && !$checked) {
                                    $disabledTask = true;
                                    $taskName = '<s>'.$taskName.'</s>';
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

                                    if(Yii::$app->user->id == $taskItem->lt_user_id) {
                                        $name = 'task_notes[' . $taskItem->lt_task_id . '_' . $date . '_' . $taskItem->lt_user_id . ']';

                                        echo Editable::widget([
                                            'name' => $name,

                                            'asPopover' => true,
                                            'displayValue' => nl2br(\yii\helpers\Html::encode($taskItem->lt_notes)),
                                            'format' => Editable::FORMAT_BUTTON,
                                            'inputType' => Editable::INPUT_TEXTAREA,
                                            'value' => $taskItem->lt_notes,
                                            'header' => 'Notes',
                                            'submitOnEnter' => false,
                                            /*'formOptions'=>[
                                                'action'=>\yii\helpers\Url::to(['member/usercp', 'id'=>$model->id]),

                                            ],*/
                                            'size' => 'lg',
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