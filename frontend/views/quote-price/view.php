<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QuotePrice */

$defaultCurrencyCode = \common\models\Currency::getDefaultCurrencyCode();
$this->title = 'Price Quote: ' . $model->id . ', Quote UID: "' . ($model->quote ? $model->quote->uid : '-') . '"';
$this->params['breadcrumbs'][] = ['label' => 'Quote Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-price-view">

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
            <?php echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'passenger_type',
                        'value' => function (\common\models\QuotePrice $model) {
                            return '<i class="fa fa-user"></i> ' . $model->getPassengerTypeName();
                        },
                        'format' => 'raw',
                        'filter' => \common\models\QuotePrice::PASSENGER_TYPE_LIST
                    ],
                    [
                        'attribute' => 'quote_id',
                        'header'    => 'Quote UID',
                        'format' => 'html',
                        'value' => function (\common\models\QuotePrice $model) {
                            return $model->quote ? Html::a($model->quote->uid, ['quotes/view', 'id' => $model->quote_id], ['target' => '_blank']) . ' (id: ' . $model->quote_id . ')' : '-';
                        },
                    ],
                    [
                        'label' => 'Lead',
                        'format' => 'raw',
                        'value' => function (\common\models\QuotePrice $model) {
                            if (!$lead = $model->quote->lead ?? null) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return Yii::$app->formatter->asLead($lead);
                        },
                    ],
                    [
                        'attribute' => 'quote.status',
                        //'header'    => 'Quote Status',
                        'value' => function (\common\models\QuotePrice $model) {
                            return $model->quote ? $model->quote->getStatusName(true) : '-';
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'created',
                        'value' => function (\common\models\QuotePrice $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'updated',
                        'value' => function (\common\models\QuotePrice $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated));
                        },
                        'format' => 'html',
                    ],
                    [
                        'label' => 'Quote Currency',
                        'value' => function (\common\models\QuotePrice $model) {
                            if (!$currency = $model->quote->q_client_currency ?? null) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return $currency;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Quote Currency Rate',
                        'value' => function (\common\models\QuotePrice $model) {
                            if (!$rate = $model->quote->q_client_currency_rate ?? null) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return $rate;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Quote Check Payment',
                        'value' => function (\common\models\QuotePrice $model) {
                            if (!$checkPayment = $model->quote->check_payment ?? null) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return Yii::$app->formatter->asBooleanBySquare($checkPayment);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Quote ServiceFee %',
                        'value' => function (\common\models\QuotePrice $model) {
                            if (!$checkPayment = $model->quote->service_fee_percent) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return $model->quote->service_fee_percent . '%';
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'fare',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) use ($defaultCurrencyCode) {
                            if (empty($model->fare)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return $model->fare . ' ' . $defaultCurrencyCode;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'taxes',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) use ($defaultCurrencyCode) {
                            if (empty($model->taxes)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return $model->taxes . ' ' . $defaultCurrencyCode;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'net',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) use ($defaultCurrencyCode) {
                            if (empty($model->net)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return $model->net . ' ' . $defaultCurrencyCode;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'mark_up',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) use ($defaultCurrencyCode) {
                            if (empty($model->mark_up)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return $model->mark_up . ' ' . $defaultCurrencyCode;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'extra_mark_up',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) use ($defaultCurrencyCode) {
                            if (empty($model->extra_mark_up)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return $model->extra_mark_up . ' ' . $defaultCurrencyCode;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'service_fee',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) use ($defaultCurrencyCode) {
                            if (empty($model->service_fee)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return $model->service_fee . ' ' . $defaultCurrencyCode;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'selling',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) use ($defaultCurrencyCode) {
                            if (empty($model->selling)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return $model->selling . ' ' . $defaultCurrencyCode;
                        },
                        'format' => 'raw',
                    ],
                ]
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'qp_client_fare',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) {
                            if (empty($model->qp_client_fare)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            $clientCurrency = $model->quote->q_client_currency ?? '';
                            return $model->qp_client_fare . ' ' . $clientCurrency;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'qp_client_taxes',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) {
                            if (empty($model->qp_client_taxes)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            $clientCurrency = $model->quote->q_client_currency ?? '';
                            return $model->qp_client_taxes . ' ' . $clientCurrency;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'qp_client_net',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) {
                            if (empty($model->qp_client_net)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            $clientCurrency = $model->quote->q_client_currency ?? '';
                            return $model->qp_client_net . ' ' . $clientCurrency;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'qp_client_markup',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) {
                            if (empty($model->qp_client_markup)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            $clientCurrency = $model->quote->q_client_currency ?? '';
                            return $model->qp_client_markup . ' ' . $clientCurrency;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'qp_client_extra_mark_up',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) {
                            if (empty($model->qp_client_extra_mark_up)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            $clientCurrency = $model->quote->q_client_currency ?? '';
                            return $model->qp_client_extra_mark_up . ' ' . $clientCurrency;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'qp_client_service_fee',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) {
                            if (empty($model->qp_client_service_fee)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            $clientCurrency = $model->quote->q_client_currency ?? '';
                            return $model->qp_client_service_fee . ' ' . $clientCurrency;
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'qp_client_selling',
                        'contentOptions' => ['class' => 'text-right'],
                        'value' => static function (\common\models\QuotePrice $model) {
                            if (empty($model->qp_client_selling)) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            $clientCurrency = $model->quote->q_client_currency ?? '';
                            return $model->qp_client_selling . ' ' . $clientCurrency;
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
