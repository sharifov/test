<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 */


$taskList = \common\models\LeadTask::find()->where(['lt_lead_id' => $lead->id, 'lt_user_id' => Yii::$app->user->id])->orderBy(['lt_date' => SORT_ASC])->all();

$dateItem = [];
$taskByDate = [];

if($taskList) {
    foreach ($taskList as $task) {
        $dateItem[$task->lt_date] = $task->lt_date;
        $taskByDate[$task->lt_date][] = $task;
    }
}

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

            <ul class="nav nav-tabs">
                <?php foreach ($dateItem as $date): ?>
                    <li class="<?=($date == date('Y-m-d') ? 'active' : '')?>">
                        <a data-toggle="tab" href="#tab-<?=\yii\helpers\Html::encode($date)?>">
                            <i class="fa fa-calendar<?=($date == date('Y-m-d') ? '' : '-times-o')?>"></i> <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(strtotime($date)))?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content">
                <?php foreach ($dateItem as $date): ?>
                <div id="tab-<?=\yii\helpers\Html::encode($date)?>" class="tab-pane fade in <?=($date == date('Y-m-d') ? 'active' : '')?>">

                    <?php
                        $dStart = new DateTime(date('Y-m-d', strtotime($lead->created)));
                        $dEnd  = new DateTime($date);
                        $dDiff = $dStart->diff($dEnd);
                        //echo $dDiff->format('%R'); // use for point out relation: smaller/greater
                        //echo $dDiff->days;
                    ?>


                    <h4>Day #<?=$dDiff->days?></h4>
                    <p>
                        <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 30px"> </th>
                                <th class="text-center" style="width: 130px">Task</th>
                                <th class="text-center" style="width: 150px">Completed Time</th>
                                <th class="text-center">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php /** @var \common\models\LeadTask $taskItem */
                            foreach ($taskByDate[$date] as $nr => $taskItem): $checked = false; ?>
                            <tr>
                                <td><?=($nr + 1)?></td>
                                <td><?=\yii\helpers\Html::checkbox('task[]', $checked, [])?></td>
                                <td><b><?=\yii\helpers\Html::encode($taskItem->ltTask->t_name)?></b></td>
                                <td><?=($taskItem->lt_completed_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDate(strtotime($taskItem->lt_completed_dt)) : '' )?></td>
                                <td><?=\yii\helpers\Html::encode($taskItem->lt_notes)?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        </table>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</div>