<?php

use common\components\SearchService;
use common\models\Department;
use common\models\Lead;
use frontend\widgets\lead\editTool\ButtonWidget;
use src\auth\Auth;
use src\model\client\helpers\ClientFormatter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Lead */
/* @var $searchModel common\models\search\QuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

/* @var $searchModelSegments common\models\search\LeadFlightSegmentSearch */
/* @var $dataProviderSegments yii\data\ActiveDataProvider */


$this->title = 'Lead ID: ' . $model->id . ', UID: ' . $model->uid;
$this->params['breadcrumbs'][] = ['label' => 'Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$isAgent = Auth::user()->isAgent();

?>
<div class="lead-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?= Html::a('<i class="fa fa-search"></i> View Lead', ['/lead/view', 'gid' => $model->gid], ['class' => 'btn btn-primary']) ?>

        <?= ButtonWidget::widget([
            'modalId' => 'modal-df',
            'url' => new \frontend\widgets\lead\editTool\Url(Url::to(['leads/edit']), ['id' => $model->id])])
?>

        <?php /*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-leads-view']) ?>
    <div class="row">

        <div class="col-md-3">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [

                    'l_client_first_name',
                    'l_client_last_name',
                    'l_client_phone',
                    'l_client_email',
                    'l_client_ua',
                    'l_client_lang',



                ],
            ]) ?>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [


                    'client_id',
                    [
                        'attribute' => 'client.name',
                        'header' => 'Client name',
                        'format' => 'raw',
                        'value' => function (\common\models\Lead $model) {
                            if ($model->client) {
                                $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                                if ($clientName === 'Client Name') {
                                    $clientName = '- - - ';
                                } else {
                                    $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                                }
                                if ($model->client->isExcluded()) {
                                    $clientName = ClientFormatter::formatExclude($model->client)  . $clientName;
                                }
                            } else {
                                $clientName = '-';
                            }

                            return $clientName;
                        },
                        //'options' => ['style' => 'width:160px'],
                        //'filter' => \common\models\Employee::getList()
                    ],

                    [
                        'attribute' => 'client.phone',
                        'header' => 'Client Phones',
                        'format' => 'raw',
                        'value' => function (\common\models\Lead $model) use ($isAgent) {
                            if ($model->client && $model->client->clientPhones) {
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
                        //'options' => ['style' => 'width:180px'],
                    ],


                    [
                        'attribute' => 'client.email',
                        'header' => 'Client Emails',
                        'format' => 'raw',
                        'value' => function (\common\models\Lead $model) use ($isAgent) {

                            if ($model->client && $model->client->clientEmails) {
                                if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
                                    $str = '- // - // - // -';
                                } else {
                                    $str = '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email'));
                                }
                            } else {
                                $str = '-';
                            }

                            return $str ?? '-';
                        },
                        //'options' => ['style' => 'width:180px'],
                    ],

                ],
            ]) ?>
        </div>
        <div class="col-md-3">
            <div class="table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'uid',
                        'gid',
                        'l_request_hash',

                        [
                            'attribute' => 'employee_id',
                            'format' => 'raw',
                            'value' => function (\common\models\Lead $model) {
                                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
                            },
                        ],

                        //'employee_id',

                        [
                            'attribute' => 'l_init_price',
                            //'format' => 'raw',
                            'value' => function (\common\models\Lead $model) {
                                return $model->l_init_price ? number_format($model->l_init_price, 2) : '-';
                            },
                        ],

                        [
                            'attribute' => 'status',
                            'value' => function (\common\models\Lead $model) {
                                return $model->getStatusName(true);
                            },
                            'format' => 'raw',

                        ],
                        [
                            'attribute' => 'l_type',
                            'value' => function (\common\models\Lead $model) {
                                if (empty($model->l_type)) {
                                    return Yii::$app->formatter->nullDisplay;
                                }
                                $types = ArrayHelper::merge(Lead::TYPE_LIST, [Lead::TYPE_BASIC => 'Basic']);
                                return $types[$model->l_type] ?? 'undefined';
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'l_call_status_id',
                            'value' => function (\common\models\Lead $model) {
                                return Lead::CALL_STATUS_LIST[$model->l_call_status_id] ?? 'undefined';
                            },
                            'format' => 'raw',
                        ],

                        [
                            'attribute' => 'l_duplicate_lead_id',
                            'label' => 'Duplicate from',
                            'value' => static function (\common\models\Lead $model) {
                                return $model->l_duplicate_lead_id ? Html::a($model->l_duplicate_lead_id, ['/leads/view', 'id' => $model->l_duplicate_lead_id], ['data-pjax' => 0, 'target' => '_blank']) : '-';
                            },
                            'format' => 'raw',

                        ],

                        [
                            'label' => 'Department',
                            'value' => function (\common\models\Lead $model) {
                                return Department::DEPARTMENT_LIST[$model->l_dep_id] ?? 'undefined';
                            },
                        ],

                        [
                            'attribute' => 'l_dep_id',
                            'value' => function (\common\models\Lead $model) {
                                return $model->project ? $model->project->name : '-';
                            },

                        ],

                        [
                            'attribute' => 'source_id',
                            'value' => function (\common\models\Lead $model) {
                                return $model->source ? $model->source->name : '-';
                            },
                            'visible' => !$isAgent
                        ],

                        [
                                'label' => 'Type create',
                                'attribute' => 'l_type_create',
                                'value' => static function (Lead $lead) {
                                    if ($lead->l_type_create === null) {
                                        return '';
                                    }
                                    return Lead::TYPE_CREATE_LIST[$lead->l_type_create] ?? 'Undefined';
                                },
                        ],

                    ],
                ]) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute' => 'trip_type',
                            'value' => function (\common\models\Lead $model) {
                                return $model->getFlightTypeName();
                            },

                        ],

                        [
                            'attribute' => 'cabin',
                            'value' => function (\common\models\Lead $model) {
                                return $model->getCabinClassName();
                            },

                        ],

                        /*'project_id',
                        'source_id',
                        'trip_type',
                        'cabin',*/
                        'adults',
                        'children',
                        'infants',
                        'notes_for_experts:ntext',


                        [
                            'attribute' => 'created',
                            'value' => function (\common\models\Lead $model) {
                                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'updated',
                            'value' => function (\common\models\Lead $model) {
                                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'l_last_action_dt',
                            'value' => function (\common\models\Lead $model) {
                                return $model->l_last_action_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->l_last_action_dt)) : $model->l_last_action_dt;
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'l_status_dt',
                            'value' => static function (\common\models\Lead $model) {
                                return $model->l_status_dt ?
                                    '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->l_status_dt)) :
                                    Yii::$app->formatter->nullDisplay;
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'l_expiration_dt',
                            'value' => static function (\common\models\Lead $model) {
                                if (!$model->l_expiration_dt) {
                                    return Yii::$app->formatter->nullDisplay;
                                }
                                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->l_expiration_dt));
                            },
                            'format' => 'raw',
                        ],
                        'additional_information',
                        'l_visitor_log_id',
                    ],
                ]) ?>
            </div>
        </div>

        <div class="col-md-3">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [


                    //'request_ip',
                    [
                        'attribute' => 'request_ip',
                        'value' => function (\common\models\Lead $model) {
                            return $model->request_ip ? Html::button($model->request_ip, ['class' => 'btn btn-info',  'id' => 'btn_show_modal', 'title' => 'Detail IP info: ' . $model->request_ip]) : '-';
                        },
                        'format' => 'raw'

                    ],
                    //'request_ip_detail:ntext',
                    'offset_gmt',
                    'snooze_for',
                    'rating',
                    'called_expert',
                    'discount_id',
                    'bo_flight_id',
                    'final_profit',
                    'tips',
                    'agents_processing_fee',
                ],
            ]) ?>

            <?php /*if($model->request_ip_detail): ?>
            <pre>
                <?
                    $data = @json_decode($model->request_ip_detail);
                    \yii\helpers\VarDumper::dump($data, 10, true);
                ?>
            </pre>
            <?php endif;*/ ?>
        </div>

    </div>
    <?php Pjax::end() ?>

    <div class="row">
        <div class="col-md-12">
            <h3>Flight Segments:</h3>
            <?php \yii\widgets\Pjax::begin(); ?>

            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProviderSegments,
                'filterModel' => $searchModelSegments,
                'columns' => [
                    'id',
                    /*[
                        'attribute' => 'lead_id',
                        'format' => 'raw',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-arrow-right"></i> '.Html::a('lead: '.$model->lead_id, ['leads/view', 'id' => $model->lead_id], ['target' => '_blank', 'data-pjax' => 0]);
                        },
                    ],*/
                    'origin',
                    'destination',
                    [
                        'attribute' => 'departure',
                        'value' => function (\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-calendar"></i> ' . date("Y-m-d", strtotime($model->departure));
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'flexibility',
                        'value' => function (\common\models\LeadFlightSegment $model) {
                            return $model->flexibility;
                        },
                        'filter' => array_combine(range(0, 5), range(0, 5)),
                    ],
                    [
                        'attribute' => 'flexibility_type',
                        'value' => function (\common\models\LeadFlightSegment $model) {
                            return $model->flexibility_type;
                        },
                        'filter' => \common\models\LeadFlightSegment::FLEX_TYPE_LIST
                    ],
                    [
                        'attribute' => 'created',
                        'value' => function (\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],

                    [
                        'attribute' => 'updated',
                        'value' => function (\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                        },
                        'format' => 'html',
                    ],

                    'origin_label',
                    'destination_label',

                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'lead-flight-segment'],
                ],
            ]); ?>

            <?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3>Quotes:</h3>
        <?php \yii\widgets\Pjax::begin(); ?>
        <p>
            <?php //= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?= \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                'id',
                'uid',
                //'lead_id',
                //'employee_id',
                [
                    'attribute' => 'employee_id',
                    'format' => 'raw',
                    'value' => function (\common\models\Quote $model) {
                        return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
                    },
                    'filter' => \common\models\Employee::getList()
                ],
                'record_locator',

                //'cabin',
                //'gds',

                [
                    'attribute' => 'gds',
                    'value' => function (\common\models\Quote $model) {
                        return '<i class="fa fa-plane"></i> ' . $model->getGdsName2();
                    },
                    'format' => 'raw',
                    'filter' => SearchService::GDS_LIST
                ],

                'pcc',

                [
                    'attribute' => 'trip_type',
                    'value' => function (\common\models\Quote $model) {
                        return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                    },
                    'filter' => \common\models\Lead::TRIP_TYPE_LIST
                ],

                [
                    'attribute' => 'cabin',
                    'value' => function (\common\models\Quote $model) {
                        return \common\models\Lead::getCabin($model->cabin) ?? '-';
                    },
                    'filter' => \common\models\Lead::CABIN_LIST
                ],
                //'trip_type',
                'main_airline_code',
                //'reservation_dump:ntext',

                [
                    'attribute' => 'reservation_dump',
                    'value' => function (\common\models\Quote $model) {
                        return '<pre style="font-size: 9px">' . $model->reservation_dump . '</pre>';
                    },
                    'format' => 'html',
                ],

                //'status',
                [
                    'attribute' => 'status',
                    'value' => function (\common\models\Quote $model) {
                        return $model->getStatusName(true);
                    },
                    'format' => 'html',
                    'filter' => \common\models\Quote::STATUS_LIST
                ],
                'check_payment:boolean',
                'fare_type',


                [
                    'header' => 'Prices',
                    'value' => function (\common\models\Quote $model) {
                        return $model->quotePricesCount ? Html::a($model->quotePricesCount, ['quote-price/index', "QuotePriceSearch[quote_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                    },
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-center'],
                ],

                //'created',
                //'updated',

                [
                    'attribute' => 'created',
                    'value' => function (\common\models\Quote $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                    },
                    'format' => 'html',
                ],

                [
                    'attribute' => 'updated',
                    'value' => function (\common\models\Quote $model) {
                        return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                    },
                    'format' => 'html',
                ],

                ['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'quotes'],

                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
        <?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <h3>Lead Preferences:</h3>
            <?php if ($model->leadPreferences) : ?>
                <?php echo DetailView::widget([
                    'model' => $model->leadPreferences,
                    'attributes' => [
                        'pref_currency',
                        'notes',
                        'pref_language',
                        'pref_airline',
                        'number_stops',
                        'clients_budget',
                        'market_price',
                    ],
                ]) ?>
            <?php else: ?>
                <?php echo Yii::$app->formatter->nullDisplay ?>
            <?php endif ?>
        </div>
    </div>
</div>



<?php
yii\bootstrap4\Modal::begin([
    'id' => 'modal-ip',
    'size' => 'modal-lg',
    'title' => '',
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);

if ($model->request_ip_detail) {
    $data = @json_decode($model->request_ip_detail);

    if ($data) {
        echo '<pre>';
        \yii\helpers\VarDumper::dump($data, 10, true);
        echo '</pre>';
    }
}
yii\bootstrap4\Modal::end();


$jsCode = <<<JS
    $(document).on('click', '#btn_show_modal', function(){
        $('#modal-ip-label').html($(this).attr('title'));
        $('#modal-ip').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);