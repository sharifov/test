<?php

?>
<h5>Calls Chart</h5>
<div class="well">
    <?php if (isset($callsInfoGraph) && $callsInfoGraph) : ?>
        <div id="info-calls-chart"></div>

        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(function () {
                var totalCallsChart = new google.visualization.ComboChart(document.getElementById('info-calls-chart'));

                let options = {
                    title: 'Dynamic of User Calls by Statuses',
                    vAxis: {title: 'Calls'},
                    annotations: {
                        alwaysOutside: true
                    },
                    hAxis: {
                        title: 'Days',
                        slantedText: true,
                        slantedTextAngle: 30,
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

                let data = google.visualization.arrayToDataTable([
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
                    <?php foreach ($callsInfoGraph as $k => $item) : ?>
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
                    options.width = document.getElementById('tab_content9').clientWidth - 15
                    totalCallsChart.draw(data, options)
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