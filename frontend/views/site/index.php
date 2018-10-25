<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadTaskSearch */
/* @var $dp1 yii\data\ActiveDataProvider */
/* @var $dp2 yii\data\ActiveDataProvider */
/* @var $dp3 yii\data\ActiveDataProvider */


$this->title = 'Dashboard - Agent';
?>
<?/*<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>*/?>

<?php
/*$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
*/


$userId = Yii::$app->user->id;
?>

<div class="site-index">

    <h1><?=$this->title?></h1>
    <div class="row">
        <div class="col-md-3">
            <table class="table table-bordered">
                <tr>
                    <th>Server Date Time (UTC)</th>
                    <td><i class="fa fa-calendar"></i> <?= date('Y-M-d [H:i]')?></td>
                </tr>
                <tr>
                    <th>Current Time Zone</th>
                    <td><i class="fa fa-globe"></i> <?= Yii::$app->formatter->timeZone?></td>
                </tr>
                <tr>
                    <th>Formatted Local Date Time</th>
                    <td><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(time())?></td>
                </tr>
            </table>

        </div>

        <div class="col-md-3">
            <table class="table table-bordered">
                <tr>
                    <th>My Username:</th>
                    <td><i class="fa fa-user"></i> <?= Yii::$app->user->identity->username?> (<?=Yii::$app->user->id?>)</td>
                </tr>
                <tr>
                    <th>My Role:</th>
                    <td><?=implode(', ', Yii::$app->user->identity->roles)?></td>
                </tr>
                <tr>
                    <th>My User Groups:</th>
                    <td><i class="fa fa-users"></i>
                        <?php
                        $groupsValue = '';
                        if( $groupsModel =  Yii::$app->user->identity->ugsGroups) {
                            $groups = \yii\helpers\ArrayHelper::map($groupsModel, 'ug_id', 'ug_name');

                            $groupsValueArr = [];
                            foreach ($groups as $group) {
                                $groupsValueArr[] = Html::tag('span', Html::encode($group), ['class' => 'label label-default']);
                            }
                            $groupsValue = implode(' ', $groupsValueArr);
                        }
                        echo $groupsValue;
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>My Project Access:</th>
                    <td><i class="fa fa-list"></i>
                        <?php
                        $projectsValue = '';

                        if($projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee()) {

                            $groupsValueArr = [];
                            foreach ($projectList as $project) {
                                $groupsValueArr[] = Html::tag('span', Html::encode($project), ['class' => 'label label-default']);
                            }
                            $projectsValue = implode(' ', $groupsValueArr);
                        }
                        echo $projectsValue;
                        ?>
                    </td>
                </tr>
            </table>

        </div>

        <div class="col-md-3">


        </div>

    </div>

    <br>

    <?php
        $columns = [
            ['class' => 'yii\grid\SerialColumn'],

            [
                //'label' => 'Lead UID',
                'attribute' => 'lt_lead_id',
                'value' => function(\common\models\LeadTask $model) {
                    return Html::a($model->lt_lead_id, ['lead/processing/' . $model->lt_lead_id], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                //'filter' => false
            ],

            [
                'label' => 'Task',
                'attribute' => 'lt_task_id',
                'value' => function(\common\models\LeadTask $model) {

                    $taskIcon = '';
                    if($model->ltTask && $model->ltTask->t_key === 'call2') {

                        $call2DelayTime = Yii::$app->params['lead']['call2DelayTime']; //(2 * 60 * 60);

                        $taskCall1 = \common\models\LeadTask::find()->where(['lt_user_id' => $model->lt_user_id, 'lt_lead_id' => $model->lt_lead_id, 'lt_date' => $model->lt_date, 'lt_task_id' => 1])->one();

                        if($taskCall1) {
                            if((strtotime($taskCall1->lt_completed_dt) + $call2DelayTime) <= time()) {
                                $call2TaskEnable = true;
                            } else {
                                $taskIcon = '<br><span class="label label-default">Call after '.Yii::$app->formatter->asDatetime(strtotime($taskCall1->lt_completed_dt) + $call2DelayTime).'</span>';
                                //'<i class="fa fa-clock-o" title="Next call '.Yii::$app->formatter->asDatetime(strtotime($taskCall1->lt_completed_dt) + $call2DelayTime).'"></i> ';
                            }
                        }
                        //$taskIcon = '<i class="fa fa-clock-o"></i>';
                    }


                    return $model->ltTask ? '<h4><span title="'.Html::encode($model->ltTask->t_description).'" class="label label-info">'.Html::encode($model->ltTask->t_name).''.$taskIcon .'</span></h4>': '-';
                },
                'format' => 'html',
                'filter' => \common\models\Task::getList()
            ],

            [
                'attribute' => 'lt_notes',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->lt_notes ? $model->lt_notes : '-';
                },
            ],

            [
                'label' => 'Lead status',
                'attribute' => 'ltLead.status',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->ltLead ? $model->ltLead->getStatusName() : '-';
                },
                'format' => 'html'
            ],

            [
                //'attribute' => 'client_id',
                'header' => 'Client name',
                'format' => 'raw',
                'value' => function(\common\models\LeadTask $model) {

                    if($model->ltLead->client) {
                        $clientName = $model->ltLead->client->first_name . ' ' . $model->ltLead->client->last_name;
                        if ($clientName === 'Client Name') {
                            $clientName = '-';
                        } else {
                            $clientName = '<i class="fa fa-user"></i> '. Html::encode($clientName);
                        }
                    } else {
                        $clientName = '-';
                    }

                    return $clientName;
                },
                'options' => ['style' => 'width:160px'],
                //'filter' => \common\models\Employee::getList()
            ],

            [
                //'attribute' => 'client_id',
                'header' => 'Client time',
                'format' => 'raw',
                'value' => function(\common\models\LeadTask $model) {

                    $clientTime = '-';

                    if($model->ltLead) {
                        $offset = $model->ltLead->offset_gmt;

                        if($offset) {
                            $offset2 = str_replace('.', ':', $offset);

                            if(isset($offset2[0])) {
                                if ($offset2[0] === '+') {
                                    $offset2 = str_replace('+', '-', $offset2);
                                } else {
                                    $offset2 = str_replace('-', '+', $offset2);
                                }
                            }

                            //$clientTime = date('H:i', time() + ($offset * 60 * 60));

                            if($offset2) {
                                $clientTime = date("H:i", strtotime("now $offset2 GMT"));

                                $clientTime = '<i class="fa fa-clock-o"></i> <b>' . Html::encode($clientTime) . '</b> (GMT: ' . $offset . ')';
                            }
                        }

                    } else {
                        $clientTime = '-';
                    }

                    return $clientTime;
                },
                'options' => ['style' => 'width:160px'],
                //'filter' => \common\models\Employee::getList()
            ],



            [
                'label' => 'Segments',
                'value' => function(\common\models\LeadTask $model) {

                    $segments = $model->ltLead->leadFlightSegments;
                    $segmentData = [];
                    if($segments) {
                        foreach ($segments as $sk => $segment) {
                            //$segmentData[] = ($sk + 1).'. <code>'.Html::a($segment->origin.'->'.$segment->destination, ['lead-flight-segment/view', 'id' => $segment->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                            $segmentData[] = ($sk + 1).'. <code>'.$segment->origin.'->'.$segment->destination.'</code>';
                        }
                    }

                    $segmentStr = implode('<br>', $segmentData);
                    return ''.$segmentStr.'';

                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
                'options' => ['style' => 'width:140px'],
            ],


            [
                'label' => 'Cabin',
                'attribute' => 'leads.cabin',
                'value' => function(\common\models\LeadTask $model) {
                    return \common\models\Lead::getCabin($model->ltLead->cabin) ?? '-';
                },
            ],

            [
                'label' => 'Adults',
                'attribute' => 'leads.adults',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->ltLead->adults ?: 0;
                },
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'label' => 'Children',
                'attribute' => 'leads.children',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->ltLead->children ?: '-';
                },
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'label' => 'Infants',
                'attribute' => 'leads.infants',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->ltLead->infants ?: '-';
                },
                'contentOptions' => ['class' => 'text-center'],
            ],


            [
                'label' => 'Lead created',
                'attribute' => 'ltLead.created',
                'value' => function(\common\models\LeadTask $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->ltLead->created));
                },
                'format' => 'html',
            ],

            [
                'label' => 'Lead pending time',
                //'attribute' => 'ltLead.created',
                'value' => function(\common\models\LeadTask $model) {
                    $time = Yii::$app->formatter->asRelativeTime(strtotime($model->ltLead->created));
                    return $time; //'<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->ltLead->created));
                },
                'format' => 'html',
            ],

            /*[
                'attribute' => 'lt_completed_dt',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->lt_completed_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->lt_completed_dt)) : '-';
                },
                'format' => 'html',
            ],*/
        ];
    ?>

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li>
                    <a data-toggle="tab" href="#tab-1">
                        <i class="fa fa-calendar-times-o"></i>  <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(strtotime("-1 days")))?> <span class="label label-default">yesterday</span>
                    </a>
                </li>
                <li class="active">
                    <a data-toggle="tab" href="#tab-2" style="background-color: #dff0d8">
                        <i class="fa fa-calendar"></i>  <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(time()))?> <span class="label label-success">today</span>
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#tab-3" style="background-color:">
                        <i class="fa fa-calendar-minus-o"></i>  <?=\yii\helpers\Html::encode(Yii::$app->formatter->asDate(strtotime("+1 days")))?> <span class="label label-warning">tomorrow</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div id="tab-1" class="tab-pane fade in">
                    <?php Pjax::begin(); ?>
                    <h4>To Do Task List <span class="label label-default">yesterday</span> (<?=Yii::$app->formatter->asDate(strtotime("-1 days"))?>):</h4>
                    <?= GridView::widget([
                        'dataProvider' => $dp1,
                        'filterModel' => $searchModel,
                        //'tableOptions' => ['class' => 'table table-bordered table-condensed table-striped table-hover'],
                        'rowOptions' => function (\common\models\LeadTask $model, $index, $widget, $grid) {
                            if ($model->lt_completed_dt) {
                                return ['class' => 'success'];
                            }
                        },
                        'columns' => $columns,
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>

                <div id="tab-2" class="tab-pane fade in active">
                    <?php Pjax::begin(); ?>
                    <h4>To Do Task List <span class="label label-success">today</span> (<?=Yii::$app->formatter->asDate(time())?>):</h4>
                    <?= GridView::widget([
                        'dataProvider' => $dp2,
                        'filterModel' => $searchModel,
                        'rowOptions' => function (\common\models\LeadTask $model, $index, $widget, $grid) {
                            if ($model->lt_completed_dt) {
                                return ['class' => 'success'];
                            }
                        },
                        'columns' => $columns,
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>

                <div id="tab-3" class="tab-pane fade in">
                    <?php Pjax::begin(); ?>
                    <h4>To Do Task List <span class="label label-warning">tomorrow</span> (<?=Yii::$app->formatter->asDate(strtotime("+1 days"))?>):</h4>
                    <?= GridView::widget([
                        'dataProvider' => $dp3,
                        'filterModel' => $searchModel,
                        'rowOptions' => function (\common\models\LeadTask $model, $index, $widget, $grid) {
                            if ($model->lt_completed_dt) {
                                return ['class' => 'success'];
                            }
                        },
                        'columns' => $columns,
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>

        </div>

    </div>

</div>