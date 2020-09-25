<?php

?>

<?php if (isset($emailsInfoGraph) && $emailsInfoGraph): ?>

    <div id="info-emails-chart"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(function () {
            var totalEmailsChart = new google.visualization.ComboChart(document.getElementById('info-emails-chart'));
            var options = {
                title : 'Dynamic of User Emails by Statuses',
                vAxis: {title: 'Emails'},
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
                <?php foreach($emailsInfoGraph as $k => $item): ?>
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
                options.width = document.getElementById('tab_content10').clientWidth
                totalEmailsChart.draw(data, options)
            })
        })
    </script>
<?php endif; ?>
