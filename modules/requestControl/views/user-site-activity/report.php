<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\requestControl\models\search\UserSiteActivitySearch */
/* @var $data array */

$this->title = 'User Site Activities Report';
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php
$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>
<div class="user-site-activity-report">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //= Html::a('Create User Site Activity', ['create'], ['class' => 'btn btn-success']) ?>
        <?php //= Html::a('<i class="fa fa-remove"></i> Clear Logs ('.(Yii::$app->params['settings']['user_site_activity_log_history_days'] ?? '-').' days limit)', ['user-site-activity/clear-logs'], ['class' => 'btn btn-danger']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search_report', ['model' => $searchModel]); ?>

    <?php //\yii\helpers\VarDumper::dump($data, 10, true)?>

    <?php if (isset($data['byHour']) && $data['byHour']) : ?>
        <div class="row">
            <div class="col-md-12">

                <div id="chart_div"></div>

                <?php
                    $this->registerJs("google.charts.load('current', {'packages':['bar']}); google.charts.setOnLoadCallback(drawChart);", \yii\web\View::POS_READY);
                ?>

                <script>
                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['Days', 'Count', {role: 'annotation'}],
                            <?php foreach ($data['byHour'] as $k => $item) :?>
                            ['<?=($item['created_hour'])?>:00, <?=date('d-M', strtotime($item['created_date']))?> ', <?=$item['cnt']?>, '<?='--'?>'],
                            <?php endforeach;?>
                        ]);

                        var options = {
                            chart: {
                                title: 'User Requests',
                                subtitle: 'UTC time format',
                            },
                            title: 'User Requests',
                            height: 500,
                            vAxis: {
                                title: 'Requests'
                            }
                        };

                        //var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));


                        var chart = new google.charts.Bar(document.getElementById('chart_div'));

                        chart.draw(data, options);
                        //chart.draw(data, google.charts.Bar.convertOptions(options));

                    }
                </script>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">

            <div class="col-md-6">
                <div id="chart_div2"></div>
                <?php if (isset($data['byUser']) && $data['byUser']) : ?>
                    <?php
                    $this->registerJs('google.charts.setOnLoadCallback(drawBasic2);', \yii\web\View::POS_READY);
                    ?>

                    <script>
                        function drawBasic2() {
                            var data = google.visualization.arrayToDataTable([
                                ['Agent', 'Count of Requests'],
                                <?php foreach ($data['byUser'] as $k => $item) :
                                    $employee = \common\models\Employee::find()->where(['id' => $item['user_id']])->limit(1)->one();
                                    if (!$employee) {
                                        continue;
                                    }
                                    ?>
                                ['<?php echo \yii\helpers\Html::encode($employee->username) ?>', <?=$item['cnt']?>],
                                <?php endforeach;?>
                            ]);

                            var options = {
                                title: 'Count of requests, limit 20 employees',
                                height: 400
                            };

                            var chart = new google.visualization.PieChart(document.getElementById('chart_div2'));
                            chart.draw(data, options);
                        }
                    </script>

                <?php endif; ?>
            </div>

        <div class="col-md-6">
            <div id="chart_div3"></div>
            <?php if (isset($data['byPage']) && $data['byPage']) : ?>
                <?php
                $this->registerJs('google.charts.setOnLoadCallback(drawBasic3);', \yii\web\View::POS_READY);
                ?>

                <script>
                    function drawBasic3() {
                        var data = google.visualization.arrayToDataTable([
                            ['Page URL', 'Count of Requests'],
                            <?php foreach ($data['byPage'] as $k => $item) : ?>
                            ['<?php echo \yii\helpers\Html::encode($item['page_url']) ?>', <?=$item['cnt']?>],
                            <?php endforeach;?>
                        ]);

                        var options = {
                            title: 'Count of requests, limit 20 pages',
                            height: 400
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('chart_div3'));
                        chart.draw(data, options);
                    }
                </script>

            <?php endif; ?>
        </div>

    </div>


    <?php Pjax::end(); ?>

</div>
