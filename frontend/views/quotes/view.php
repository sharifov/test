<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Quote */
/* @var $searchModel common\models\search\QuotePriceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote: ' . $model->id . ', UID: ' . $model->uid;
$this->params['breadcrumbs'][] = ['label' => 'Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$showGdsOfferId = ($user->isAdmin() || $user->isSuperAdmin() || $user->isQa());
?>
<div class="quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php /*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <div class="row">
        <div class="col-md-3">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'uid',
                    //'lead_id',
                    [
                        'attribute' => 'lead_id',
                        'format' => 'html',
                        'value' => function (\common\models\Quote $model) {
                            return '<i class="fa fa-arrow-right"></i> ' . Html::a($model->lead_id, ['leads/view', 'id' => $model->lead_id], ['target' => '_blank']);
                        },
                    ],
                    [
                        'attribute' => 'employee_id',
                        'format' => 'raw',
                        'value' => function (\common\models\Quote $model) {
                            return $model->employee ? '<i class="fa fa-user"></i> ' . $model->employee->username : '-';
                        },

                    ],
                    'record_locator',
                    'check_payment:boolean',
                    'type_id:quoteType',
                    [
                        'attribute' => 'quoteSearchCid',
                        'value' => static function (\common\models\Quote $model) {
                            return $model->quoteSearchCid->qsc_cid;
                        },
                        'visible' => \src\services\quote\QuoteSearchCidService::userIsCanSeeCid($model),
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'status',
                        'value' => function (\common\models\Quote $model) {
                            return $model->getStatusName(true);
                        },
                        'format' => 'raw',
                        'filter' => \common\models\Quote::STATUS_LIST
                    ],

                    [
                        'attribute' => 'gds',
                        'value' => function (\common\models\Quote $model) {
                            return '<i class="fa fa-plane"></i> ' . $model->getGdsName2();
                        },
                        'format' => 'raw',

                    ],
                    'pcc',
                    [
                        'attribute' => 'trip_type',
                        'value' => function (\common\models\Quote $model) {
                            return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                        },

                    ],
                    [
                        'attribute' => 'cabin',
                        'value' => function (\common\models\Quote $model) {
                            return \common\models\Lead::getCabin($model->cabin) ?? '-';
                        },
                        'filter' => \common\models\Lead::CABIN_LIST
                    ],
                    'main_airline_code',
                     'provider_project_id:projectName',
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [

                    //'reservation_dump:ntext',
                    [
                        'attribute' => 'reservation_dump',
                        'value' => function (\common\models\Quote $model) {
                            return '<pre>' . $model->reservation_dump . '</pre>';
                        },
                        'format' => 'html',
                    ],

                    'fare_type',
                    //'created',
                    //'updated',
                    [
                        'attribute' => 'created',
                        'value' => function (\common\models\Quote $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'updated',
                        'value' => function (\common\models\Quote $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'service_fee_percent',
                        'visible' => $showGdsOfferId,
                    ],
                    'q_client_currency',
                    'q_client_currency_rate',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3>Prices:</h3>
            <?php \yii\widgets\Pjax::begin(); ?>
            <p>
                <?php //= Html::a('Create Lead', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'options' => ['style' => 'width:80px'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'passenger_type',
                        'value' => function (\common\models\QuotePrice $model) {
                            return '<i class="fa fa-user"></i> ' . $model->getPassengerTypeName();
                        },
                        'format' => 'raw',
                        'filter' => \common\models\QuotePrice::PASSENGER_TYPE_LIST
                    ],

                    [
                        'attribute' => 'selling',
                        'options' => ['style' => 'width:110px'],
                        //'format' => ['decimal',2],
                        'contentOptions' => ['class' => 'text-right'],
                        'format' => 'currency'
                    ],
                    [
                        'attribute' => 'net',
                        'options' => ['style' => 'width:110px'],
                        'contentOptions' => ['class' => 'text-right'],
                        'format' => 'currency'
                    ],
                    [
                        'attribute' => 'fare',
                        'options' => ['style' => 'width:110px'],
                        'contentOptions' => ['class' => 'text-right'],
                        'format' => 'currency'
                    ],

                    [
                        'attribute' => 'taxes',
                        'options' => ['style' => 'width:110px'],
                        'contentOptions' => ['class' => 'text-right'],
                        'format' => 'currency'
                    ],

                    [
                        'attribute' => 'mark_up',
                        'options' => ['style' => 'width:110px'],
                        'contentOptions' => ['class' => 'text-right'],
                        'format' => 'currency'
                    ],
                    [
                        'attribute' => 'extra_mark_up',
                        'options' => ['style' => 'width:110px'],
                        'contentOptions' => ['class' => 'text-right'],
                        'format' => 'currency'
                    ],
                    [
                        'attribute' => 'agent_processing_fee'
                    ],
                    [
                        'attribute' => 'created',
                        'value' => function (\common\models\QuotePrice $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'raw',
                    ],

                    [
                        'attribute' => 'updated',
                        'value' => function (\common\models\QuotePrice $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                        },
                        'format' => 'raw',
                    ],


                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'quote-price'],

                ],
            ]); ?>
            <?php \yii\widgets\Pjax::end(); ?>
        </div>
    </div>

    <?php if ($showGdsOfferId) : ?>
    <div class="row">
        <div class="col-md-12">
            <h3>Origin Search Data:</h3>
            <div>
                <pre><small><?php \yii\helpers\VarDumper::dump(@json_decode($model->origin_search_data, true), 10, true); ?></small></pre>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
