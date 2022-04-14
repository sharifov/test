<?php

use src\entities\call\CallGraphsSearch;
use src\viewmodel\call\ViewModelTotalCallGraph;

/**
 * @var $viewModel ViewModelTotalCallGraph
 */
?>

<?php if ($viewModel->callData) : ?>
    <div class="btn-toolbar">
        <?= $this->render('_call_graph_export', ['viewModel' => $viewModel]) ?>
        <div class="btn-group" role="group">
            <button id="lineType" class="btn btn-outline-secondary btn-group ml-2" value="LineChart"><i
                        class="fa fa-line-chart blue"></i></button>
            <button id="columnType" class="btn btn-outline-secondary btn-group" value="ColumnChart"><i
                        class="fa fa-bar-chart blue"></i></button>
        </div>
    </div>
    <div id="myChart"></div>


    <div class="row d-flex align-items-center">
        <div class="col-md-2">
            <label class="control-label">View Columns on Graph</label>
            <?= \yii\helpers\Html::checkboxList(
                'totalChartColumns',
                $viewModel->callGraphsSearch->totalChartColumns,
                $viewModel->callGraphsSearch::getChartTotalCallTextList(),
                [
                    'itemOptions' => [
                        'class' => 'totalChartColumns',
                        'data-name' => $viewModel->callGraphsSearch->formName() . '[totalChartColumns][]'
                    ]
                ]
            ) ?>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label class="control-label">Measure</label>
                <?= \yii\helpers\Html::dropDownList(
                    'chartTotalCallsVaxis',
                    $viewModel->callGraphsSearch->chartTotalCallsVaxis,
                    $viewModel->callGraphsSearch::getChartTotalCallsVaxisTextList(),
                    [
                        'class' => 'form-control chartTotalCallsVaxis',
                        'data-name' => $viewModel->callGraphsSearch->formName() . '[chartTotalCallsVaxis]'
                    ]
                ) ?>
            </div>
        </div>
    </div>

    <?= $this->render('_total_calls_chart_summary', [
        'totalCallsDbData' => $viewModel->callData,
        'groupsCount' => $viewModel->groupsCount
    ]) ?>

    <script type="text/javascript">
        $(document).ready(function () {

            $('a[class^="export-full"]').off();

            var graphData;
            var totalCallsData = <?= $viewModel->totalCallsGraphData ?>;
            var totalCallsDataAvg = <?= $viewModel->totalCallsGraphDataAvg ?>;
            var totalCallsRecDurationData = <?= $viewModel->totalCallsRecDurationData ?>;
            var totalCallsRecDurationDataAVG = <?= $viewModel->totalCallsRecDurationDataAVG ?>;
            var groupBy = 'Grouped by ' + '<?= CallGraphsSearch::DATE_FORMAT_TEXT[$viewModel->callGraphsSearch->callGraphGroupBy] ?>';

            var selectedMeasure = +$('.chartTotalCallsVaxis').val();
            var measuresText = <?= json_encode(CallGraphsSearch::getChartTotalCallsVaxisTextList()) ?>;
            var timeRange = '<?= $viewModel->callGraphsSearch->createTimeRange ?>';

            if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>) {
                graphData = totalCallsData;
            } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>) {
                graphData = totalCallsDataAvg;
            } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>) {
                graphData = totalCallsRecDurationData;
            } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>) {
                graphData = totalCallsRecDurationDataAVG;
            }

            google.charts.load('current', {'packages': ['corechart', 'bar']});
            google.charts.setOnLoadCallback(function () {

                var chartType = document.getElementById("lineType").value;
                var totalCallsChart = new google.visualization[chartType](document.getElementById('myChart'));

                var colors = ['#8ec5ff', '#dd4b4e', '#587ca6'];

                var options = {
                    title: measuresText[selectedMeasure] + ': ' + timeRange + ' ' + groupBy,
                    chartArea: {width: '95%', right: 10},
                    textStyle: {
                        color: '#596b7d'
                    },
                    titleColor: '#596b7d',
                    fontSize: 14,
                    color: '#596b7d',
                    colors: colors,
                    enableInteractivity: true,
                    height: 650,
                    animation: {
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
                        slantedText: true,
                        slantedTextAngle: 30,
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
                    },
                    theme: 'material',
                    //isStacked: true,
                    bar: {groupWidth: "50%"}
                };

                // let newGraphData = [];
                var data = google.visualization.arrayToDataTable(graphData);
                var view = new google.visualization.DataView(data);
                var arr = [0];
                var c = [];

                var indexes = {
                    0: [1, 2],
                    1: [3, 4],
                };

                $('.totalChartColumns:checked').each(function (i, elem) {
                    if (
                        selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>
                        || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>
                        || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>
                        || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>
                    ) {
                        indexes[i].forEach(function (e) {
                            arr.push(e);
                        });
                        c.push(colors[+$(elem).val() - 1]);
                    } else {
                        arr.push((+$(elem).val()));
                        c.push(colors[+$(elem).val() - 1]);
                    }
                });
                totalCallsChart.draw(data, options);
               /* setTimeout(function () {
                    options.colors = c;
                    view.setColumns(arr);
                    totalCallsChart.draw(view, options);
                }, 1);*/
                $(window).resize(function () {
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
                    var selectedMeasure = +$('.chartTotalCallsVaxis').val();
                    $('.totalChartColumns:checked').each(function (i, elem) {
                        if (
                            selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>
                            || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>
                            || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>
                            || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>
                        ) {
                            indexes[i].forEach(function (e) {
                                arr.push(e);
                            });
                            c.push(colors[+$(elem).val() - 1]);
                        } else {
                            arr.push((+$(elem).val()));
                            c.push(colors[+$(elem).val() - 1]);
                        }
                    });
                    options.colors = c;
                    options.title = measuresText[val] + ': ' + timeRange + ' ' + groupBy;

                    $('select[name="' + $(this).attr('data-name') + '"]').val($(this).val()).change();
                    view.setColumns(arr);
                    totalCallsChart.draw(data, options);
                    totalCallsChart.draw(view, options);
                });

                $('.totalChartColumns').on('change', function () {
                    var data = google.visualization.arrayToDataTable(graphData);
                    var view = new google.visualization.DataView(data);
                    var arr = [0];
                    let c = [];
                    var selectedMeasure = +$('.chartTotalCallsVaxis').val();
                    $('.totalChartColumns').each(function (i, elem) {
                        if ($(elem).prop('checked')) {
                            if (
                                selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>
                                || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>
                                || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>
                                || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>
                            ) {
                                indexes[i].forEach(function (e) {
                                    arr.push(e);
                                });
                                c.push(colors[+$(elem).val() - 1]);
                            } else {
                                arr.push((+$(elem).val()));
                                c.push(colors[+$(elem).val() - 1]);
                            }
                        }

                        $('input[name="' + $(elem).attr('data-name') + '"][value="' + $(elem).val() + '"]').prop('checked', $(elem).prop('checked'));
                    });

                    if (arr.length < 2) {
                        $(this).prop('checked', !$(this).prop('checked'));
                        createNotifyByObject({
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

                $(window).on('resize', function () {
                    var data = google.visualization.arrayToDataTable(graphData);
                    var view = new google.visualization.DataView(data);
                    var arr = [0];
                    let c = [];
                    var selectedMeasure = +$('.chartTotalCallsVaxis').val();
                    $('.totalChartColumns').each(function (i, elem) {
                        if ($(elem).prop('checked')) {
                            if (
                                selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>
                                || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>
                                || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>
                                || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>
                            ) {
                                indexes[i].forEach(function (e) {
                                    arr.push(e);
                                });
                                c.push(colors[+$(elem).val() - 1]);
                            } else {
                                arr.push((+$(elem).val()));
                                c.push(colors[+$(elem).val() - 1]);
                            }
                        }

                        $('input[name="' + $(elem).attr('data-name') + '"][value="' + $(elem).val() + '"]').prop('checked', $(elem).prop('checked'));
                    });

                    if (arr.length < 2) {
                        $(this).prop('checked', !$(this).prop('checked'));
                        createNotifyByObject({
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

                $("#lineType").on('click', function () {
                    chartType = document.getElementById("lineType").value;
                    totalCallsChart = new google.visualization[chartType](document.getElementById('myChart'));
                    //totalCallsChart.draw(data, options);
                    var selectedMeasure = +$('.chartTotalCallsVaxis').val();

                    if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>) { // calls
                        graphData = totalCallsData;
                        options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS) ?>';
                    } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>) {
                        graphData = totalCallsDataAvg;
                        options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG) ?>';
                    } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>) { // calls recording
                        graphData = totalCallsRecDurationData;
                        options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION) ?>';
                    } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>) {
                        graphData = totalCallsRecDurationDataAVG;
                        options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG) ?>';
                    }

                    let data = google.visualization.arrayToDataTable(graphData);
                    let view = new google.visualization.DataView(data);
                    let arr = [0];
                    let c = [];

                    $('.totalChartColumns:checked').each(function (i, elem) {
                        if ($(elem).prop('checked')) {
                            if (
                                selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>
                                || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>
                                || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>
                                || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>
                            ) {
                                indexes[i].forEach(function (e) {
                                    arr.push(e);
                                });
                                c.push(colors[+$(elem).val() - 1]);
                            } else {
                                arr.push((+$(elem).val()));
                                c.push(colors[+$(elem).val() - 1]);
                            }
                        }
                    });
                    options.colors = c;
                    options.title = measuresText[selectedMeasure] + ': ' + timeRange  + ' ' + groupBy;

                    $('select[name="' + $(this).attr('data-name') + '"]').val($(this).val()).change();

                    view.setColumns(arr);
                    totalCallsChart.draw(data, options);
                    //totalCallsChart.draw(view, options);
                });

                $("#columnType").on('click', function () {
                    chartType = document.getElementById("columnType").value;
                    totalCallsChart = new google.visualization[chartType](document.getElementById('myChart'));
                    //totalCallsChart.draw(data, options);
                    var selectedMeasure = +$('.chartTotalCallsVaxis').val();

                    if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>) { // calls
                        graphData = totalCallsData;
                        options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS) ?>';
                    } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>) {
                        graphData = totalCallsDataAvg;
                        options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG) ?>';
                    } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>) { // calls recording
                        graphData = totalCallsRecDurationData;
                        options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION) ?>';
                    } else if (selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>) {
                        graphData = totalCallsRecDurationDataAVG;
                        options.vAxis.title = '<?= CallGraphsSearch::getChartTotalCallsVaxisText(CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG) ?>';
                    }

                    let data = google.visualization.arrayToDataTable(graphData);
                    let view = new google.visualization.DataView(data);
                    let arr = [0];
                    let c = [];

                    $('.totalChartColumns:checked').each(function (i, elem) {
                        if (
                            selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION ?>
                            || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_REC_DURATION_AVG ?>
                            || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS_AVG ?>
                            || selectedMeasure === <?= CallGraphsSearch::CHART_TOTAL_CALLS_VAXIS_CALLS ?>
                        ) {
                            indexes[i].forEach(function (e) {
                                arr.push(e);
                            });
                            c.push(colors[+$(elem).val() - 1]);
                        } else {
                            arr.push((+$(elem).val()));
                            c.push(colors[+$(elem).val() - 1]);
                        }
                    });
                    options.colors = c;
                    options.title = measuresText[selectedMeasure] + ': ' + timeRange + ' ' + groupBy;

                    $('select[name="' + $(this).attr('data-name') + '"]').val($(this).val()).change();

                    view.setColumns(arr);
                    totalCallsChart.draw(data, options);
                    //totalCallsChart.draw(view, options);
                });
            });
        });
    </script>
<?php else : ?>
    <div class="row">
        <div class="col-md-12 text-center">
            <p style="margin: 0;">Not Found Data</p>
        </div>
    </div>
<?php endif; ?>