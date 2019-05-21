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
            <?php foreach ($model->quoteTrips as $tripKey => $trip):?>
                <?php $segments = $trip->quoteSegments;?>
                <div class="trip__leg">
                    <h4 class="trip__subtitle">
                        <span class="trip__leg-type"><?php if(count($model->quoteTrips) < 3 && $tripKey == 0):?>Depart<?php elseif(count($model->quoteTrips) < 3 && $tripKey > 0):?>Return<?php else:?><?= ($tripKey+1);?> Trip<?php endif?></span>
                        <span class="trip__leg-date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segments[0]->qs_departure_time),'EEE d MMM')?></span>
                    </h4>
                    <div class="trip__card">
                        <div class="trip__details trip-detailed" id="flight-leg-1">
                            <!--Segment1-->
                            <?php foreach ($segments as $key => $segment):?>
                                <?php if($key > 0):?>
                                    <?php $prevSegment = $segments[$key-1];?>
                                    <div class="trip-detailed__layover">
                                        <span class="trip-detailed__layover-location">Layover in <?= (!$segment->departureAirport)?:$segment->departureAirport->city;?> (<?= $segment->qs_departure_airport_code?>)</span>
                                        <span class="trip-detailed__layover-duration"><?= SearchService::getLayoverDuration($prevSegment->qs_arrival_time,$segment->qs_departure_time)?></span>
                                    </div>
                                <?php endif;?>
                                <div class="trip-detailed__segment segment">
                                    <div class="segment__wrapper">
                                        <div class="segment__options">
                                            <img src="//www.gstatic.com/flights/airline_logos/70px/<?= $segment->qs_marketing_airline?>.png" alt="<?= $segment->qs_marketing_airline?>" class="segment__airline-logo">
                                            <div class="segment__cabin-xs"><?= SearchService::getCabin($segment->qs_cabin)?></div>
                                            <div class="segment__airline">
                                                <?php $airline = Airline::findIdentity($segment->qs_marketing_airline);
                                                if($airline !== null) echo $airline->name?>
                                            </div>
                                            <div class="segment__flight-nr">Flight <?= $segment->qs_marketing_airline?> <?= $segment->qs_flight_number?></div>
                                        </div>

                                        <div class="segment__location segment__location--from">
                                            <span class="segment__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->qs_departure_time),'h:mm a')?></span>
                                            <span class="segment__airport"><?= (!$segment->departureAirport)?:$segment->departureAirport->name;?> (<?= $segment->qs_departure_airport_code?>)</span>
                                            <span class="segment__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->qs_departure_time),'EEEE, MMM d')?></span>
                                        </div>

                                        <div class="segment__location segment__location--to">
                                            <span class="segment__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->qs_arrival_time),'h:mm a')?></span>
                                            <span class="segment__airport"><?= (!$segment->arrivalAirport)?:$segment->arrivalAirport->name;?> (<?= $segment->qs_arrival_airport_code?>)</span>
                                            <span class="segment__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->qs_arrival_time),'EEEE, MMM d')?></span>
                                        </div>

                                        <div class="segment__duration-wrapper">
                                            <div class="segment__duration-time"><?= SearchService::durationInMinutes($segment->qs_duration)?></div>
                                            <div class="segment__cabin"><?= SearchService::getCabin($segment->qs_cabin)?></div>
                                        </div>
                                    </div>
                                    <div class="segment__note">
                                        <?php if($segment->qs_operating_airline != $segment->qs_marketing_airline):?>Operated by <?php $airline = Airline::findIdentity($segment->qs_operating_airline);if($airline) echo $airline->name; else echo $segment->qs_operating_airline?>.<?php endif;?>
                                        <?php if(!empty($segment->quoteSegmentBaggages)):?>
                                            <span class="badge badge-primary"><i class="fa fa-suitcase"></i>&nbsp;
                                    <?php foreach ($segment->quoteSegmentBaggages as $baggage):?>
                                        <?php if(isset($baggage->qsb_allow_pieces)):?>
                                            <?= \Yii::t('search', '{n, plural, =0{no baggage} one{# piece} other{# pieces}}', ['n' => $baggage->qsb_allow_pieces]);?>
                                        <?php elseif(isset($baggage->qsb_allow_weight)):?>
                                            <?= $baggage->qsb_allow_weight.$baggage->qsb_allow_unit?>
                                        <?php endif;?>
                                        <?php break; endforeach;?>
                                    </span>
                                        <?php endif;?>
                                        <?php if(!empty($segment->quoteSegmentBaggageCharges)):?>
                                            <?php $paxCode = null;?>
                                            <?php foreach ($segment->quoteSegmentBaggageCharges as $baggageCh):?>
                                                <?php if($paxCode == null){
                                                    $paxCode = $baggageCh->qsbc_pax_code;
                                                }elseif ($paxCode != $baggageCh->qsbc_pax_code){
                                                    break;
                                                }
                                                ?>
                                                <span title="<?= (isset($baggageCh->qsbc_max_size)?$baggageCh->qsbc_max_size:'').' '.(isset($baggageCh->qsbc_max_weight)?$baggageCh->qsbc_max_weight:'')?>"
                                                      class="badge badge-light"><i class="fa fa-plus"></i>&nbsp;<i class="fa fa-suitcase"></i>&nbsp;<?= $baggageCh->qsbc_price?>$</span>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                        <?php if(isset($segment->qs_meal)):?><span class="badge badge-light" title="<?= $segment->qs_meal?>"><i class="fa fa-cutlery"></i></span><?php endif;?>
                                        <?php if(isset($segment->qs_stop) && $segment->qs_stop > 0):?>
                                            <div class="text-danger"><i class="fa fa-warning"></i> <?= \Yii::t('search', '{n, plural, =0{no technical stops} one{# technical stop} other{# technical stops}}', ['n' => $segment->qs_stop]);?></div>
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


