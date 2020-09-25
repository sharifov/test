<?php

?>

<?php if (isset($chatInfoGraph) && $chatInfoGraph): ?>

    <div id="info-chat-chart"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(function () {
            var totalChatChart = new google.visualization.ComboChart(document.getElementById('info-chat-chart'));
            var options = {
                title : 'Dynamic of User Chat by Statuses',
                vAxis: {title: 'Chats'},
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
                    'Generate',
                    'Pending',
                    'Transfer',
                    'Closed',
                    {role: 'annotation'}
                ],
                <?php foreach($chatInfoGraph as $k => $item): ?>
                [
                    '<?=($item['createdDate']) ?>',
                    <?= $item['chatGenerated'] ?>,
                    <?= $item['chatPending'] ?>,
                    <?= $item['chatTransfer'] ?>,
                    <?= $item['chatClosed'] ?>,
                    'Total: <?= $item['chatGenerated'] + $item['chatPending'] + $item['chatTransfer'] + $item['chatClosed'] ?>',
                ],
                <?php endforeach; ?>
            ]);
            totalChatChart.draw(data, options);

            $(window).on('resize', function () {
                options.width = document.getElementById('tab_content12').clientWidth
                totalChatChart.draw(data, options)
            })
        })
    </script>
<?php endif; ?>
