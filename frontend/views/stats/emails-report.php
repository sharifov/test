<?php
use yii\widgets\Pjax;
/**
 * @var $emailsGraphData []
 */
$js = <<<JS
    $('#viewMode0').click(function() {        
        $('#viewMode1, #viewMode2, #viewMode3').removeClass('active focus');
        $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: 'hours', emailsType: $('#emails_type').val()}, type: 'POST', url: 'emails-graph', async:true, push: false});
    });

    $('#viewMode1').click(function() {        
        $('#viewMode0, #viewMode2, #viewMode3').removeClass('active focus');
        $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: 'days', emailsType: $('#emails_type').val()}, type: 'POST', url: 'emails-graph', async:true, push: false});
    });
    
    $('#viewMode2').click(function() {        
        $('#viewMode0, #viewMode1, #viewMode3').removeClass('active focus');
        $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: 'weeks', emailsType: $('#emails_type').val()}, type: 'POST', url: 'emails-graph', async:true, push: false});
    });
    
    $('#viewMode3').click(function() {        
        $('#viewMode0, #viewMode1, #viewMode2').removeClass('active focus');
        $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: 'months', emailsType: $('#emails_type').val()}, type: 'POST', url: 'emails-graph', async:true, push: false});
    });
    
    $('#emails_type').on('change', function() {
        let groupBy = $('input[name^="viewMode"]:checked').val();
        if( typeof groupBy === 'undefined'){
            let dates = $('#call-stats-picker').val().split(' / ');
            if (dates[0] == dates[1]){
                groupBy = '0';
            } else {
                groupBy = '1';
            }
        }
        let groupingOps = ["hours", "days", "weeks", "months"];        
        
        switch (this.value) {
          case '0' :
              $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: groupingOps[groupBy], emailsType: this.value}, type: 'POST', url: 'emails-graph', async:true, push: false});
          break;
          case '1' :
              $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: groupingOps[groupBy], emailsType: this.value}, type: 'POST', url: 'emails-graph', async:true, push: false});
          break;          
          case '2' :
              $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), groupBy: groupingOps[groupBy], emailsType: this.value}, type: 'POST', url: 'emails-graph', async:true, push: false});
          break;
        }
    });
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
$this->title = 'Emails Report';
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="">
    <h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>
    <div class="panel panel-default">
        <div class="panel-heading"><i class="fa fa-bar-chart"></i> Emails Chart</div>
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
                                    'minDate' => '2019-01-01',
                                    'maxDate' => date("Y-m-d"),
                                    'timePicker'=> false,
                                    'timePickerIncrement'=>15,
                                    'locale'=>[
                                        'format'=>'Y-m-d',
                                        'separator' => ' / '
                                    ],
                                ],
                                'pluginEvents'=>[
                                    "apply.daterangepicker"=>"function(){                                    
                                     $.pjax({container: '#calls-graph-pjax', data: {dateRange: $('#call-stats-picker').val(), emailsType: $('#emails_type').val()}, type: 'POST', url: 'emails-graph', async:true, push: false});
                                     let dates = $('#call-stats-picker').val().split(' / ');
                                     if (dates[0] == dates[1]){
                                        $('#viewMode0').addClass('active focus');
                                        $('#viewMode1').removeClass('active focus');
                                     } else {
                                        $('#viewMode0').removeClass('active focus');
                                        $('#viewMode1').addClass('active focus');
                                     }                                                              
                                                            
                                  }",
                                ],

                            ]);?>
                        </div>

                        <div class="col-md-3 " id="viewMode">
                            <div class="btn-group btn-group-justified" data-toggle="buttons">
                                <label class="btn btn-success active focus" id="viewMode0">
                                    <input type="radio" class="sr-only"  name="viewMode" value="0">
                                    Hours
                                </label>
                                <label class="btn btn-success" id="viewMode1">
                                    <input type="radio" class="sr-only"  name="viewMode" value="1">
                                    Days
                                </label>
                                <label class="btn btn-success" id="viewMode2">
                                    <input type="radio" class="sr-only"  name="viewMode" value="2">
                                    Weeks
                                </label>
                                <label class="btn btn-success" id="viewMode3">
                                    <input type="radio" class="sr-only"  name="viewMode" value="3">
                                    Month
                                </label>
                            </div>
                        </div>

                        <div class="col-xs-1">
                            <select id="emails_type" class="form-control" required="">
                                <option value="0">All</option>
                                <option value="2">INBOX</option>
                                <option value="1">OUTBOX</option>
                            </select>
                        </div>

                        <?php Pjax::begin(['id' => 'calls-graph-pjax']); ?>
                        <div class="x_content">
                            <?php if ($emailsGraphData): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="chart_div"></div>
                                        <?php
                                        $this->registerJs("google.charts.load('current', {'packages':['corechart']}); google.charts.setOnLoadCallback(drawChart);", \yii\web\View::POS_READY);
                                        ?>
                                        <script>
                                            function drawChart() {
                                                let data = google.visualization.arrayToDataTable([
                                                    ['Time Line', 'Emails Done', 'Emails Error', {role: 'annotation'}],
                                                    <?php foreach($emailsGraphData as $k => $item):?>
                                                    ['<?=  ($item['weeksInterval'] == null)
                                                        ? date($item['timeLine'], strtotime($item['time']))
                                                        : date($item['timeLine'], strtotime($item['time'])) .' / '. date($item['timeLine'], strtotime($item['weeksInterval']));

                                                        ?>', <?=$item['done']?>, <?=$item['error']?>, '<?=''?>'],
                                                    <?php endforeach;?>
                                                ]);

                                                let options = {
                                                    height: 550,
                                                    vAxis: {
                                                        textStyle: {
                                                            fontSize: 10
                                                        },
                                                        format:"#",
                                                        viewWindow: {
                                                            min: 1,
                                                        },
                                                    },
                                                    hAxis: {
                                                        textStyle: {
                                                            fontSize: 10
                                                        },
                                                    },
                                                    seriesType: 'bars',
                                                    bar: {
                                                        groupWidth: 25
                                                    },
                                                    chartArea:{
                                                        left:35,
                                                        top:22,
                                                        width:'100%',
                                                        height:'85%'
                                                    },
                                                    legend: {
                                                        position: 'top',
                                                        textStyle: {
                                                            fontSize: 10
                                                        },
                                                        alignment: 'end'
                                                    },
                                                    tooltip: {
                                                        textStyle: {
                                                            fontSize: 14
                                                        }
                                                    }

                                                };
                                                let chart = new google.visualization.ComboChart(document.getElementById('chart_div'));

                                                chart.draw(data, options);
                                                $(window).resize(function(){
                                                    chart.draw(data, options); // redraw the graph on window resize
                                                });
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