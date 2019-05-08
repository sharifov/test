<?php
use yii\widgets\Pjax;
/**
 * @var $callsGraphData []
 */
$js = <<<JS
    $('#viewMode0').click(function() {
        $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: 'hours'}, type: 'PJAX', url: 'calls-graph', async:true, push: false});
    });

    $('#viewMode1').click(function() {
        $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: 'days'}, type: 'PJAX', url: 'calls-graph', async:true, push: false});
    });
    
    $('#viewMode2').click(function() {
        $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: 'weeks'}, type: 'PJAX', url: 'calls-graph', async:true, push: false});
    });
    
    $('#viewMode3').click(function() {
        $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: 'months'}, type: 'PJAX', url: 'calls-graph', async:true, push: false});
    });
JS;
$this->registerJs($js, \yii\web\View::POS_READY);


$this->title = 'Calls Report';
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!-- bar chart -->
<div class="">
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
                                     //$('#viewMode').removeClass('hidden');                                 
                                  }",
                                ],

                            ]);?>
                        </div>

                        <div class="col-md-3 " id="viewMode">
                            <div class="btn-group btn-group-justified" data-toggle="buttons">
                                <label class="btn btn-primary  active" id="viewMode0">
                                    Hours
                                </label>
                                <label class="btn btn-primary" id="viewMode1">
                                    <input type="radio" class="sr-only"  name="viewMode" value="1">
                                    Days
                                </label>
                                <label class="btn btn-primary" id="viewMode2">
                                    <input type="radio" class="sr-only"  name="viewMode" value="2">
                                    Weeks
                                </label>
                                <label class="btn btn-primary" id="viewMode3">
                                    <input type="radio" class="sr-only"  name="viewMode" value="3">
                                    Month
                                </label>
                            </div>
                        </div>

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
                                                    ['<?=  ($item['weeksInterval'] == null)
                                                        ? date($item['timeLine'], strtotime($item['time']))
                                                        : date($item['timeLine'], strtotime($item['time'])) .' / '. date($item['timeLine'], strtotime($item['weeksInterval']));

                                                        ?>', <?=$item['completed']?>, <?=$item['no-answer']?>, <?=$item['busy']?>, '<?='--'?>'],
                                                    <?php endforeach;?>
                                                ]);

                                                let options = {
                                                    chart: {
                                                        //title: 'Calls graph',
                                                        //subtitle: 'Calls info - Last ?? days',
                                                    },
                                                    height: 545,
                                                    vAxis: {
                                                        title: 'Requests'
                                                    },
                                                    //legend: { position: 'none' },
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