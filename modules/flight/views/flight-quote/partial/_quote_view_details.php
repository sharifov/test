<?php

use common\components\SearchService;
use common\models\Airline;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteData\ProductQuoteDataKey;
use yii\helpers\Html;
use src\helpers\product\ProductQuoteHelper;

/* @var $this yii\web\View */
/* @var $productQuote ProductQuote*/
/* @var $flightQuote FlightQuote*/
?>

<div class="row">
    <?php if ($flightQuote) : ?>
    <div class="col-md-12">
        <h2><i class="fa fa-info-circle"></i> Product Quote Info:</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <tr>
                    <th style="width: 50px">Id</th>
                    <th>Exp.</th>
                    <th>Status</th>
                    <th>Service Fee, USD</th>
                    <th>Markup, USD</th>
                    <th>Agent Markup, USD</th>
                    <th>Price, USD</th>
                    <th>Client Price, <?= Html::encode($productQuote->pq_client_currency) ?></th>
                </tr>
                <tr>
                    <td class="text-center"><?= $productQuote->pq_id ?></td>
                    <td class="text-center <?= (ProductQuoteHelper::checkingExpirationDate($productQuote) ? 'success' : 'danger') ?>">
                        <?= Yii::$app->formatter->asDatetime($productQuote->pq_expiration_dt) ?>
                    </td>
                    <td class="text-center"><?= \modules\product\src\entities\productQuote\ProductQuoteStatus::asFormat($productQuote->pq_status_id) ?></td>
                    <td class="text-right" title="Service fee percent: <?= $productQuote->pq_service_fee_percent ?> %">
                        $<?= number_format($productQuote->pq_service_fee_sum, 2) ?>
                    </td>
                    <td class="text-right">$<?= number_format($productQuote->pq_app_markup, 2) ?></td>
                    <td class="text-right">$<?= number_format($productQuote->pq_agent_markup, 2) ?></td>
                    <td class="text-right">$<?= number_format($productQuote->pq_price, 2) ?></td>
                    <td class="text-right"><?= number_format($productQuote->pq_client_price, 2) ?></td>
                </tr>
            </table>
        </div>
        <p>
            <small>
                <span title="Client Currency">
                    Currency: 1 <i class="fa fa-usd"></i> = <?= $productQuote->pq_client_currency_rate ?> <?= Html::encode($productQuote->pq_client_currency) ?>
                </span>
                /
                <span title="Created Date Time">
                    <i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(strtotime($productQuote->pq_created_dt)) ?>
                </span>
            </small>
        </p>

        <h2><i class="fa fa-male"></i> Flight Quote Pax Prices:</h2>
        <?php if ($flightQuotePaxPrices = $flightQuote->flightQuotePaxPrices) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                <tr>
                    <th style="width: 50px">Nr</th>
                    <th>Pax Type</th>
                    <th style="width: 50px">Count</th>
                    <th>Fare, USD</th>
                    <th>Taxes, USD</th>
                    <th>Client Fare, <?= Html::encode($productQuote->pq_client_currency) ?></th>
                    <th>Client Taxes, <?= Html::encode($productQuote->pq_client_currency) ?></th>
                    <th title="System Markup">Processing Fee, USD</th>
                    <th title="Extra Markup">Agent Markup, USD</th>
                    <th>Total, USD</th>
                    <th>Client Total, <?= Html::encode($productQuote->pq_client_currency) ?></th>
                </tr>
                <?php foreach ($flightQuotePaxPrices as $nr => $flightQuotePaxPrice) : ?>
                    <tr>
                        <td><?= ($nr + 1) ?>.</td>
                        <td>
                            <span class="badge badge-info">
                                <?php echo FlightPax::getPaxTypeById($flightQuotePaxPrice->qpp_flight_pax_code_id) ?>
                            </span>
                        </td>
                        <td class="text-center"><?php echo $flightQuotePaxPrice->qpp_cnt ?></td>
                        <td class="text-right">$<?= $flightQuotePaxPrice->qpp_fare ?></td>
                        <td class="text-right">$<?= $flightQuotePaxPrice->qpp_tax ?></td>
                        <td class="text-right"><?= $flightQuotePaxPrice->qpp_client_fare ?></td>
                        <td class="text-right"><?= $flightQuotePaxPrice->qpp_client_tax ?></td>
                        <td class="text-right">$<?php echo $flightQuotePaxPrice->qpp_system_mark_up ?></td>
                        <td class="text-right">$<?php echo $flightQuotePaxPrice->qpp_agent_mark_up ?></td>
                        <td class="text-right" title="Fare + Tax + ProcessingFee + Agent Markup">
                            $<?= number_format($flightQuotePaxPrice->getTotalPrice(), 2) ?>
                        </td>
                        <td class="text-right" title="Client Fare + Client Tax + (ProcessingFee + Agent Markup) * Client Currency Rate">
                            <?= number_format($flightQuotePaxPrice->getClientTotalPrice(), 2) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
            </div>
        <?php else : ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Flight Quote Pax Prices</strong> data is empty.
            </div>
        <?php endif ?>

        <h2><i class="fa fa-cubes"></i> Product Quote Data:</h2>
        <?php if ($pqDatas = $productQuote->productQuoteData) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th style="width: 50px">Nr</th>
                        <th>Name</th>
                        <th>Value</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                    <?php foreach ($pqDatas as $nr => $pqData) : ?>
                        <tr>
                            <td><?= ($nr + 1) ?>.</td>
                            <td title="Name key ID: <?= Html::encode($pqData->pqd_key) ?>">
                                <span class="badge badge-primary"><?= Html::encode(ProductQuoteDataKey::getList()[$pqData->pqd_key]) ?></span>
                            </td>
                            <td class="text-center"><?= Html::encode($pqData->pqd_value) ?></td>
                            <td class="text-center"><?= $pqData->pqd_created_dt ?></td>
                            <td class="text-center"><?= $pqData->pqd_updated_dt ?></td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>
        <?php else : ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Product Quote Data</strong> is empty.
            </div>
        <?php endif ?>

        <h2><i class="fa fa-cubes"></i> Product Quote Options:</h2>
        <?php if ($pqOptions = $productQuote->productQuoteOptions) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                <tr>
                    <th style="width: 50px">Nr</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Price, USD</th>
                    <th>Client Price</th>
                    <th>Extra Markup, USD</th>
                    <th title="Request Data">Data</th>
                </tr>
                <?php foreach ($pqOptions as $nr => $pqOption) : ?>
                    <tr>
                        <td><?= ($nr + 1) ?>.</td>
                        <td title="Id: <?= Html::encode($pqOption->pqo_id) ?>">
                            <span class="badge badge-primary"><?= Html::encode($pqOption->pqo_name) ?></span>
                        </td>
                        <td class="text-center"><?= Html::encode($pqOption->getStatusName()) ?></td>
                        <td class="text-right">$<?= $pqOption->pqo_price ?></td>
                        <td class="text-right"><?= $pqOption->pqo_client_price ?> </td>
                        <td class="text-right">$<?= $pqOption->pqo_extra_markup ?></td>
                        <td class="text-center" title="<?= $pqOption->pqo_request_data ?
                            \yii\helpers\VarDumper::dumpAsString(json_decode($pqOption->pqo_request_data, true)) : '' ?>">
                            <?= $pqOption->pqo_request_data ? '<i class="fa fa-info-circle" data-toggle="tooltip"></i> Data' : '' ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
            </div>
        <?php else : ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Product Quote Options</strong> data is empty.
            </div>
        <?php endif ?>
    </div>
    <div class="col-md-12">
        <h2><i class="fa fa-plane"></i> Flight Trips:</h2>
        <?php foreach ($flightQuote->flightQuoteTrips as $tripKey => $trip) :?>
                <?php $segments = $trip->flightQuoteSegments;?>
                <?php if ($segments) : ?>
                <div>
                    <div class="trip__subtitle">
                        <span>
                            <?php if ($flightQuote->fqFlight->fl_trip_type_id !== Flight::TRIP_TYPE_MULTI_DESTINATION) : ?>
                                <?php if (count($flightQuote->flightQuoteTrips) < 3 && $tripKey == 0) : ?>
                                    Depart
                                <?php elseif (count($flightQuote->flightQuoteTrips) < 3 && $tripKey > 0) : ?>
                                    Return
                                <?php else : ?>
                                    <?= ($tripKey + 1);?> Trip
                                <?php endif ?>
                            <?php else : ?>
                                <?= ($tripKey + 1);?> Trip
                            <?php endif ?>
                        </span>
                        -
                        <span>
                            <?= Yii::$app->formatter_search->asDatetime(strtotime($segments[0]->fqs_departure_dt), 'EEE, d MMM YYYY')?>
                        </span>
                    </div>
                    <div class="trip__card">
                        <div class="trip__details trip-detailed">
                            <?php foreach ($segments as $key => $segment) :?>
                                <?php if ($key > 0) :?>
                                    <?php $prevSegment = $segments[$key - 1];?>
                                    <div class="trip-detailed__layover">
                                        <span class="trip-detailed__layover-location">
                                            Layover in <?= Html::encode($segment->getDepartureAirportCity())?>
                                            (<?= Html::encode($segment->fqs_departure_airport_iata)?>)
                                        </span>
                                        <span class="trip-detailed__layover-duration">
                                            <?= SearchService::getLayoverDuration($prevSegment->fqs_arrival_dt, $segment->fqs_departure_dt)?>
                                        </span>
                                    </div>
                                <?php endif;?>
                                <div
                                    class="trip-detailed__segment segment"
                                    style="background-color: <?=$segment->getTicketColor()?>"
                                    data-id="<?php echo $segment->fqs_id?>"
                                    data-iata="<?php echo $segment->fqs_departure_airport_iata . ':' . $segment->fqs_arrival_airport_iata ?>">
                                    <div class="segment__wrapper">
                                        <div class="segment__options">
                                            <?= Html::img($segment->getAirlineLogoImg(), ['alt' => $segment->fqs_marketing_airline, 'class' => 'segment__airline-logo']) ?>

                                            <div class="segment__cabin-xs">
                                                <?= SearchService::getCabin($segment->fqs_cabin_class, $segment->fqs_cabin_class_basic)?>
                                            </div>
                                            <div>
                                                <?= Html::encode($segment->getMarketingAirlineName()) ?>
                                            </div>
                                            <div class="segment__flight-nr">
                                                Flight <?= Html::encode($segment->fqs_marketing_airline)?>
                                                <?= Html::encode($segment->fqs_flight_number) ?>
                                            </div>
                                        </div>

                                        <div class="segment__location segment__location--from">
                                            <span class="segment__time">
                                                <?= Yii::$app->formatter_search->asDatetime(strtotime($segment->fqs_departure_dt), 'h:mm a')?>
                                            </span>
                                            <span class="segment__date">
                                                <?= Yii::$app->formatter_search->asDatetime(strtotime($segment->fqs_departure_dt), 'EEEE, MMM d')?>
                                            </span>
                                            <span class="segment__airport">
                                                <?= Html::encode($segment->getDepartureAirportName()) ?>
                                                (<?= Html::encode($segment->fqs_departure_airport_iata)?>)
                                            </span>

                                        </div>

                                        <div class="segment__location segment__location--to">
                                            <span class="segment__time">
                                                <?= Yii::$app->formatter_search->asDatetime(strtotime($segment->fqs_arrival_dt), 'h:mm a')?>
                                            </span>
                                            <span class="segment__date">
                                                <?= Yii::$app->formatter_search->asDatetime(strtotime($segment->fqs_arrival_dt), 'EEEE, MMM d')?>
                                            </span>
                                            <span class="segment__airport">
                                                <?= Html::encode($segment->getArrivalAirportName()) ?>
                                                (<?= Html::encode($segment->fqs_arrival_airport_iata)?>)
                                            </span>
                                        </div>

                                        <div class="segment__duration-wrapper">
                                            <div>
                                                <b>
                                                    <?= SearchService::durationInMinutes($segment->fqs_duration)?>
                                                </b>
                                            </div>
                                            <div>
                                                <?= SearchService::getCabin($segment->fqs_cabin_class, $segment->fqs_cabin_class_basic)?>
                                            </div>
                                            <?php if ($segment->fqs_ticket_id) :?>
                                                <div class="warning" title="Ticket <?=$segment->fqs_ticket_id?>">
                                                    <span class="fa fa-ticket"></span> Tick <?=$segment->fqs_ticket_id?>
                                                </div>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                    <div class="segment__note">
                                        <?php if ($segment->fqs_operating_airline !== $segment->fqs_marketing_airline) :?>
                                            Operated by <?= Html::encode($segment->getOperatingAirlineName()) ?>
                                        <?php endif; ?>

                                        <?php if (!empty($segment->flightQuoteSegmentPaxBaggages)) :?>
                                            <?php foreach ($segment->flightQuoteSegmentPaxBaggages as $baggage) :?>
                                                <span class="badge badge-primary"><i class="fa fa-suitcase"></i>&nbsp;
                                                <?php if (isset($baggage->qsb_allow_pieces)) :?>
                                                    <?= \Yii::t('search', '{n, plural, =0{no baggage} one{# piece} other{# pieces}}', ['n' => $baggage->qsb_allow_pieces]);?>
                                                <?php elseif (isset($baggage->qsb_allow_weight)) :?>
                                                    <?= $baggage->qsb_allow_weight . $baggage->qsb_allow_unit?>
                                                <?php endif;?>
                                                </span>

                                                <?php if (isset($baggage->qsb_carry_one)) :?>
                                                    <?php if ((bool) $baggage->qsb_carry_one === false) :?>
                                                        <span class="fa-stack " title="CarryOn Disable">
                                                            <i class="fa fa-shopping-bag fa-stack-1x"></i>
                                                            <i class="fa fa-ban fa-stack-2x text-danger"></i>
                                                        </span>
                                                    <?php endif ?>
                                                <?php endif ?>

                                                <?php break;
                                            endforeach;?>

                                        <?php endif;?>
                                        <?php if (!empty($segment->flightQuoteSegmentPaxBaggageCharges)) :?>
                                            <?php $paxCode = null;?>
                                            <?php foreach ($segment->flightQuoteSegmentPaxBaggageCharges as $baggageCh) :?>
                                                <?php if ($paxCode == null) {
                                                    $paxCode = $baggageCh->qsbc_flight_pax_code_id;
                                                } elseif ($paxCode != $baggageCh->qsbc_flight_pax_code_id) {
                                                    break;
                                                }
                                                ?>
                                                <span title="<?= (isset($baggageCh->qsbc_max_size) ? $baggageCh->qsbc_max_size : '') . ' ' . (isset($baggageCh->qsbc_max_weight) ? $baggageCh->qsbc_max_weight : '')?>"
                                                      class="badge badge-light"><i class="fa fa-plus"></i>&nbsp;<i class="fa fa-suitcase"></i>&nbsp;<?= $baggageCh->qsbc_price?>$</span>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                        <?php if (isset($segment->fqs_meal)) :
                                            ?><span class="badge badge-light" title="<?= $segment->fqs_meal?>"><i class="fa fa-cutlery"></i></span><?php
                                        endif;?>
                                        <?php if ($segment->fqs_recheck_baggage == true && $segment->fqs_recheck_baggage !== null) :
                                            ?> <h5 class="danger"><i class="fa fa-warning"></i> Bag re-check may be required</h5> <?php
                                        endif;?>
                                        <?php if (isset($segment->fqs_stop) && $segment->fqs_stop > 0) :?>
                                            <h5 class="danger">
                                                <i class="fa fa-warning"></i> <?= \Yii::t('search', '{n, plural, =0{no technical stops} one{# technical stop} other{# technical stops}}', ['n' => $segment->fqs_stop])?>
                                            </h5>

                                            <table class="table table-bordered table-striped">
                                                <?php if ($segment->flightQuoteSegmentStops) : ?>
                                                    <tr>
                                                        <th>Location</th>
                                                        <th>Departure DateTime</th>
                                                        <th>Arrival DateTime</th>
                                                        <th>Duration</th>
                                                        <th>Elapsed Time</th>
                                                        <th>Equipment</th>
                                                    </tr>
                                                    <?php foreach ($segment->flightQuoteSegmentStops as $stop) :?>
                                                        <tr>
                                                            <td><?=$stop->locationAirport ? Html::encode('(' . $stop->locationAirport->iata . ') ' . $stop->locationAirport->city . ', ' . $stop->locationAirport->country) : ($stop->qss_location_iata ?? '-')?></td>
                                                            <td><?=$stop->qss_departure_dt ? Yii::$app->formatter_search->asDatetime(strtotime($stop->qss_departure_dt), 'EE, MMM d, h:mm a') : '-'?></td>
                                                            <td><?=$stop->qss_arrival_dt ? Yii::$app->formatter_search->asDatetime(strtotime($stop->qss_arrival_dt), 'EE, MMM d, h:mm a') : '-'?></td>
                                                            <td><?=$stop->qss_duration ? SearchService::durationInMinutes($stop->qss_duration) : '-'?></td>
                                                            <td><?=$stop->qss_elapsed_time ? SearchService::durationInMinutes($stop->qss_elapsed_time) : '-'?></td>
                                                            <td><?=$stop->qss_equipment ? Html::encode($stop->qss_equipment) : '-'?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif;?>
                                            </table>

                                        <?php endif;?>
                                    </div>
                                </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
        <?php endforeach;?>
    </div>
    <?php else : ?>
        <p>Not found quote details</p>
    <?php endif; ?>
</div>
