<?php

use sales\viewmodel\chat\ViewModelChatGraph;

/**
 * @var $viewModel ViewModelChatGraph
 */
?>

<?php if ($viewModel->preparedData): ?>
    <div id="myChart"></div>
    <script type="text/javascript">
        $(document).ready( function () {
            $('a[class^="export-full"]').off();
            var graphData = <?= $viewModel->preparedData ?>;

            google.charts.load('current', {'packages':['corechart','bar']});
            google.charts.setOnLoadCallback(function () {
                var totalCallsChart = new google.visualization.ColumnChart(document.getElementById('myChart'));

                var colors = ['#8ec5ff', '#dd4b4e', '#587ca6'];

                var options = {
                    title: 'Client Chat Activity',
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
                        title: 'Chats',
                        titleColor: '#596b7d',
                    },
                    theme: 'material',
                    isStacked: true,
                    bar: {groupWidth: "50%"}
                };

                var data = google.visualization.arrayToDataTable(graphData);
                totalCallsChart.draw(data, options);

                $(window).on('resize', function () {
                    totalCallsChart.draw(data, options);
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