<?php

use common\components\SearchService;
use common\models\Lead;
use common\models\Quote;
use frontend\helpers\QuoteHelper;
use modules\flight\src\helpers\FlightQuoteHelper;
use src\auth\Auth;
use src\helpers\quote\ImageHelper;
use src\model\flightQuoteLabelList\entity\FlightQuoteLabelList;
use src\repositories\quote\QuotePriceRepository;
use src\services\CurrencyHelper;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var $resultKey int
 * @var $result []
 * @var $airlines []
 * @var $locations []
 * @var $keyCache string
 * @var Lead $lead
 * @var int $chatId
 */

$user = Yii::$app->user->identity;
$showGdsOfferId = ($user->isAdmin() || $user->isSuperAdmin() || $user->isQa());

$leadQuotes = $lead->quotes;
$flightQuotes = [];
foreach ($leadQuotes as $quote) {
    $key = json_decode($quote->origin_search_data, true)['key'] ?? '';
    $flightQuotes[$key] = $quote;
}
?>
<?php

$totalDuration = $result['duration'];
$stops = $result['stops'];
$time = $result['time'];
$airportChange = $result['airportChange'];
$technicalStopCnt = $result['technicalStopCnt'];
$bagFilter = $result['bagFilter'];

$isQuoteAssignedToFlight = array_key_exists($result['key'], $flightQuotes);

$urlCreateQuoteFromSearch = Url::to(['client-chat-flight-quote/create-quote-from-search', 'leadId' => $lead->id]);
$urlSendQuoteFromSearch = Url::to(['client-chat-flight-quote/send-quote-from-search', 'leadId' => $lead->id]);

$keyId = md5($result['key']);

$currencySymbol = CurrencyHelper::getSymbolByCode($result['currency'] ?? null);
?>
<div
    class="quote search-result__quote <?= !$isQuoteAssignedToFlight ?: 'quote--selected' ?>"
    data-price="<?= $result['price']?>"
    data-durationmax="<?= max($totalDuration)?>"
    data-duration="<?= json_encode($totalDuration)?>"
    data-stop="<?= json_encode($stops)?>"
    data-time='<?= json_encode($time)?>'
    data-fareType="<?= (isset($result['fareType'])) ? $result['fareType'] : ''?>"
    data-airline="<?= $result['validatingCarrier']?>"
    id="search-result__quote-<?= $resultKey?>"
    data-changeairport="<?= $airportChange ?>" data-baggage="<?= isset($bagFilter) ? $bagFilter : '' ?>"
    style="z-index: inherit;"
    >
    <div class="quote__heading">
        <div class="quote__heading-left">
            <span class="quote__id"><strong># <?= $resultKey + 1 ?></strong></span>
            <span class="quote__vc">
                <span class="quote__vc-logo">
                    <?php $airlineLogo = '//www.gstatic.com/flights/airline_logos/70px/' . $result['validatingCarrier'] . '.png' ?>
                    <?php if (ImageHelper::checkImageGstaticExist($airlineLogo)) : ?>
                        <span class="quote__vc-logo">
                            <img src="<?php echo $airlineLogo ?>" alt="<?= $result['validatingCarrier']?>" class="quote__vc-img">
                        </span>
                    <?php endif ?>
                </span>
                <span class="quote__vc-name"><?= (!isset($airlines[$result['validatingCarrier']])) ?: $airlines[$result['validatingCarrier']];?><strong> [<?= $result['validatingCarrier']?>]</strong></span>
            </span>
            <?php /* ?>
            <div class="quote__gds">
                GDS: <strong><?= SearchService::getGDSName($result['gds'])?></strong>
            </div>
            <div class="quote__pcc">
                PCC: <strong><?= $result['pcc']?></strong>
            </div>
            <?php */ ?>
            <div class="quote__seats">
                Seats left: <strong class="text-danger"><i class="fa fa-fire"></i> <?= $result['maxSeats']?></strong>
            </div>
            <?php if ($showGdsOfferId && !empty($result['gdsOfferId'])) : ?>
                <div class="quote__seats">
                    <strong class="text-success" data-toggle="tooltip" title="GDS Offer ID <?= \yii\helpers\Html::encode($result['gdsOfferId']) ?>"><i class="fas fa-passport"></i></strong>
                </div>
            <?php endif; ?>

            <?php if (!empty($result['tickets'])) :?>
                <div class="quote__seats">
                    <span class="fa fa-ticket warning"></span> Separate Ticket (<?=count($result['tickets'])?>)
                </div>
            <?php endif;?>

            <?php if ($technicalStopCnt) :?>
                <div class="quote__seats" title="Technical Stops">
                    <span class="fa fa-warning danger"></span>Tech Stops (<?= $technicalStopCnt?>)
                </div>
            <?php endif;?>

            <?php if ($prodTypes = ArrayHelper::getValue($result, 'meta.prod_types')) :?>
                <div class="quote__seats">
                    <?php if (is_array($prodTypes)) : ?>
                        <?php foreach ($prodTypes as $label) : ?>
                            <span class="fa fa-tags text-success" title="<?php echo Html::encode($label) ?>"></span> <?php echo FlightQuoteLabelList::getDescriptionByKey($label) ?>
                        <?php endforeach ?>
                    <?php else : ?>
                        <span class="fa fa-tags text-success" title="<?php echo Html::encode($prodTypes) ?>"></span> <?php echo FlightQuoteLabelList::getDescriptionByKey($prodTypes) ?>
                    <?php endif ?>
                </div>
            <?php endif;?>

        </div>
        <div class="quote__heading-right text-success">
            <strong class="quote__quote-price">
                <?php echo $currencySymbol ?> <?= $result['price'] ?>
            </strong>
        </div>
    </div>
    <div class="quote_search_wrapper">
        <div class="quote__trip">
            <?php $tripsInfo = [];
            $hasAirportChange = false;?>
            <?php foreach ($result['trips'] as $trip) :?>
                <?php
                $segmentsCnt = count($trip['segments']);
                $stopCnt = $segmentsCnt - 1;
                $firstSegment = $trip['segments'][0];
                $lastSegment = $trip['segments'][$segmentsCnt - 1];
                $tripsInfo[] = ((!isset($locations[$firstSegment['departureAirportCode']])) ?: $locations[$firstSegment['departureAirportCode']]['city']) . ' ??? ' . ((!isset($locations[$lastSegment['arrivalAirportCode']])) ?: $locations[$lastSegment['arrivalAirportCode']]['city']);
                $cabins = [];
                $hasFreeBaggage = false;
                $freeBaggageInfo = '';
                $previousSegment = null;
                $marketingAirlines = [];
                $airlineNames = [];
                $needRecheck = false;
                foreach ($trip['segments'] as $segment) {
                    if (!in_array(SearchService::getCabin($segment['cabin']), $cabins)) {
                        $cabins[] = SearchService::getCabin($segment['cabin'], !empty($segment['cabinIsBasic']));
                    }

                    if (isset($segment['recheckBaggage']) && $segment['recheckBaggage'] == true) {
                        $needRecheck = true;
                    }

                    if (isset($segment['stop']) && $segment['stop'] > 0) {
                        $stopCnt += $segment['stop'];
                    }
                    if ($hasFreeBaggage === false && isset($segment['baggage'])) {
                        foreach ($segment['baggage'] as $baggage) {
                            if (isset($baggage['allowPieces']) && $baggage['allowPieces'] > 0) {
                                $freeBaggageInfo = 'Free baggage - ' . $baggage['allowPieces'] . 'pcs';
                            } elseif (isset($baggage['allowWeight'])) {
                                $freeBaggageInfo = 'Free baggage - ' . $baggage['allowWeight'] . $baggage['allowUnit'];
                            }
                            if (!empty($freeBaggageInfo)) {
                                $hasFreeBaggage = true;
                            }
                        }
                    }
                    if ($previousSegment !== null && $segment['departureAirportCode'] !== $previousSegment['arrivalAirportCode']) {
                        $hasAirportChange = true;
                    }
                    if (!in_array($segment['marketingAirline'], $marketingAirlines)) {
                        $marketingAirlines[] = $segment['marketingAirline'];
                        if (isset($airlines[$segment['marketingAirline']])) {
                            $airlineNames[] =  $airlines[$segment['marketingAirline']];
                        }
                    }
                    $previousSegment = $segment;
                }
                ?>
                <div class="quote__segment">
                    <div class="quote__info">
                        <?php if (count($marketingAirlines) === 1) :?>
                            <?php if ($marketingAirlines[0]) : ?>
                                <?php $airlineLogo = '//www.gstatic.com/flights/airline_logos/70px/' . $marketingAirlines[0] . '.png' ?>
                                <span class="quote__vc-logo">
                                    <img src="<?php echo $airlineLogo ?>" alt="<?= $marketingAirlines[0]?>" class="quote__airline-logo">
                                </span>
                            <?php endif ?>
                        <?php else :?>
                            <img src="/img/multiple_airlines.png" alt="<?= implode(', ', $marketingAirlines)?>" class="quote__airline-logo">
                        <?php endif;?>
                        <div class="quote__info-options">
                            <div class="quote__duration"><?= SearchService::durationInMinutes($trip['duration'])?></div>
                            <div class="quote__airline-name"><?= implode(', ', $airlineNames);?></div>
                        </div>
                    </div>
                    <div class="quote__itinerary">
                        <div class="quote__itinerary-col quote__itinerary-col--from">
                            <div class="quote__datetime">
                                <span class="quote__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment['departureTime']), 'h:mm a')?></span>
                                <span class="quote__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment['departureTime']), 'MMM d')?></span>
                            </div>
                            <div class="quote__location">
                                <div class="quote__airport">
                                    <span class="quote__city"><?= (!isset($locations[$firstSegment['departureAirportCode']])) ?: $locations[$firstSegment['departureAirportCode']]['city'];?></span>
                                    <span class="quote__iata"><?= $firstSegment['departureAirportCode']?></span>
                                </div>
                            </div>
                        </div>
                        <div class="quote__itinerary-col quote__itinerary-col--to">
                            <div class="quote__datetime">
                                <span class="quote__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment['arrivalTime']), 'h:mm a')?></span>
                                <span class="quote__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment['arrivalTime']), 'MMM d')?></span>
                            </div>
                            <div class="quote__location">
                                <div class="quote__airport">
                                    <span class="quote__city"><?= (!isset($locations[$lastSegment['arrivalAirportCode']])) ?: $locations[$lastSegment['arrivalAirportCode']]['city'];?></span>
                                    <span class="quote__iata"><?= $lastSegment['arrivalAirportCode']?></span>
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
        <div class="quote__badges">

            <?php $meta = !empty($result['meta']) ? $result['meta'] : null ?>
            <?php echo QuoteHelper::formattedFreeBaggage($meta) ?>

            <!--<span class="quote__badge quote__badge--amenities <?php /*if (!$hasFreeBaggage) :
                */?>quote__badge--disabled<?php
/*                                                              endif;*/?>" data-toggle="tooltip"
                  title="<?/*= ($freeBaggageInfo) ? $freeBaggageInfo : 'No free baggage'*/?>" data-original-title="<?/*= ($freeBaggageInfo) ? $freeBaggageInfo : 'No free baggage'*/?>">
                <i class="fa fa-suitcase"></i><span class="quote__badge-num"></span>
            </span>-->

            <?php

            if ($needRecheck) {
                $bagText = 'Bag re-check may be required'; //SearchService::getRecheckBaggageText();
            } else {
                $bagText = 'Bag re-check not required';
            }


            ?>

            <span class="quote__badge quote__badge--warning <?=$needRecheck ? '' : 'quote__badge--disabled'?>" data-toggle="tooltip"
                  title="<?= Html::encode($bagText)?>"
                  data-original-title="<?= Html::encode($bagText)?>">
                <i class="fa fa-warning"></i>
            </span>

            <span class="quote__badge <?php if ($hasAirportChange) :
                ?>quote__badge--warning<?php
                                      else :
                                            ?>quote__badge--disabled<?php
                                      endif;?>"
                  data-toggle="tooltip" title="<?= ($hasAirportChange) ? 'Airports Change' : 'No Airports Change'?>" data-original-title="<?= ($hasAirportChange) ? 'Airports Change' : 'No Airports Change'?>">
                <i class="fa fa-exchange"></i>
            </span>

            <?php echo QuoteHelper::formattedPenalties($result['penalties'] ?? null) ?>

            <?php echo QuoteHelper::formattedMetaRank($meta) ?>

        </div>
        <div class="quote__actions">
            <table class="table table-striped table-prices">
                <thead>
                <tr>
                    <th>Pax</th>
                    <th>Q</th>
                    <?php if (isset($result['prices']['markup']) && $result['prices']['markup'] > 0) :
                        ?><th class="text-right">MU, <?php echo $currencySymbol ?></th><?php
                    endif;?>
                    <th  class="text-right">Ex Mkp, <?php echo $currencySymbol ?></th>
                    <th class="text-right">SP, <?php echo $currencySymbol ?></th>
                </tr>
                </thead>
                <tbody>
                <?php $paxTotal = 0;?>
                <?php foreach ($result['passengers'] as $paxCode => $pax) :?>
                    <tr><?php $paxTotal += $pax['cnt'];?>
                        <th><?= $paxCode?></th>
                        <td>x <?= $pax['cnt']?></td>
                        <?php if (isset($result['prices']['markup']) && $result['prices']['markup'] > 0) :
                            ?><td class="text-right"><?= (isset($pax['markup'])) ? $pax['markup'] : ''?></td><?php
                        endif;?>
                        <td class="box_ex_markup_<?php echo $keyId ?> text-right">
                            <?php $readonly = $isQuoteAssignedToFlight ? 'readonly="1"' : '' ?>
                            <?php $border = $isQuoteAssignedToFlight ? ' border: 0; ' : '' ?>
                    <?php
                        $value = '';
                        /** @var Quote $quote */
                    if (
                        $isQuoteAssignedToFlight &&
                        ($quote = $flightQuotes[$result['key']] ?? null) &&
                        $price = QuotePriceRepository::findByQuoteIdAndPaxCode($quote->id, $paxCode)
                    ) {
                        $value = !empty($price->qp_client_extra_mark_up) ? $price->qp_client_extra_mark_up : $price->extra_mark_up;
                    }
                    ?>
                            <input
                                type="number"
                                value="<?php echo $value ?>"
                                <?php echo $readonly ?>
                                id="ex_markup_<?php echo $result['key'] ?>_<?php echo $paxCode?>"
                                class="ex_markup"
                                data-pax-code="<?php echo $paxCode?>"
                                maxlength="10"
                                autocomplete="off"
                                style="width: 56px; <?php echo $border ?>" />
                        </td>
                        <td class="text-right"><?= $pax['price']?></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
                <tfoot>
                <tr>
                    <th>Total</th>
                    <td><?= $paxTotal?></td>
                    <?php if (isset($result['prices']['markup']) && $result['prices']['markup'] > 0) :
                        ?><td class="text-right"><?= $result['prices']['markup']?></td><?php
                    endif;?>
                    <td></td>
                    <td class="text-right"><?= $result['prices']['totalPrice']?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="quote__details" id="search_result_item_<?= $resultKey?>" style="display:none;">
        <div class="trip">
            <div class="trip__item">
                <!-- Depart -->
                <?php foreach ($result['trips'] as $tripKey => $trip) :?>
                    <div class="trip__leg">
                        <h4 class="trip__subtitle">
                            <span class="trip__leg-type"><?php if (count($result['trips']) < 3 && $tripKey == 0) :
                                ?>Depart<?php
                                                         elseif (count($result['trips']) < 3 && $tripKey > 0) :
                                                                ?>Return<?php
                                                         else :
                                                                ?><?= ($tripKey + 1);?> Trip<?php
                                                         endif?></span>
                            <span class="trip__leg-date"><?= Yii::$app->formatter_search->asDatetime(strtotime($trip['segments'][0]['departureTime']), 'EEE d MMM')?></span>
                        </h4>
                        <div class="trip__card">
                            <div class="trip__details trip-detailed" id="flight-leg-1">
                                <!--Segment1-->
                                <?php foreach ($trip['segments'] as $key => $segment) :?>
                                    <?php
                                    $projectName = '';
                                    $departCountryName =  $locations[$segment['departureAirportCode']]['city'] ?? $segment['departureAirportCode'];
                                    $arrivalCountryName =  $locations[$segment['arrivalAirportCode']]['city'] ?? $segment['arrivalAirportCode'];
                                    ?>

                                    <?php if ($key > 0) :?>
                                        <?php $prevSegment = $trip['segments'][$key - 1];?>
                                        <div class="trip-detailed__layover">
                                            <span class="trip-detailed__layover-location">Layover in <?= (!isset($locations[$segment['departureAirportCode']])) ?: $locations[$segment['departureAirportCode']]['city'];?> (<?= $segment['departureAirportCode']?>)</span>
                                            <span class="trip-detailed__layover-duration"><?= SearchService::getLayoverDuration($prevSegment['arrivalTime'], $segment['departureTime'])?></span>
                                        </div>
                                    <?php endif;?>
                                    <div class="trip-detailed__segment segment">
                                        <div class="segment__wrapper">
                                            <div class="segment__options">
                                                <img src="//www.gstatic.com/flights/airline_logos/70px/<?= $segment['marketingAirline']?>.png" alt="<?= $segment['marketingAirline']?>" class="segment__airline-logo">
                                                <div class="segment__cabin-xs"><?= SearchService::getCabin($segment['cabin'], !empty($segment['cabinIsBasic']))?></div>
                                                <div class="segment__airline"><?= (!isset($airlines[$segment['marketingAirline']])) ?: $airlines[$segment['marketingAirline']];?></div>
                                                <div class="segment__flight-nr">Flight <?= $segment['marketingAirline']?> <?= $segment['flightNumber']?></div>
                                            </div>

                                            <div class="segment__location segment__location--from">
                                                <span class="segment__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment['departureTime']), 'h:mm a')?></span>
                                                <span class="segment__airport"><?= (!isset($locations[$segment['departureAirportCode']])) ?: $locations[$segment['departureAirportCode']]['name'];?> (<?= $segment['departureAirportCode']?>)</span>
                                                <span class="segment__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment['departureTime']), 'EEEE, MMM d')?></span>
                                            </div>

                                            <div class="segment__location segment__location--to">
                                                <span class="segment__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment['arrivalTime']), 'h:mm a')?></span>
                                                <span class="segment__airport"><?= (!isset($locations[$segment['arrivalAirportCode']])) ?: $locations[$segment['arrivalAirportCode']]['name'];?> (<?= $segment['arrivalAirportCode']?>)</span>
                                                <span class="segment__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment['arrivalTime']), 'EEEE, MMM d')?></span>
                                            </div>

                                            <div class="segment__duration-wrapper">
                                                <div class="segment__duration-time"><?= SearchService::durationInMinutes($segment['duration'])?></div>
                                                <div class="segment__cabin"><?= SearchService::getCabin($segment['cabin'], !empty($segment['cabinIsBasic']))?></div>
                                            </div>
                                        </div>
                                        <div class="segment__note search_fq">
                                            <?php if ($segment['operatingAirline'] != $segment['marketingAirline']) :
                                                ?>Operated by <?= (!isset($airlines[$segment['operatingAirline']])) ?: $airlines[$segment['operatingAirline']];?>.<?php
                                            endif;?>
                                            <?php if (isset($segment['baggage'])) :?>
                                                <?php foreach ($segment['baggage'] as $baggage) :?>
                                                    <span class="badge badge-primary"><i class="fa fa-suitcase"></i>&nbsp;
                                                    <?php if (isset($baggage['allowPieces'])) :?>
                                                        <?= \Yii::t('search', '{n, plural, =0{no baggage} one{# piece} other{# pieces}}', ['n' => $baggage['allowPieces']]);?>
                                                    <?php elseif (isset($baggage['allowWeight'])) :?>
                                                        <?= $baggage['allowWeight'] . $baggage['allowUnit']?>
                                                    <?php endif;?>
                                            </span>
                                                    <?php if (isset($baggage['charge'])) :?>
                                                        <?php foreach ($baggage['charge'] as $charge) :?>
                                                            <span title="<?= (isset($charge['maxSize']) ? $charge['maxSize'] : '') . ' ' . (isset($charge['maxWeight']) ? $charge['maxWeight'] : '')?>" class="badge badge-light"><i class="fa fa-plus"></i>&nbsp;
                                            <i class="fa fa-suitcase"></i>&nbsp;<?= (isset($charge['price'])) ? $charge['price'] : ''?>$</span>
                                                        <?php endforeach;?>
                                                    <?php endif;?>

                                                        <?php if (isset($baggage['carryOn'])) :?>
                                                            <?php if ((bool) $baggage['carryOn'] === false) :?>
                                                                <span class="fa-stack" title="CarryOn Disable">
                                                                    <i class="fa fa-shopping-bag fa-stack-1x"></i>
                                                                    <i class="fa fa-ban fa-stack-2x text-danger"></i>
                                                                </span>
                                                            <?php endif ?>
                                                        <?php endif ?>

                                                    <?php break;
                                                endforeach;?>
                                            <?php endif;?>
                                            <?php if (isset($segment['meal'])) :
                                                ?><span class="badge badge-light" title="<?= $segment['meal']?>"><i class="fa fa-cutlery"></i></span><?php
                                            endif;?>
                                            <?php if ($segment['recheckBaggage']) :
                                                ?> <h5 class="danger" title="<?=\yii\helpers\Html::encode(SearchService::getRecheckBaggageText($departCountryName))?>"><i class="fa fa-warning"></i> Bag re-check may be required</h5> <?php
                                            endif;?>
                                            <?php if (isset($segment['stop']) && $segment['stop'] > 0) :?>
                                                <h5 class="danger"><i class="fa fa-warning"></i> <?= \Yii::t('search', '{n, plural, =0{no technical stops} one{# technical stop} other{# technical stops}}', ['n' => $segment['stop']])?></h5>

                                                <table class="table table-bordered table-striped">
                                                    <?php if (isset($segment['stops']) && is_array($segment['stops'])) : ?>
                                                        <tr>
                                                            <th>Location</th>
                                                            <th>Departure DateTime</th>
                                                            <th>Arrival DateTime</th>
                                                            <th>Duration</th>
                                                            <th>Elapsed Time</th>
                                                            <th>Equipment</th>
                                                        </tr>
                                                        <?php foreach ($segment['stops'] as $stop) :?>
                                                            <tr>
                                                                <td><?=isset($stop['locationCode'], $locations[$stop['locationCode']]) ? \yii\helpers\Html::encode('(' . $stop['locationCode'] . ') ' . $locations[$stop['locationCode']]['city'] . ', ' . $locations[$stop['locationCode']]['country']) : ($stop['locationCode'] ?? '-')?></td>
                                                                <td><?=$stop['departureDateTime'] ? Yii::$app->formatter_search->asDatetime(strtotime($stop['departureDateTime']), 'EEEE, MMM d [h:mm a]') : '-'?></td>
                                                                <td><?=$stop['arrivalDateTime'] ? Yii::$app->formatter_search->asDatetime(strtotime($stop['arrivalDateTime']), 'EEEE, MMM d [h:mm a]') : '-'?></td>
                                                                <td><?=isset($stop['duration']) ? SearchService::durationInMinutes($stop['duration']) : '-'?></td>
                                                                <td><?=(isset($stop['elapsedTime']) && $stop['elapsedTime']) ? SearchService::durationInMinutes($stop['elapsedTime']) : '-'?></td>
                                                                <td><?=isset($stop['equipment']) ? \yii\helpers\Html::encode($stop['equipment']) : '-'?></td>
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
                <?php endforeach;?>
            </div>
        </div>
    </div>

    <div class="quote__footer">


        <div class="quote__footer-left">
            <!--            --><?php //if(isset($result['tickets']) && $result['tickets']):?>
            <!--                <span class="fa fa-warning warning"></span> Separate Ticket (--><?php //=count($result['tickets'])?><!--)-->
            <!--            --><?php //endif;?>
        </div>
        <div class="quote__footer-right">
            <?= Html::button('<i class="fa fa-eye"></i>&nbsp; <span>Details</span>', [
                'class' => 'btn btn-primary search_quote_details__btn',
                'data-title' => implode(', ', $tripsInfo),
                'data-target' => '#search_result_item_' . $resultKey,
            ]) ?>

            <div class="btn-group js-btn-box" style="margin: 0 7px; height: 32px;">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-bars"></i> Add
                </button>
                <div class="dropdown-menu small">
                    <?= Html::button('<i class="fa fa-plus"></i>&nbsp; <span>Add Quote</span>', [
                        'class' => 'dropdown-item quote__btn',
                        'data-title' => implode(', ', $tripsInfo),
                        'data-key' => $result['key'],
                        'data-gds' => $result['gds'],
                        'data-key-cache' => $keyCache,
                        'data-result' => 'search-result__quote-' . $resultKey,
                        'data-project' => $lead->project_id,
                        'data-chat-id' => $chatId,
                        'data-send-quote' => false,
                        'data-url' => $urlCreateQuoteFromSearch,
                        'disabled' => $isQuoteAssignedToFlight,
                        'data-key-id' => $keyId,
                    ]) ?>

                    <?php if ($projectRelations = $lead->project->projectRelations) : ?>
                        <?php foreach ($projectRelations as $relatedProject) : ?>
                            <?php echo
                            Html::button(
                                '<i class="fa fa-plus"></i>&nbsp; <span>Add Quote to ' . $relatedProject->prlRelatedProject->name . '</span>',
                                [
                                    'class' => 'dropdown-item quote__btn',
                                    'style' => 'width: 180px; margin-left: 0; margin-bottom: 2px; text-align: left;',
                                    'data-title' => implode(', ', $tripsInfo),
                                    'data-key' => $result['key'],
                                    'data-gds' => $result['gds'],
                                    'data-key-cache' => $keyCache,
                                    'data-result' => 'search-result__quote-' . $resultKey,
                                    'data-project' => $relatedProject->prl_related_project_id,
                                    'data-send-quote' => false,
                                    'data-chat-id' => $chatId,
                                    'data-url' => $urlCreateQuoteFromSearch,
                                    'data-key-id' => $keyId,
                                ]
                            )
                            ?>
                        <?php endforeach ?>
                    <?php endif; ?>
                </div>
            </div>


            <div class="btn-group js-btn-box" style="margin: 0 7px; height: 32px;">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-bars"></i> Send
                </button>
                <div class="dropdown-menu small">

                    <?= Html::button('<i class="fa fa-paper-plane"></i>&nbsp; <span>Send</span>', [
                        'class' => 'dropdown-item quote__btn',
                        'data-title' => implode(', ', $tripsInfo),
                        'data-key' => $result['key'],
                        'data-gds' => $result['gds'],
                        'data-key-cache' => $keyCache,
                        'data-result' => 'search-result__quote-' . $resultKey,
                        'data-project' => $lead->project_id,
                        'data-chat-id' => $chatId,
                        'data-send-quote' => true,
                        'data-url' => $urlSendQuoteFromSearch,
                        'data-key-id' => $keyId,
                    ]) ?>

                    <?php if ($projectRelations = $lead->project->projectRelations) : ?>
                        <?php foreach ($projectRelations as $relatedProject) : ?>
                            <?php echo
                             Html::button('<i class="fa fa-plus"></i>&nbsp; <span>Send Quote of ' . $relatedProject->prlRelatedProject->name . ' Provider</span>', [
                                 'class' => 'dropdown-item quote__btn',
                                 'data-title' => implode(', ', $tripsInfo),
                                 'data-key' => $result['key'],
                                 'data-gds' => $result['gds'],
                                 'data-key-cache' => $keyCache,
                                 'data-result' => 'search-result__quote-' . $resultKey,
                                 'data-project' => $relatedProject->prl_related_project_id,
                                 'data-chat-id' => $chatId,
                                 'data-send-quote' => true,
                                 'data-url' => $urlSendQuoteFromSearch,
                                 'data-key-id' => $keyId,
                             ])
                            ?>
                        <?php endforeach ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
