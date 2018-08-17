<?php

/* @var $this yii\web\View */
/* @var $dataStats [] */
/* @var $dataSources [] */
/* @var $dataEmployee [] */
/* @var $dataEmployeeSold [] */



$this->title = 'Dashboard';
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php
$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>

<div class="site-index">

    <h4>Server date & time: <?=date('c')?></h4>
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
                    <div class="count"><?=\backend\models\Log::find()->where("log_time BETWEEN ".strtotime(date('Y-m-d'))." AND ".strtotime(date('Y-m-d H:i:s')))->count()?></div>
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
                            ['Days', 'All', 'Pending', 'Booked', 'Sold', {role: 'annotation'}],
                            <? foreach($dataStats as $k => $item):?>
                            ['<?=date('d M', strtotime($item['created_date']))?>', <?=$item['done_count']?>, <?=$item['pending_count']?>, <?=$item['book_count']?>, <?=$item['sold_count']?>, '<?=($item['done_count'] )?>'],
                            <? endforeach;?>

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
    <? endif; ?>

    <hr/>

    <div class="row">
        <div class="col-md-12">

            <?php if ($dataSources): ?>
                <div class="col-md-4">
                    <div id="chart_div_projects"></div>
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
                                ['<?php



                                    echo \yii\helpers\Html::encode($project->name).' (apiUser: '.$item['al_user_id'].')' ?>', <?=$item['cnt']?>],
                                <? endforeach;?>
                            ]);

                            var options = {
                                title: 'Project API Request stats - Last 30 days',
                                height: 400
                            };

                            var chart = new google.visualization.PieChart(document.getElementById('chart_div_projects'));
                            chart.draw(data, options);
                        }
                    </script>
                </div>
            <? endif; ?>



            <?php if ($dataEmployee): ?>
                <div class="col-md-4">
                    <div id="chart_div2"></div>
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
                                ['<?php

                                    echo \yii\helpers\Html::encode($employee->username) ?>', <?=$item['cnt']?>],
                                <? endforeach;?>
                            ]);

                            var options = {
                                title: 'Employees & Leads - Last 30 days',
                                height: 400
                            };

                            var chart = new google.visualization.PieChart(document.getElementById('chart_div2'));
                            chart.draw(data, options);
                        }
                    </script>
                </div>
            <? endif; ?>


            <?php if ($dataEmployeeSold): ?>
                <div class="col-md-4">
                    <div id="chart_div3"></div>
                    <?php
                    $this->registerJs('google.charts.setOnLoadCallback(drawBasic3);', \yii\web\View::POS_READY);
                    ?>

                    <script>
                        function drawBasic3() {
                            var data = google.visualization.arrayToDataTable([
                                ['Employee', 'Count of leads'],
                                <?php foreach($dataEmployee as $k => $item):
                                $employee = \common\models\Employee::find()->where(['id' => $item['employee_id']])->one();
                                if(!$employee) continue;

                                ?>
                                ['<?php

                                    echo \yii\helpers\Html::encode($employee->username) ?>', <?=$item['cnt']?>],
                                <? endforeach;?>
                            ]);

                            var options = {
                                title: 'Employees & Leads, status Sold - Last 30 days',
                                height: 400
                            };

                            var chart = new google.visualization.PieChart(document.getElementById('chart_div3'));
                            chart.draw(data, options);
                        }
                    </script>
                </div>
            <? endif; ?>




        </div>
    </div>

</div>
