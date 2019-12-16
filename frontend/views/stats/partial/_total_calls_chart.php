<?php

use common\models\search\CallGraphsSearch;

/**
 * @var $totalCallsDbData array
 * @var $totalCallsGraphData string
 * @var $totalCallsGraphDataAvg string
 * @var $totalCallsRecDurationData string
 * @var $totalCallsRecDurationDataAVG string
 * @var $model CallGraphsSearch
 */

?>

<?php if ($totalCallsDbData): ?>
<div id="myChart"></div>


<div class="row d-flex align-items-center">
    <div class="col-md-2">
        <label class="control-label">View Columns on Graph</label>
        <?= \yii\helpers\Html::checkboxList('totalChartColumns', $model->totalChartColumns, $model::getChartTotalCallTextList(), [
            'itemOptions' => [
                'class' => 'totalChartColumns',
                'data-name' => $model->formName().'[totalChartColumns][]'
            ]
        ]) ?>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label">Measure</label>
            <?= \yii\helpers\Html::dropDownList('chartTotalCallsVaxis', $model->chartTotalCallsVaxis, $model::getChartTotalCallsVaxisTextList(), [
				'class' => 'form-control chartTotalCallsVaxis',
                'data-name' => $model->formName().'[chartTotalCallsVaxis]'
            ]) ?>
        </div>
    </div>
</div>

<?= $this->render('_total_calls_chart_summary', [
	'totalCallsDbData' => $totalCallsDbData,
]) ?>

<script type="text/javascript">
    $(document).ready( function () {

        var graphData;
        var totalCallsRecDurationData = <?= $totalCallsRecDurationData ?>;
        var totalCallsRecDurationDataAVG = <?= $totalCallsRecDurationDataAVG ?>;
        var totalCallsData = <?= $totalCallsGraphData ?>;
        var totalCallsDataAvg = <?= $totalCallsGraphDataAvg ?>;

        var selectedMeasure = +$('.chartTotalCallsVaxis').val();
        var measuresText = <?= json_encode(CallGraphsSearch::getChartTotalCallsVaxisTextList()) ?>;
        var timeRange = '<?= $model->createTimeRange ?>';

        if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>) {
            graphData = totalCallsData;
        } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>) {
            graphData = totalCallsDataAvg;
        } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>) {
            graphData = totalCallsRecDurationData;
        } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>) {
            graphData = totalCallsRecDurationDataAVG;
        }

        google.charts.load('current', {'packages':['corechart','bar']});
        google.charts.setOnLoadCallback(function () {
            var totalCallsChart = new google.visualization.ColumnChart(document.getElementById('myChart'));

            var colors = ['#8ec5ff', '#dd4b4e', '#587ca6'];

            var options = {
                title: measuresText[selectedMeasure]+': ' + timeRange,
                theme: 'material',
                chartArea:{width:'95%', right: 10},
                textStyle: {
                    color: '#596b7d'
                },
                titleColor: '#596b7d',
                fontSize: 14,
                color: '#596b7d',
                colors: colors,
                enableInteractivity: true,
                height: 650,
                animation:{
                    duration: 200,
                    easing: 'linear',
                    startup: true
                },
                legend: {
                    position: 'top',
                    alignment: 'end'
                },
                hAxis: {
                    title: 'Date',
                    slantedText:true,
                    slantedTextAngle:30,
                    textStyle: {
                        fontSize: 12,
                        color: '#596b7d',
                    },
                    titleColor: '#596b7d',

                },
                vAxis: {
                    format: 'short',
                    title: 'Calls',
                    titleColor: '#596b7d',
                }
            };

            // let newGraphData = [];
            var data = google.visualization.arrayToDataTable(graphData);
            var view = new google.visualization.DataView(data);
            var arr = [0];
            var c = [];
            $('.totalChartColumns:checked').each( function(i, elem) {
                arr.push((+$(elem).val()));
                c.push(colors[+$(elem).val()-1]);
            });
            view.setColumns(arr);
            options.colors = c;

            totalCallsChart.draw(data, options);
            totalCallsChart.draw(view, options);
            $(window).resize(function(){
                totalCallsChart.draw(data, options);
            });

            $('.chartTotalCallsVaxis').on('change', function () {
                let val = +$(this).val();

                if (val === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>) { // calls
                    graphData = totalCallsData;
                    options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS) ?>';
                } else if (val === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>) {
                    graphData = totalCallsDataAvg;
                    options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG) ?>';
                } else if (val === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>) { // calls recording
                    graphData = totalCallsRecDurationData;
                    options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION) ?>';
                } else if (val === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>) {
                    graphData = totalCallsRecDurationDataAVG;
                    options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG) ?>';
                }

                let data = google.visualization.arrayToDataTable(graphData);
                let view = new google.visualization.DataView(data);
                let arr = [0];
                let c = [];
                $('.totalChartColumns:checked').each( function(i, elem) {
                    arr.push((+$(elem).val()));
                    c.push(colors[+$(elem).val()-1]);
                });
                options.colors = c;
                options.title = measuresText[val]+': ' + timeRange;

                $('select[name="'+$(this).attr('data-name')+'"]').val($(this).val()).change();

                view.setColumns(arr);
                totalCallsChart.draw(data, options);
                totalCallsChart.draw(view, options);
            });

            $('.totalChartColumns').on('change', function () {
                var data = google.visualization.arrayToDataTable(graphData);
                var view = new google.visualization.DataView(data);
                var arr = [0];
                let c = [];
                $('.totalChartColumns').each( function(i, elem) {
                    if ($(elem).prop('checked')) {
                        arr.push(+$(elem).val());
                        c.push(colors[+$(elem).val()-1]);
                    }

                    if (arr.length > 1) {
                        $('input[name="'+$(elem).attr('data-name')+'"][value="'+$(elem).val()+'"]').prop('checked', $(elem).prop('checked'));
                    }
                });

                if (arr.length < 2) {
                    $(this).prop('checked', !$(this).prop('checked'));
                    new PNotify({
                        title: 'Warning',
                        text: 'Graph must contain min 1 column',
                        type: 'warning'
                    });
                    return false;
                }

                view.setColumns(arr);
                options.colors = c;
                totalCallsChart.draw(view, options);
            });
        });
    });
</script>
<?php else: ?>
    <div class="row">
        <div class="col-md-12 text-center">
            <p style="margin: 0;">Not Found Data</p>
        </div>
    </div>
<?php endif; ?>