<?php

?>
<h5>Sms Chart</h5>
<div class="well">
    <?php if (isset($smsInfoGraph) && $smsInfoGraph) : ?>
        <div id="info-sms-chart"></div>

        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(function () {
                var totalSmsChart = new google.visualization.ComboChart(document.getElementById('info-sms-chart'));
                var options = {
                    title: 'Dynamic of User Sms by Statuses',
                    vAxis: {title: 'Sms'},
                    hAxis: {
                        title: 'Days',
                        slantedText: true,
                        slantedTextAngle: 30,
                    },
                    annotations: {
                        alwaysOutside: true
                    },
                    seriesType: 'bars',
                    legend: {
                        position: 'top',
                        alignment: 'end',
                        textStyle:{fontSize:10}
                    },
                    height: 650,
                    width: 1160,
                    chartArea: {width: '95%', right: 10},
                    theme: 'material',
                    isStacked: true,
                    //enableInteractivity: true
                };

                var data = google.visualization.arrayToDataTable([
                    [
                        'Days',
                        'Done',
                        'Error',
                        {role: 'annotation'}
                    ],
                    <?php foreach ($smsInfoGraph as $k => $item) : ?>
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
                    options.width = document.getElementById('tab_content11').clientWidth - 15
                    totalSmsChart.draw(data, options)
                })
            })
        </script>
    <?php else : ?>
        <div class="row">
            <div class="col-md-12 text-center">
                <p style="margin: 0;">No results found.</p>
            </div>
        </div>
    <?php endif; ?>
</div>