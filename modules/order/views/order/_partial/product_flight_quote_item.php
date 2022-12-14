<?php

/**
 * @var $model ProductQuote
 */

use common\components\SearchService;
use frontend\helpers\QuoteHelper;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use src\auth\Auth;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

$flightQuote = FlightQuote::findByProductQuoteId($model);

$totalAmountQuote = 0.0;

?>

<?php Pjax::begin(['id' => 'pjax-product-quote-' . $model->pq_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <div class="x_panel" id="quote-<?=$model->pq_id?>">
        <div class="x_title">

            <span class="badge badge-white">Q<?=($model->pq_id)?></span>
            <?= ProductQuoteStatus::asFormat($model->pq_status_id) ?>


            <ul class="nav navbar-right panel_toolbox">
                <!--            <li>-->
                <!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
                <!--            </li>-->
                <!--<?php /*if (!$model->isDeclined()) : */?>
                    <li class="dropdown dropdown-offer-menu" data-product-quote-id="<?/*=($model->pq_id)*/?>" data-lead-id="<?/*=($model->pqProduct->pr_lead_id)*/?>" data-url="<?/*=\yii\helpers\Url::to(['/offer/offer/list-menu-ajax'])*/?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="far fa-handshake"></i> Offers</a>
                        <div class="dropdown-menu" role="menu">
                            <?php /*// ajax loaded content */?>
                        </div>
                    </li>

                    <li class="dropdown dropdown-order-menu" data-product-quote-id="<?/*=($model->pq_id)*/?>" data-lead-id="<?/*=($model->pqProduct->pr_lead_id)*/?>" data-url="<?/*=\yii\helpers\Url::to(['/order/order/list-menu-ajax'])*/?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fas fa-money-check-alt"></i> Orders</a>
                        <div class="dropdown-menu" role="menu">
                            <?php /*// ajax loaded content */?>
                        </div>
                    </li>
                <?php /*endif; */?>-->

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars text-warning"></i></a>
                    <div class="dropdown-menu" role="menu">
                        <h6 class="dropdown-header" title="FlightQuoteId: <?php echo $model->flightQuote->fq_id ?>">Quote Q<?=($model->pq_id)?></h6>

                        <!--<?/*= Html::a('<i class="fa fa-plus-circle"></i> Add option', null, [
                            'class' => 'dropdown-item text-success btn-add-product-quote-option',
                            //'data-product-quote-id' => $model->hq_product_quote_id,
                            'data-url' => \yii\helpers\Url::to(['/product/product-quote-option/create-ajax', 'id' => $model->pq_id]),
                            //'data-product-id' => $model->hqProductQuote->pq_product_id,
                        ]) */?>

                        <?/*= Html::a('<i class="fa fa-list"></i> Status log', null, [
                            'class' => 'dropdown-item text-secondary btn-product-quote-status-log',
                            'data-url' => \yii\helpers\Url::to(['/product/product-quote-status-log/show', 'gid' => $model->pq_gid]),
                            'data-gid' => $model->pq_gid,
                            'title' => 'View status log'
                        ]) */?>-->

                        <?php if ($flightQuote) : ?>
                            <?php
                            $tripsInfo = [];
                            $needRecheck = false;
                            $firstSegment = null;
                            $lastSegment = null;
                            ?>
                            <?php foreach ($flightQuote->flightQuoteTrips as $trip) :?>
                                <?php
                                $segments = $trip->flightQuoteSegments;
                                $segmentsCnt = count($segments);

                                if ($segments) {
                                    $stopCnt = $segmentsCnt - 1;
                                    $firstSegment = $segments[0];
                                    $lastSegment = $segments[$segmentsCnt - 1];
                                    //                  $lastSegment = end($segments);
                                    $cabins = [];
                                    $marketingAirlines = [];
                                    $airlineNames = [];
                                    foreach ($segments as $segment) {
                                        if (!in_array(SearchService::getCabin($segment->fqs_cabin_class), $cabins, false)) {
                                            $cabins[] = SearchService::getCabin($segment->fqs_cabin_class, $segment->fqs_cabin_class_basic);
                                        }
                                        if (isset($segment->fqs_recheck_baggage) && $segment->fqs_recheck_baggage) {
                                            $needRecheck = true;
                                        }
                                        if (isset($segment->fqs_stop) && $segment->fqs_stop > 0) {
                                            $stopCnt += $segment->fqs_stop;
                                        }
                                        if (!in_array($segment->fqs_marketing_airline, $marketingAirlines)) {
                                            $marketingAirlines[] = $segment->fqs_marketing_airline;

                                            $airlineNames[] = $segment->marketingAirline ? $segment->marketingAirline->name : $segment->fqs_marketing_airline;

                                            /*$airline = Airline::findIdentity($segment->qs_marketing_airline);
                                            if($airline){
                                                $airlineNames[] =  $airline->name;
                                            }else{
                                                $airlineNames[] = $segment->qs_marketing_airline;
                                            }*/
                                        }
                                    }

                                    $tripsInfo[] = $firstSegment->fqs_departure_airport_iata . ' ??? ' . $lastSegment->fqs_arrival_airport_iata;
                                }
                                ?>
                            <?php endforeach;?>


                            <?= Html::a('<i class="fa fa-search"></i> Details', null, [
                                'class' => 'btn-flight-quote-details dropdown-item',
                                'data-id' => $model->pq_id,
                                'data-title' => implode(', ', $tripsInfo),
                                'data-url' => Url::to(['/flight/flight-quote/ajax-quote-details', 'id' => $model->pq_id]),
                                //'data-target' => '#quote_detail_'.$model->uid,
                                'title' => 'Details'
                            ]) ?>


                            <!--<?/*= Html::a('<i class="fa fa-clone"></i> Clone', null, [
                                'class' => 'dropdown-item btn-clone-product-quote',
                                'data-product-quote-id' => $model->pq_id,
                                'data-flight-quote-id' => $model->flightQuote->fq_id,
                                'data-product-id' => $model->pq_product_id,
                                'title' => 'Clone quote'
                            ]) */?>

                            <?php /* echo Html::a('<i class="fa fa-eye"></i> Checkout Page', $model->getCheckoutUrlPage(), [
                                'class' => 'dropdown-item',
                                'target'    => '_blank',
                                'title'     => 'View checkout',
                                'data-pjax' => 0
                            ]);
                            */?>-->
                        <?php endif;?>

                        <!--<?php /*if (Auth::can('/flight/flight-quote/ajax-book') && $model->isBookable()) : */?>
                            <?/*= Html::a(
                                '<i class="fa fa-share-square"></i> Create Book',
                                null,
                                [
                                    'class' => 'dropdown-item js-btn-book-flight-quote',
                                    'data-url' => Url::to('/flight/flight-quote/ajax-book'),
                                    'data-flight-quote-id' => $model->flightQuote->fq_id,
                                    'data-product-id' => $model->pq_product_id,
                                ]
                            ) */?>
                        <?php /*endif */?>

                        <?php /*if (Auth::can('/flight/flight-quote/ajax-file-generate') && $model->isBooked()) : */?>
                            <?/*= Html::a(
                                '<i class="fa fa-file-pdf-o"></i> Generate PDF',
                                null,
                                [
                                    'class' => 'dropdown-item js-btn-generate-pdf-flight-quote',
                                    'data-url' => Url::to('/flight/flight-quote/ajax-file-generate'),
                                    'data-flight-quote-id' => $model->flightQuote->fq_id,
                                ]
                            ) */?>
                        <?php /*endif */?>

                        <?php /*if (Auth::can('/flight/flight-quote/cancel')) : */?>
                            <?/*= Html::a(
                                '<i class="fa fa-share-square"></i> Cancel Booking',
                                null,
                                [
                                    'class' => 'dropdown-item btn-flight-quote-cancel-book',
                                    'data-url' => Url::to('/flight/flight-quote/cancel'),
                                    'data-id' => $model->flightQuote->fq_id,
                                    'data-product-id' => $model->pq_product_id,
                                ]
                            ) */?>
                        <?php /*endif;*/?>

                        <?php /*if (Auth::can('/flight/flight-quote/void')) : */?>
                            <?/*= Html::a(
                                '<i class="fa fa-share-square"></i> Void Booking',
                                null,
                                [
                                    'class' => 'dropdown-item btn-flight-quote-void-book',
                                    'data-url' => Url::to('/flight/flight-quote/void'),
                                    'data-id' => $model->flightQuote->fq_id,
                                    'data-product-id' => $model->pq_product_id,
                                ]
                            ) */?>
                        <?php /*endif;*/?>

                        <?php /*if ($model->isDeletable()) : */?>
                            <div class="dropdown-divider"></div>
                            <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete quote', null, [
                                'class' => 'dropdown-item text-danger btn-delete-product-quote',
                                'data-product-quote-id' => $model->pq_id,
                                'data-flight-quote-id' => $model->flightQuote->fq_id,
                                'data-product-id' => $model->pq_product_id,
                            ]) */?>
                        <?php /*endif;*/?>-->

                    </div>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block">

            <?php /*= $this->render('../quotes/quote_list', [
            'dataProvider' => $quotesProvider,
            'lead' => $lead,
            'leadForm' => $leadForm,
            'is_manager' => $is_manager,
        ])*/ ?>
            <i class="fa fa-user"></i> <?=$model->pqOwnerUser ? Html::encode($model->pqOwnerUser->username) : '-'?>,
            <?php if ($flightQuote && $flightQuote->fq_created_expert_name) : ?>
                <i class="fa fa-user-secret"></i> <?= Html::encode($flightQuote->fq_created_expert_name)?>,
            <?php endif; ?>
            <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->pqProduct->pr_created_dt)) ?>,
            <?php /*<i title="code: <?=\yii\helpers\Html::encode($model->pq_gid)?>">GID: <?=\yii\helpers\Html::encode($model->pq_gid)?></i>*/ ?>



            <table class="table table-striped table-striped">
                <tr>
                    <?php if ($flightQuote->isAlternative()) : ?>
                        <td>
                            <?=$flightQuote->isAlternative() ? \yii\helpers\Html::tag('i', '', ['class' => 'fa fa-font', 'title' => 'Alternative quote']) : ''?>
                        </td>
                    <?php endif;?>


                    <?php if (ProductQuoteRelation::isClone($model->pq_id)) : ?>
                        <td>
                            <span class="badge badge-warning" style="padding-left: 5px">CLONE</span>
                        </td>
                    <?php elseif (ProductQuoteRelation::isReplace($model->pq_id)) : ?>
                        <td>
                            <span class="badge badge-warning" style="padding-left: 5px">REPLACE</span>
                        </td>
                    <?php endif;?>

                    <td>
                        <?php if ($mainAirlines = FlightQuoteHelper::getMainAirline($flightQuote)) : ?>
                            <?php if (count($mainAirlines) === 1) : ?>
                                <span class="quote__vc" title="Main Airline">
                                    <span class="quote__vc-logo">
                                        <img src="//www.gstatic.com/flights/airline_logos/70px/<?= $mainAirlines[0]['code'] ?>.png"
                                            alt="<?= $mainAirlines[0]['code'] ?>" class="quote__vc-img" />
                                    </span>
                                    <?= $mainAirlines[0]['name'] ?> &nbsp;[<strong><?= $mainAirlines[0]['code'] ?></strong>]
                                </span>
                            <?php else : ?>
                                <?php $dataHtml = ""; ?>
                                    <?php foreach ($mainAirlines as $mainAirline) : ?>
                                        <?php $dataHtml .= "<span class='quote__vc' title='Main Airline'>" ?>
                                        <?php $dataHtml .= "<span class='quote__vc-logo'>" ?>
                                        <?php $dataHtml .= "<img src='//www.gstatic.com/flights/airline_logos/70px/" . $mainAirline['code'] . ".png'
                                                alt=" . $mainAirline['code'] . " class='quote__vc-img' />" ?>
                                        <?php $dataHtml .= "</span>" ?>

                                        <?php $dataHtml .= $mainAirline['name'] . "&nbsp;[<strong>" . $mainAirline['code'] . "</strong>]" ?>
                                        <?php $dataHtml .= "</span>" ?>
                                    <?php endforeach ?>
                                <span class="light-tooltip-box">
                                    <b
                                        data-toggle="tooltip"
                                        data-type="light-tooltip-box"
                                        data-html="true"
                                        data-template='<div class="tooltip light-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                                        title="<?php echo $dataHtml ?>">Multiple</b>
                                </span>
                            <?php endif ?>
                        <?php endif ?>
                    </td>

                    <td>
                        <?php $ngsDto = FlightQuoteHelper::getNgsDtoOfSelectedQuote($flightQuote); ?>

                        <strong><?= FlightQuoteHelper::formattedNgs($ngsDto) ?></strong>
                        <?php /*
                        <div class="quote__gds" title="GDS / <?php if (!empty($flightQuote->fq_gds_offer_id)) :
                            echo 'GDS Offer ID: ' . \yii\helpers\Html::encode($flightQuote->fq_gds_offer_id) . ' /';
                                                             endif; ?> PCC">
                            <strong><?= SearchService::getGDSName($flightQuote->fq_gds)?></strong>
                            <?php if (!empty($flightQuote->fq_gds_offer_id)) : ?>
                                <i class="fas fa-passport success"></i>
                            <?php endif; ?>
                            / <i><?= Html::encode($flightQuote->fq_gds_pcc)?></i>
                        </div>
 */ ?>
                        <div>
                        </div>
                    </td>
                    <?php /*
                    <td>
                        <span title="<?= !$flightQuote->createdByExpert() ? 'Agent' : 'Expert'?>: <?= \yii\helpers\Html::encode($flightQuote->getEmployeeName())?>">
                            <?php echo !$flightQuote->createdByExpert() ? '<i class="fa fa-user text-info"></i>' : '<i class="fa fa-user-secret text-warning"></i>'; ?>
                            <strong><?= $flightQuote->getEmployeeName() ?></strong>
                        </span>
                    </td>*/ ?>
                    <?php Pjax::begin(['id' => 'pjax-quote_estimation_profit-' . $flightQuote->fq_id, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
                    <?php $priceData = FlightQuoteHelper::getPricesData($flightQuote); ?>
                    <?php /*if($model->isApplied() && $model->pqProduct->prLead->final_profit !== null): ?>
                            <td>
                                Agent Profit:
                                <button id="quote_profit_<?= $model->pq_id?>" data-toggle="popover" data-html="true" data-trigger="click" data-placement="top" data-container="body" title="Final Profit" class="popover-class quote__profit btn btn-info"
                                        data-content='<?= FlightQuoteHelper::getEstimationProfitText($priceData) ?>'>
                                    <?= '$'.FlightQuoteHelper::getFinalProfit($flightQuote) ?>
                                </button>
                            </td>
                            <?php else:?>
                            <td>
                                Agent Profit:
                                <a id="quote_profit_<?= $model->pq_id?>" data-toggle="popover" data-html="true" data-trigger="click" data-placement="top" data-container="body" title="Estimation Profit" class="popover-class quote__profit"
                                   data-content='<?= FlightQuoteHelper::getEstimationProfitText($priceData) ?>'>
                                    <?= FlightQuoteHelper::getEstimationProfit($priceData) ?>$
                                </a>
                            </td>
                            <?php endif;*/?>
                    <td>
                                <span class="<?=$model->pq_profit_amount < 0 ? 'danger' : ($model->pq_profit_amount > 0 ? 'success' : 'default') ?>" title="Profit amount: <?= number_format($model->pq_profit_amount, 2) ?> $" data-toggle="tooltip">
                                    <i class="fas fa-donate"></i> <?= number_format($model->pq_profit_amount, 2) ?>
                                </span>
                    </td>
                    <?php Pjax::end(); ?>

                    <td class="text-right">
                        <?php $baggageInfo = FlightQuoteHelper::getBaggageInfo($flightQuote); ?>
                        <?php $hasAirportChange = FlightQuoteHelper::hasAirportChange($flightQuote); ?>
                        <?php $ticketSegments = FlightQuoteHelper::getTicketSegments($flightQuote); ?>
                        <?php $meta = FlightQuoteHelper::getMetaInfo($flightQuote); ?>
                        <?php $penalties = FlightQuoteHelper::getPenaltiesInfo($flightQuote); ?>

                        <?php if ($ticketSegments) :?>
                            <span title="Separate Ticket (<?=count($ticketSegments)?>)" data-toggle="tooltip">
                                    <i class="fa fa-ticket fa-border text-info"> <?=count($ticketSegments)?></i>
                                </span>
                        <?php endif; ?>

                        <?php echo FlightQuoteHelper::formattedFreeBaggage($meta, 'success') ?>

                        <span class="<?=$baggageInfo['hasFreeBaggage'] ? ($baggageInfo['freeBaggageInfo'] ? 'success' : 'warning') : ''?>" data-toggle="tooltip"
                              title="<?= ($baggageInfo['freeBaggageInfo']) ? 'Free baggage - ' . $baggageInfo['freeBaggageInfo'] : 'No free baggage'?>"
                              data-original-title="<?= ($baggageInfo['freeBaggageInfo']) ? 'Free baggage - ' . $baggageInfo['freeBaggageInfo'] : 'No free baggage'?>">
                                    <i class="fa fa-suitcase fa-border"></i><span class="quote__badge-num"></span>
                            </span>

                        <?php if ($needRecheck) : ?>
                            <span class="<?=$needRecheck ? 'warning' : ''?>" data-toggle="tooltip"
                                  title="<?= ($needRecheck) ? 'Bag re-check may be required' : 'Bag re-check not required'?>"
                                  data-original-title="<?= ($needRecheck) ? 'Bag re-check may be required' : 'Bag re-check not required'?>">
                                      <i class="fas fa-warning fa-border"></i>
                                </span>
                        <?php endif; ?>


                        <?php if ($hasAirportChange) : ?>
                            <span class="<?=$hasAirportChange ? 'warning' : 'default'?>" data-toggle="tooltip"
                                  title="<?= ($hasAirportChange) ? 'Airports Change' : 'No Airports Change'?>"
                                  data-original-title="<?= ($hasAirportChange) ? 'Airports Change' : 'No Airports Change'?>">
                                        <i class="fa fa-exchange fa-border"></i>
                                </span>
                        <?php endif; ?>

                        <?php echo FlightQuoteHelper::formattedRanking($meta)?>
                        <?php echo FlightQuoteHelper::formattedCheapest($meta)?>
                        <?php echo FlightQuoteHelper::formattedFastest($meta)?>
                        <?php echo FlightQuoteHelper::formattedBest($meta)?>

                        <?php echo FlightQuoteHelper::formattedPenalties($penalties)?>

                    </td>

                </tr>

            </table>

            <?php if ($flightQuote) : ?>
                <div class="col-md-12">
                    <div class="quote__trip">

                        <?php foreach ($flightQuote->flightQuoteTrips as $trip) :?>
                            <?php

                            $segments = $trip->flightQuoteSegments;
                            if ($segments) {
                                $segmentsCnt = count($segments);
                                $stopCnt = $segmentsCnt - 1;
                                $firstSegment = $segments[0];
                                $lastSegment = $segments[$segmentsCnt - 1];
                                //                  $lastSegment = end($segments);
                                $cabins = [];
                                $marketingAirlines = [];
                                $airlineNames = [];
                                foreach ($segments as $segment) {
                                    if (!in_array(SearchService::getCabin($segment->fqs_cabin_class), $cabins, false)) {
                                        $cabins[] = SearchService::getCabin($segment->fqs_cabin_class, $segment->fqs_cabin_class_basic);
                                    }
                                    if (isset($segment->fqs_recheck_baggage) && $segment->fqs_recheck_baggage) {
                                        $needRecheck = true;
                                    }
                                    if (isset($segment->fqs_stop) && $segment->fqs_stop > 0) {
                                        $stopCnt += $segment->fqs_stop;
                                    }
                                    if (!in_array($segment->fqs_marketing_airline, $marketingAirlines)) {
                                        $marketingAirlines[] = $segment->fqs_marketing_airline;

                                        $airlineNames[] = $segment->marketingAirline ? $segment->marketingAirline->name : $segment->fqs_marketing_airline;

                                        /*$airline = Airline::findIdentity($segment->qs_marketing_airline);
                                        if($airline){
                                            $airlineNames[] =  $airline->name;
                                        }else{
                                            $airlineNames[] = $segment->qs_marketing_airline;
                                        }*/
                                    }
                                }
                            } else {
                                continue;
                            }
                            ?>
                            <div class="quote__segment">
                                <div class="quote__info">
                                    <?php if (count($marketingAirlines) == 1) :?>
                                        <img src="//www.gstatic.com/flights/airline_logos/70px/<?= $marketingAirlines[0]?>.png" alt="<?= $marketingAirlines[0]?>" class="quote__airline-logo">
                                    <?php else :?>
                                        <img src="/img/multiple_airlines.png" alt="<?= implode(', ', $marketingAirlines)?>" class="quote__airline-logo">
                                    <?php endif;?>
                                    <div class="quote__info-options">
                                        <div class="quote__duration"><?= SearchService::durationInMinutes($trip->fqt_duration)?></div>
                                        <div class="quote__airline-name"><?= implode(', ', $airlineNames);?></div>
                                    </div>
                                </div>
                                <div class="quote__itinerary">
                                    <div class="quote__itinerary-col quote__itinerary-col--from">
                                        <div class="quote__datetime">
                                            <span class="quote__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->fqs_departure_dt), 'h:mm a')?></span>
                                            <span class="quote__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->fqs_departure_dt), 'MMM d')?></span>
                                        </div>
                                        <div class="quote__location">
                                            <div class="quote__airport">
                                                <span class="quote__city"><?= ($firstSegment->fqs_departure_airport_iata) ? $firstSegment->departureAirport->city : $firstSegment->fqs_departure_airport_iata?></span>
                                                <span class="quote__iata"><?= $firstSegment->fqs_departure_airport_iata?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="quote__itinerary-col quote__itinerary-col--to">
                                        <div class="quote__datetime">
                                            <span class="quote__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->fqs_arrival_dt), 'h:mm a')?></span>
                                            <span class="quote__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->fqs_arrival_dt), 'MMM d')?></span>
                                        </div>
                                        <div class="quote__location">
                                            <div class="quote__airport">
                                                <span class="quote__city"><?= ($lastSegment->arrivalAirport) ? $lastSegment->arrivalAirport->city : $lastSegment->fqs_arrival_airport_iata?></span>
                                                <span class="quote__iata"><?= $lastSegment->fqs_arrival_airport_iata?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="quote__additional-info">
                                    <div class="quote__stops">
                                        <span class="quote__stop-quantity"><?= \Yii::t('search', '{n, plural, =0{Nonstop} one{# stop} other{# stops}}', ['n' => $stopCnt]);?></span>
                                    </div>
                                    <div class="quote__cabin"><?= implode(', ', $cabins)?></div>
                                </div>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>

                <?php Pjax::begin(['id' => 'pjax-quote_prices-' . $flightQuote->fq_id, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
                <?= $this->render('flight_quote_item_prices', [
                    'quote' => $model,
                    'flightQuote' => $flightQuote,
                    'priceData' => $priceData
                ]); ?>
                <?php Pjax::end(); ?>


            <?php else : ?>
                <div class="d-flex justify-content-center align-items-center">
                    <p style="margin: 20px 0;">Not found quote data</p>
                </div>
            <?php endif; ?>

            <?= $this->render('@frontend/views/lead/quotes/partial/_quote_option_list', ['productQuote' => $model]) ?>

            <?= $this->render('@frontend/views/lead/quotes/partial/_quote_total', ['productQuote' => $model]) ?>

        </div>
    </div>
<?php Pjax::end(); ?>

<?php
$css = <<<CSS
    .tooltip.light-tooltip .tooltip-inner {
      background-color: #e8e8e8 !important;
      color: #596b7d !important;
    }
    .tooltip.light-tooltip .arrow::before, .bs-tooltip-auto[x-placement^="top"] .arrow::before {
      border-top-color: #e8e8e8 !important;
    }
CSS;
$this->registerCss($css);

$js = <<<JS
    $(document).on('pjax:end', function() {
         $('[data-toggle="tooltip"]').tooltip({html:true});
    });

    $('[data-toggle="tooltip"]').tooltip({html:true});
JS;
$this->registerJs($js, View::POS_READY);

