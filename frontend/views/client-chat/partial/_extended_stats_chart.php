<?php

use sales\entities\chat\ChatExtendedGraphsSearch;

/**
 * @var $viewModel \sales\viewModel\chat\ViewModelChatExtendedGraph
 */
?>

<?php if ($viewModel->preparedData) : ?>
    <div class="btn-group" role="group">
        <button id="lineType" class="btn btn-outline-secondary btn-group ml-2" value="LineChart"><i class="fa fa-line-chart blue"></i></button>
        <button id="columnType" class="btn btn-outline-secondary btn-group" value="ColumnChart"><i class="fa fa-bar-chart blue"></i></button>
    </div>

    <div id="myChart"></div>
    <script type="text/javascript">
        $(document).ready(function () {
            var graphData = <?= $viewModel->preparedData ?>;
            let timeRange = '<?= $viewModel->chatExtendedGraphsSearch->createTimeRange ?>';
            let groupBy = 'Grouped by ' + '<?= ChatExtendedGraphsSearch::DATE_FORMAT_TEXT[$viewModel->chatExtendedGraphsSearch->graphGroupBy] ?>';
            google.charts.load('current', {'packages': ['corechart', 'bar']});
            google.charts.setOnLoadCallback(function () {
                //var colors = ['#8ec5ff', '#dd4b4e', '#587ca6'];
                var options = {
                    title: 'Dynamic of New Initiated Chats by Client / Agent: ' + timeRange + ' ' + groupBy,
                    chartArea: {width: '95%', right: 10},
                    textStyle: {
                        color: '#596b7d'
                    },
                    titleColor: '#596b7d',
                    fontSize: 14,
                    color: '#596b7d',
                    //colors: colors,
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
                        title: 'Chats',
                        titleColor: '#596b7d',
                    },
                    theme: 'material',
                    //isStacked: true,
                    bar: {groupWidth: "50%"},
                    tooltip: {isHtml: true}
                };

                var chartType = document.getElementById("lineType").value;
                var totalCallsChart = new google.visualization[chartType](document.getElementById('myChart'));

                var data = google.visualization.arrayToDataTable(graphData);

                totalCallsChart.draw(data, options);

                $(window).on('resize', function () {
                    totalCallsChart.draw(data, options);
                });

                $("#lineType").on('click', function () {
                    chartType = document.getElementById("lineType").value;
                    totalCallsChart = new google.visualization[chartType](document.getElementById('myChart'));
                    totalCallsChart.draw(data, options);
                });

                $("#columnType").on('click', function () {
                    chartType = document.getElementById("columnType").value;
                    totalCallsChart = new google.visualization[chartType](document.getElementById('myChart'));
                    totalCallsChart.draw(data, options);
                });
            });
        });
    </script>

    <?= $this->render('_extended_stats_chart_summary', [
        'viewModel' => $viewModel,
    ]) ?>

<?php else : ?>
    <div class="row">
        <div class="col-md-12 text-center">
            <p style="margin: 0;">Not Found Data</p>
        </div>
    </div>
<?php endif; ?>