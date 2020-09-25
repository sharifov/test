<?php

?>

<?php if (isset($callsInfoGraph) && $callsInfoGraph): ?>

    <div id="info-calls-chart"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(function () {
            var totalCallsChart = new google.visualization.ComboChart(document.getElementById('info-calls-chart'));

            //var colors = ['#8ec5ff', '#dd4b4e', '#587ca6'];

            /*var options = {
                title: 'User Activity Dynamics',
                chartArea: {width: '95%', right: 10},
                textStyle: {
                    color: '#596b7d'
                },
                titleColor: '#596b7d',
                fontSize: 14,
                //color: '#596b7d',
                //colors: colors,
                //enableInteractivity: true,
                height: 350,
                width: 1145,
                animation: {
                    duration: 200,
                    easing: 'linear',
                    startup: true
                },
                //legend: {
                //    position: 'top',
                //    alignment: 'end'
                //},
                hAxis: {
                    title: '',
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
                    title: 'Requests',
                    titleColor: '#596b7d',
                },
                theme: 'material',
                //isStacked: false,
                bar: {groupWidth: "50%"}
            };*/

            var options = {
                title : 'Dynamic of User Calls by Statuses',
                vAxis: {title: 'Calls'},
                hAxis: {
                    title: 'Days',
                    slantedText: true,
                    slantedTextAngle: 30,
                },
                seriesType: 'bars',
                //series: {6: {type: 'bars'}},
                /*series: {
                    8: {
                        annotations: {
                            stem: {
                                color: "#a03e3e",
                                length: 28
                            },
                            textStyle: {
                                color: "#000000",
                            }
                        },
                        enableInteractivity: false,
                        tooltip: "none",
                        visibleInLegend: false
                    }
                },*/
                legend: {
                    position: 'top',
                    alignment: 'end'
                },
                height: 650,
                width: 1145,
                chartArea: {width: '95%', right: 10},
                theme: 'material',
                isStacked: true,
                enableInteractivity: true
            };

            var data = google.visualization.arrayToDataTable([
                [
                    'Days',
                    'Complete',
                    'Busy',
                    'Not Answered',
                    'Failed',
                    'Canceled',
                    'Declined',
                    {role: 'annotation'}
                ],
                <?php foreach($callsInfoGraph as $k => $item): ?>
                [
                    '<?=($item['createdDate']) ?>',
                    <?= $item['callsComplete'] ?>,
                    <?= $item['callsBusy'] ?>,
                    <?= $item['callsNotAnswered'] ?>,
                    <?= $item['callsFailed'] ?>,
                    <?= $item['callsCanceled'] ?>,
                    <?= $item['callsDeclined'] ?>,
                    'Total: <?= $item['callsComplete'] + $item['callsBusy'] + $item['callsNotAnswered'] + $item['callsFailed'] + $item['callsCanceled'] + $item['callsDeclined'] ?>',
                ],
                <?php endforeach; ?>
            ]);
            totalCallsChart.draw(data, options);

            $(window).on('resize', function () {
                options.width = document.getElementById('tab_content9').clientWidth
                totalCallsChart.draw(data, options)
            })
        })
    </script>
<?php endif; ?>
