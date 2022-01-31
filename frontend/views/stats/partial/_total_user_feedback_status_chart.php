<?php
/** @var $statusData array */
?>

<div class="x_panel tile fixed_height_320 overflow_hidden">
    <div class="x_title">
        <h2>User Feedback Statuses</h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <table class="" style="width:100%">
            <tbody>
            <tr>
                <th style="width:37%;">
                    <p>Top 5</p>
                </th>
                <th>
                    <div class="col-lg-7 col-md-7 col-sm-7 ">
                        <p class="">Status</p>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-5 ">
                        <p class="">Total</p>
                    </div>
                </th>
            </tr>
            <tr>
                <td>
                    <canvas id="productsShareChart"></canvas>
                </td>
                <td>
                    <table class="tile_info statusesChartData">
                        <tbody>
                        <?php foreach ($statusData as $statusName => $count) : ?>
                            <tr>
                                <td>
                                    <p><i class="fa fa-square"></i><?= $statusName ?></p>
                                </td>
                                <td><?= $count ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$data = json_encode(array_values($statusData));
$dataNames = json_encode(array_keys($statusData));
$ProductsShareChart = <<< JS
(function () {
let data = JSON.parse('$data');
let dataNames = JSON.parse('$dataNames');
let ctxProductsShare = document.getElementById('productsShareChart').getContext('2d');
let backgroundColor = ["#a9c3cc", "#eacf44", "#ea5558", "#6ec289"];
let backgroundColorChart = ["#a9c3cc", "#eacf44", "#ea5558", "#6ec289"];
$('.statusesChartData .fa-square').each(function (i, e) {
     $(e).css('color', backgroundColor.shift());
});
console.log(ctxProductsShare);
new Chart(ctxProductsShare, {
    type: 'doughnut', 
    tooltipFillColor: "rgba(51, 51, 51, 0.55)",  
    data: {
        labels: dataNames,
        datasets: [{      
            data: data,
            backgroundColor: backgroundColorChart,
            hoverBackgroundColor: ["#b9d5de", "#ffe14a", "#ff5c61", "#7fdf9d"],
            borderWidth: 1
        }]
    },
    options: {
        responsive: false,
        maintainAspectRatio : false,
        legend: {
            display: false,
            position: 'right',
        },
        layout: {
            padding: {
                left: -140,
                right: 0,
                top: 0,
                bottom: 0
            }
        },
    }
});
})();
JS;
$this->registerJs($ProductsShareChart, \yii\web\View::POS_END);
?>