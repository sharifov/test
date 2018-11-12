<?php
use common\components\SearchService;
use yii\bootstrap\Html;

/**
 * @var $resultKey int
 * @var $result []
 * @var $airlines []
 * @var $locations []
 */

?>
<?php $totalDuration = []; $stops = [];
foreach ($result['trips'] as $trip){
    if(isset($trip['duration'])){
        $totalDuration[] = $trip['duration'];
    }
    $stopCnt = count($trip['segments']) - 1;
    foreach ($trip['segments'] as $segment){
        if(isset($segment['stop']) && $segment['stop'] > 0){
            $stopCnt += $segment['stop'];
        }
    }
    $stops[] = $stopCnt;
}
?>
<div class="quote search-result__quote" data-price="<?= $result['prices']['totalPrice']?>"
data-durationmax="<?= max($totalDuration)?>" data-duration="<?= json_encode($totalDuration)?>"
data-stop="<?= json_encode($stops)?>"
data-airline="<?= $result['validatingCarrier']?>">
	<div class="quote__heading">
		<div class="quote__heading-left">
			<span class="quote__vc">
				<span class="quote__vc-logo">
					<img src="//www.gstatic.com/flights/airline_logos/70px/<?= $result['validatingCarrier']?>.png" alt="<?= $result['validatingCarrier']?>" class="quote__vc-img">
				</span>
				<span class="quote__vc-name"><?= (!isset($airlines[$result['validatingCarrier']]))?:$airlines[$result['validatingCarrier']];?><strong> [<?= $result['validatingCarrier']?>]</strong></span>
			</span>
			<div class="quote__gds">
				GDS: <strong><?= SearchService::getGDSName($result['gds'])?></strong>
			</div>
			<div class="quote__pcc">
				PCC: <strong><?= $result['pcc']?></strong>
			</div>
			<div class="quote__seats">
				Seats left: <strong class="text-danger"><i class="fa fa-fire"></i> <?= $result['maxSeats']?></strong>
			</div>
		</div>
		<div class="quote__heading-right text-success">
			<strong class="quote__quote-price">$<?= $result['prices']['totalPrice']?></strong>
		</div>
	</div>
	<div class="quote__wrapper">
		<div class="quote__trip">
			<?php $tripsInfo = []?>
			<?php foreach ($result['trips'] as $trip):?>
			<?php
			$segmentsCnt = count($trip['segments']);
			$stopCnt = $segmentsCnt - 1;
			$firstSegment = $trip['segments'][0];
			$lastSegment = $trip['segments'][$segmentsCnt-1];
			$tripsInfo[] = ((!isset($locations[$firstSegment['departureAirportCode']]))?:$locations[$firstSegment['departureAirportCode']]['city']).' → '.((!isset($locations[$lastSegment['arrivalAirportCode']]))?:$locations[$lastSegment['arrivalAirportCode']]['city']);
			$cabins = [];
			$hasFreeBaggage = false;
			$hasAirportChange = false;
			$freeBaggageInfo = '';
			$previousSegment = null;
			$marketingAirlines = [];
			$airlineNames = [];
            foreach ($trip['segments'] as $segment){
                if(!in_array(SearchService::getCabin($segment['cabin']), $cabins)){
                    $cabins[] = SearchService::getCabin($segment['cabin']);
                }
                if(isset($segment['stop']) && $segment['stop'] > 0){
                    $stopCnt += $segment['stop'];
                }
                if(isset($segment['baggage']) && $hasFreeBaggage == false){
                    foreach ($segment['baggage'] as $baggage){
                        if(isset($baggage['allowPieces']) && $baggage['allowPieces'] > 0){
                            $freeBaggageInfo = 'Free baggage - '.$baggage['allowPieces'].'pcs';
                        }elseif(isset($baggage['allowWeight'])){
                            $freeBaggageInfo = 'Free baggage - '.$baggage['allowWeight'].$baggage['allowUnit'];
                        }
                        if(!empty($freeBaggageInfo)){
                            $hasFreeBaggage = true;
                        }
                    }
                }
                if($previousSegment !== null && $segment['departureAirportCode'] != $previousSegment['arrivalAirportCode']){
                    $hasAirportChange = true;
                }
                if(!in_array($segment['marketingAirline'], $marketingAirlines)){
                    $marketingAirlines[] = $segment['marketingAirline'];
                    if(isset($airlines[$segment['marketingAirline']])){
                        $airlineNames[] =  $airlines[$segment['marketingAirline']];
                    }
                }
                $previousSegment = $segment;
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
						<div class="quote__duration"><?= SearchService::durationInMinutes($trip['duration'])?></div>
						<div class="quote__airline-name"><?= implode(', ',$airlineNames);?></div>
					</div>
				</div>
				<div class="quote__itinerary">
					<div class="quote__itinerary-col quote__itinerary-col--from">
						<div class="quote__datetime">
							<span class="quote__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment['departureTime']),'h:mm a')?></span>
							<span class="quote__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment['departureTime']),'MMM d')?></span>
						</div>
						<div class="quote__location">
							<div class="quote__airport">
								<span class="quote__city"><?= (!isset($locations[$firstSegment['departureAirportCode']]))?:$locations[$firstSegment['departureAirportCode']]['city'];?></span>
								<span class="quote__iata"><?= $firstSegment['departureAirportCode']?></span>
							</div>
						</div>
					</div>
					<div class="quote__itinerary-col quote__itinerary-col--to">
						<div class="quote__datetime">
							<span class="quote__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment['arrivalTime']),'h:mm a')?></span>
							<span class="quote__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment['arrivalTime']),'MMM d')?></span>
						</div>
						<div class="quote__location">
							<div class="quote__airport">
								<span class="quote__city"><?= (!isset($locations[$lastSegment['arrivalAirportCode']]))?:$locations[$lastSegment['arrivalAirportCode']]['city'];?></span>
								<span class="quote__iata"><?= $lastSegment['arrivalAirportCode']?></span>
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
			<span class="quote__badge quote__badge--amenities <?php if(!$hasFreeBaggage):?>quote__badge--disabled<?php endif;?>" data-toggle="tooltip"
			title="<?= ($freeBaggageInfo)?$freeBaggageInfo:'No free baggage'?>" data-original-title="<?= ($freeBaggageInfo)?$freeBaggageInfo:'No free baggage'?>">
				<i class="fa fa-suitcase"></i><span class="quote__badge-num"></span>
			</span>
			<span class="quote__badge <?php if($hasAirportChange):?>quote__badge--warning<?php else:?>quote__badge--disabled<?php endif;?>"
				data-toggle="tooltip" title="<?= ($hasAirportChange)?'Airports Change':'No Airports Change'?>" data-original-title="<?= ($hasAirportChange)?'Airports Change':'No Airports Change'?>">
				<i class="fa fa-exchange"></i>
			</span>
		</div>
		<div class="quote__actions">
			<table class="table table-striped table-prices">
				<thead>
					<tr>
						<th>Pax</th>
						<th>Q</th>
						<th>NP, $</th>
					</tr>
				</thead>
				<tbody>
					<?php $paxTotal = 0;?>
					<?php foreach ($result['passengers'] as $paxCode => $pax):?>
					<tr><?php $paxTotal += $pax['cnt'];?>
						<th><?= $paxCode?></th>
						<td>x <?= $pax['cnt']?></td>
						<td><?= $pax['price']?></td>
					</tr>
					<?php endforeach;?>
				</tbody>
				<tfoot>
					<tr>
						<th>Total</th>
						<td><?= $paxTotal?></td>
						<td><?= $result['prices']['totalPrice']?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<div class="quote__details" id="result_<?= $resultKey?>" style="display:none;">
       <div class="text-right">
       	<?= Html::button('<i class="fa fa-check"></i>&nbsp; <span>Select</span>', [
                 'class' => 'btn btn-success create_quote__btn',
		         'data-title' => implode(', ',$tripsInfo),
                'data-key' => $result['key'],
            ]) ?>
        </div>
		<div class="trip">
            <div class="trip__item">
                <!-- Depart -->
                <?php foreach ($result['trips'] as $tripKey => $trip):?>
                <div class="trip__leg">
                    <h4 class="trip__subtitle">
                        <span class="trip__leg-type"><?php if(count($result['trips']) < 3 && $tripKey == 0):?>Depart<?php elseif(count($result['trips']) < 3 && $tripKey > 0):?>Return<?php else:?><?= ($tripKey+1);?> Trip<?php endif?></span>
                        <span class="trip__leg-date"><?= Yii::$app->formatter_search->asDatetime(strtotime($trip['segments'][0]['departureTime']),'EEE d MMM')?></span>
                    </h4>
                    <div class="trip__card">
                        <div class="trip__details trip-detailed" id="flight-leg-1">
                            <!--Segment1-->
                            <?php foreach ($trip['segments'] as $key => $segment):?>
                            <?php if($key > 0):?>
                            <?php $prevSegment = $trip['segments'][$key-1];?>
                            <div class="trip-detailed__layover">
                				<span class="trip-detailed__layover-location">Layover in <?= (!isset($locations[$segment['departureAirportCode']]))?:$locations[$segment['departureAirportCode']]['city'];?> (<?= $segment['departureAirportCode']?>)</span>
                                <span class="trip-detailed__layover-duration"><?= SearchService::getLayoverDuration($prevSegment['arrivalTime'],$segment['departureTime'])?></span>
                            </div>
                            <?php endif;?>
                            <div class="trip-detailed__segment segment">
                                <div class="segment__wrapper">
                                    <div class="segment__options">
                                        <img src="//www.gstatic.com/flights/airline_logos/70px/<?= $segment['marketingAirline']?>.png" alt="<?= $segment['marketingAirline']?>" class="segment__airline-logo">
                                        <div class="segment__cabin-xs"><?= SearchService::getCabin($segment['cabin'])?></div>
                                        <div class="segment__airline"><?= (!isset($airlines[$segment['marketingAirline']]))?:$airlines[$segment['marketingAirline']];?></div>
                                        <div class="segment__flight-nr">Flight <?= $segment['marketingAirline']?> <?= $segment['flightNumber']?></div>
                                    </div>

                                    <div class="segment__location segment__location--from">
                                        <span class="segment__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment['departureTime']),'h:mm a')?></span>
                                        <span class="segment__airport"><?= (!isset($locations[$segment['departureAirportCode']]))?:$locations[$segment['departureAirportCode']]['name'];?> (<?= $segment['departureAirportCode']?>)</span>
                                        <span class="segment__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment['departureTime']),'EEEE, MMM d')?></span>
                                    </div>

                                    <div class="segment__location segment__location--to">
                                        <span class="segment__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment['arrivalTime']),'h:mm a')?></span>
                                        <span class="segment__airport"><?= (!isset($locations[$segment['arrivalAirportCode']]))?:$locations[$segment['arrivalAirportCode']]['name'];?> (<?= $segment['arrivalAirportCode']?>)</span>
                                        <span class="segment__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($segment['arrivalTime']),'EEEE, MMM d')?></span>
                                    </div>

                                    <div class="segment__duration-wrapper">
                                        <div class="segment__duration-time"><?= SearchService::durationInMinutes($segment['duration'])?></div>
                                        <div class="segment__cabin"><?= SearchService::getCabin($segment['cabin'])?></div>
                                    </div>
                                </div>
                                <div class="segment__note">
                                	<?php if($segment['operatingAirline'] != $segment['marketingAirline']):?>Operated by <?= (!isset($airlines[$segment['operatingAirline']]))?:$airlines[$segment['operatingAirline']];?>.<?php endif;?>
                                	<?php if(isset($segment['baggage'])):?>
                                    	<span class="badge badge-light"><i class="fa fa-suitcase"></i>&nbsp;
                                    	<?php foreach ($segment['baggage'] as $baggage):?>
                                        	<?php if(isset($baggage['allowPieces'])):?>
                                        		<?= \Yii::t('search', '{n, plural, =0{no baggage} one{# piece} other{# pieces}}', ['n' => $baggage['allowPieces']]);?>
                                        	<?php elseif(isset($baggage['allowWeight'])):?>
                                        		<?= $baggage['allowWeight'].$baggage['allowUnit']?>
                                        	<?php endif;?>
                                    	<?php break; endforeach;?>
                                    	</span>
                                	<?php endif;?>
                                	<?php if(isset($segment['meal'])):?><span class="badge badge-light" title="<?= $segment['meal']?>"><i class="fa fa-cutlery"></i></span><?php endif;?>
                                	<?php if(isset($segment['stop']) && $segment['stop'] > 0):?>
                                		<div class="text-danger"><i class="fa fa-warning"></i> <?= \Yii::t('search', '{n, plural, =0{no technical stops} one{# technical stop} other{# technical stops}}', ['n' => $segment['stop']]);?></div>
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
		<div class="quote__footer-left"></div>
		<div class="quote__footer-right">
			 <?= Html::button('<i class="fa fa-eye"></i>&nbsp; <span>Details</span>', [
                 'class' => 'btn btn-primary search_details__btn',
			     'data-title' => implode(', ',$tripsInfo),
			     'data-target' => '#result_'.$resultKey,
                ]) ?>
            <?= Html::button('<i class="fa fa-check"></i>&nbsp; <span>Select</span>', [
                 'class' => 'btn btn-success create_quote__btn',
		         'data-title' => implode(', ',$tripsInfo),
                'data-key' => $result['key'],
            ]) ?>
		</div>
	</div>
</div>