<?php

use common\models\Employee;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var $this yii\web\View
 * @var $startDateTime string
 * @var $endDateTime string
 * @var $model Employee
 * @var $data yii\data\ActiveDataProvider
 * @var $datePickerModel  \yii\base\DynamicModel
 * @var $userActivity modules\requestControl\models\search\UserSiteActivitySearch
 * @var $callLogDataProvider \yii\data\ActiveDataProvider
 * @var $callLogSearchModel \src\model\callLog\entity\callLog\search\CallLogSearch
 * @var $emailDataProvider \yii\data\ActiveDataProvider
 * @var $emailSearchModel \common\models\search\EmailSearch
 */

$this->title = 'User Info';
$this->params['breadcrumbs'][] = ['label' => 'Employee List', 'url' => ['employee/list']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <div class="user-info">
        <h1><i class="fa fa-user"></i> <?= Html::encode($this->title) ?></h1>
        <?php //Pjax::begin();?>
        <?php // echo $this->render('_search', ['model' => $searchModel]);?>

        <p>
            <?php if (\src\auth\Auth::can('/employee/edit')) : ?>
                <?= Html::a('<i class="fa fa-edit"></i> Update', ['employee/update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <?php endif; ?>
        </p>

        <?php //Pjax::end();?>

        <div class="x_panel">
            <div class="x_title">
                <h2>User Report <small>Activity report</small></h2>
                <?php /*
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 30px, 0px);">
                            <a class="dropdown-item" href="#">Settings 1</a>
                            <a class="dropdown-item" href="#">Settings 2</a>
                        </div>

                    </li>
                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                </ul>
 */ ?>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block;">
                <div class="col-md-3 col-sm-3  profile_left">

                    <?php
                    $gravUrl = $model->getGravatarUrl(200);
                    $roles = $model->getRoles();
                    ?>
                    <?php if ($gravUrl) : ?>
                        <div class="profile_img">
                            <div id="crop-avatar">
                                <?= Html::img($gravUrl, ['alt' => 'Avatar', 'class' => 'img-responsive avatar-view', 'title' => $model->nickname]) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <h3><?= Html::encode($model->nickname) ?></h3>
                    <ul class="list-unstyled user_data">
                        <li>
                            <i class="fa fa-user user-profile-icon"></i> Username: <?= Html::encode($model->username) ?>
                        </li>
                        <li>
                            <i class="fa fa-envelope user-profile-icon"></i> Email: <?= Html::encode($model->email) ?>
                        </li>
                        <li>
                            <i class="fa fa-briefcase user-profile-icon"></i>
                            Role: <?= $roles ? \yii\bootstrap4\Html::encode(implode(', ', $roles)) : '-' ?>
                        </li>
                        <li>
                            <i class="fa fa-user user-profile-icon"></i> Full
                            Name: <?= Html::encode($model->full_name) ?>
                        </li>

                        <!--                        <li class="m-top-xs">-->
                        <!--                            <i class="fa fa-external-link user-profile-icon"></i>-->
                        <!--                            <a href="http://www.kimlabs.com/profile/" target="_blank">www.kimlabs.com</a>-->
                        <!--                        </li>-->
                    </ul>
                    <br>

                    <!--                    <h4>Skills</h4>-->
                    <!--                    <ul class="list-unstyled user_data">-->
                    <!--                        <li>-->
                    <!--                            <p>Web Applications</p>-->
                    <!--                            <div class="progress progress_sm">-->
                    <!--                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="50" style="width: 50%;" aria-valuenow="50"></div>-->
                    <!--                            </div>-->
                    <!--                        </li>-->
                    <!--                        <li>-->
                    <!--                            <p>Website Design</p>-->
                    <!--                            <div class="progress progress_sm">-->
                    <!--                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="70" style="width: 70%;" aria-valuenow="70"></div>-->
                    <!--                            </div>-->
                    <!--                        </li>-->
                    <!--                        <li>-->
                    <!--                            <p>Automation &amp; Testing</p>-->
                    <!--                            <div class="progress progress_sm">-->
                    <!--                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="30" style="width: 30%;" aria-valuenow="30"></div>-->
                    <!--                            </div>-->
                    <!--                        </li>-->
                    <!--                        <li>-->
                    <!--                            <p>UI / UX</p>-->
                    <!--                            <div class="progress progress_sm">-->
                    <!--                                <div class="progress-bar bg-green" role="progressbar" data-transitiongoal="50" style="width: 50%;" aria-valuenow="50"></div>-->
                    <!--                            </div>-->
                    <!--                        </li>-->
                    <!--                    </ul>-->

                </div>
                <?php Pjax::begin(['timeout' => 10000, 'enablePushState' => false]) ?>
                <div class="col-md-9 col-sm-9 ">
                    <?= $this->render('partial/_info_search', [
                        'datePickerModel' => $datePickerModel
                    ]) ?>

                    <!--<?php /*= $this->render('partial/_info_user_monitor', [
                        'data' => $data,
                        'startDateTime' => $startDateTime,
                        'endDateTime' => $endDateTime,
                    ]) */ ?> -->

                    <?php if (isset($userActivity['byHour']) && $userActivity['byHour']) : ?>
                        <div id="chart_div"></div>

                        <script type="text/javascript">
                            google.charts.load('current', {'packages': ['corechart', 'bar']});
                            google.charts.setOnLoadCallback(function () {
                                let totalRequestsChart = new google.visualization.ColumnChart(document.getElementById('chart_div'));

                                //var colors = ['#8ec5ff', '#dd4b4e', '#587ca6'];

                                let options = {
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
                                    height: 300,
                                    width: 1145,
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

                                let data = google.visualization.arrayToDataTable([
                                    ['Days', 'Requests', {role: 'annotation'}],
                                    <?php foreach ($userActivity['byHour'] as $k => $item) : ?>
                                    ['<?=($item['created_hour']) ?>:00, <?=date('d-M', strtotime($item['created_date'])) ?> ', <?= $item['cnt'] ?>, '<?= ' ' ?>'],
                                    <?php endforeach; ?>
                                ]);
                                totalRequestsChart.draw(data, options);

                                $(window).on('resize', function () {
                                    options.width = document.getElementById('tab_content1').clientWidth
                                    totalRequestsChart.draw(data, options)
                                })
                            })
                        </script>
                    <?php endif; ?>

                    <!--                    <div class="profile_title">-->
                    <!--                        <div class="col-md-6">-->
                    <!--                            <h2>User Activity Report</h2>-->
                    <!--                        </div>-->
                    <!--                        <div class="col-md-6">-->
                    <!--                            <div id="reportrange" class="pull-right" style="margin-top: 5px; background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #E6E9ED">-->
                    <!--                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>-->
                    <!--                                <span>August 5, 2020 - September 3, 2020</span> <b class="caret"></b>-->
                    <!--                            </div>-->
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <!--                    <div id="graph_bar" style="width: 100%; height: 280px; position: relative; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"><svg height="280" version="1.1" width="1655" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="overflow: hidden; position: relative; left: -0.25px;"><desc style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">Created with Raphaël @@VERSION</desc><defs style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></defs><text x="44.859375" y="212.5318574445625" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">0</tspan></text><path fill="none" stroke="#aaaaaa" d="M57.359375,212.5318574445625H1630" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="44.859375" y="165.64889308342185" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal"><tspan dy="4.000455583421854" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">750</tspan></text><path fill="none" stroke="#aaaaaa" d="M57.359375,165.64889308342185H1630" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="44.859375" y="118.76592872228125" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal"><tspan dy="3.992491222281245" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">1,500</tspan></text><path fill="none" stroke="#aaaaaa" d="M57.359375,118.76592872228125H1630" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="44.859375" y="71.88296436114064" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal"><tspan dy="4.000151861140637" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">2,250</tspan></text><path fill="none" stroke="#aaaaaa" d="M57.359375,71.88296436114064H1630" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="44.859375" y="25" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal"><tspan dy="4" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">3,000</tspan></text><path fill="none" stroke="#aaaaaa" d="M57.359375,25H1630" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="1551.36796875" y="225.0318574445625" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,138.3756,945.4538)"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">Other</tspan></text><text x="1394.10390625" y="225.0318574445625" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,88.6159,870.1719)"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 6S Plus</tspan></text><text x="1236.83984375" y="225.0318574445625" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,71.1053,772.3144)"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 6S</tspan></text><text x="1079.57578125" y="225.0318574445625" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,35.018,687.4699)"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 6 Plus</tspan></text><text x="922.31171875" y="225.0318574445625" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,17.4027,589.8006)"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 6</tspan></text><text x="765.04765625" y="225.0318574445625" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-14.2182,501.7028)"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 5S</tspan></text><text x="607.78359375" y="225.0318574445625" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-39.4797,409.3929)"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 5</tspan></text><text x="450.51953125" y="225.0318574445625" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-74.9197,323.9748)"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 3GS</tspan></text><text x="293.25546875" y="225.0318574445625" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-99.5389,231.1002)"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 4S</tspan></text><text x="135.99140625" y="225.0318574445625" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-124.8082,138.7903)"><tspan dy="3.9927949445624904" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 4</tspan></text><rect x="77.0173828125" y="188.77782216825125" width="117.94804687499999" height="23.754035276311242" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="234.2814453125" y="171.5874019024997" width="117.94804687499999" height="40.9444555420628" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="391.5455078125" y="195.34143717881093" width="117.94804687499999" height="17.190420265751555" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="548.8095703125" y="114.32767476275994" width="117.94804687499999" height="98.20418268180255" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="706.0736328125" y="171.5874019024997" width="117.94804687499999" height="40.9444555420628" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="863.3376953125" y="77.88398379936663" width="117.94804687499999" height="134.64787364519586" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="1020.6017578125" y="141.019709139036" width="117.94804687499999" height="71.5121483055265" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="1177.8658203125" y="64.31917944420994" width="117.94804687499999" height="148.21267800035255" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="1335.1298828125" y="120.57873667757869" width="117.94804687499999" height="91.9531207669838" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="1492.3939453125" y="126.82979859239744" width="117.94804687499999" height="85.70205885216505" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect></svg><div class="morris-hover morris-default-style" style="left: 83.9914px; top: 111px; display: none;"><div class="morris-hover-row-label">iPhone 4</div><div class="morris-hover-point" style="color: #26B99A">-->
                    <!--                                Geekbench:-->
                    <!--                                380-->
                    <!--                            </div></div></div>-->

                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#tab_content3" role="tab" id="profile-tab3"
                                                                      data-toggle="tab" aria-expanded="false" class=""
                                                                      aria-selected="false">Profile</a>
                            </li>
                            <!-- <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false" class="" aria-selected="false">Projects Worked on</a>-->
                            <!-- </li>-->
                            <li role="presentation" class=""><a href="#tab_content2" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">User data</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content1" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">Recent Activity</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content4" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">User Failed Login</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content5" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">Calls Stats</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content6" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">Emails Stats</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content7" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">Sms Stats</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content8" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">Chats Stats</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content9" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">Calls Chart</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content10" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">Emails Chart</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content11" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">SMS Chart</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content12" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">Chat Chart</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content13" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">Leads</a>
                            </li>
                            <li role="presentation" class=""><a href="#tab_content14" id="home-tab" role="tab"
                                                                data-toggle="tab" aria-expanded="true" class=""
                                                                aria-selected="true">Cases</a>
                            </li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div role="tabpanel" class="tab-pane fade" id="tab_content1" aria-labelledby="home-tab">
                                <?= $this->render('partial/_info_activity', [
                                    'model' => $model,
                                    'userActivity' => $userActivity,
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="home-tab">
                                <?= $this->render('partial/_info_user_data', [
                                    'model' => $model,
                                    'userDataProvider' => $userDataProvider,
                                ]) ?>
                            </div>
                            <!--                            <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">-->
                            <!--                                --><?php //= $this->render('partial/_info_project', [
                            //                                    'model' => $model,
                            //                                ])?>
                            <!--                            </div>-->
                            <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_profile', [
                                    'model' => $model,
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content4" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_user_failed_login', [
                                    'model' => $model,
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content5" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_calls', [
                                    'callLogDataProvider' => $callLogDataProvider,
                                    'callLogSearchModel' => $callLogSearchModel
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content6" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_emails', [
                                    'emailDataProvider' => $emailDataProvider,
                                    'emailSearchModel' => $emailSearchModel
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content7" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_sms', [
                                    'smsDataProvider' => $smsDataProvider,
                                    'smsSearchModel' => $smsSearchModel
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content8" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_chats', [
                                    'chatDataProvider' => $chatDataProvider,
                                    'chatSearchModel' => $chatSearchModel
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content9" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_calls_chart', [
                                    'callsInfoGraph' => $callsInfoGraph
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content10" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_emails_chart', [
                                    'emailsInfoGraph' => $emailsInfoGraph
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content11" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_sms_chart', [
                                    'smsInfoGraph' => $smsInfoGraph
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content12" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_chat_chart', [
                                    'chatInfoGraph' => $chatInfoGraph
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content13" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_leads', [
                                    'leadsInfoDataProvider' => $leadsInfoDataProvider,
                                    'leadsSearchModel' => $leadsSearchModel
                                ]) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="tab_content14" aria-labelledby="profile-tab">
                                <?= $this->render('partial/_info_cases', [
                                    'casesInfoDataProvider' => $casesInfoDataProvider,
                                    'casesSearchModel' => $casesSearchModel
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $jsTabs = <<<JS
                    $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {                        
                        localStorage.setItem('activeTab', $(e.target).attr('href'));
                    });                    
                    
                    var activeTab = localStorage.getItem('activeTab');                    
                    if (activeTab) {
                       $('a[href="' + activeTab + '"]').tab('show');
                    }
                    JS;
                $this->registerJs($jsTabs, \yii\web\View::POS_LOAD)
                ?>

                <?php Pjax::end() ?>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
    var activeTab = localStorage.getItem('activeTab');                    
    if (!activeTab) {
         $('#profile-tab3').addClass('active')
         $('#tab_content3').removeClass('fade').addClass('active')
    }
JS;

$this->registerJs($js, \yii\web\View::POS_LOAD)
?>