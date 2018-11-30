<?php
/**
 * @var $model Quote
 * @var $appliedQuote obj
 * @var $leadId int
 * @var $leadForm LeadForm
 */


use common\models\Quote;
use common\models\Airline;
use common\components\SearchService;
use yii\bootstrap\Html;
use yii\helpers\Url;
?>
<div class="quote quote--highlight">
	<div class="quote__details" id="quote_detail_<?= $model->uid?>" style="display:none;">
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
	<?php $tripsInfo = []?>
	<?php foreach ($model->quoteTrips as $trip):?>
	<?php
	$segments = $trip->quoteSegments;
	$segmentsCnt = count($segments);
	$firstSegment = $segments[0];
	$lastSegment = $segments[$segmentsCnt-1];
	$tripsInfo[] = ($firstSegment->departureAirport && $lastSegment->arrivalAirport)?
        			$firstSegment->departureAirport->city.' → '.$lastSegment->arrivalAirport->city:
        			$firstSegment->qs_departure_airport_code.' → '.$lastSegment->qs_arrival_airport_code;
	?>
	<?php endforeach;?>
	<div class="quote__heading">
		<div class="quote__heading-left">
			<?php if ($leadForm->mode != $leadForm::VIEW_MODE && in_array($model->status, [$model::STATUS_CREATED, $model::STATUS_SEND])) : ?>
			<div class="custom-checkbox">
				<input class="quotes-uid" id="q<?= $model->uid ?>" value="<?= $model->uid ?>" type="checkbox" name="quote[<?= $model->uid ?>]">
                <label for="q<?= $model->uid ?>"></label>
			</div>
            <?php endif; ?>
			<span class="quote__id">Quote <strong>#<?= $model->uid ?></strong></span>
			<span class="quote__vc">
				<span class="quote__vc-logo"><img src="//www.gstatic.com/flights/airline_logos/70px/<?= $model->main_airline_code?>.png" alt="" class="quote__vc-img"></span>
			</span>
			<span class="quote__vc-name"><?php $airline = Airline::findIdentity($model->main_airline_code);if($airline !== null) echo $airline->name?> <strong>[<?= $model->main_airline_code?>]</strong></span>

			<div class="quote__gds">
				GDS: <strong><?= SearchService::getGDSName($model->gds)?></strong>
			</div>
			<div class="quote__pcc">
				PCC: <strong><?= $model->pcc?></strong>
			</div>
			<span class="quote__creator" data-toggle="tooltip" title="" data-original-title="<?= ($model->created_by_seller) ? 'Agent' : 'Expert'?> <?= $model->employee_name?>"><i class="fa fa-user"></i>&nbsp;<strong><?= $model->employee_name?></strong></span>
		</div>
		<div class="quote__heading-right">
			<?= $model->getStatusSpan()?>
			<div class="btn-group">
				<?php if ($appliedQuote === null) {
                    echo Html::button('<i class="fa fa-copy"></i>', [
                        'class' => 'btn btn-primary add-clone-alt-quote',
                        'data-uid' => $model->uid,
                        'data-url' => Url::to(['quote/clone', 'leadId' => $leadId, 'qId' => $model->id]),
                        'title' => 'Clone'
                    ]);
                } ?>
				<?= Html::button('<i class="fa fa-history"></i>',[
                    'style' => 'color: #ffffff;',
                    'class' => 'view-status-log sl-quote__status-log btn btn-primary',
                    'data-id' => $model->id,
                    'title' => 'View status log'
                ]) ?>
                <?= Html::button('<i class="fa fa-eye"></i>', [
                    'class' => 'btn btn-primary quote_details__btn',
			        'data-title' => implode(', ',$tripsInfo),
                    'data-target' => '#quote_detail_'.$model->uid,
                    'title' => 'Details'
                ]) ?>
                <?= Html::button('<i title="" class="fa fa-terminal"></i>', [
                    'class' => 'btn btn-primary popover-class',
                    'title' => 'Reservation Dump',
                    'data-toggle' => 'popover',
                    'data-html' => 'true',
                    'data-title' => 'Reservation Dump',
                    'data-trigger' => 'click',
                    'data-placement' => 'left',
                    'data-container' => 'body',
                    'data-content' => '<div class="popover-dump">
                                    <button class="btn btn-primary btn-clipboard popover-dump-copy" data-clipboard-text="'.$model->reservation_dump.'"><i class="fa fa-copy"></i></button>
                                    '.str_replace("\n", '<br/>', $model->reservation_dump).'
                                    </div>',
                ]);?>
			</div>
		</div>
	</div>
	<div class="quote__wrapper">
		<div class="quote__trip">
			<?php foreach ($model->quoteTrips as $trip):?>
			<?php
			$firstSegment = null;
			$lastSegment = null;

			$segments = $trip->quoteSegments;
			if( $segments ) {
    			$segmentsCnt = count($segments);
    			$stopCnt = $segmentsCnt - 1;
    			$firstSegment = $segments[0];
    			$lastSegment = end($segments);
    			$cabins = [];
    			$marketingAirlines = [];
    			$airlineNames = [];
    			foreach ($segments as $segment){
    			    if(!in_array(SearchService::getCabin($segment->qs_cabin), $cabins)){
    			        $cabins[] = SearchService::getCabin($segment->qs_cabin);
    			    }
    			    if(isset($segment->qs_stop) && $segment->qs_stop > 0){
    			        $stopCnt += $segment->qs_stop;
    			    }
    			    if(!in_array($segment->qs_marketing_airline, $marketingAirlines)){
    			        $marketingAirlines[] = $segment->qs_marketing_airline;
    			        $airline = Airline::findIdentity($segment->qs_marketing_airline);
    			        if($airline){
    			            $airlineNames[] =  $airline->name;
    			        }else{
    			            $airlineNames[] = $segment->qs_marketing_airline;
    			        }

    			    }
    			}
			} else {
			    continue;
			}
			?>
			<div class="quote__segment">
				<div class="quote__info">
					<?php if(count($marketingAirlines) == 1):?>
					<img src="//www.gstatic.com/flights/airline_logos/70px/<?= $marketingAirlines[0]?>.png" alt="<?= $marketingAirlines[0]?>" class="quote__airline-logo">
					<?php else:?>
					<img src="/img/multiple_airlines.png" alt="<?= implode(', ',$marketingAirlines)?>" class="quote__airline-logo">
					<?php endif;?>
					<div class="quote__info-options">
						<div class="quote__duration"><?= SearchService::durationInMinutes($trip->qt_duration)?></div>
						<div class="quote__airline-name"><?= implode(', ',$airlineNames);?></div>
					</div>
				</div>
				<div class="quote__itinerary">
					<div class="quote__itinerary-col quote__itinerary-col--from">
						<div class="quote__datetime">
							<span class="quote__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->qs_departure_time),'h:mm a')?></span>
							<span class="quote__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->qs_departure_time),'MMM d')?></span>
						</div>
						<div class="quote__location">
							<div class="quote__airport">
								<span class="quote__city"><?= ($firstSegment->departureAirport)?$firstSegment->departureAirport->city:$firstSegment->qs_departure_airport_code?></span>
								<span class="quote__iata"><?= $firstSegment->qs_departure_airport_code?></span>
							</div>
						</div>
					</div>
					<div class="quote__itinerary-col quote__itinerary-col--to">
						<div class="quote__datetime">
							<span class="quote__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->qs_arrival_time),'h:mm a')?></span>
							<span class="quote__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->qs_arrival_time),'MMM d')?></span>
						</div>
						<div class="quote__location">
							<div class="quote__airport">
								<span class="quote__city"><?= ($lastSegment->arrivalAirport)?$lastSegment->arrivalAirport->city:$lastSegment->qs_arrival_airport_code?></span>
								<span class="quote__iata"><?= $lastSegment->qs_arrival_airport_code?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="quote__additional-info">
					<div class="quote__stops">
						<span class="quote__stop-quantity"><?= \Yii::t('search', '{n, plural, =0{Nonstop} one{# stop} other{# stops}}', ['n' => $stopCnt]);?></span>
					</div>
					<div class="quote__cabin"><?= implode(', ',$cabins)?></div>
				</div>
			</div>
			<?php endforeach;?>

		</div>
		<div class="quote__badges">
			<span class="quote__badge quote__badge--amenities <?php if(!$model->hasFreeBaggage):?>quote__badge--disabled<?php endif;?>" data-toggle="tooltip"
			 title="<?= ($model->freeBaggageInfo)?'Free baggage - '.$model->freeBaggageInfo:'No free baggage'?>"
			data-original-title="<?= ($model->freeBaggageInfo)?'Free baggage - '.$model->freeBaggageInfo:'No free baggage'?>">
				<i class="fa fa-suitcase"></i><span class="quote__badge-num"></span>
			</span>
			<span class="quote__badge <?php if($model->hasAirportChange):?>quote__badge--warning<?php else:?>quote__badge--disabled<?php endif;?>" data-toggle="tooltip"
			 title="<?= ($model->hasAirportChange)?'Airports Change':'No Airports Change'?>"
			  data-original-title="<?= ($model->hasAirportChange)?'Airports Change':'No Airports Change'?>">
				<i class="fa fa-exchange"></i>
			</span>
		</div>
		<div class="quote__actions">
			<?php \yii\widgets\Pjax::begin(['id' => 'pjax-quote_prices-'.$model->id, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
			<?= $this->render('_quote_prices', [
                    'quote' => $model
                ]); ?>
            <?php \yii\widgets\Pjax::end(); ?>
		</div>
	</div>
</div>