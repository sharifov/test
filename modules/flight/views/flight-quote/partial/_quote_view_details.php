<?php

use common\components\SearchService;
use common\models\Airline;
use modules\flight\models\FlightQuote;
use modules\product\src\entities\productQuote\ProductQuote;

/* @var $this yii\web\View */
/* @var $productQuote ProductQuote*/
/* @var $flightQuote FlightQuote*/

?>


<div class="quote__details">
	<div class="trip">
		<div class="trip__item">
            <?php if($flightQuote): ?>
			<?php foreach ($flightQuote->flightQuoteTrips as $tripKey => $trip):?>
				<?php $segments = $trip->flightQuoteSegments;?>
				<div class="trip__leg">
					<h4 class="trip__subtitle">
						<span class="trip__leg-type"><?php if(count($flightQuote->flightQuoteTrips) < 3 && $tripKey == 0):?>Depart<?php elseif(count($flightQuote->flightQuoteTrips) < 3 && $tripKey > 0):?>Return<?php else:?><?= ($tripKey+1);?> Trip<?php endif?></span>
						<span class="trip__leg-date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segments[0]->fqs_departure_dt),'EEE d MMM')?></span>
					</h4>
					<div class="trip__card">
						<div class="trip__details trip-detailed" id="flight-leg-1">
							<!--Segment1-->
							<?php foreach ($segments as $key => $segment):?>
								<?php if($key > 0):

									//$ticketBgColor = $segment->qs_ticket_id;

									?>
									<?php $prevSegment = $segments[$key-1];?>
									<div class="trip-detailed__layover">
										<span class="trip-detailed__layover-location">Layover in <?= (!$segment->departureAirport)?:$segment->departureAirport->city;?> (<?= $segment->fqs_departure_airport_iata?>)</span>
										<span class="trip-detailed__layover-duration"><?= SearchService::getLayoverDuration($prevSegment->fqs_arrival_dt,$segment->fqs_departure_dt)?></span>
									</div>
								<?php endif;?>
								<div class="trip-detailed__segment segment" style="background-color: <?=$segment->getTicketColor()?>">
									<div class="segment__wrapper">
										<div class="segment__options">
											<img src="//www.gstatic.com/flights/airline_logos/70px/<?= $segment->fqs_marketing_airline?>.png" alt="<?= $segment->fqs_marketing_airline?>" class="segment__airline-logo">
											<div class="segment__cabin-xs"><?= SearchService::getCabin($segment->fqs_cabin_class)?></div>
											<div class="segment__airline">
												<?php $airline = Airline::findIdentity($segment->fqs_marketing_airline);
												if($airline !== null) echo $airline->name?>
											</div>
											<div class="segment__flight-nr">Flight <?= $segment->fqs_marketing_airline?> <?= $segment->fqs_flight_number?></div>
										</div>

										<div class="segment__location segment__location--from">
											<span class="segment__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->fqs_departure_dt),'h:mm a')?></span>
											<span class="segment__airport"><?= (!$segment->departureAirport)?:$segment->departureAirport->name;?> (<?= $segment->fqs_departure_airport_iata?>)</span>
											<span class="segment__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->fqs_departure_dt),'EEEE, MMM d')?></span>
										</div>

										<div class="segment__location segment__location--to">
											<span class="segment__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->fqs_arrival_dt),'h:mm a')?></span>
											<span class="segment__airport"><?= (!$segment->arrivalAirport)?:$segment->arrivalAirport->name;?> (<?= $segment->fqs_arrival_airport_iata?>)</span>
											<span class="segment__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment->fqs_arrival_dt),'EEEE, MMM d')?></span>
										</div>

										<div class="segment__duration-wrapper">
											<div class="segment__duration-time"><?= SearchService::durationInMinutes($segment->fqs_duration)?></div>
											<div class="segment__cabin"><?= SearchService::getCabin($segment->fqs_cabin_class)?></div>
											<?php if($segment->fqs_ticket_id):?>
												<div class="warning" title="Ticket <?=$segment->fqs_ticket_id?>"><span class="fa fa-ticket"></span> Tick <?=$segment->fqs_ticket_id?></div>
											<?php endif;?>
										</div>
									</div>
									<div class="segment__note">
										<?php if($segment->fqs_operating_airline != $segment->fqs_marketing_airline):?>Operated by <?php $airline = Airline::findIdentity($segment->fqs_operating_airline);if($airline) echo $airline->name; else echo $segment->fqs_operating_airline?>.<?php endif;?>
										<?php if(!empty($segment->flightQuoteSegmentPaxBaggages)):?>
											<span class="badge badge-primary"><i class="fa fa-suitcase"></i>&nbsp;
                                    <?php foreach ($segment->flightQuoteSegmentPaxBaggages as $baggage):?>
										<?php if(isset($baggage->qsb_allow_pieces)):?>
											<?= \Yii::t('search', '{n, plural, =0{no baggage} one{# piece} other{# pieces}}', ['n' => $baggage->qsb_allow_pieces]);?>
										<?php elseif(isset($baggage->qsb_allow_weight)):?>
											<?= $baggage->qsb_allow_weight.$baggage->qsb_allow_unit?>
										<?php endif;?>
										<?php break; endforeach;?>
                                    </span>
										<?php endif;?>
										<?php if(!empty($segment->flightQuoteSegmentPaxBaggageCharges)):?>
											<?php $paxCode = null;?>
											<?php foreach ($segment->flightQuoteSegmentPaxBaggageCharges as $baggageCh):?>
												<?php if($paxCode == null){
													$paxCode = $baggageCh->qsbc_flight_pax_code_id;
												}elseif ($paxCode != $baggageCh->qsbc_flight_pax_code_id){
													break;
												}
												?>
												<span title="<?= (isset($baggageCh->qsbc_max_size)?$baggageCh->qsbc_max_size:'').' '.(isset($baggageCh->qsbc_max_weight)?$baggageCh->qsbc_max_weight:'')?>"
													  class="badge badge-light"><i class="fa fa-plus"></i>&nbsp;<i class="fa fa-suitcase"></i>&nbsp;<?= $baggageCh->qsbc_price?>$</span>
											<?php endforeach;?>
										<?php endif;?>
										<?php if(isset($segment->fqs_meal)):?><span class="badge badge-light" title="<?= $segment->fqs_meal?>"><i class="fa fa-cutlery"></i></span><?php endif;?>
										<?php if ($segment->fqs_recheck_baggage == true && $segment->fqs_recheck_baggage !== null):?> <h5 class="danger"><i class="fa fa-warning"></i> Bag re-check may be required</h5> <?php endif;?>
										<?php if(isset($segment->fqs_stop) && $segment->fqs_stop > 0):?>

											<h5 class="danger">
												<i class="fa fa-warning"></i> <?= \Yii::t('search', '{n, plural, =0{no technical stops} one{# technical stop} other{# technical stops}}', ['n' => $segment->fqs_stop])?>
											</h5>

											<table class="table table-bordered table-striped">
												<?php if($segment->flightQuoteSegmentStops): ?>
													<tr>
														<th>Location</th>
														<th>Departure DateTime</th>
														<th>Arrival DateTime</th>
														<th>Duration</th>
														<th>Elapsed Time</th>
														<th>Equipment</th>
													</tr>
													<?php foreach ($segment->flightQuoteSegmentStops as $stop):?>
														<tr>
															<td><?=$stop->locationAirport ? \yii\helpers\Html::encode('('.$stop->locationAirport->iata.') '.$stop->locationAirport->city . ', '. $stop->locationAirport->country) : ($stop->qss_location_iata ?? '-')?></td>
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
            <?php else: ?>
                <p>Not found details</p>
            <?php endif; ?>
		</div>
	</div>
</div>


