<?php

?>
<h5>Email Chart</h5>
<div class="well">
    <?php if (isset($emailsInfoGraph) && $emailsInfoGraph) : ?>
        <div id="info-emails-chart"></div>

        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(function () {
                let totalEmailsChart = new google.visualization.ComboChart(document.getElementById('info-emails-chart'));
                let options = {
                    title: 'Dynamic of User Emails by Statuses',
                    vAxis: {title: 'Emails'},
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
                        textStyle:{fontSize:10},
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
                        'Done',
                        'Error',
                        {role: 'annotation'}
                    ],
                    <?php foreach ($emailsInfoGraph as $k => $item) : ?>
                    [
                        '<?=($item['createdDate']) ?>',
                        <?= $item['emailsDone'] ?>,
                        <?= $item['emailsError'] ?>,
                        'Total: <?= $item['emailsDone'] + $item['emailsError'] ?>',
                    ],
                    <?php endforeach; ?>
                ]);
                totalEmailsChart.draw(data, options);

                $(window).on('resize', function () {
                    options.width = document.getElementById('tab_content10').clientWidth - 15
                    totalEmailsChart.draw(data, options)
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