<?php
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\Lead;
use common\models\Quote;
use yii\helpers\Url;
use common\models\Airport;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $multipleForm \frontend\models\LeadMultipleForm */
/* @var $isAgent bool */
/* @var $salary float */
/* @var $salaryBy string */

$this->title = 'Sold';

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}

?>
<style>
.dropdown-menu {
	z-index: 1010 !important;
}
</style>
<div class="lead-index">

    <?php Pjax::begin(); //['id' => 'lead-pjax-list', 'timeout' => 5000, 'enablePushState' => true, 'clientOptions' => ['method' => 'GET']]); ?>
    <?php if(isset($salary)):?>
    <h3>Salary by <?= $salaryBy?>: $<?= number_format($salary['salary'],2)?>
    (Base: $<?= intval($salary['base'])?>, Commission: <?= $salary['commission']?>%, Bonus: $<?= $salary['bonus']?>)</h3>
    <?php endif;?>
    <?= $this->render('_search_sold', ['model' => $searchModel]); ?>

    <?php $form = \yii\bootstrap\ActiveForm::begin(['options' => ['data-pjax' => true]]); // ['action' => ['leads/update-multiple'] ?>

    <?php

    $gridColumns = [
        [
            'attribute' => 'pending',
            'label' => 'Pending Time',
            'value' => function ($model) {
                return Lead::getPendingAfterCreate($model->created);
            },
            'format' => 'raw'
        ],

        [
            'attribute' => 'id',
            'label' => 'Lead ID / Sale ID (BO)',
            'value' => function ($model) {
                return sprintf('%d / %d', $model['id'], $model['bo_flight_id']);
            }
        ],
        [
            'label' => 'PNR',
            'value' => function ($model) {
                if (! empty($model['additional_information'])) {
                    $additionally = new \common\models\local\LeadAdditionalInformation();
                    $additionally->setAttributes(@json_decode($model['additional_information'], true));
                    return (! empty($additionally->pnr)) ? $additionally->pnr : '-';
                }
                return '-';
            },
        ],
        [
            'label' => 'Passengers',
            'value' => function ($model) {
                $content = [];
                if (! empty($model['additional_information'])) {
                    $additionally = new \common\models\local\LeadAdditionalInformation();
                    $additionally->setAttributes(@json_decode($model['additional_information'], true));
                    $content = (! empty($additionally->passengers)) ? $additionally->passengers : $content;
                }
                return implode('<br/>', $content);
            },
            'format' => 'raw'
        ],
        [
            // 'attribute' => 'client_id',
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
                } else {
                    $clientName = '-';
                }

                return $clientName;
            },
            'options' => [
                'style' => 'width:160px'
            ]
            // 'filter' => \common\models\Employee::getList()
        ],
        [
            'header' => 'Client Emails',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) use ($isAgent) {
                if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
                    $str = '- // - // - // -';
                } else {
                    $str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                }

                return $str ?? '-';
            },
            'options' => [
                'style' => 'width:180px'
            ]
        ],

        [
            'label' => 'Client Phone',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) use ($isAgent) {
                if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
                    $str = '- // - // - // -';
                } else {
                    $str = $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';
                }

                return $str ?? '-';
            },
            'options' => [
                'style' => 'width:180px'
            ]
        ],
        [
            'label' => 'Destination',
            'value' => function (\common\models\Lead $model) {
                $segments = $model->leadFlightSegments;
                $segmentData = [];
                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        $airport = Airport::findIdentity($segment->destination);
                        if ($airport) {
                            return $airport->city . " (" . $segment->destination . ")";
                        }
                        return $segment->destination;
                    }
                }
                return '';
            },
            'format' => 'raw'
        ],

        [
            'header' => 'Agent',
            'attribute' => 'employee_id',
            'format' => 'raw',
            'value' => function (\common\models\Lead $model) {
                return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
            },
            'filter' => $userList,
            'visible' => !$isAgent,
        ],
        [
            'label' => 'Profit',
            'value' => function ($model) {
                $quote = $model->getBookedQuote();
                return "<strong>$" . number_format(Quote::countProfit($quote->id), 2) . "</strong>";
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Date of Issue',
            'attribute' => 'updated',
            'value' => function ($model) {
                return $model['updated'];
            },
            'format' => 'datetime',
            'filter' => DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'updated',
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy'
                ]
            ]),
            'contentOptions'=>['style'=>'width: 180px;text-align:center;']
        ],
        [
            'label' => 'Date of Departure',
            'value' => function ($model) {
                $quote = $model->getBookedQuote();
                if (isset($quote['reservation_dump']) && ! empty($quote['reservation_dump'])) {
                    $data = [];
                    $segments = Quote::parseDump($quote['reservation_dump'], false, $data, true);
                    return $segments[0]['departureDateTime']->format('Y-m-d H:i');
                }
                return $model['departure'];
            },
            'format' => 'raw'
        ],
        [
            'label' => 'Rating',
            'contentOptions' => [
                'style' => 'width: 90px;',
                'class' => 'text-center'
            ],
            'options' => [
                'class' => 'text-right'
            ],
            'value' => function ($model) {
                return Lead::getRating2($model['rating']);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'source_id',
            'label' => 'Market Info',
            'visible' => ! $isAgent,
            'value' => function (\common\models\Lead $model) {
                return $model->source ? $model->source->name : '-';
            },
            'filter' => \common\models\Source::getList(),
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{action}',
            'buttons' => [
                'action' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-search"></i>', Url::to([
                        'lead/quote',
                        'type' => 'sold',
                        'id' => $model['id']
                    ]), [
                        'class' => 'btn btn-info btn-sm',
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View lead'
                    ]);
                }
            ]
        ]
    ];

    ?>
<?php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'toolbar' => false,
    'pjax' => false,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'floatHeader' => true,
    'floatHeaderOptions' => [
        'scrollingTop' => 20
    ],
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> Sold</h3>'
    ]

]);

?>


    <?php \yii\bootstrap\ActiveForm::end(); ?>


    <?php Pjax::end(); ?>


</div>
