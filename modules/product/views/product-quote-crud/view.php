<?php

use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\helpers\product\ProductQuoteHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ProductQuote */

$this->title = 'Product Quote: ' . $model->pq_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->pq_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->pq_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Status Log', ['/product/product-quote-status-log-crud/index', 'ProductQuoteStatusLogCrudSearch[pqsl_product_quote_id]' => $model->pq_id], ['class' => 'btn btn-warning']) ?>
    </p>

    <div class="row">
        <div class="col-md-3">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'pq_id',
                    'pq_gid',
                    'clone:productQuote',
                    'pq_name',
                    'pqProduct:product',
                    'pq_order_id',
                    'pq_description:ntext',
                    'pq_status_id:productQuoteStatus',
                    'pqOwnerUser:userName',
                    'pqCreatedUser:userName',
                    'pqUpdatedUser:userName',
                    'pq_created_dt:byUserDateTime',
                    'pq_updated_dt:byUserDateTime',
                ],
            ]) ?>
        </div>
        <div class="col-md-9">

            <?php if ($model->flightQuote && $model->flightQuote->flightQuotePaxPrices): ?>
            <div>
                <label for="" class="control-label">Flight Quote Pax Price</label>
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => (new \yii\data\ArrayDataProvider([
                        'models' => $model->flightQuote->flightQuotePaxPrices,
                        'pagination' => false
                    ])),
                    'columns' => [
                        'qpp_id',
                        [
                            'label' => 'Pax Code',
                            'value' => static function (FlightQuotePaxPrice $flightQuotePaxPrice) {
                                return FlightPax::getPaxTypeById($flightQuotePaxPrice->qpp_flight_pax_code_id);
                            },
						],
                        'qpp_cnt:ntext:X',
                        'qpp_origin_fare',
                        'qpp_origin_tax',
                        [
                            'label' => 'System Rate',
                            'value' => static function (FlightQuotePaxPrice $flightQuotePaxPrice) use ($model) {
                                return $model->pq_origin_currency . ' - ' .$model->pq_origin_currency_rate;
                            }
                        ],
                        'qpp_fare',
                        'qpp_tax',
                        [
                            'label' => 'Net = Fare + Tax',
                            'value' => static function (FlightQuotePaxPrice $flightQuotePaxPrice) {
                                return ProductQuoteHelper::roundPrice($flightQuotePaxPrice->qpp_fare + $flightQuotePaxPrice->qpp_tax, 2);
                            }
                        ],
                        'qpp_system_mark_up:ntext:MK',
                        'qpp_agent_mark_up:ntext:ExMK',
                        [
                            'label' => 'SFP %',
                            'value' => static function (FlightQuotePaxPrice $flightQuotePaxPrice) {
                                return $flightQuotePaxPrice->qppFlightQuote->fq_service_fee_percent . ' %';
                            }
                        ],
                        [
                            'label' => 'SFA',
                            'value' => static function (FlightQuotePaxPrice $flightQuotePaxPrice) use ($model) {
								$net = $flightQuotePaxPrice->qpp_fare + $flightQuotePaxPrice->qpp_tax;
								$selling = ($net + $flightQuotePaxPrice->qpp_system_mark_up + $flightQuotePaxPrice->qpp_agent_mark_up);
								return number_format($selling * $flightQuotePaxPrice->qppFlightQuote->fq_service_fee_percent / 100, 2);
                            }
                        ],
                        [
                            'label' => 'SSP = (Net + MK + ExMK) + SFP',
                            'value' => static function (FlightQuotePaxPrice $flightQuotePaxPrice) {
                                $net = $flightQuotePaxPrice->qpp_fare + $flightQuotePaxPrice->qpp_tax;
                                $selling = ($net + $flightQuotePaxPrice->qpp_system_mark_up + $flightQuotePaxPrice->qpp_agent_mark_up);
                                $sfp = $selling * $flightQuotePaxPrice->qppFlightQuote->fq_service_fee_percent / 100;
                                return ProductQuoteHelper::roundPrice($selling + $sfp);
                            }
                        ],
                        [
                            'label' => 'Client Rate',
                            'value' => static function (FlightQuotePaxPrice $flightQuotePaxPrice) use ($model) {
                                return $model->pq_client_currency . ' - ' . $model->pq_client_currency_rate;
                            }
                        ],
                        [
                            'label' => 'Client Price = SSP * CR',
                            'value' => static function (FlightQuotePaxPrice $flightQuotePaxPrice) use ($model){
                                $net = $flightQuotePaxPrice->qpp_fare + $flightQuotePaxPrice->qpp_tax;
                                $selling = ($net + $flightQuotePaxPrice->qpp_system_mark_up + $flightQuotePaxPrice->qpp_agent_mark_up);
                                $sfp = $selling * $flightQuotePaxPrice->qppFlightQuote->fq_service_fee_percent / 100;
                                return number_format( ($sfp + $selling) * $model->pq_client_currency_rate, 2);
                            }
                        ],
                    ],
                ]) ?>
            </div>
            <?php endif; ?>
            <div>
				<?= DetailView::widget([
					'model' => $model,
					'attributes' => [
						'pq_origin_price:ntext:Origin Price = Origin Fare + Origin Tax',
						'pq_service_fee_sum:ntext:Service Fee Sum = SFA SUM',
						'pq_price:ntext:Price = SSP SUM',
						'pq_client_price:ntext:Client Price = Price * Client Currency Rate',
						'pq_origin_currency',
						'pq_client_currency',
						'pq_origin_currency_rate',
						'pq_client_currency_rate',
						'pq_profit_amount',
					],
				]) ?>
            </div>
        </div>
    </div>

</div>
