<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
use sales\model\clientChatFeedback\entity\ClientChatFeedback;
use yii\grid\ActionColumn;
use yii\helpers\Html;

/**
 * @var $viewModel \sales\viewModel\chat\ViewModelChatFeedbackGraph
 */

?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php if ($viewModel->prepareStatsData): ?>

    <div id="myChart"></div>
    <script type="text/javascript">
        $(document).ready(function () {
            var graphData = <?= $viewModel->prepareStatsData ?>;

            google.charts.load('current', {'packages': ['corechart', 'bar']});
            google.charts.setOnLoadCallback(function () {
                var feedbackChartData = new google.visualization.ColumnChart(document.getElementById('myChart'));

                //var colors = ['#8ec5ff', '#dd4b4e', '#587ca6'];

                var options = {
                    title: 'Feedback Rating',
                    chartArea: {width: '95%', right: 10},
                    textStyle: {
                        color: '#596b7d'
                    },
                    titleColor: '#596b7d',
                    fontSize: 14,
                    color: '#596b7d',
                    //colors: colors,
                    enableInteractivity: true,
                    height: 650,
                    animation: {
                        duration: 200,
                        easing: 'linear',
                        startup: true
                    },
                    legend: {
                        position: 'top',
                        alignment: 'end'
                    },
                    hAxis: {
                        title: 'Date',
                        slantedText: true,
                        slantedTextAngle: 30,
                        textStyle: {
                            fontSize: 12,
                            color: '#596b7d',
                        },
                        titleColor: '#596b7d',

                    },
                    vAxis: {
                        //format: 'short',
                        title: 'Feedback Percentage Scale',
                        titleColor: '#596b7d',
                        minValue: 0,
                        ticks: [0, .2, .4, .6, .8, 1]
                    },
                    theme: 'material',
                    isStacked: 'percent',
                    bar: {groupWidth: "40%"},
                    tooltip: {isHtml: true}
                };

                var data = google.visualization.arrayToDataTable(graphData);
                feedbackChartData.draw(data, options);

                $(window).on('resize', function () {
                    feedbackChartData.draw(data, options);
                });
            });
        });
    </script>

    <?php Pjax::begin(['enablePushState' => false, 'clientOptions' => ['method' => 'POST']]) ?>
    <?= GridView::widget([
        'dataProvider' => $viewModel->dataProvider,
        'filterModel' => $viewModel->chatFeedbackGraphsSearch,
        'columns' => [
            /*[
                'label' => 'ID',
                'attribute' => 'ccf_id',
                'options' => [
                    'style' => 'width:100px'
                ],
            ],*/
            [
                'label' => 'Client',
                'attribute' => 'ccf_client_id',
                'value' => static function ($model) {
                    return $model['first_name'] . ' ' . $model['last_name'];
                },
                'options' => [
                    'style' => 'width:200px'
                ],
                'filterInputOptions' => [
                        'class' => 'form-control',
                        'placeholder' => 'Client ID'
                ]
            ],

            [
                'label' => 'User',
                'attribute' => 'ccf_user_id',
                'value' => static function ($model) {
                    return $model['username'];
                },
                'options' => [
                    'style' => 'width:200px'
                ],
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'User ID'
                ]
            ],
            [
                'label' => 'Rating',
                'attribute' => 'ccf_rating',
                'value' => static function ($model) {
                    $star = '<span class="fa fa-star green"></span>';
                    $stars = '';
                    for ($i = 0; $i < $model['ccf_rating']; $i++) {
                        $stars .= $star;
                    }
                    return $stars;
                },
                'format' => 'raw',
                'options' => [
                    'style' => 'width:160px'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'filter' => ClientChatFeedback::RATING_LIST,
            ],
            [
                'label' => 'Remark',
                'attribute' => 'ccf_message',
                'filter' => [0 => 'With Remark', 1 => 'Without Remark' ]
            ],
            [
                'label' => 'Created',
                'attribute' => 'ccf_created_dt',
                'value' => static function ($model) {
                    return $model['ccf_created_dt'] ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model['ccf_created_dt'])) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $viewModel->chatFeedbackGraphsSearch,
                    'attribute' => 'ccf_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
                'options' => [
                    'style' => 'width:160px'
                ],
            ],
            [
                'class' => ActionColumn::class,
                //'template' => '{view}<br />{room}',
                'template' => '{room}',
                'contentOptions' => ['style' => 'width:35px; white-space: normal;'],
                'buttons' => [
                    /*'view' => static function ($url, ClientChat $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            [$url],
                            [
                                'target' => '_blank',
                                'data-pjax' => 0,
                                'title' => 'View',
                            ]);
                    },*/
                    'room' => static function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-list-alt"></span>',
                            ['/client-chat-qa/room', 'rid' => $model['cch_rid']],
                            [
                                'target' => '_blank',
                                'data-pjax' => 0,
                                'title' => 'Dialog',
                            ]);
                    },
                ],
            ],
        ],
        'emptyTextOptions' => ['class' => 'text-center']
    ]) ?>
    <?php Pjax::end() ?>
<?php else: ?>
    <div class="row">
        <div class="col-md-12 text-center">
            <p style="margin: 0;">Not Found Data</p>
        </div>
    </div>
<?php endif; ?>
