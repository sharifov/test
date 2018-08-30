<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Search Leads';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-index table-responsive">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'Lead Id',
                'attribute' => 'id',
                'value' => function ($model) {
                    return Html::a($model->id, ['leads/view', 'id' => $model->id]);
                },
                'format' => 'raw',
                'options' => ['style' => 'width:100px'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'Lead UID',
                'attribute' => 'uid',
                'options' => ['style' => 'width:100px'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'BO Sale ID',
                'attribute' => 'bo_flight_id',
            ],
            [
                'header' => 'Client name',
                'format' => 'raw',
                'value' => function (\common\models\Lead $model) {
                    return $model->client ? '<i class="fa fa-user"></i> ' . Html::encode($model->client->first_name . ' ' . $model->client->last_name) : '-';
                },
                'options' => ['style' => 'width:160px'],
            ],
            [
                'header' => 'Client Emails/Phones',
                'format' => 'raw',
                'value' => function (\common\models\Lead $model) {
                    $str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                    $str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';

                    return $str ?? '-';
                },
                'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'status',
                'value' => function (\common\models\Lead $model) {
                    return $model->getStatusName(true);
                },
                'format' => 'html',
                'filter' => \common\models\Lead::STATUS_LIST
            ],
            [
                'header' => 'Quotes',
                'value' => function (\common\models\Lead $model) {
                    return $model->quotesCount ? Html::a($model->quotesCount, ['quote/index', "QuoteSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'header' => 'Segments',
                'value' => function (\common\models\Lead $model) {

                    $segments = $model->leadFlightSegments;
                    $segmentData = [];
                    if ($segments) {
                        foreach ($segments as $sk => $segment) {
                            $segmentData[] = ($sk + 1) . '. <code>' . Html::a($segment->origin . '->' . $segment->destination, ['lead-flight-segment/view', 'id' => $segment->id], ['target' => '_blank', 'data-pjax' => 0]) . '</code>';
                        }
                    }

                    $segmentStr = implode('<br>', $segmentData);
                    return '' . $segmentStr . '';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'Market Info',
                'attribute' => 'source_id',
                'value' => function (\common\models\Lead $model) {
                    return $model->source ? $model->source->name : '-';
                },
                'filter' => \common\models\Source::getList()
            ],

            [
                'attribute' => 'trip_type',
                'value' => function (\common\models\Lead $model) {
                    return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                },
                'filter' => \common\models\Lead::TRIP_TYPE_LIST
            ],

            [
                'attribute' => 'cabin',
                'value' => function (\common\models\Lead $model) {
                    return \common\models\Lead::getCabin($model->cabin) ?? '-';
                },
                'filter' => \common\models\Lead::CABIN_LIST
            ],

            [
                'attribute' => 'adults',
                'value' => function (\common\models\Lead $model) {
                    return $model->adults ?: 0;
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'children',
                'value' => function (\common\models\Lead $model) {
                    return $model->children ?: '-';
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'attribute' => 'infants',
                'value' => function (\common\models\Lead $model) {
                    return $model->infants ?: '-';
                },
                'filter' => array_combine(range(0, 9), range(0, 9)),
                'contentOptions' => ['class' => 'text-center'],
            ],

            [
                'header' => 'Agent',
                'format' => 'raw',
                'value' => function (\common\models\Lead $model) {
                    return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
                },
                'filter' => !in_array(Yii::$app->user->identity->role, ['agent', 'coach'])
                    ? Html::activeDropDownList($searchModel, 'employee_id', \common\models\Employee::getList(), [
                        'prompt' => '',
                        'class' => 'form-control'
                    ])
                    : null,
            ],
            [
                'header' => 'Created',
                'value' => function (\common\models\Lead $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime($model->created, 'php:Y-m-d [H:i]');
                },
                'format' => 'html',
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
