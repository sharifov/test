<?php

use common\models\Employee;
use common\models\UserParams;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model Employee */
/* @var $userActivity */

?>

<?php /*if (isset($userActivity['byHour']) && $userActivity['byHour']): */ ?><!--

    <div id="chart_div"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages': ['corechart', 'bar']});
        google.charts.setOnLoadCallback(function () {
            var totalCallsChart = new google.visualization.ColumnChart(document.getElementById('chart_div'));

            //var colors = ['#8ec5ff', '#dd4b4e', '#587ca6'];

            var options = {
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
                // legend: {
                //     position: 'top',
                //     alignment: 'end'
                // },
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
            };

            var data = google.visualization.arrayToDataTable([
                ['Days', 'Requests', {role: 'annotation'}],
                <?php /*foreach($userActivity['byHour'] as $k => $item): */ ?>
                ['<? /*=($item['created_hour']) */ ?>:00, <? /*=date('d-M', strtotime($item['created_date'])) */ ?> ', <? /*= $item['cnt'] */ ?>, '<? /*= ' ' */ ?>'],
                <?php /*endforeach; */ ?>
            ]);
            totalCallsChart.draw(data, options);

            $(window).on('resize', function () {
                options.width = document.getElementById('tab_content1').clientWidth
                totalCallsChart.draw(data, options)
            })
        })
    </script>
--><?php /*endif; */ ?>

<?php Pjax::begin() ?>
<h5>Recent Activity</h5>
<div class="well">
    <?= GridView::widget([
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $userActivity['byPage'],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]),
        'emptyTextOptions' => ['class' => 'text-center'],
        'columns' => [
            [
                'label' => 'Page Url',
                'attribute' => 'page_url',
            ],
            [
                'label' => 'Visits',
                'attribute' => 'cnt',
            ],
        ]
    ]);
?>
</div>
<?php Pjax::end() ?>

<!--
<ul class="messages">
    <li>
        <img src="images/img.jpg" class="avatar" alt="Avatar">
        <div class="message_date">
            <h3 class="date text-info">24</h3>
            <p class="month">May</p>
        </div>
        <div class="message_wrapper">
            <h4 class="heading">Desmond Davison</h4>
            <blockquote class="message">Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu
                stumptown aliqua butcher retro keffiyeh dreamcatcher synth.
            </blockquote>
            <br>
            <p class="url">
                <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
                <a href="#"><i class="fa fa-paperclip"></i> User Acceptance Test.doc </a>
            </p>
        </div>
    </li>
    <li>
        <img src="images/img.jpg" class="avatar" alt="Avatar">
        <div class="message_date">
            <h3 class="date text-error">21</h3>
            <p class="month">May</p>
        </div>
        <div class="message_wrapper">
            <h4 class="heading">Brian Michaels</h4>
            <blockquote class="message">Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu
                stumptown aliqua butcher retro keffiyeh dreamcatcher synth.
            </blockquote>
            <br>
            <p class="url">
                <span class="fs1" aria-hidden="true" data-icon=""></span>
                <a href="#" data-original-title="">Download</a>
            </p>
        </div>
    </li>
    <li>
        <img src="images/img.jpg" class="avatar" alt="Avatar">
        <div class="message_date">
            <h3 class="date text-info">24</h3>
            <p class="month">May</p>
        </div>
        <div class="message_wrapper">
            <h4 class="heading">Desmond Davison</h4>
            <blockquote class="message">Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu
                stumptown aliqua butcher retro keffiyeh dreamcatcher synth.
            </blockquote>
            <br>
            <p class="url">
                <span class="fs1 text-info" aria-hidden="true" data-icon=""></span>
                <a href="#"><i class="fa fa-paperclip"></i> User Acceptance Test.doc </a>
            </p>
        </div>
    </li>
    <li>
        <img src="images/img.jpg" class="avatar" alt="Avatar">
        <div class="message_date">
            <h3 class="date text-error">21</h3>
            <p class="month">May</p>
        </div>
        <div class="message_wrapper">
            <h4 class="heading">Brian Michaels</h4>
            <blockquote class="message">Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu
                stumptown aliqua butcher retro keffiyeh dreamcatcher synth.
            </blockquote>
            <br>
            <p class="url">
                <span class="fs1" aria-hidden="true" data-icon=""></span>
                <a href="#" data-original-title="">Download</a>
            </p>
        </div>
    </li>
</ul>-->