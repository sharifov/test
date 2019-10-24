<?php

use common\models\Employee;
use common\models\Lead;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\LeadQcall;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadQcallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Redial';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;
$userIsFreeForCall = $user->isCallFree();

?>
    <div class="lead-qcall-list">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-md-12">
                <?php Pjax::begin(['id' => 'redial-call-box-pjax', 'enablePushState' => false, 'enableReplaceState' => false]); ?>
                    <div id="redial-call-box">
                        <div class="text-center badge badge-warning call-status" style="font-size: 35px">
                            <span id="text-status-call">Ready</span>
                        </div>
                    </div>
                <?php Pjax::end(); ?>
            </div>
        </div>

        <p></p>

        <?php Pjax::begin(['id' => 'lead-redial-pjax', 'enablePushState' => false, 'enableReplaceState' => true]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => static function (LeadQcall $model, $index, $widget, $grid) {
                if (!$model->lqcLead->isCallReady()) {
                    return ['class' => 'danger'];
                }
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'lqc_lead_id',
                [
                    'attribute' => 'lqc_lead_id',
                    'value' => static function (LeadQcall $model) {
                        return Html::a($model->lqc_lead_id, ['lead/view', 'gid' => $model->lqcLead->gid], ['target' => '_blank', 'data-pjax' => 0]);
                    },
                    'format' => 'raw'
                ],
                [
                    'label' => 'Status',
                    'value' => static function (LeadQcall $model) {
                        return $model->lqcLead->getStatusName(true);
                    },
                    'format' => 'raw',
                ],
                [
                    'label' => 'Call status',
                    'value' => static function (LeadQcall $model) {
                        return Lead::CALL_STATUS_LIST[$model->lqcLead->l_call_status_id] ?? '-';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'lqcLead.project_id',
                    'value' => static function (LeadQcall $model) {
                        return $model->lqcLead->project ? '<span class="badge badge-info">' . Html::encode($model->lqcLead->project->name) . '</span>' : '-';
                    },
                    'format' => 'raw',
                    'options' => [
                        'style' => 'width:120px'
                    ],
                ],

                [
                    'attribute' => 'lqcLead.source_id',
                    'value' => static function (LeadQcall $model) {
                        return $model->lqcLead->source ? $model->lqcLead->source->name : '-';
                    },
                ],

                [
                    'header' => 'Client time',
                    'format' => 'raw',
                    'value' => static function (LeadQcall $model) {
                        return $model->lqcLead->getClientTime2();
                    },
                    'options' => ['style' => 'width:90px'],
                ],

                [
                    'attribute' => 'employee_id',
                    'format' => 'raw',
                    'value' => static function (LeadQcall $model) {
                        return $model->lqcLead->employee ? '<i class="fa fa-user"></i> ' . $model->lqcLead->employee->username : '-';
                    },
                    'filter' => false
                ],

                [
                    'attribute' => 'lqcLead.pending',
                    'label' => 'Pending Time',
                    'value' => static function (LeadQcall $model) {

                        $createdTS = strtotime($model->lqcLead->created);

                        $diffTime = time() - $createdTS;
                        $diffHours = (int)($diffTime / (60 * 60));


                        $str = ($diffHours > 3 && $diffHours < 73) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                        $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqcLead->created));

                        return $str;
                    },
                    'options' => [
                        'style' => 'width:160px'
                    ],
                    'format' => 'raw'
                ],

                [
                    'label' => 'Out Calls',
                    'value' => static function (LeadQcall $model) {
                        $cnt = $model->lqcLead->getCountCalls(\common\models\Call::CALL_TYPE_OUT);
                        return $cnt ?: '-';
                    },
                    'options' => [
                        'style' => 'width:60px'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    //'format' => 'raw'
                ],


                'lqc_weight',
                [
                    'attribute' => 'lqc_dt_from',
                    'value' => static function (LeadQcall $model) {
                        return $model->lqc_dt_from ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_dt_from)) : '-';
                    },
                    'format' => 'raw'
                ],

                [
                    'attribute' => 'lqc_dt_to',
                    'value' => static function (LeadQcall $model) {
                        return $model->lqc_dt_to ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lqc_dt_to)) : '-';
                    },
                    'format' => 'raw'
                ],

                [
                    'label' => 'Duration',
                    'value' => static function (LeadQcall $model) {
                        return Yii::$app->formatter->asDuration(strtotime($model->lqc_dt_to) - strtotime($model->lqc_dt_from));
                    },
                ],

                [
                    'label' => 'Deadline',
                    'value' => static function (LeadQcall $model) {
                        $timeTo = strtotime($model->lqc_dt_to);
                        return time() <= $timeTo ? Yii::$app->formatter->asDuration($timeTo - time()) : 'deadline';
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {call}',
                    'buttons' => [
                        'view' => static function ($url, LeadQcall $model) {
                            return Html::a('<i class="glyphicon glyphicon-search"></i> View', [
                                'lead-qcall/view',
                                'id' => $model->lqc_lead_id
                            ], [
                                'class' => 'btn btn-info btn-xs',
                                'target' => '_blank',
                                'data-pjax' => 0,
                                'title' => 'View',
                            ]);
                        },
                        'call' => static function ($url, LeadQcall $model) use ($userIsFreeForCall) {
                            return Html::button('<i class="fa fa-phone"></i> Call', [
                                'class' => 'btn btn-primary btn-xs lead-redial-btn',
                                'disabled' => (!$model->lqcLead->isCallReady() || !$userIsFreeForCall) ? 'disabled' : false,
                                'data-url' => Url::to(['lead-redial/redial']),
                                'data-gid' => $model->lqcLead->gid,
                            ]);
                        }
                    ]
                ],
            ],
        ]); ?>

        <?php Pjax::end(); ?>

    </div>

<?php

$js = <<<JS
    $("body").on("click", ".lead-redial-btn", function(e) {
        $("body").find("#redial-call-box").html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
        $.pjax.reload({
            container: '#redial-call-box-pjax', 
            async: false, 
            push: false, 
            replace: false, 
            url: $(this).data('url'), 
            type: 'post',
            data: {gid: $(this).data('gid')}
        });
    });
JS;

$this->registerJs($js);
