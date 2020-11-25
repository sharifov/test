<?php
$this->title = 'Client stats';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-stats">

    <div class="row">
        <div class="col-md-4">
            <div id="chart_div_projects"></div>
            <?php if ($data) : ?>
                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                <script>
                    function drawBasic() {
                        var data = google.visualization.arrayToDataTable([
                            ['Project', 'Count'],
                            <?php foreach ($data as $item) : ?>
                            ['<?= \yii\helpers\Html::encode($item['projectName']) . ' (' . $item['countClients'] . ')\'' ?> , <?= $item['countClients'] ?>],
                            <?php endforeach;?>
                            ]);

                        var options = {
                            title: 'Client project stats',
                            height: 400
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('chart_div_projects'));
                        chart.draw(data, options);
                    }

                    google.charts.load('current', {packages: ['corechart', 'bar']});
                    google.charts.setOnLoadCallback(drawBasic);
                </script>
            <?php endif; ?>
        </div>

    </div>
</div>
