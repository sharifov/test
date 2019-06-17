<?php
/**
 * @var $apiStats []
 * @var $format string
 */
$js = <<<JS

function generateChartPreloader() {
              return "<div class='chartPreloader' style='width:100%; height:50%'>" + 
              "<i class='fa fa-spinner fa-pulse fa-4x' style='color: #CCCCCC;  position: relative;  top: 250px;  left: 45%;  transform: translate(-50%, -50%);'></i>" +
              "</div>"
}
JS;
$this->registerJs($js);
$this->title = 'API Logs Report';

use yii\widgets\Pjax; ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<div class="">
    <h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>
    <div class="panel panel-default">
        <div class="panel-heading"><i class="fa fa-bar-chart"></i> API Logs Chart</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 col-sm-6 col-xs-12">
                    <div class="x_panel">
                        <div class="col-md-3">
                            <?=\kartik\daterange\DateRangePicker::widget([
                                'options'=>['id'=>'api-stats-picker'],
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
                                     $('#chart_div').html(generateChartPreloader());                                    
                                     $.pjax({container: '#api-graph-pjax', data: {dateRange: $('#api-stats-picker').val(), callType: $('#call_type').val()}, type: 'POST', url: 'api-graph', async:true, push: false});
                                     let dates = $('#api-stats-picker').val().split(' / ');
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

                        <!--<div class="col-md-3 " id="viewMode">
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
                            <select id="call_type" class="form-control" required="">
                                <option value="0">All</option>
                                <option value="2">INCOMING</option>
                                <option value="1">OUTGOING</option>
                            </select>
                        </div>-->

                        <?php Pjax::begin(['id' => 'api-graph-pjax']); ?>
                        <div class="x_content">
                            <?php if ($apiStats): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="chart_div" style="height:550px">
                                            <div class="chartPreloader" style="width:100%; height:50%">
                                                <i class="fa fa-spinner fa-pulse fa-4x" style=" color: #CCCCCC;  position: relative;  top: 250px;  left: 45%;  transform: translate(-50%, -50%); "></i>
                                            </div>
                                        </div>
                                        <?php
                                        $this->registerJs("google.charts.load('current', {'packages':['corechart']}); google.charts.setOnLoadCallback(drawChart);", \yii\web\View::POS_READY);
                                        ?>
                                        <script>
                                            function drawChart() {
                                                let data = google.visualization.arrayToDataTable([
                                                    ['Time Line', 'communication/voice', 'communication/sms', 'communication/email', 'lead/create', 'lead/sold-update', 'quote/create', 'quote/update', 'quote/get-info'],
                                                    <?php foreach($apiStats as $k => $item):?>
                                                    ['<?= date($format, strtotime($item['timeLine']))?>', <?= $item['cVoice'] ?>, <?= $item['cSms'] ?>, <?= $item['cEmail'] ?>, <?= $item['lCreate'] ?>, <?= $item['leadSU']?>, <?= $item['qCreate']?>, <?= $item['qUpdate']?>, <?= $item['qInfo']?>],
                                                    <?php endforeach; ?>
                                                ]);

                                                let options = {
                                                    theme: 'material',
                                                    height: 550,
                                                    vAxis: {
                                                        textStyle: {
                                                            fontSize: 12
                                                        },
                                                        format:"#",
                                                        viewWindow: {
                                                            min: 1,
                                                        },
                                                    },
                                                    hAxis: {
                                                        textStyle: {
                                                            fontSize: 12
                                                        },
                                                    },
                                                    seriesType: 'bars',
                                                    bar: {
                                                        groupWidth: 55
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
                                                            fontSize: 12
                                                        },
                                                        alignment: 'end'
                                                    },
                                                    tooltip: {
                                                        textStyle: {
                                                            fontSize: 14
                                                        },
                                                        isHtml: true
                                                    },
                                                    animation:{
                                                        duration: 1000,
                                                        easing: 'linear',
                                                        startup: true
                                                    }
                                                };
                                                let chart = new google.visualization.ComboChart(document.getElementById('chart_div'));

                                                chart.draw(data, options);
                                                $(window).resize(function(){
                                                    chart.draw(data, options); // redraw the graph on window resize
                                                });
                                            }

                                            /*function createCustomHTMLContent(status ,hourRange, statusAmount, totalPrice, totalDuration) {
                                                return '<div style="padding:5px 5px 5px 5px;">' +
                                                    '<table class="medals_layout">' +
                                                    '<tr>' +
                                                    '<td style="padding-right:5px;">Time: </td>' + '<td><b>' + hourRange + '</b></td>' + '</tr>' +
                                                    '<tr>' +
                                                    '<td style="padding-right:5px;">' + status +'</td>' + '<td><b>' + statusAmount + '</b></td>' + '</tr>' +
                                                    '<tr>' +
                                                    '<td style="padding-right:5px;">Total Price: </td>' +
                                                    '<td><b>' + totalPrice + ' $</b></td>' + '</tr>' +
                                                    '<tr>' +
                                                    '<td style="padding-right:5px;">Duration: </td>' +
                                                    '<td><b>' + totalDuration + ' s</b></td>' + '</tr>' +
                                                    '</table>' +
                                                    '</div>';
                                            }*/
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