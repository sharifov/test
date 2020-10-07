<?php

?>
<h5>Client Chat Chart</h5>
<div class="well">
    <?php if (isset($chatInfoGraph) && $chatInfoGraph): ?>

        <div id="info-chat-chart"></div>

        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(function () {
                let totalChatChart = new google.visualization.ComboChart(document.getElementById('info-chat-chart'));
                let options = {
                    title: 'Dynamic of User Chat by Statuses',
                    vAxis: {title: 'Chats'},
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
                    width: 1125,
                    chartArea: {width: '95%', right: 10},
                    theme: 'material',
                    isStacked: true,
                    //enableInteractivity: true
                };

                let data = google.visualization.arrayToDataTable([
                    [
                        'Days',
                        'New',
                        'Pending',
                        'Progress',
                        'Transfer',
                        'Hold',
                        'Closed',
                        {role: 'annotation'}
                    ],
                    <?php foreach($chatInfoGraph as $k => $item): ?>
                    [
                        '<?=($item['createdDate']) ?>',
                        <?= $item['chatNew'] ?>,
                        <?= $item['chatPending'] ?>,
                        <?= $item['chatProgress'] ?>,
                        <?= $item['chatTransfer'] ?>,
                        <?= $item['chatHold'] ?>,
                        <?= $item['chatClosed'] ?>,
                        'Total: <?= $item['chatNew'] + $item['chatPending'] + $item['chatProgress'] + $item['chatTransfer'] + $item['chatHold'] + $item['chatClosed'] ?>',
                    ],
                    <?php endforeach; ?>
                ]);
                totalChatChart.draw(data, options);

                $(window).on('resize', function () {
                    options.width = document.getElementById('tab_content12').clientWidth - 20
                    totalChatChart.draw(data, options)
                })
            })
        </script>
    <?php else: ?>
        <div class="row">
            <div class="col-md-12 text-center">
                <p style="margin: 0;">No results found.</p>
            </div>
        </div>
    <?php endif; ?>
</div>