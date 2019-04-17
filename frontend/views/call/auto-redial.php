<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
//use kartik\grid\GridView;
use yii\grid\GridView;
use common\models\Lead;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $checkShiftTime bool */
/* @var $isAgent bool */
/* @var $isAccessNewLead bool */
/* @var $accessLeadByFrequency array */
/* @var $user \common\models\Employee */
/* @var $newLeadsCount integer */

/* @var $leadModel Lead */
/* @var $callModel \common\models\Call */


/* @var $searchModelSegments common\models\search\LeadFlightSegmentSearch */
/* @var $dataProviderSegments yii\data\ActiveDataProvider */
/* @var $isActionFind bool */
/* @var $callData [] */

/* @var $myPendingLeadsCount int */
/* @var $allPendingLeadsCount int */


/* @var $searchModelCall common\models\search\CallSearch */
/* @var $dataProviderCall yii\data\ActiveDataProvider */
/* @var $projectList [] */


$this->title = 'Auto find & redial';

/*if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}*/

/*$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js', [
    //'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);*/

/*$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);*/

$bundle = \frontend\assets\TimerAsset::register($this);


$this->params['breadcrumbs'][] = ['label' => 'Calls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<script>

    var takeTimerId = 0;

    function openInNewTab(url, name) {
        //var strWindowFeatures = "menubar=no,location=no,resizable=yes,scrollbars=yes,status=no";
        var windowObjectReference = window.open(url, 'window' + name); //, strWindowFeatures);
        windowObjectReference.focus();
    }

    function autoredialInit() {
        console.info('autoredialInit()');
        $.pjax.reload({container:'#pjax-auto-redial', 'scrollTo': false});
    }

    function startAutoTake() {
        //takeTimerId = setTimeout(function() { openInNewTab(url, name) }, 20000);
        console.log('Create takeTimerId: ' + takeTimerId);

        $('#auto_take_timer').timer({format: '%M:%S', duration: 30, countdown: true, callback: function() {

                var url = $('#auto_take_timer').parent().attr('href');
                var name = $('#auto_take_timer').data('name');

                //alert(name);
                $(this).timer('remove');
                openInNewTab(url, name)

            },}).timer('start');

    }

    function endAutoTake() {
        console.log('endAutoTake, current takeTimerId: ' + takeTimerId);
        $('#auto_take_timer').timer('remove');
        //console.log('endAutoTake response: ' + clearTimeout(takeTimerId));
    }

    function webCallUpdate(obj) {
        //console.log('--- webCallUpdate ---');
        //console.info('webCallUpdate - 3');
        //status: "completed", duration: "1", snr: "3"


        if(obj.status !== undefined) {

            //$('#call_autoredial_status').html(obj.status);

            if (obj.status === 'completed') {
                endAutoTake();
                //stopCall(obj.duration); //updateCommunication();
                autoredialInit();
            } else if (obj.status === 'initiated') {
                //endAutoTake();
                //startCall();
            } else if (obj.status === 'ringing') {
                autoredialInit();
                //startAutoTake();
                //endAutoTake();
                //startCall();
            } else if (obj.status === 'in-progress') {
                autoredialInit();
                // startAutoTake();
                //startCallTimer();
                //$('#div-call-timer').timer('resume');
            } else if (obj.status === 'busy') {
                endAutoTake();
                //stopCall(0);
                //updateCommunication();
                autoredialInit();
            } else if (obj.status === 'no-answer') {
                //stopCall(0);
                //updateCommunication();
                endAutoTake();
                autoredialInit();
            }
        }

        //console.info('webCallUpdate - 4');

        //$('.click_after_call_update').trigger('click');
    }




    function startTimer(sec) {
        var seconds = new Date().getTime() + (1000 * sec);

        $('#clock').countdown(seconds)
            .on('update.countdown', function(event) {
                var format = '%M:%S';
                /*if(event.offset.totalDays > 0) {
                  format = '%-d day%!d ' + format;
                }
                if(event.offset.weeks > 0) {
                  format = '%-w week%!w ' + format;
                }*/
                $(this).html(event.strftime(format));
            })
            .on('finish.countdown', function(event) {
                //$(this).html('This call has expired!').parent().addClass('disabled');

                /*var phone_to = $('#call-to-number').val();
                var phone_from = $('#call-from-number').val();

                var project_id = $('#call-project-id').val();
                var lead_id = $('#call-lead-id').val();*/

                //$.pjax.reload({container:'#pjax-auto-redial', data: 'act=show', type: 'POST', 'scrollTo': false});

                //webCall(phone_from, phone_to, project_id, lead_id);

            });
    }
</script>

    <h1>
        <i class="fa fa-tty"></i> <?=\yii\helpers\Html::encode($this->title)?>
    </h1>

    <div class="call-auto-redial">
        <?php Pjax::begin(['id' => 'pjax-auto-redial', 'timeout' => 5000, 'enablePushState' => false]); ?>

        <div class="row top_tiles">

            <?php if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)): ?>
            <div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count">
                        <?=Lead::find()->where(['status' => Lead::STATUS_PENDING])->count()?>
                    </div>
                    <h3>Total Pending Leads</h3>
                    <p>Total Leads - status Pending</p>
                </div>
            </div>


            <div class="animated flipInY col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count">
                        <?=$allPendingLeadsCount?>
                    </div>
                    <h3>Accessed Pending Leads</h3>
                    <p>Accessed all pending Leads (delay, client time: 09:00 - 21:00)</p>
                </div>
            </div>
            <?php endif; ?>

            <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-bars"></i></div>
                    <div class="count">
                        <?=$myPendingLeadsCount?>
                    </div>
                    <h3>Allowed Pending Leads</h3>
                    <p>Allowed pending leads (project, phone, client time: 09:00 - 21:00)</p>
                </div>
            </div>



            <div class="animated flipInY col-md-2 col-sm-6 col-xs-12" title="My Project / Phone List">
                <table class="table table-bordered">
                    <tr>
                        <th>Online</th>
                        <td><?=$user->isOnline() ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>'; ?></td>
                    </tr>
                    <tr>
                        <th>Call Status Ready</th>
                        <td><?=$user->isCallStatusReady() ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>'; ?></td>
                    </tr>
                    <tr>
                        <th>Call Free</th>
                        <td><?=$user->isCallFree() ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>'; ?></td>
                    </tr>
                </table>
            </div>

            <div class="animated flipInY col-md-2 col-sm-6 col-xs-12" title="My Project / Phone List">
                <?php
                if($user->userProjectParams):
                    ?>

                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Project</th>
                            <th>Phone</th>
                        </tr>
                        <?php
                        $nr = 1;
                        foreach ($user->userProjectParams as $upp):?>
                            <tr>
                                <td><?=Html::encode($upp->uppProject->name)?></td>
                                <td><?=Html::encode($upp->upp_tw_phone_number)?></td>
                            </tr>
                        <? endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>


        <?//=date('Y-m-d H:i:s')?>

        <?php
            //\yii\helpers\VarDumper::dump(Yii::$app->request->get(), 10, true);
            //\yii\helpers\VarDumper::dump(Yii::$app->request->post(), 10, true);
        ?>

        <p>

            <?//= Html::a('<i class="fa fa-refresh"></i> Auto Redial INIT', ['auto-redial', 'act' => 'init'], ['class' => 'btn btn-info click_after_call_update', 'id' => 'btn-auto-redial-init']) ?>

            <?php if($isActionFind && $leadModel):?>
                <div class="text-center badge badge-warning" style="font-size: 35px">
                    <i class="fa fa-spinner fa-spin"></i> Processing ...
                </div>
            <?php elseif($callModel):?>
                <div class="text-center badge badge-warning" style="font-size: 35px">
                    <i class="fa fa-spinner fa-spin"></i> <?=$callModel->getStatusName()?> <?=$callModel->c_to?> ...
                </div>
            <?php else: ?>

                <?php if($checkShiftTime && $isAccessNewLead):?>
                    <div class="text-center">
                        <?= Html::a('<i class="fa fa-search"></i> Find new Lead and make Call', ['auto-redial', 'act' => 'find'], ['class' => 'btn btn-lg btn-success']) ?>
                    </div>
                <?php endif; ?>

                <?php if($isActionFind):?>
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Sorry! No lead was found.</strong> Last request time: <?=Yii::$app->formatter->asTime(time())?>
                    </div>
                    <?/*<p>Last request time: <?=Yii::$app->formatter->asTime(time())?></p>*/?>
                <?php endif; ?>

                <?/*<div class="countdown text-center badge badge-warning" style="font-size: 35px">
                    <i class="fa fa-clock-o"></i>
                    <span id="clock">00:00</span>
                </div>*/?>
            <?php endif; ?>

            <?//=$this->registerJs('startTimer(10);');?>


        </p>



        <?php if($user->userProfile):?>
            <hr>
            <div class="row">
                <div class="col-md-6">

                    <?php if($leadModel && !$callModel): ?>

                        <?php if(isset($callData['error']) && $callData['error']):?>
                            <div class="alert alert-danger" role="alert"><strong>Error:</strong> <?=Html::encode($callData['error'])?></div>
                            <?=$this->registerJs("createNotify('Call Error', '". Html::encode($callData['error'])."', 'error');");?>
                        <?php else: ?>

                            <?php if($callData): ?>

                                <?=$this->registerJs("webCall('". $callData['phone_from']."', '". $callData['phone_to']."', ". $callData['project_id'].", ". $callData['lead_id'].", 'auto-redial');");?>
                                <?//=$this->registerJs('autoredialInit(); startAutoTake();');?>
                                <?//=$this->registerJs('startTimer(20);');?>
                            <?php endif; ?>

                        <?php endif; ?>

                        <div class="hidden">
                            <?//php \yii\helpers\VarDumper::dump($callData, 10, true) ?>
                        </div>

                        <div class="row">

                            <div class="x_panel">
                                <div class="x_title">
                                    <h2><i class="fa fa-list"></i> Find new Lead <?=$leadModel->id?></h2>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <div class="col-md-6">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $leadModel,
                                            'attributes' => [
                                                //'id',
                                                'uid',
                                                //'client_id',
                                                [
                                                    'attribute' => 'client.name',
                                                    'header' => 'Client name',
                                                    'format' => 'raw',
                                                    'value' => function(\common\models\Lead $model) {
                                                        if($model->client) {
                                                            $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                                                            if ($clientName === 'Client Name') {
                                                                $clientName = '- - - ';
                                                            } else {
                                                                $clientName = '<i class="fa fa-user"></i> '. Html::encode($clientName);
                                                            }
                                                        } else {
                                                            $clientName = '-';
                                                        }

                                                        return $clientName;
                                                    },
                                                    'options' => ['style' => 'width:160px'],
                                                    //'filter' => \common\models\Employee::getList()
                                                ],

                                                [
                                                    'attribute' => 'client.phone',
                                                    'header' => 'Client Phones',
                                                    'format' => 'raw',
                                                    'value' => function(\common\models\Lead $model) use ($isAgent) {
                                                        if($model->client && $model->client->clientPhones) {
                                                            if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
                                                                $str = '- // - // - // -';
                                                            } else {
                                                                $str = '<i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone'));
                                                            }
                                                        } else {
                                                            $str = '-';
                                                        }

                                                        return $str ?? '-';
                                                    },
                                                    'options' => ['style' => 'width:180px'],
                                                ],


                                               /* [
                                                    'attribute' => 'client.email',

                                                    'format' => 'raw',
                                                    'value' => function(\common\models\Lead $model) use ($isAgent) {

                                                        if($model->client && $model->client->clientEmails) {
                                                            if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
                                                                $str = '- // - // - // -';
                                                            } else {
                                                                $str = '<i class="fa fa-envelope"></i> '.implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email'));
                                                            }
                                                        } else {
                                                            $str = '-';
                                                        }

                                                        return $str ?? '-';
                                                    },
                                                    'options' => ['style' => 'width:180px'],
                                                ],*/

                                                /*[
                                                    'attribute' => 'employee_id',
                                                    'format' => 'raw',
                                                    'value' => function(\common\models\Lead $model) {
                                                        return $model->employee ? '<i class="fa fa-user"></i> '.$model->employee->username : '-';
                                                    },
                                                ],*/

                                                //'employee_id',



                                                [
                                                    'attribute' => 'status',
                                                    'value' => function(\common\models\Lead $model) {
                                                        return $model->getStatusName(true);
                                                    },
                                                    'format' => 'html',

                                                ],
                                                [
                                                    'attribute' => 'project_id',
                                                    'value' => function(\common\models\Lead $model) {
                                                        return $model->project ? $model->project->name : '-';
                                                    },

                                                ],

                                                [
                                                    'attribute' => 'source_id',
                                                    'value' => function(\common\models\Lead $model) {
                                                        return $model->source ? $model->source->name : '-';
                                                    },
                                                ],

                                                /*[
                                                    'label' => 'Segments',
                                                    'value' => function (\common\models\Lead $model) {

                                                        $segments = $model->leadFlightSegments;
                                                        $segmentData = [];
                                                        if ($segments) {
                                                            foreach ($segments as $sk => $segment) {
                                                                $segmentData[] = ($sk + 1) . '. <small>' . $segment->origin_label . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination_label . ' ('.$segment->departure.')</small>';
                                                            }
                                                        }

                                                        $segmentStr = implode('<br>', $segmentData);
                                                        return '' . $segmentStr . '';
                                                        // return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                                                    },
                                                    'format' => 'raw',
                                                    'visible' => ! $isAgent,
                                                    'contentOptions' => [
                                                        'class' => 'text-left'
                                                    ],
                                                    'options' => [
                                                        'style' => 'width:140px'
                                                    ]
                                                ],*/

                                                /*[
                                                    'header' => 'Client time',
                                                    'format' => 'raw',
                                                    'value' => function(\common\models\Lead $model) {
                                                        return $model->getClientTime2();
                                                    },
                                                    //'options' => ['style' => 'width:80px'],
                                                    //'filter' => \common\models\Employee::getList()
                                                ],*/

                                            ],
                                        ]) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= \yii\widgets\DetailView::widget([
                                            'model' => $leadModel,
                                            'attributes' => [
                                                [
                                                    'attribute' => 'trip_type',
                                                    'value' => function(\common\models\Lead $model) {
                                                        return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                                                    },

                                                ],

                                                [
                                                    'attribute' => 'cabin',
                                                    'value' => function(\common\models\Lead $model) {
                                                        return \common\models\Lead::getCabin($model->cabin) ?? '-';
                                                    },

                                                ],

                                                /*'project_id',
                                                'source_id',
                                                'trip_type',
                                                'cabin',*/
                                                /*'adults',
                                                'children',
                                                'infants',*/
                                                'offset_gmt',
                                                [
                                                    'label' => 'Client time',
                                                    'format' => 'raw',
                                                    'value' => function(\common\models\Lead $model) {
                                                        return $model->getClientTime2();
                                                    },
                                                    //'options' => ['style' => 'width:80px'],
                                                    //'filter' => \common\models\Employee::getList()
                                                ],

                                                //'discount_id',

                                                /*[
                                                    'label' => 'Pax',
                                                    'value' => function (\common\models\Lead $model) {
                                                        return '<span title="adult"><i class="fa fa-male"></i> '. $model->adults .'</span> / <span title="child"><i class="fa fa-child"></i> ' . $model->children . '</span> / <span title="infant"><i class="fa fa-info"></i> ' . $model->infants.'</span>';
                                                    },
                                                    'format' => 'raw',
                                                ],*/


                                                [
                                                    'attribute' => 'created',
                                                    'value' => function(\common\models\Lead $model) {
                                                        return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                                                    },
                                                    'format' => 'raw',
                                                ],
                                                [
                                                    //'attribute' => 'pending',
                                                    'label' => 'Pending Time',
                                                    'value' => function (\common\models\Lead $model) {
                                                        $createdTS = strtotime($model->created);

                                                        $diffTime = time() - $createdTS;
                                                        $diffHours = (int) ($diffTime / (60 * 60));

                                                        return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours.' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                                                    },
                                                    'options' => [
                                                        'style' => 'width:180px'
                                                    ],
                                                    'format' => 'raw',
                                                ],
                                            ],
                                        ]) ?>
                                    </div>
                                </div>
                            </div>


                        </div>

                    <?php else: ?>
                        <?/*php
                            if(!Yii::$app->request->isPjax || Yii::$app->request->get('act') === 'start') {
                                $this->registerJs('startTimer(10);');
                            } else {
                                $this->registerJs('startTimer(30);');
                            }*/
                        ?>
                    <?php endif; ?>

                </div>
                <div class="col-md-6">
                    <?/*<h3>Call status: <span class="badge badge-info" id="call_autoredial_status"><?=$callModel ? $callModel->getStatusName() : '-'?></span></h3>*/?>
                    <?php if($callModel): ?>
                        <div class="x_panel">
                            <div class="x_title">
                                <h2><i class="fa fa-phone-square"></i> Current Call: <?=$callModel->c_id?></h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">


                                <?/*<div class="countdown text-center badge badge-warning" style="font-size: 15px">
                                    <i class="fa fa-clock-o"></i>
                                    <span id="clock">00:<?=(time() - strtotime($callModel->c_created_dt))?></span>
                                </div>*/?>

                                <div class="col-md-6">

                                    <?= \yii\widgets\DetailView::widget([
                                        'model' => $callModel,
                                        'attributes' => [
                                            //'c_call_status',
                                            [
                                                'attribute' => 'c_call_status',
                                                'value' => function (\common\models\Call $model) {
                                                    return $model->getStatusLabel();
                                                },
                                                'format' => 'raw'
                                            ],
                                            //'c_id',
                                            //'c_call_sid',
                                            //'c_call_type_id',
                                            [
                                                'attribute' => 'c_call_type_id',
                                                'value' => function (\common\models\Call $model) {
                                                    return $model->getCallTypeName();
                                                },
                                            ],
                                            'c_from',
                                            [
                                                'label' => 'Client Time',
                                                'value' => function (\common\models\Call $model) {
                                                    return $model->cLead ? $model->cLead->getClientTime2() : '';
                                                },
                                                'format' => 'raw'
                                            ],
                                            //'c_to',

                                        ],
                                    ]) ?>
                                </div>

                                <div class="col-md-6">
                                    <?= \yii\widgets\DetailView::widget([
                                        'model' => $callModel,
                                        'attributes' => [
                                            //'c_caller_name',
                                            //'c_call_duration',
                                            [
                                                'attribute' => 'c_project_id',
                                                'value' => function (\common\models\Call $model) {
                                                    return $model->cProject ? '<span class="badge badge-info">'.$model->cProject->name .'</span>' : '-';
                                                },
                                                'format' => 'raw'
                                            ],
                                            /*[
                                                'attribute' => 'c_lead_id',
                                                'value' => function (\common\models\Call $model) {
                                                    return  $model->c_lead_id ? Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid], ['data-pjax' => 0, 'target' => '_blank']) : '';
                                                },
                                                'format' => 'raw'
                                            ],*/
                                            [
                                                'attribute' => 'c_created_user_id',
                                                'value' => function (\common\models\Call $model) {
                                                    return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                                                },
                                                'format' => 'raw'
                                            ],
                                            [
                                                'attribute' => 'c_created_dt',
                                                'value' => function (\common\models\Call $model) {
                                                    return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt)) : '-';
                                                },
                                                'format' => 'raw'
                                            ],
                                            /*[
                                                'attribute' => 'c_updated_dt',
                                                'value' => function (\common\models\Call $model) {
                                                    return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                                                },
                                                'format' => 'raw'
                                            ],*/


                                        ],
                                    ]) ?>
                                </div>

                                <div class="col-md-12 text-center">
                                    <?php if($callModel->cLead && $callModel->cLead->status === Lead::STATUS_PENDING): ?>
                                        <?=Html::a('<i class="fa fa-download"></i> Take Lead <b id="auto_take_timer" data-name="'.$callModel->cLead->id.'">00:30</b>', ['lead/auto-take', 'gid' => $callModel->cLead->gid], [
                                            'class' => 'btn btn-success',
                                            'target' => '_blank',
                                            'data-pjax' => 0
                                        ])?>
                                        <?=$this->registerJs('startAutoTake();');?>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Warning!</strong> Not found user profile for User (<?=$user->id?>)
            </div>
        <?php endif; ?>
        <hr>

        <div class="row">
            <?php if(!$checkShiftTime): ?>
                <div class="col-md-4">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> New leads are only available on your shift. (Current You time: <?=Yii::$app->formatter->asTime(time())?>)
                    </div>

                    <?/*php \yii\helpers\VarDumper::dump(Yii::$app->user->identity->getShiftTime(), 10, true)?>
                <?php echo date('Y-m-d H:i:s')*/?>
                </div>
            <?php endif; ?>

            <?php if(!$isAccessNewLead): ?>
                <div class="col-md-4">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> Access is denied - action "take new lead"
                    </div>
                </div>
            <?php endif; ?>


            <?php if(isset($accessLeadByFrequency['access']) && $accessLeadByFrequency['access'] == false): ?>
                <div class="col-md-4">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> New leads will be available in <span id="left-time-countdown" data-elapsed="<?= $accessLeadByFrequency['takeDtUTC']->format('U') - time()?>" data-countdown="<?= $accessLeadByFrequency['takeDtUTC']->format('Y-m-d H:i')?>"><?=Yii::$app->formatter->asTime($accessLeadByFrequency['takeDt'])?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <h3>Last 10 Outgoing calls:</h3>

        <?= GridView::widget([
            'dataProvider' => $dataProviderCall,
            //'filterModel' => false, //$searchModelCall,
            //'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
            'rowOptions' => function (\common\models\Call $model, $index, $widget, $grid) {
                if ($model->c_call_status === \common\models\Call::CALL_STATUS_BUSY || $model->c_call_status === \common\models\Call::CALL_STATUS_NO_ANSWER) {
                    return ['class' => 'danger'];
                } elseif ($model->c_call_status === \common\models\Call::CALL_STATUS_RINGING || $model->c_call_status === \common\models\Call::CALL_STATUS_QUEUE ) {
                    return ['class' => 'warning'];
                } /*elseif ($model->c_call_status === \common\models\Call::CALL_STATUS_COMPLETED) {
                    return ['class' => 'success'];
                }*/
            },
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'c_id',
                    'value' => function (\common\models\Call $model) {
                        return $model->c_id;
                    },
                    'options' => ['style' => 'width: 100px']
                ],

                //'c_is_new:boolean',
                //'c_call_sid',
                /*[
                    'attribute' => 'c_call_type_id',
                    'value' => function (\common\models\Call $model) {
                        return $model->getCallTypeName();
                    },
                    'filter' => \common\models\Call::CALL_TYPE_LIST
                ],*/
                [
                    'attribute' => 'c_project_id',
                    'value' => function (\common\models\Call $model) {
                        return $model->cProject ? '<span class="badge badge-info">'.Html::encode($model->cProject->name).'</span>' : '-';
                    },
                    'format' => 'raw',
                    'filter' => $projectList
                ],
                [
                    'attribute' => 'c_lead_id',
                    'value' => function (\common\models\Call $model) {
                        return  $model->c_lead_id; //$model->c_lead_id ? Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid], ['target' => '_blank', 'data-pjax' => 0]) : '-';
                    },
                    'format' => 'raw'
                ],
                'c_from',
                'c_to',
                [
                    'attribute' => 'c_call_status',
                    'value' => function (\common\models\Call $model) {
                        return $model->getStatusLabel();
                    },
                    'format' => 'raw',
                    'filter' => \common\models\Call::CALL_STATUS_LIST
                ],

                //'c_caller_name',
                'c_call_duration',
                [
                    'attribute' => 'c_recording_url',
                    'value' => function (\common\models\Call $model) {
                        return  $model->c_recording_url ? '<audio controls="controls" style="width: 350px; height: 25px"><source src="'.$model->c_recording_url.'" type="audio/mpeg"> </audio>' : '-';
                    },
                    'format' => 'raw'
                ],
                //'c_recording_sid',
                'c_recording_duration',
                /*[
                    'attribute' => 'c_created_user_id',
                    'value' => function (\common\models\Call $model) {
                        return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'c_updated_dt',
                    'value' => function (\common\models\Call $model) {
                        return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                    },
                    'format' => 'raw'
                ],*/

                [
                    'attribute' => 'c_created_dt',
                    'value' => function (\common\models\Call $model) {
                        return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt)) : '-';
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view2}',
                    'buttons' => [
                        'view2' => function ($url, $model) {
                            return Html::a('<i class="glyphicon glyphicon-search"></i>', $url, [
                                'title' => 'View',
                                'data-pjax' => 0,
                                'target' => '_blank'
                            ]);
                        },
                    ],
                ],


            ],
        ]); ?>

        <?php if($dataProvider) :?>
        <h3>Pending leads:</h3>
        <?php
        $gridColumns = [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'label' => 'Lead ID',
                'value' => function (\common\models\Lead $model) {
                    return $model->id;
                },
                //'visible' => ! $isAgent,
                'options' => [
                    'style' => 'width:80px'
                ]
            ],
            [
                'label' => 'Pending Time',
                'value' => function (\common\models\Lead $model) {
                    $createdTS = strtotime($model->created);

                    $diffTime = time() - $createdTS;
                    $diffHours = (int) ($diffTime / (60 * 60));

                    return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours.' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                },
                'options' => [
                    'style' => 'width:180px'
                ],
                'format' => 'raw',
                'visible' => ! $isAgent,
            ],

            [
                'attribute' => 'created',
                'value' => function (\common\models\Lead $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                },

                'format' => 'raw',
                'options' => [
                    'style' => 'width:180px'
                ],
                'filter' => false,
                'enableSorting' => ! $isAgent
            ],

            [
                'header' => 'Client',
                'format' => 'raw',
                'value' => function (\common\models\Lead $model) {

                    if ($model->client) {
                        $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                        if ($clientName === 'Client Name') {
                            $clientName = '- - - ';
                        } else {
                            $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                        }

                        $str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                        $str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';

                        $clientName .= '<br>' . $str;
                    } else {
                        $clientName = '-';
                    }

                    return $clientName;
                },
                'visible' => ! $isAgent,
                'options' => [
                    'style' => 'width:160px'
                ]
            ],/*
        [
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'value' => function ($model) {
                return Lead::getClientTime($model['id']);
            },
            'format' => 'raw'
        ], */



            /*[
                'attribute' => 'Request Details',
                'content' => function (\common\models\Lead $model) {
                    $content = '';
                    $content .= $model->getFlightDetails();
                    $content .= ' (<i class="fa fa-male"></i> x' . ($model->adults . '/' . $model->children . '/' . $model->infants) . ')<br/>';

                    $content .= sprintf('<strong>Cabin:</strong> %s', Lead::getCabin($model['cabin']));

                    return $content;
                },
                'format' => 'raw'
            ],*/

            [
                'header' => 'Depart',
                'value' => function (\common\models\Lead $model) {

                    $segments = $model->leadFlightSegments;

                    if ($segments) {
                        foreach ($segments as $sk => $segment) {
                            return date('d-M-Y', strtotime($segment->departure));
                        }
                    }
                    return '-';

                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'options' => [
                    'style' => 'width:100px'
                ]
            ],

            [
                'header' => 'Segments',
                'value' => function (\common\models\Lead $model) {

                    $segments = $model->leadFlightSegments;
                    $segmentData = [];
                    if ($segments) {
                        foreach ($segments as $sk => $segment) {
                            $segmentData[] = ($sk + 1) . '. <small>' . $segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination . '</small>';
                        }
                    }

                    $segmentStr = implode('<br>', $segmentData);
                    return '' . $segmentStr . '';
                    // return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                },
                'format' => 'raw',
                //'visible' => ! $isAgent,
                'contentOptions' => [
                    'class' => 'text-left'
                ],
                'options' => [
                    'style' => 'width:140px'
                ]
            ],

            [
                'label' => 'Pax',
                'value' => function (\common\models\Lead $model) {
                    return '<span title="adult"><i class="fa fa-male"></i> '. $model->adults .'</span> / <span title="child"><i class="fa fa-child"></i> ' . $model->children . '</span> / <span title="infant"><i class="fa fa-info"></i> ' . $model->infants.'</span>';
                },
                'format' => 'raw',
                //'visible' => ! $isAgent,
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'options' => [
                    'style' => 'width:100px'
                ]
            ],


            [
                'attribute' => 'cabin',
                'value' => function (\common\models\Lead $model) {
                    return \common\models\Lead::getCabin($model->cabin) ?? '-';
                },
                'filter' => false //\common\models\Lead::CABIN_LIST
            ],



            /*[
                'header' => 'Client time',
                'format' => 'raw',
                'value' => function (\common\models\Lead $model) {
                    return $model->getClientTime();
                },
                'visible' => ! $isAgent,
                //'options' => ['style' => 'width:110px'],

            ],*/


            [
                'header' => 'Client time',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->getClientTime2();
                },
                //'options' => ['style' => 'width:80px'],
                //'filter' => \common\models\Employee::getList()
            ],

            [
                'label' => 'Client time2',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->l_client_time;
                },
                //'options' => ['style' => 'width:80px'],
            ],


            [
                'header' => 'Project',
                'attribute' => 'project_id',
                'filter' => false,
                'value' => function (\common\models\Lead $model) {
                    return $model->project ? $model->project->name : '-';
                },
            ],

            [
                'attribute' => 'status',
                'value' => function (\common\models\Lead $model) {
                    return $model->getStatusLabel(); //Lead::STATUS_LIST[$model->status] ?? '-';
                },
                'filter' => Lead::STATUS_LIST,
                'format' => 'raw',
            ],

            [
                'attribute' => 'l_call_status_id',
                'value' => function (\common\models\Lead $model) {
                    return Lead::CALL_STATUS_LIST[$model->l_call_status_id] ?? '-';
                },
                'filter' => Lead::CALL_STATUS_LIST
            ],

            [
                'attribute' => 'request_ip',
                'value' => function (\common\models\Lead $model) {
                    return $model->request_ip;
                },
            ],

            [
                'attribute' => 'l_pending_delay_dt',
                'value' => function (\common\models\Lead $model) {
                    return $model->l_pending_delay_dt ? Yii::$app->formatter->asDatetime(strtotime($model->l_pending_delay_dt)) : '-';
                },
            ],
        ];

            echo GridView::widget([

                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $gridColumns,

                'rowOptions' => function (Lead $model) {
                    if ($model->l_pending_delay_dt && time() < strtotime($model->l_pending_delay_dt)) {
                        return ['class' => 'danger'];
                    }

                    if (!$model->l_client_time && (time() - strtotime($model->created)) > (Lead::PENDING_ALLOW_CALL_TIME_MINUTES * 60)) {
                        return ['class' => 'danger'];
                    }
                }

            ]);

        ?>

        <?php endif; ?>

        <?php Pjax::end(); ?>
    </div>


<?php //$this->registerJs('$(".dial").knob();', \yii\web\View::POS_READY); ?>

<?php
$js = <<<JS


    /*function startTimers() {
    
        $(".timer").each(function( index ) {
            var sec = $( this ).data('sec');
            var control = $( this ).data('control');
            var format = $( this ).data('format');
            //var id = $( this ).data('id');
            //$( this ).addClass( "foo" );
            $(this).timer({format: format, seconds: sec}).timer(control);
            //console.log( index + ": " + $( this ).text() );
        });
    
        //$('.timer').timer('remove');
        //$('.timer').timer({format: '%M:%S', seconds: 0}).timer('start');
    }

    

    $('#btn-user-call-map-refresh').on('click', function () {
        // $('#modal-dialog').find('.modal-content').html('');
        $.pjax.reload({container:'#pjax-call-list'});
    });*/

    $(document).on('pjax:start', function() {
        //$("#modalUpdate .close").click();
    });

    $(document).on('pjax:end', function() {
        //startTimers();
    });
    
    /*startTimers();
        
    setTimeout(function runTimerRefresh() {
       $('#btn-user-call-map-refresh').click();
      setTimeout(runTimerRefresh, 30000);
    }, 30000);*/

    


JS;
$this->registerJs($js);




