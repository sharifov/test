<?php
/**
 * @var $apiStats []
 * @var $format string
 */
use yii\helpers\Html;
$js = <<<JS

$('#viewMode0').click(function() {
        $('#chart_div').html(generateChartPreloader());
        $('#viewMode1, #viewMode2').removeClass('active focus');
        $.pjax({container: '#api-graph-pjax', data: {dateRange: $('#api-stats-picker').val(), groupBy: 'H', project: $('#projects').val(), action: $('#apiList').val()}, type: 'POST', url: 'api-graph', async:true, push: false});
    });

    $('#viewMode1').click(function() {
        $('#chart_div').html(generateChartPreloader());
        $('#viewMode0, #viewMode2').removeClass('active focus');
        $.pjax({container: '#api-graph-pjax', data: {dateRange: $('#api-stats-picker').val(), groupBy: 'D', project: $('#projects').val(), action: $('#apiList').val()}, type: 'POST', url: 'api-graph', async:true, push: false});
    });
    
    $('#viewMode2').click(function() {
        $('#chart_div').html(generateChartPreloader());
        $('#viewMode0, #viewMode1').removeClass('active focus');
        $.pjax({container: '#api-graph-pjax', data: {dateRange: $('#api-stats-picker').val(), groupBy: 'M', project: $('#projects').val(), action: $('#apiList').val()}, type: 'POST', url: 'api-graph', async:true, push: false});
    });
    
    $('#projects').on('change', function() {
        $('#chart_div').html(generateChartPreloader());
        let groupBy = $('input[name^="viewMode"]:checked').val();
        if( typeof groupBy === 'undefined'){
            let dates = $('#api-stats-picker').val().split(' / ');
            if (dates[0] == dates[1]){
                groupBy = '0';
            } else {
                groupBy = '1';
            }
        }
        let groupingOps = ["H", "D", "M"];
        $.pjax({container: '#api-graph-pjax', data: {dateRange: $('#api-stats-picker').val(), groupBy: groupingOps[groupBy], project: this.value, action: $('#apiList').val()}, type: 'POST', url: 'api-graph', async:true, push: false});
    });
    
    $('#apiList').on('change', function() {
        $('#chart_div').html(generateChartPreloader());
        let groupBy = $('input[name^="viewMode"]:checked').val();
        if( typeof groupBy === 'undefined'){
            let dates = $('#api-stats-picker').val().split(' / ');
            if (dates[0] == dates[1]){
                groupBy = '0';
            } else {
                groupBy = '1';
            }
        }
        let groupingOps = ["H", "D", "M"];        
        
        let api = '';
        switch (this.value) {
          case 'v1/communication/voice' :
              api = 0;
              break;
          case 'v1/communication/sms' :
              api = 1;
              break;
          case 'v1/communication/email' :
              api = 2;
              break;
          case 'v1/lead/create' :
              api = 3;
              break;
          case 'v1/lead/sold-update' :
              api = 4;
              break;
          case 'v1/quote/create' :
              api = 5;
              break;
          case 'v1/quote/update' :
              api = 6;
              break;
          case 'v2/quote/get-info' :
              api = 7;
              break;
        }        
        $.pjax({container: '#api-graph-pjax', data: {dateRange: $('#api-stats-picker').val(), groupBy: groupingOps[groupBy], project: $('#projects').val(), action: api}, type: 'POST', url: 'api-graph', async:true, push: false});
    });

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
                                     $.pjax({container: '#api-graph-pjax', data: {dateRange: $('#api-stats-picker').val(), project: $('#projects').val(), action: $('#apiList').val()}, type: 'POST', url: 'api-graph', async:true, push: false});
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
                                    Month
                                </label>
                            </div>
                        </div>

                        <div class="col-xs-1">
                            <?= Html::dropDownList('projectsList', null,  \common\models\ApiUser::getList(), [
                                'prompt' => 'All',
                                'id' => 'projects',
                                'class' => 'form-control'
                            ]) ?>
                        </div>

                        <div class="col-xs-1">
                            <?= Html::dropDownList('projectsList', null,  \common\models\ApiLog::getActionFilter(), [
                                'prompt' => 'All',
                                'id' => 'apiList',
                                'class' => 'form-control'
                            ]) ?>
                        </div>

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
                                                    ['Time Line',
                                                        <?php if ($action == '' || $action == 0) :?>
                                                        'communication/voice',
                                                        {'type': 'string', 'role': 'tooltip', 'p': {'html': true}},
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 1) :?>
                                                        'communication/sms',
                                                        {'type': 'string', 'role': 'tooltip', 'p': {'html': true}},
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 2) :?>
                                                        'communication/email',
                                                        {'type': 'string', 'role': 'tooltip', 'p': {'html': true}},
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 3) :?>
                                                        'lead/create',
                                                        {'type': 'string', 'role': 'tooltip', 'p': {'html': true}},
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 4) :?>
                                                        'lead/sold-update',
                                                        {'type': 'string', 'role': 'tooltip', 'p': {'html': true}},
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 5) :?>
                                                        'quote/create',
                                                        {'type': 'string', 'role': 'tooltip', 'p': {'html': true}},
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 6) :?>
                                                        'quote/update',
                                                        {'type': 'string', 'role': 'tooltip', 'p': {'html': true}},
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 7) :?>
                                                        'quote/get-info',
                                                        {'type': 'string', 'role': 'tooltip', 'p': {'html': true}}
                                                        <?php endif;?>
                                                    ],
                                                    <?php foreach($apiStats as $k => $item): ?>
                                                    ['<?= date($format, strtotime($item['timeLine']))?>',
                                                        <?php if ($action == '' || $action == 0) :?>
                                                        <?= $item['cVoice'] ?>, customHTMLContent('<?= date($format, strtotime($item['timeLine']))?>', 'communication/voice', '<?= $item['cVoice'] ?>', '<?= isset($item['cAvgTimeV']) ? round($item['cAvgTimeV'], 2) : ''?>', '<?= isset($item['cMemV']) ? Yii::$app->formatter->asShortSize($item['cMemV'],2) : '' ?>'),
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 1) :?>
                                                        <?= $item['cSms'] ?>, customHTMLContent('<?= date($format, strtotime($item['timeLine']))?>', 'communication/sms', '<?= $item['cSms'] ?>', '<?= isset($item['cAvgTimeS']) ? round($item['cAvgTimeS'], 2) : ''?>', '<?= isset($item['cMemS']) ? Yii::$app->formatter->asShortSize($item['cMemS'], 2) : '' ?>'),
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 2) :?>
                                                        <?= $item['cEmail'] ?>, customHTMLContent('<?= date($format, strtotime($item['timeLine']))?>', 'communication/email', '<?= $item['cEmail'] ?>', '<?= isset($item['cAvgTimeE']) ? round($item['cAvgTimeE'], 2) : ''?>', '<?= isset($item['cMemE']) ? Yii::$app->formatter->asShortSize($item['cMemE'], 2) : '' ?>'),
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 3) :?>
                                                        <?= $item['lCreate'] ?>, customHTMLContent('<?= date($format, strtotime($item['timeLine']))?>', 'lead/create', '<?= $item['lCreate'] ?>', '<?= isset($item['lAvgTimeC']) ? round($item['lAvgTimeC'], 2) : ''?>', '<?= isset($item['lMemC']) ? Yii::$app->formatter->asShortSize($item['lMemC'], 2) : '' ?>'),
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 4) :?>
                                                        <?= $item['leadSU']?>, customHTMLContent('<?= date($format, strtotime($item['timeLine']))?>', 'lead/sold-update', '<?= $item['leadSU'] ?>', '<?= isset($item['lAvgTimeSU']) ? round($item['lAvgTimeSU'], 2) : ''?>', '<?= isset($item['lMemSU']) ? Yii::$app->formatter->asShortSize($item['lMemSU'], 2) : '' ?>'),
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 5) :?>
                                                        <?= $item['qCreate']?>, customHTMLContent('<?= date($format, strtotime($item['timeLine']))?>', 'quote/create', '<?= $item['qCreate'] ?>', '<?= isset($item['qAvgTimeC']) ? round($item['qAvgTimeC'], 2) : ''?>', '<?= isset($item['qMemC']) ? Yii::$app->formatter->asShortSize($item['qMemC'], 2) : '' ?>'),
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 6) :?>
                                                        <?= $item['qUpdate']?>, customHTMLContent('<?= date($format, strtotime($item['timeLine']))?>', 'quote/update', '<?= $item['qUpdate'] ?>', '<?= isset($item['qAvgTimeU']) ? round($item['qAvgTimeU'], 2) : ''?>', '<?= isset($item['qMemU']) ? Yii::$app->formatter->asShortSize($item['qMemU'], 2) : '' ?>'),
                                                        <?php endif;?>
                                                        <?php if ($action == '' || $action == 7) :?>
                                                        <?= $item['qInfo']?>, customHTMLContent('<?= date($format, strtotime($item['timeLine']))?>', 'quote/get-info', '<?= $item['qInfo'] ?>', '<?= isset($item['qAvgTimeI']) ? round($item['qAvgTimeI'], 2) : ''?>', '<?= isset($item['qMemI']) ? Yii::$app->formatter->asShortSize($item['qMemI'], 2) : '' ?>')
                                                        <?php endif;?>
                                                    ],
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

                                            function customHTMLContent(timeLine, api, apiCnt, avgTime, avgMem) {
                                                return '<div style="padding:5px 5px 5px 5px;">' +
                                                    '<table class="medals_layout">' +
                                                    '<tr>' +
                                                    '<td style="padding-right:5px;">Time: </td>' + '<td><b>' + timeLine + '</b></td>' + '</tr>' +
                                                    '<tr>' +
                                                    '<td style="padding-right:5px;">' + api +':</td>' + '<td><b>' + apiCnt + '</b></td>' + '</tr>' +
                                                    '<tr>' +
                                                    '<td style="padding-right:5px;">Avg. Execution Time: </td>' + '<td><b>' + avgTime + ' ms</b></td>' + '</tr>' +
                                                    '<tr>' +
                                                    '<td style="padding-right:5px;">Avg. Memory usage: </td>' + '<td><b>' + avgMem + '</b></td>' + '</tr>' +
                                                    '</table>' +
                                                    '</div>';
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