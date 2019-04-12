<?php
use yii\widgets\Pjax;
/**
 * @var $callsGraphData []
 */

$this->title = 'Calls Report';
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!-- bar chart -->
<div class="stats-call-sms">
    <h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>
    <div class="panel panel-default">
        <div class="panel-heading"><i class="fa fa-bar-chart"></i> Calls Chart</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 col-sm-6 col-xs-12">
                    <div class="x_panel">
                        <div class="col-md-3">
                            <?=\kartik\daterange\DateRangePicker::widget([
                                'options'=>['id'=>'call-stats-picker'],
                                'name'=>'callStatsRange',
                                'convertFormat'=>true,
                                'presetDropdown'=>true,
                                'hideInput'=>true,
                                'useWithAddon'=>true,
                                'pluginOptions'=>[
                                    'timePicker'=> false,
                                    'timePickerIncrement'=>15,
                                    'locale'=>[
                                        'format'=>'Y-m-d',
                                        'separator' => ' / '
                                    ],
                                ],
                                'pluginEvents'=>[
                                    "apply.daterangepicker"=>"function(){
                                console.log($('#call-stats-picker').val());
                                  $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val()}, type: 'PJAX', url: 'calls-graph', async:true, push: false});
                                 }",
                                ],

                            ]);?>
                        </div>

                        <!--<div class="x_title">
                            <h2>Call Chart by Status <small></small></h2>-->
                        <!--<ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#">Settings 1</a>
                                    </li>
                                    <li><a href="#">Settings 2</a>
                                    </li>
                                </ul>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>-->
                        <!--    <div class="clearfix"></div>
                        </div>-->
                        <?php Pjax::begin(['id' => 'calls-graph-pjax']); ?>
                        <div class="x_content">

                            <?php if ($callsGraphData): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="chart_div"></div>

                                        <?php
                                        $this->registerJs("google.charts.load('current', {'packages':['bar']}); google.charts.setOnLoadCallback(drawChart);", \yii\web\View::POS_READY);
                                        ?>

                                        <script>
                                            function drawChart() {
                                                let data = google.visualization.arrayToDataTable([
                                                    ['Time Line', 'Completed', 'Canceled', 'Busy', {role: 'annotation'}],
                                                    <?php foreach($callsGraphData as $k => $item):?>
                                                    ['<?=date($item['timeLine'], strtotime($item['time']))?>', <?=$item['completed']?>, <?=$item['no-answer']?>, <?=$item['busy']?>, '<?='--'?>'],
                                                    <?php endforeach;?>
                                                ]);

                                                let options = {
                                                    chart: {
                                                        title: 'Calls graph',
                                                        subtitle: 'Calls info - Last ?? days',
                                                    },
                                                    title: 'Lead data',
                                                    height: 545,
                                                    vAxis: {
                                                        title: 'Requests'
                                                    },
                                                };
                                                //var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
                                                let chart = new google.charts.Bar(document.getElementById('chart_div'));

                                                chart.draw(data, options);
                                                $(window).resize(function(){
                                                    chart.draw(data, options); // redraw the graph on window resize
                                                });
                                                //chart.draw(data, google.charts.Bar.convertOptions(options));

                                            }
                                        </script>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /bar charts -->