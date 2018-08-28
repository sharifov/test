<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QuotePrice */

$this->title = 'Price Quote: ' . $model->id . ', Quote UID: "'.($model->quote ? $model->quote->uid : '-').'"';
$this->params['breadcrumbs'][] = ['label' => 'Quote Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-price-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?//= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            //'quote_id',

            [
                'attribute' => 'quote_id',
                'header'    => 'Quote UID',
                'format' => 'html',
                'value' => function(\common\models\QuotePrice $model) {
                    return $model->quote ? Html::a($model->quote->uid, ['quote/view', 'id' => $model->quote_id], ['target' => '_blank']) . ' (id: '.$model->quote_id.')' : '-';
                },
            ],

            /*[
                'attribute' => 'quote.uid',
                'header'    => 'Quote UID',
            ],*/
            [
                'attribute' => 'quote.status',
                //'header'    => 'Quote Status',
                'value' => function(\common\models\QuotePrice $model) {
                    return $model->quote ? $model->quote->getStatusName(true) : '-';
                },
                'format' => 'html',
            ],

        ],
    ]) ?>
        </div>
        <div class="col-md-3">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [


                    [
                        'attribute' => 'selling',
                        //'format' => ['decimal',2],
                        'contentOptions' => ['class' => 'text-right'],
                        'format' => 'currency'
                    ],
                    [
                        'attribute' => 'net',
                        'contentOptions' => ['class' => 'text-right'],
                        'format' => 'currency'
                    ],
                    [
                        'attribute' => 'fare',
                        'contentOptions' => ['class' => 'text-right'],
                        'format' => 'currency'
                    ],

                    [
                        'attribute' => 'taxes',
                        'contentOptions' => ['class' => 'text-right'],
                        'format' => 'currency'
                    ],

                ],
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [

                    [
                        'attribute' => 'passenger_type',
                        'value' => function(\common\models\QuotePrice $model) {
                            return '<i class="fa fa-user"></i> '.$model->getPassengerTypeName();
                        },
                        'format' => 'raw',
                        'filter' => \common\models\QuotePrice::PASSENGER_TYPE_LIST
                    ],



                    [
                        'attribute' => 'mark_up',
                        'options' => ['style' => 'width:110px'],

                    ],
                    [
                        'attribute' => 'extra_mark_up',
                        'options' => ['style' => 'width:110px'],

                    ],



                ],
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [

                    [
                        'attribute' => 'created',
                        'value' => function(\common\models\QuotePrice $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime($model->created, 'php:Y-m-d [H:i]');
                        },
                        'format' => 'html',
                    ],

                    [
                        'attribute' => 'updated',
                        'value' => function(\common\models\QuotePrice $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime($model->updated, 'php:Y-m-d [H:i]');
                        },
                        'format' => 'html',
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
