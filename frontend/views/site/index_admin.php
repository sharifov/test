<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataStats [] */
/* @var $dataSources [] */
/* @var $dataEmployee [] */
/* @var $dataEmployeeSold [] */

/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Dashboard - Admin';
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php
$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
//Yii::$app->formatter->timeZone = 'Asia/Calcutta';


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



    <div class="">
        <div class="row top_tiles">

            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-users"></i></div>
                    <div class="count"><?=\common\models\Lead::find()->where("DATE(created) = DATE(NOW())")->count()?></div>
                    <h3>Leads</h3>
                    <p>Today count of Leads</p>
                </div>
            </div>
            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-cubes"></i></div>
                    <div class="count"><?=\common\models\Quote::find()->where("DATE(created) = DATE(NOW())")->count()?></div>
                    <h3>Quotes</h3>
                    <p>Today count of Quotes</p>
                </div>
            </div>
            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-sitemap"></i></div>
                    <div class="count"><?=\common\models\ApiLog::find()->where("DATE(al_request_dt) = DATE(NOW())")->count()?></div>
                    <h3>API Requests</h3>
                    <p>Today count of API Requests</p>
                </div>
            </div>
            <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count"><?=\frontend\models\Log::find()->where("log_time BETWEEN ".strtotime(date('Y-m-d'))." AND ".strtotime(date('Y-m-d H:i:s')))->count()?></div>
                    <h3>System Logs</h3>
                    <p>Today count of System Logs</p>
                </div>
            </div>
        </div>

    </div>

    <?php if ($dataStats): ?>
        <div class="row">
            <div class="col-md-12">


                <div id="chart_div"></div>


                <?php
                    $this->registerJs("google.charts.load('current', {'packages':['bar']}); google.charts.setOnLoadCallback(drawChart);", \yii\web\View::POS_READY);
                ?>

                <script>
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Days', 'Not Trash', 'Trash', 'Pending', 'Processing + On Hold', 'Follow Up', 'Sold', {role: 'annotation'}],
                            <?php foreach($dataStats as $k => $item):?>
                            ['<?=date('d M', strtotime($item['created_date']))?>', <?=$item['done_count']?>, <?=$item['trash_count']?>, <?=$item['pending_count']?>, <?=$item['proc_count']?>, <?=$item['book_count']?>, <?=$item['sold_count']?>, '<?='--'?>'],
                            <?php endforeach;?>

                            <?//=$item['sum_price'].'$'?>
                        ]);

                        var options = {
                            chart: {
                                title: 'Lead request',
                                subtitle: 'Lead request - Last 30 days',
                            },
                            title: 'Lead data',
                            height: 400,
                            vAxis: {
                                title: 'Requests'
                            }
                        };

                        //var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));


                        var chart = new google.charts.Bar(document.getElementById('chart_div'));

                        chart.draw(data, options);
                        //chart.draw(data, google.charts.Bar.convertOptions(options));

                    }
                </script>
            </div>
        </div>
    <?php endif; ?>

    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-4">
                <div id="chart_div_projects"></div>
                <?php if ($dataSources): ?>

                        <?php
                            $this->registerJs('google.charts.setOnLoadCallback(drawBasic1);', \yii\web\View::POS_READY);
                        ?>

                        <script>
                            function drawBasic1() {
                                var data = google.visualization.arrayToDataTable([
                                    ['Project', 'Count'],
                                    <?php foreach($dataSources as $k => $item):

                                        $user = \common\models\ApiUser::findOne($item['al_user_id']);
                                        if(!$user) continue;

                                        $project = $user->auProject;
                                        if(!$project) continue;

                                    ?>
                                    ['<?php echo \yii\helpers\Html::encode($project->name).' (apiUser: '.$item['al_user_id'].')' ?>', <?=$item['cnt']?>],
                                    <?php endforeach;?>
                                ]);

                                var options = {
                                    title: 'Project API Request stats - Last <?=$days2?> days',
                                    height: 400
                                };

                                var chart = new google.visualization.PieChart(document.getElementById('chart_div_projects'));
                                chart.draw(data, options);
                            }
                        </script>

                <?php endif; ?>
            </div>


            <div class="col-md-4">
                <div id="chart_div2"></div>
                <?php if($dataEmployee): ?>

                        <?php
                            $this->registerJs('google.charts.setOnLoadCallback(drawBasic2);', \yii\web\View::POS_READY);
                        ?>

                        <script>
                            function drawBasic2() {
                                var data = google.visualization.arrayToDataTable([
                                    ['Employee', 'Count of leads'],
                                    <?php foreach($dataEmployee as $k => $item):
                                        $employee = \common\models\Employee::find()->where(['id' => $item['employee_id']])->one();
                                        if(!$employee) continue;

                                    ?>
                                    ['<?php echo \yii\helpers\Html::encode($employee->username) ?>', <?=$item['cnt']?>],
                                    <?php endforeach;?>
                                ]);

                                var options = {
                                    title: 'Leads by Employees (Processing + On Hold) - Last <?=$days2?> days, limit 20 employees',
                                    height: 400
                                };

                                var chart = new google.visualization.PieChart(document.getElementById('chart_div2'));
                                chart.draw(data, options);
                            }
                        </script>

                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <div id="chart_div3"></div>
                <?php if ($dataEmployeeSold): ?>

                        <?
                            $this->registerJs('google.charts.setOnLoadCallback(drawBasic3);', \yii\web\View::POS_READY);
                        ?>

                        <script>
                            function drawBasic3() {
                                var data = google.visualization.arrayToDataTable([
                                    ['Employee', 'Count of leads'],
                                    <?php foreach($dataEmployeeSold as $k => $item):
                                    $employee = \common\models\Employee::find()->where(['id' => $item['employee_id']])->one();
                                    if(!$employee) continue;

                                    ?>
                                    ['<?php echo \yii\helpers\Html::encode($employee->username) ?>', <?=$item['cnt']?>],
                                    <?php endforeach;?>
                                ]);

                                var options = {
                                    title: 'Leads by Employees (Sold) - Last <?=$days2?> days, limit 20 employees',
                                    height: 400
                                };

                                var chart = new google.visualization.PieChart(document.getElementById('chart_div3'));
                                chart.draw(data, options);
                            }
                        </script>

                <?php endif; ?>
            </div>


        </div>
    </div>


    <br>

    <?php Pjax::begin(); ?>
    <div class="panel panel-default">
        <div class="panel-heading">Agents Stats <?=$searchModel->date_range ? '(' . $searchModel->date_range . ')' : ''?></div>
        <div class="panel-body">





            <div class="row">

                <?php $form = ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'get',
                    'options' => [
                        'data-pjax' => 1
                    ],
                ]); ?>

                <div class="col-md-3">
                    <?php
                    echo  \kartik\daterange\DateRangePicker::widget([
                        'model'=> $searchModel,
                        'attribute' => 'date_range',
                        //'name'=>'date_range',
                        'useWithAddon'=>true,
                        //'value'=>'2015-10-19 12:00 AM - 2015-11-03 01:00 PM',
                        'presetDropdown'=>true,
                        'hideInput'=>true,
                        'convertFormat'=>true,
                        'startAttribute' => 'datetime_start',
                        'endAttribute' => 'datetime_end',
                        //'startInputOptions' => ['value' => date('Y-m-d', strtotime('-5 days'))],
                        //'endInputOptions' => ['value' => '2017-07-20'],
                        'pluginOptions'=>[
                            'timePicker'=> false,
                            'timePickerIncrement'=>15,
                            'locale'=>['format'=>'Y-m-d']
                        ]
                    ]);
                    ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-primary']) ?>
                    <?//= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>


            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions' => function (\common\models\Employee $model, $index, $widget, $grid) {
                    if ($model->deleted) {
                        return ['class' => 'danger'];
                    }
                },
                'columns' => [
                    [
                        'attribute' => 'id',
                        'contentOptions' => ['class' => 'text-center'],
                        'options' => ['style' => 'width:60px'],
                    ],
                    [
                        'attribute' => 'username',
                        'value' => function (\common\models\Employee $model) {
                            return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->username);
                        },
                        'format' => 'raw',
                        //'contentOptions' => ['title' => 'text-center'],
                        'options' => ['style' => 'width:180px'],
                    ],

                    [
                        //'attribute' => 'username',
                        'label' => 'Role',
                        'value' => function (\common\models\Employee $model) {
                            $roles = $model->getRoles();
                            return $roles ? implode(', ', $roles) : '-';
                        },
                        'options' => ['style' => 'width:150px'],
                        //'format' => 'raw'
                    ],

                    /*'email:email',
                    [
                        'attribute' => 'status',
                        'filter' => [$searchModel::STATUS_ACTIVE => 'Active', $searchModel::STATUS_DELETED => 'Deleted'],
                        'value' => function (\common\models\Employee $model) {
                            return ($model->status === $model::STATUS_DELETED) ? '<span class="label label-danger">Deleted</span>' : '<span class="label label-success">Active</span>';
                        },
                        'format' => 'html'
                    ],*/

                    [
                        'label' => 'User Groups',
                        'attribute' => 'user_group_id',
                        'value' => function (\common\models\Employee $model) {

                            $groups = $model->getUserGroupList();
                            $groupsValueArr = [];

                            foreach ($groups as $group) {
                                $groupsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' . Html::encode($group), ['class' => 'label label-default']);
                            }

                            $groupsValue = implode(' ', $groupsValueArr);

                            return $groupsValue;
                        },
                        'format' => 'raw',
                        'filter' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()
                    ],

                    [
                        'label' => 'Tasks Result for Period',
                        'value' => function(\common\models\Employee $model) use ($searchModel) {
                            return $model->getTaskStats($searchModel->datetime_start, $searchModel->datetime_end);
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                        /*'filter' => \kartik\daterange\DateRangePicker::widget([
                            'model'=> $searchModel,
                            'attribute' => 'date_range',
                            //'name'=>'date_range',
                            'useWithAddon'=>true,
                            //'value'=>'2015-10-19 12:00 AM - 2015-11-03 01:00 PM',
                            'presetDropdown'=>true,
                            'hideInput'=>true,
                            'convertFormat'=>true,
                            'startAttribute' => 'datetime_start',
                            'endAttribute' => 'datetime_end',
                            //'startInputOptions' => ['value' => date('Y-m-d', strtotime('-5 days'))],
                            //'endInputOptions' => ['value' => '2017-07-20'],
                            'pluginOptions'=>[
                                'timePicker'=> false,
                                'timePickerIncrement'=>15,
                                'locale'=>['format'=>'Y-m-d']
                            ]
                        ])*/
                        //'options' => ['style' => 'width:200px'],

                    ],
                    [
                        'label' => 'Processing',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_PROCESSING], $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_PROCESSING,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Hold On',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_ON_HOLD], $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_ON_HOLD,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Booked',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_BOOKED], $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_BOOKED,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Sold',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_SOLD], $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_SOLD,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Follow Up',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_FOLLOW_UP], $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_FOLLOW_UP,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Trash',
                        'value' => function (\common\models\Employee $model) use ($searchModel) {
                            $cnt = $model->getLeadCountByStatus([\common\models\Lead::STATUS_TRASH], $searchModel->datetime_start, $searchModel->datetime_end);
                            return $cnt ? Html::a($cnt, ['lead-flow/index',
                                'LeadFlowSearch[employee_id]' => $model->id,
                                'LeadFlowSearch[status]' => \common\models\Lead::STATUS_TRASH,
                                'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                            ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                        },
                        'format' => 'raw',
                    ]


                    /*[
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update}',
                        'visibleButtons' => [
                            'update' => function (\common\models\Employee $model, $key, $index) {
                                return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                            },
                        ],

                    ],*/
                ]
            ])
            ?>


        </div>
    </div>
    <?php Pjax::end(); ?>

</div>