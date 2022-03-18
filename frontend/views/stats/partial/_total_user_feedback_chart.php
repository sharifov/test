<?php

/**
 * @var \src\viewModel\userFeedback\ViewModelUserFeedbackGraph $viewModel
 */

use modules\user\userFeedback\entity\UserFeedback;

?>

<div class="btn-toolbar"></div>
<div id="myChart"></div>

<div class="row d-flex align-items-center">
    <div class="col-md-2">
        <label class="control-label">View Columns on Graph</label>
        <?= \yii\helpers\Html::checkboxList(
            'totalChartColumns',
            array_keys(UserFeedback::getTypeList()),
            UserFeedback::getTypeList(),
            [
                'itemOptions' => [
                    'class' => 'totalChartColumns',
                    'data-name' => $viewModel->userFeedbackSearch->formName() . '[chartColumns][]'
                ]
            ]
) ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('a[class^="export-full"]').off();

        var graphData;
        var totalCallsData = <?= $viewModel->totalGraphData ?>;
        var timeRange = '<?= $viewModel->userFeedbackSearch->createTimeRange ?>';
        var title = 'User Feedback: ';

        graphData = totalCallsData;

        google.charts.load('current', {'packages': ['corechart', 'bar']});
        google.charts.setOnLoadCallback(function () {

            var chartType = 'ColumnChart';
            var totalCallsChart = new google.visualization[chartType](document.getElementById('myChart'));

            var colors = ['#8ec5ff', '#dd4b4e', '#587ca6'];

            var options = {
                title: title + timeRange,
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
                    title: 'User Feedback',
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
                2: [5, 6],
            };

            totalCallsChart.draw(data, options);
            $(window).resize(function () {
                totalCallsChart.draw(data, options);
            });

            $('.totalChartColumns').on('change', function () {
                var data = google.visualization.arrayToDataTable(graphData);
                var view = new google.visualization.DataView(data);
                var arr = [0];
                let c = [];
                $('.totalChartColumns').each(function (i, elem) {
                    if ($(elem).prop('checked')) {
                        indexes[i].forEach(function (e) {
                            arr.push(e);
                        });
                        c.push(colors[+$(elem).val() - 1]);
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
                $('.totalChartColumns').each(function (i, elem) {
                    if ($(elem).prop('checked')) {
                        indexes[i].forEach(function (e) {
                            arr.push(e);
                        });
                        c.push(colors[+$(elem).val() - 1]);
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

        });
    });
</script>