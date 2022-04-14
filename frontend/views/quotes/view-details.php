<?php

use common\components\SearchService;
use common\models\Airline;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Quote */

?>

<div class="quote__details">
    <div class="trip">
        <div class="trip__item">
            <?php foreach ($model->quoteTrips as $tripKey => $trip) :?>
                <?php $segments = $trip->quoteSegments;?>
                <div class="trip__leg">
                    <h4 class="trip__subtitle">
                        <span class="trip__leg-type"><?php if (count($model->quoteTrips) < 3 && $tripKey == 0) :
                            ?>Depart<?php
                                                     elseif (count($model->quoteTrips) < 3 && $tripKey > 0) :
                                                            ?>Return<?php
                                                     else :
                                                            ?><?= ($tripKey + 1);?> Trip<?php
                                                     endif?></span>
                        <span class="trip__leg-date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segments[0]->qs_departure_time), 'EEE d MMM')?></span>
                    </h4>
                    <div class="trip__card">
                        <div class="trip__details trip-detailed" id="flight-leg-1">
                            <!--Segment1-->
                            <?php foreach ($segments as $key => $segment) :?>
                                <?php if ($key > 0) :
                                    //$ticketBgColor = $segment->qs_ticket_id;

                                    ?>
                                    <?php $prevSegment = $segments[$key - 1];?>
                                    <div class="trip-detailed__layover">
                                        <span class="trip-detailed__layover-location">Layover in <?= (!$segment->departureAirport) ?: $segment->departureAirport->city;?> (<?= $segment->qs_departure_airport_code?>)</span>
                                        <span class="trip-detailed__layover-duration"><?= SearchService::getLayoverDuration($prevSegment->qs_arrival_time, $segment->qs_departure_time)?></span>
                                    </div>
                                <?php endif;?>
                                <div class="trip-detailed__segment segment" style="background-color: <?=$segment->getTicketColor()?>">
                                    <div class="segment__wrapper">
                                        <div class="segment__options">
                                            <img src="//www.gstatic.com/flights/airline_logos/70px/<?= $segment->qs_marketing_airline?>.png" alt="<?= $segment->qs_marketing_airline?>" class="segment__airline-logo">
                                            <div class="segment__cabin-xs"><?= SearchService::getCabin($segment->qs_cabin, $segment->qs_cabin_basic)?></div>
                                            <div class="segment__airline">
                                                <?php $airline = Airline::findIdentity($segment->qs_marketing_airline);
                                                if ($airline !== null) {
                                                    echo $airline->name;
                                                }?>
                                            </div>
                                            <div class="segment__flight-nr">Flight <?= $segment->qs_marketing_airline?> <?= $segment->qs_flight_number?></div>
                                        </div>

                                        <div class="segment__location segment__location--from">
                                            <span class="segment__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->qs_departure_time), 'h:mm a')?></span>
                                            <span class="segment__airport"><?= (!$segment->departureAirport) ?: $segment->departureAirport->name;?> (<?= $segment->qs_departure_airport_code?>)</span>
                                            <span class="segment__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->qs_departure_time), 'EEEE, MMM d')?></span>
                                        </div>

                                        <div class="segment__location segment__location--to">
                                            <span class="segment__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->qs_arrival_time), 'h:mm a')?></span>
                                            <span class="segment__airport"><?= (!$segment->arrivalAirport) ?: $segment->arrivalAirport->name;?> (<?= $segment->qs_arrival_airport_code?>)</span>
                                            <span class="segment__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->qs_arrival_time), 'EEEE, MMM d')?></span>
                                        </div>

                                        <div class="segment__duration-wrapper">
                                            <div class="segment__duration-time"><?= SearchService::durationInMinutes($segment->qs_duration)?></div>
                                            <div class="segment__cabin"><?= SearchService::getCabin($segment->qs_cabin, $segment->qs_cabin_basic)?></div>
                                            <?php if ($segment->qs_ticket_id) :?>
                                                <div class="warning" title="Ticket <?=$segment->qs_ticket_id?>"><span class="fa fa-ticket"></span> Tick <?=$segment->qs_ticket_id?></div>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                    <div class="segment__note">
                                        <?php if ($segment->qs_operating_airline != $segment->qs_marketing_airline) :
                                            ?>Operated by <?php $airline = Airline::findIdentity($segment->qs_operating_airline);
if ($airline) {
    echo $airline->name;
} else {
    echo $segment->qs_operating_airline;
}?>.<?php
                                        endif;?>

                                        <?php if (!empty($segment->quoteSegmentBaggages)) :?>
                                            <?php foreach ($segment->quoteSegmentBaggages as $baggage) :?>
                                            <span
                                                title="<?php echo Html::encode($baggage->qsb_allow_max_weight) . "\n" .
                                                    Html::encode($baggage->qsb_allow_max_size) ?>"
                                                class="badge badge-primary">
                                                    <i class="fa fa-suitcase"></i>&nbsp;
                                                <?php if (isset($baggage->qsb_allow_pieces)) :?>
                                                    <?= \Yii::t(
                                                        'search',
                                                        '{n, plural, =0{no baggage} one{# piece} other{# pieces}}',
                                                        ['n' => $baggage->qsb_allow_pieces]
                                                    ) ?>
                                                <?php elseif (isset($baggage->qsb_allow_weight)) :?>
                                                    <?= $baggage->qsb_allow_weight . $baggage->qsb_allow_unit?>
                                                <?php endif;?>
                                            </span>

                                                <?php if (isset($baggage->qsb_carry_one)) :?>
                                                    <?php if ((bool) $baggage->qsb_carry_one === false) :?>
                                                        <span class="fa-stack" title="CarryOn Disable">
                                                            <i class="fa fa-shopping-bag fa-stack-1x"></i>
                                                            <i class="fa fa-ban fa-stack-2x text-danger"></i>
                                                        </span>
                                                    <?php endif ?>
                                                <?php endif ?>

                                            <?php endforeach;?>
                                        <?php endif;?>

                                        <?php if (!empty($segment->quoteSegmentBaggageCharges)) :?>
                                            <?php foreach ($segment->quoteSegmentBaggageCharges as $baggageCh) :?>
                                                <span
                                                    title="<?php echo
                                                        'Piece ' . $baggageCh->qsbc_first_piece . ' - ' . $baggageCh->qsbc_last_piece . "\n" .
                                                        Html::encode($baggageCh->qsbc_max_size) . "\n" .
                                                        Html::encode($baggageCh->qsbc_max_weight) ?>"
                                                    class="badge badge-light">
                                                        <i class="fa fa-plus"></i>&nbsp;
                                                        <i class="fa fa-suitcase"></i>&nbsp;
                                                        <?= $baggageCh->qsbc_price?>
                                                        <?php echo $model->clientCurrency->cur_symbol ?? '' ?>
                                                </span>
                                            <?php endforeach;?>
                                        <?php endif;?>

                                        <?php if (isset($segment->qs_meal)) :
                                            ?><span class="badge badge-light" title="<?= $segment->qs_meal?>"><i class="fa fa-cutlery"></i></span><?php
                                        endif;?>

                                        <?php if ($segment->qs_recheck_baggage == true && $segment->qs_recheck_baggage !== null) :
                                            ?> <h5 class="danger"><i class="fa fa-warning"></i> Bag re-check may be required</h5> <?php
                                        endif;?>
                                        <?php if (isset($segment->qs_stop) && $segment->qs_stop > 0) :?>
                                            <h5 class="danger">
                                                <i class="fa fa-warning"></i> <?= \Yii::t('search', '{n, plural, =0{no technical stops} one{# technical stop} other{# technical stops}}', ['n' => $segment->qs_stop])?>
                                            </h5>

                                            <table class="table table-bordered table-striped">
                                                <?php if ($segment->quoteSegmentStops) : ?>
                                                    <tr>
                                                        <th>Location</th>
                                                        <th>Departure DateTime</th>
                                                        <th>Arrival DateTime</th>
                                                        <th>Duration</th>
                                                        <th>Elapsed Time</th>
                                                        <th>Equipment</th>
                                                    </tr>
                                                    <?php foreach ($segment->quoteSegmentStops as $stop) :?>
                                                        <tr>
                                                            <td><?=$stop->locationAirport ? \yii\helpers\Html::encode('(' . $stop->locationAirport->iata . ') ' . $stop->locationAirport->city . ', ' . $stop->locationAirport->country) : ($stop->qss_location_code ?? '-')?></td>
                                                            <td><?=$stop->qss_departure_dt ? Yii::$app->formatter_search->asDatetime(strtotime($stop->qss_departure_dt), 'EE, MMM d, h:mm a') : '-'?></td>
                                                            <td><?=$stop->qss_arrival_dt ? Yii::$app->formatter_search->asDatetime(strtotime($stop->qss_arrival_dt), 'EE, MMM d, h:mm a') : '-'?></td>
                                                            <td><?=$stop->qss_duration ? SearchService::durationInMinutes($stop->qss_duration) : '-'?></td>
                                                            <td><?=$stop->qss_elapsed_time ? SearchService::durationInMinutes($stop->qss_elapsed_time) : '-'?></td>
                                                            <td><?=$stop->qss_equipment ? \yii\helpers\Html::encode($stop->qss_equipment) : '-'?></td>
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


