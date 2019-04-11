<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */
/* @var $dataProviderCommunication \yii\data\ActiveDataProvider */
/* @var $datetime_start string */
/* @var $datetime_end string */
/* @var $callsGraphData [] */

$bundle = \frontend\assets\TimelineAsset::register($this);
$this->title = 'Stats Calls & SMS';

/*$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
//$this->registerJs($js, \yii\web\View::POS_READY);*/

$userId = Yii::$app->user->id;

if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('qa', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\Project::getListByUser(Yii::$app->user->id);
}

?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <div class="stats-call-sms">
        <h1><i class="fa fa-bar-chart"></i> <?=$this->title?></h1>
        <!-- bar chart -->
        <div class="row">
            <div class="col-md-12 col-sm-6 col-xs-12">
                <div class="x_panel">
                    <!--<div class="x_title">
                        <h2>Call Chart by Status <small></small></h2>-->
                    <!--<ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Settings 1</a>
                                </li>
                                <li><a href="#">Settings 2</a>
                                </li>
                            </ul>
                        </li>
                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                        </li>
                    </ul>-->
                    <!--    <div class="clearfix"></div>
                    </div>-->
                    <div class="x_content">
                        <!-- <div id="graph_bar" style="width: 100%; height: 280px; position: relative; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"><svg height="280" version="1.1" width="384" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="overflow: hidden; position: relative;"><desc style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">Created with Raphaël @@VERSION</desc><defs style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></defs><text x="44.84375" y="212.540819576375" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal"><tspan dy="4.009569576374986" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">0</tspan></text><path fill="none" stroke="#aaaaaa" d="M57.34375,212.540819576375H359" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="44.84375" y="165.65561468228123" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal"><tspan dy="4.014989682281225" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">750</tspan></text><path fill="none" stroke="#aaaaaa" d="M57.34375,165.65561468228123H359" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="44.84375" y="118.7704097881875" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal"><tspan dy="4.004784788187493" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">1,500</tspan></text><path fill="none" stroke="#aaaaaa" d="M57.34375,118.7704097881875H359" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="44.84375" y="71.88520489409373" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal"><tspan dy="4.010204894093732" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">2,250</tspan></text><path fill="none" stroke="#aaaaaa" d="M57.34375,71.88520489409373H359" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="44.84375" y="25" text-anchor="end" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal"><tspan dy="4" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">3,000</tspan></text><path fill="none" stroke="#aaaaaa" d="M57.34375,25H359" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><text x="343.9171875" y="225.040819576375" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-79.9983,252.876)"><tspan dy="4.009569576374986" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">Other</tspan></text><text x="313.7515625" y="225.040819576375" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-106.7655,250.4921)"><tspan dy="4.009569576374986" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 6S Plus</tspan></text><text x="253.4203125" y="225.040819576375" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-114.3991,213.595)"><tspan dy="4.009569576374986" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 6 Plus</tspan></text><text x="193.0890625" y="225.040819576375" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-117.6582,173.6239)"><tspan dy="4.009569576374986" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 5S</tspan></text><text x="162.9234375" y="225.040819576375" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-119.94,154.2209)"><tspan dy="4.009569576374986" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 5</tspan></text><text x="132.7578125" y="225.040819576375" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-132.3954,141.7008)"><tspan dy="4.009569576374986" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 3GS</tspan></text><text x="102.5921875" y="225.040819576375" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-134.025,121.7152)"><tspan dy="4.009569576374986" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 4S</tspan></text><text x="72.4265625" y="225.040819576375" text-anchor="middle" font-family="sans-serif" font-size="12px" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-family: sans-serif; font-size: 12px; font-weight: normal;" font-weight="normal" transform="matrix(0.8192,-0.5736,0.5736,0.8192,-136.3068,102.3122)"><tspan dy="4.009569576374986" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">iPhone 4</tspan></text><rect x="61.114453125" y="188.7856490967008" width="22.624218749999997" height="23.755170479674177" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="91.280078125" y="171.59440730219978" width="22.624218749999997" height="40.9464122741752" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="121.445703125" y="195.34957778187396" width="22.624218749999997" height="17.191241794501025" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="151.611328125" y="114.33194372487995" width="22.624218749999997" height="98.20887585149504" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="181.776953125" y="171.59440730219978" width="22.624218749999997" height="40.9464122741752" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="211.942578125" y="77.88651112053773" width="22.624218749999997" height="134.65430845583725" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="242.108203125" y="141.02525371125066" width="22.624218749999997" height="71.51556586512433" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="272.27382812499997" y="64.32105850451327" width="22.624218749999997" height="148.2197610718617" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="302.439453125" y="120.58330437742579" width="22.624218749999997" height="91.9575151989492" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect><rect x="332.605078125" y="126.83466502997162" width="22.624218749999997" height="85.70615454640337" rx="0" ry="0" fill="#26b99a" stroke="none" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></rect></svg><div class="morris-hover morris-default-style" style="left: 50.5922px; top: 111px; display: none;"><div class="morris-hover-row-label">iPhone 4S</div><div class="morris-hover-point" style="color: #26B99A">
                                     Geekbench:
                                     655
                                 </div></div></div>-->
                        <?php if ($callsGraphData): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="chart_div"></div>

                                    <?php
                                    $this->registerJs("google.charts.load('current', {'packages':['bar']}); google.charts.setOnLoadCallback(drawChart);", \yii\web\View::POS_READY);
                                    ?>

                                    <script>
                                        function drawChart() {
                                            let data = google.visualization.arrayToDataTable([
                                                ['Time Line', 'Completed', 'Canceled', 'Busy', {role: 'annotation'}],
                                                <?php foreach($callsGraphData as $k => $item):?>
                                                ['<?=date('H:i', strtotime($item['time']))?>', <?=$item['completed']?>, <?=$item['no-answer']?>, <?=$item['busy']?>, '<?='--'?>'],
                                                <?php endforeach;?>
                                            ]);

                                            let options = {
                                                chart: {
                                                    title: 'Calls graph',
                                                    subtitle: 'Calls info - Last ?? days',
                                                },
                                                title: 'Lead data',
                                                height: 400,
                                                vAxis: {
                                                    title: 'Requests'
                                                },
                                            };
                                            //var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
                                            let chart = new google.charts.Bar(document.getElementById('chart_div'));

                                            chart.draw(data, options);
                                            $(window).resize(function(){
                                                chart.draw(data, options); // redraw the graph on window resize
                                            });
                                            //chart.draw(data, google.charts.Bar.convertOptions(options));

                                        }
                                    </script>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
        <!-- /bar charts -->

        <?php Pjax::begin(); ?>
        <div class="panel panel-default">
            <div class="panel-heading"><i class="fa fa-bar-chart"></i> Call & SMS Stats</div>
            <div class="panel-body">

                <div class="row">

                    <?php $form = ActiveForm::begin([
                        'action' => ['call-sms'],
                        'method' => 'get',
                        'options' => [
                            'data-pjax' => 1
                        ],
                    ]); ?>

                    <div class="col-md-3">
                        <?php
                        echo  \kartik\daterange\DateRangePicker::widget([
                            'model'=> $searchModel,
                            'attribute' => 'date_range',
                            //'name'=>'date_range',
                            'useWithAddon'=>true,
                            //'value' => $datetime_start . ' - ' . $datetime_end,
                            'presetDropdown'=>true,
                            'hideInput'=>true,
                            'convertFormat'=>true,
                            'startAttribute' => 'datetime_start',
                            'endAttribute' => 'datetime_end',
                            //'startInputOptions' => ['value' => $datetime_start],
                            //'endInputOptions' => ['value' => $datetime_end],
                            'pluginOptions'=>[
                                'timePicker'=> false,
                                'timePickerIncrement'=>15,
                                'locale'=>['format'=>'Y-m-d']
                            ]
                        ]);
                        ?>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-success']) ?>
                        <?//= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>


                <?= GridView::widget([
                    'dataProvider' => $dataProviderCommunication,
                    'filterModel' => $searchModel,
                    /*'rowOptions' => function (\common\models\Employee $model, $index, $widget, $grid) {
                        if ($model->deleted) {
                            return ['class' => 'danger'];
                        }
                    },*/
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'options' => ['style' => 'width:100px'],
                        ],

                        [
                            'label' => 'Obj Id',
                            'attribute' => 'id',
                            'value' => function ($model) {
                                return $model['id'];
                            },
                            'options' => ['style' => 'width:100px'],
                            //'format' => 'raw',
                        ],

                        /*[
                            'attribute' => 'id',
                            'contentOptions' => ['class' => 'text-center'],
                            'options' => ['style' => 'width:60px'],
                        ],
                        [
                            'attribute' => 'username',
                            'value' => function (\common\models\Employee $model) {
                                return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->username);
                            },
                            'format' => 'raw',
                            //'contentOptions' => ['title' => 'text-center'],
                            'options' => ['style' => 'width:180px'],
                        ],

                        [
                            //'attribute' => 'username',
                            'label' => 'Role',
                            'value' => function (\common\models\Employee $model) {
                                $roles = $model->getRoles();
                                return $roles ? implode(', ', $roles) : '-';
                            },
                            'options' => ['style' => 'width:150px'],
                            //'format' => 'raw'
                        ],*/



                        [
                            'label' => 'Communication Type',
                            'attribute' => 'communication_type_id',
                            'value' => function ($model) {
                                return \common\models\search\CommunicationSearch::COMM_TYPE_LIST[$model['communication_type_id']] ?? '-';
                            },
                            //'format' => 'raw',
                            'filter' => \common\models\search\CommunicationSearch::COMM_TYPE_LIST
                        ],

                        [
                            'label' => 'Type / Status',
                            'value' => function ($model) {

                                $type = '';
                                $statusTitle = '';

                                if($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {

                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        $type = $call->getCallTypeName();
                                        $statusTitle = $call->getStatusName(); //'INIT';

                                        /*if ($call->c_call_type_id == \common\models\Call::CALL_TYPE_IN) {
                                            $type = 'Incoming';
                                        } else if ($call->c_call_type_id == \common\models\Call::CALL_TYPE_OUT) {
                                            $type = 'Outgoing';
                                        }*/
                                    }
                                } elseif($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {

                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {

                                        $type = $sms->getTypeName();
                                        $statusTitle = $sms->getStatusName(); //'INIT';

                                        /*if ($sms->s_type_id == \common\models\Sms::TYPE_INBOX) {
                                            $type = 'Incoming';
                                        } else if ($sms->s_type_id == \common\models\Sms::TYPE_OUTBOX) {
                                            $type = 'Outgoing';
                                        }*/
                                    }
                                }


                                return $type . ' / '.$statusTitle.'';
                            },
                            'format' => 'raw',
                            //'filter' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()
                        ],


                        [
                            'label' => 'Created Date',
                            'attribute' => 'created_dt',
                            'value' => function ($model) {
                                return $model['created_dt'] ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model['created_dt']), 'php:Y-m-d [H:i:s]') : '-';
                            },
                            'format' => 'raw',
                            //'filter' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()
                        ],

                        [
                            'label' => 'Agent Phone',
                            'value' => function ($model) {

                                $phone = '-';


                                if($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {

                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if($call->c_call_status == \common\models\Call::CALL_TYPE_IN) {
                                            $phone = $call->c_from;
                                        } else {
                                            $phone = $call->c_to;
                                        }
                                    }

                                } elseif($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {

                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if ($sms->s_type_id == \common\models\Sms::TYPE_INBOX) {
                                            $phone = $sms->s_phone_from;
                                        } else if ($sms->s_type_id == \common\models\Sms::TYPE_OUTBOX) {
                                            $phone = $sms->s_phone_to;
                                        }
                                    }
                                }

                                return $phone; //$model['lead_id'];
                            },
                            //'format' => 'raw',
                        ],

                        [
                            'label' => 'Agent Name',
                            'attribute' => 'created_user_id',
                            'value' => function ($model) {

                                $agent = '-';

                                if($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {

                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if($call->cCreatedUser) {
                                            $agent = $call->cCreatedUser->username;
                                        }
                                    }
                                }

                                elseif($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {

                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if($sms->sCreatedUser) {
                                            $agent = $sms->sCreatedUser->username;
                                        }
                                    }
                                }

                                return  Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::encode($agent);
                            },
                            'format' => 'raw',
                            'filter' => $userList
                        ],

                        [
                            'label' => 'Agent Group',
                            'attribute' => 'user_group_id',
                            'value' => function ($model) {
                                $user = null;

                                if($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {

                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if($call->cCreatedUser) {
                                            $user = $call->cCreatedUser;
                                        }
                                    }
                                }

                                elseif($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {

                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if($sms->sCreatedUser) {
                                            $user = $sms->sCreatedUser;
                                        }
                                    }
                                }


                                if($user) {
                                    $groups = $user->getUserGroupList();
                                    $groupsValueArr = [];

                                    foreach ($groups as $group) {
                                        $groupsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' . Html::encode($group), ['class' => 'label label-default']);
                                    }

                                    $groupsValue = implode(' ', $groupsValueArr);
                                } else {
                                    $groupsValue = '';
                                }

                                return $groupsValue;
                            },
                            'format' => 'raw',
                            'filter' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()
                        ],

                        [
                            'label' => 'Client Phone',
                            'value' => function ($model) {
                                $phone = '-';


                                if($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {

                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if($call->c_call_status == \common\models\Call::CALL_TYPE_IN) {
                                            $phone = $call->c_to;
                                        } else {
                                            $phone = $call->c_from;
                                        }
                                    }
                                }

                                elseif($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {

                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if ($sms->s_type_id == \common\models\Sms::TYPE_INBOX) {
                                            $phone = $sms->s_phone_to;
                                        } else if ($sms->s_type_id == \common\models\Sms::TYPE_OUTBOX) {
                                            $phone = $sms->s_phone_from;
                                        }
                                    }
                                }

                                return $phone;
                            },
                            'format' => 'raw',
                        ],


                        [
                            //'label' => 'Lead Id',
                            'attribute' => 'lead_id',
                            'value' => function ($model) {
                                $lead = \common\models\Lead::findOne($model['lead_id']);
                                return $lead ? Html::a($model['lead_id'], ['lead/view', 'gid' => $lead->gid], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                            },
                            'format' => 'raw',
                        ],

                        [
                            'label' => 'Project',
                            'attribute' => 'project_id',
                            'value' => function ($model) {
                                $project = null;

                                if($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {

                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if($call->cProject) {
                                            $project = $call->cProject;
                                        }
                                    }
                                }

                                elseif($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {

                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if($sms->sProject) {
                                            $project = $sms->sProject;
                                        }
                                    }
                                }

                                if($project) {
                                    return $project->name;
                                } else {
                                    return '-';
                                }

                            },
                            'filter' => $projectList
                            //'format' => 'raw',
                        ],

                        [
                            'label' => 'Length',
                            'value' => function ($model) {

                                $duration = '-';

                                if($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {

                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call) {
                                        if($call->c_call_duration) {
                                            $duration = Yii::$app->formatter->asDuration($call->c_call_duration);
                                        }
                                    }
                                } elseif($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {

                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {
                                        if($sms->s_sms_text) {
                                            $duration = mb_strlen($sms->s_sms_text);
                                        }
                                    }
                                }
                                return $duration;
                            },
                            'format' => 'raw',
                        ],

                        [
                            'label' => 'View',
                            'value' => function ($model) {

                                $view = '-';

                                if($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_VOICE) {
                                    $call = \common\models\Call::findOne($model['id']);
                                    if ($call && $call->c_recording_url) {
                                        $view =  '<audio controls="controls" style="width: 300px; height: 25px"><source src="'.$call->c_recording_url.'" type="audio/mpeg"> </audio>';
                                    }
                                } else if($model['communication_type_id'] == \common\models\search\CommunicationSearch::COMM_TYPE_SMS) {
                                    $sms = \common\models\Sms::findOne($model['id']);
                                    if ($sms) {

                                        $view =  Html::button('<i class="fa fa-search"></i> View', [

                                            'class' => 'btn btn-xs btn-info view_sms',
                                            //'data-toggle' => 'popover',

                                            'title' => strip_tags($sms->s_sms_text),
                                            'data-content' => nl2br($sms->s_sms_text),
                                            //'data-placement' => 'left',

                                            //'data-original-title' => 'Select Emails',
                                        ]);

                                        //$view = Html::a('<i class="fa fa-search"></i> View', '#', ['class' => 'btn btn-xs btn-info']);
                                    }
                                }

                                return $view;
                            },
                            'format' => 'raw',
                        ],

                        /*
                         [
                             'label' => 'Booked',
                             'value' => function (\common\models\Employee $model) use ($searchModel) {
                                 $cnt = $model->getLeadCountByStatuses([\common\models\Lead::STATUS_BOOKED], null, $searchModel->datetime_start, $searchModel->datetime_end);
                                 return $cnt ? Html::a($cnt, ['lead-flow/index',
                                     'LeadFlowSearch[employee_id]' => $model->id,
                                     'LeadFlowSearch[status]' => \common\models\Lead::STATUS_BOOKED,
                                     'LeadFlowSearch[created_date_from]' => $searchModel->datetime_start,
                                     'LeadFlowSearch[created_date_to]' => $searchModel->datetime_end
                                 ], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                             },
                             'format' => 'raw',
                         ],*/



                        /*[
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update}',
                            'visibleButtons' => [
                                'update' => function (\common\models\Employee $model, $key, $index) {
                                    return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                                },
                            ],

                        ],*/
                    ]
                ])
                ?>


            </div>
        </div>
        <?php Pjax::end(); ?>

    </div>

<?php \yii\bootstrap\Modal::begin(['id' => 'modal-sms-preview',
    'header' => '<h2>SMS preview</h2>',
    'size' => \yii\bootstrap\Modal::SIZE_LARGE
])?>
<?php \yii\bootstrap\Modal::end()?>


<?php
$js = <<<JS

    $('body').on('click', '.view_sms', function() {
        var data = $(this).data('content');
        var previewPopup = $('#modal-sms-preview');
        previewPopup.find('.modal-body').html(data);
        previewPopup.modal('show');
    });

    // $('[data-toggle="popover"]').popover({ 'sanitize': false, 'html': true});
JS;

$this->registerJs($js);

