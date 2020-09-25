<?php

?>

<?php if (isset($smsInfoGraph) && $smsInfoGraph): ?>

    <div id="info-sms-chart"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(function () {
            var totalSmsChart = new google.visualization.ComboChart(document.getElementById('info-sms-chart'));
            var options = {
                title : 'Dynamic of User Sms by Statuses',
                vAxis: {title: 'Sms'},
                hAxis: {
                    title: 'Days',
                    slantedText: true,
                    slantedTextAngle: 30,
                },
                seriesType: 'bars',
                //series: {5: {type: 'bars'}},
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
                    'Done',
                    'Error',
                    {role: 'annotation'}
                ],
                <?php foreach($smsInfoGraph as $k => $item): ?>
                [
                    '<?=($item['createdDate']) ?>',
                    <?= $item['smsDone'] ?>,
                    <?= $item['smsError'] ?>,
                    'Total: <?= $item['smsDone'] + $item['smsError'] ?>',
                ],
                <?php endforeach; ?>
            ]);
            totalSmsChart.draw(data, options);

            $(window).on('resize', function () {
                options.width = document.getElementById('tab_content11').clientWidth
                totalSmsChart.draw(data, options)
            })
        })
    </script>
<?php endif; ?>
