<?php
/**
 * @var $model ProductQuote
 */

use common\components\SearchService;
use modules\flight\models\FlightQuote;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$flightQuote = FlightQuote::findByProductQuoteId($model);

?>



<div class="quote quote--highlight" id="quote-<?=$model->pq_id?>">

    <?php if($flightQuote): ?>
	<?php $tripsInfo = []?>
	<?php foreach ($flightQuote->flightQuoteTrips as $trip):?>
		<?php
		$segments = $trip->flightQuoteSegments;
		$segmentsCnt = count($segments);
		$firstSegment = $segments[0];
		$lastSegment = $segments[$segmentsCnt-1];
		$tripsInfo[] = $firstSegment->fqs_departure_airport_iata.' → '.$lastSegment->fqs_arrival_airport_iata;
//		$tripsInfo[] = ($firstSegment->fqs_departure_airport_iata && $lastSegment->fqs_arrival_airport_iata)?
//			$firstSegment->fqs_departure_airport_iata.' → '.$lastSegment->fqs_arrival_airport_iata:
//			$firstSegment->fqs_departure_airport_iata.' → '.$lastSegment->fqs_arrival_airport_iata;
		?>
	<?php endforeach;?>
	<div class="quote__heading flex-wrap">
		<div class="quote__heading-left">
<!--			--><?php //if ($flightQuote->isOriginal()): ?>
<!--				<span class="label label-primary">--><?php //= FlightQuote::getTypeName($flightQuote->fq_type_id) ?><!--</span>-->
<!--			--><?php //elseif (in_array($model->pq_status_id, [ProductQuoteStatus::NEW , ProductQuoteStatus::, Quote::STATUS_OPENED])) : ?>
<!--				<div class="custom-checkbox">-->
<!--					<input class="quotes-uid" id="q--><?php //= $model->uid ?><!--" value="--><?php //= $model->uid ?><!--" data-id="--><?php //=$model->id?><!--" type="checkbox" name="quote[--><?php //= $model->uid ?><!--]">-->
<!--					<label for="q--><?php //= $model->uid ?><!--"></label>-->
<!--				</div>-->
<!--			--><?php //endif; ?>

			<?=$flightQuote->isAlternative() ? \yii\helpers\Html::tag('i', '', ['class' => 'fa fa-font', 'title' => 'Alternative quote']) : ''?>

			<?= ProductQuoteStatus::getStatusSpan($model)?>

<!--			<span class="quote__id">QUID: <strong>--><?php //= $model->pq_id ?><!--</strong></span>-->

            <?php if ($model->pq_clone_id): ?>
                <span class="badge badge-warning" style="padding-left: 5px">CLONE</span>
            <?php endif;?>

			<span class="quote__vc" title="Main Airline">
				<span class="quote__vc-logo">
                    <img src="//www.gstatic.com/flights/airline_logos/70px/<?= $flightQuote->fq_main_airline ?>.png" alt="" class="quote__vc-img">
                </span>

                <?php $airline = $flightQuote->mainAirline;
				if($airline) { echo \yii\helpers\Html::encode($airline->name); }
				?> &nbsp;[<strong><?= $flightQuote->fq_main_airline?></strong>]
            </span>

			<div class="quote__gds" title="GDS / <?php if (!empty($flightQuote->fq_gds_offer_id)): echo 'GDS Offer ID: ' . \yii\helpers\Html::encode($flightQuote->fq_gds_offer_id) . ' /'; endif; ?> PCC">
				<strong><?= SearchService::getGDSName($flightQuote->fq_gds)?></strong>
				<?php if (!empty($flightQuote->fq_gds_offer_id)): ?>
					<i class="fas fa-passport success"></i>
				<?php endif; ?>
				/ <i><?= $flightQuote->fq_gds_pcc?></i>
			</div>
			<span title="<?= !$flightQuote->createdByExpert() ? 'Agent' : 'Expert'?>: <?= \yii\helpers\Html::encode($flightQuote->getEmployeeName())?>">
                <?php echo !$flightQuote->createdByExpert() ? '<i class="fa fa-user text-info"></i>' : '<i class="fa fa-user-secret text-warning"></i>'; ?>
                <strong><?= $flightQuote->getEmployeeName() ?></strong>
            </span>
			<?php
			$ticketSegments = FlightQuoteHelper::getTicketSegments($flightQuote);
			if($ticketSegments):?>
				<span title="Separate Ticket">
                    <i class="fa fa-ticket warning"></i> (<?=count($ticketSegments)?>)
                </span>
			<?php endif; ?>

			<?php Pjax::begin(['id' => 'pjax-quote_estimation_profit-'.$flightQuote->fq_id, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
			<?php $priceData = FlightQuoteHelper::getPricesData($flightQuote); ?>

			<?php if($model->isApplied() && $model->pqProduct->prLead->final_profit !== null): ?>
				<button id="quote_profit_<?= $model->pq_id?>" data-toggle="popover" data-html="true" data-trigger="click" data-placement="top" data-container="body" title="Final Profit" class="popover-class quote__profit btn btn-info"
						data-content='<?= FlightQuoteHelper::getEstimationProfitText($priceData) ?>'>
					<?= '$'.FlightQuoteHelper::getFinalProfit($flightQuote) ?>
				</button>
			<?php else:?>

				<a id="quote_profit_<?= $model->pq_id?>" data-toggle="popover" data-html="true" data-trigger="click" data-placement="top" data-container="body" title="Estimation Profit" class="popover-class quote__profit"
				   data-content='<?= FlightQuoteHelper::getEstimationProfitText($priceData) ?>'>
                    <?= FlightQuoteHelper::getEstimationProfit($priceData) ?>$
				</a>
			<?php endif;?>


			<?php Pjax::end(); ?>
		</div>
		<div class="quote__heading-right">

			<div class="dropdown">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="fas fa-list-ul"></span>
					<span class="caret"></span>
				</button>
				<div class="dropdown-menu flight_quote_drop_down_menu">

                    <span>
                        <?= Html::a('<i class="fa fa-search"></i> Details', null, [
                            'class' => 'btn-flight-quote-details dropdown-item',
                            'data-id' => $model->pq_id,
                            'data-title' => implode(', ',$tripsInfo),
                            'data-url' => Url::to(['/flight/flight-quote/ajax-quote-details', 'id' => $model->pq_id]),
                            //'data-target' => '#quote_detail_'.$model->uid,
                            'title' => 'Details'
                        ]) ?>
                    </span>

                    <?= Html::a('<i class="fa fa-list"></i> Status log', null, [
                        'class' => 'dropdown-item text-success btn-product-quote-status-log',
                        'data-url' => \yii\helpers\Url::to(['/product/product-quote-status-log/show', 'gid' => $flightQuote->fqProductQuote->pq_gid]),
                        'data-gid' => $flightQuote->fqProductQuote->pq_gid,
                        'title' => 'View status log'
                    ]) ?>

                    <?= Html::a('<i class="fa fa-list"></i> Clone', null, [
                        'class' => 'dropdown-item btn-clone-product-quote',
                        'data-product-quote-id' => $flightQuote->fq_product_quote_id,
                        'data-flight-quote-id' => $flightQuote->fq_id,
                        'data-product-id' => $flightQuote->fqProductQuote->pq_product_id,
                    ]) ?>
<!---->
<!--					--><?php //= Html::a('<i class="fa fa-list-alt"></i> Reserv. dump', null, [
//						'class' => 'btn-reservation-dump dropdown-item',
//						'title' => 'Reservation Dump quote: ' . $model->uid,
//						'data-content' => \yii\helpers\Html::encode($model->reservation_dump)
//					]) ?>
<!---->
<!---->
<!--					--><?php //if (!$appliedQuote): ?>
<!---->
<!--						--><?php // echo Html::a('<i class="fa fa-copy"></i> Clone', null, [
//							'class' => 'clone-quote-by-uid-self dropdown-item',
//							'data-uid' => $model->uid,
//							// 'data-url' => Url::to(['quote/clone', 'leadId' => $leadId, 'qId' => $model->id]),
//							'title' => 'Clone'
//						]);
//						?>
<!---->
<!--					--><?php //endif; ?>
<!---->
<!--					--><?php //if(!$model->isDeclined()):?>
<!---->
						<?php  echo Html::a('<i class="fa fa-eye"></i> Checkout Page', $model->getCheckoutUrlPage(), [
							'class' => 'dropdown-item',
							'target'    => '_blank',
							'title'     => 'View checkout',
							'data-pjax' => 0
						]);
						?>
<!---->
<!--					--><?php //endif; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="quote__wrapper">
        <div class="row" style="width: 100%; margin: 0;">
            <div class="col-md-10">
                <div class="quote__trip">
                    <?php
                    $needRecheck = false;
                    $firstSegment = null;
                    $lastSegment = null;
                    ?>
                    <?php foreach ($flightQuote->flightQuoteTrips as $trip):?>
                        <?php

                        $segments = $trip->flightQuoteSegments;
                        if( $segments ) {
                            $segmentsCnt = count($segments);
                            $stopCnt = $segmentsCnt - 1;
                            $firstSegment = $segments[0];
                            $lastSegment = $segments[$segmentsCnt-1];
        //					$lastSegment = end($segments);
                            $cabins = [];
                            $marketingAirlines = [];
                            $airlineNames = [];
                            foreach ($segments as $segment){
                                if(!in_array(SearchService::getCabin($segment->fqs_cabin_class), $cabins, false)){
                                    $cabins[] = SearchService::getCabin($segment->fqs_cabin_class);
                                }
                                if (isset($segment->fqs_recheck_baggage) && $segment->fqs_recheck_baggage){
                                    $needRecheck = true;
                                }
                                if(isset($segment->fqs_stop) && $segment->fqs_stop > 0){
                                    $stopCnt += $segment->fqs_stop;
                                }
                                if(!in_array($segment->fqs_marketing_airline, $marketingAirlines)){
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
                                <?php if(count($marketingAirlines) == 1):?>
                                    <img src="//www.gstatic.com/flights/airline_logos/70px/<?= $marketingAirlines[0]?>.png" alt="<?= $marketingAirlines[0]?>" class="quote__airline-logo">
                                <?php else:?>
                                    <img src="/img/multiple_airlines.png" alt="<?= implode(', ',$marketingAirlines)?>" class="quote__airline-logo">
                                <?php endif;?>
                                <div class="quote__info-options">
                                    <div class="quote__duration"><?= SearchService::durationInMinutes($trip->fqt_duration)?></div>
                                    <div class="quote__airline-name"><?= implode(', ',$airlineNames);?></div>
                                </div>
                            </div>
                            <div class="quote__itinerary">
                                <div class="quote__itinerary-col quote__itinerary-col--from">
                                    <div class="quote__datetime">
                                        <span class="quote__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->fqs_departure_dt),'h:mm a')?></span>
                                        <span class="quote__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->fqs_departure_dt),'MMM d')?></span>
                                    </div>
                                    <div class="quote__location">
                                        <div class="quote__airport">
                                            <span class="quote__city"><?= ($firstSegment->fqs_departure_airport_iata)?$firstSegment->departureAirport->city:$firstSegment->fqs_departure_airport_iata?></span>
                                            <span class="quote__iata"><?= $firstSegment->fqs_departure_airport_iata?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="quote__itinerary-col quote__itinerary-col--to">
                                    <div class="quote__datetime">
                                        <span class="quote__time"><?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->fqs_arrival_dt),'h:mm a')?></span>
                                        <span class="quote__date"><?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->fqs_arrival_dt),'MMM d')?></span>
                                    </div>
                                    <div class="quote__location">
                                        <div class="quote__airport">
                                            <span class="quote__city"><?= ($lastSegment->arrivalAirport)?$lastSegment->arrivalAirport->city:$lastSegment->fqs_arrival_airport_iata?></span>
                                            <span class="quote__iata"><?= $lastSegment->fqs_arrival_airport_iata?></span>
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
            </div>
            <div class="col-md-2">
                <div class="d-flex flex-column align-items-center justify-content-start" style="width: 100%; height: 100%;">
                    <?php if(!$model->isDeclined()): ?>
                        <span style="margin: 20px 0;" class="dropdown dropdown-offer-menu" data-product-quote-id="<?=($model->pq_id)?>" data-lead-id="<?=($model->pqProduct->pr_lead_id)?>" data-url="<?=\yii\helpers\Url::to(['/offer/offer/list-menu-ajax'])?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="far fa-handshake"></i> Offers</a>
                            <span class="dropdown-menu" role="menu">
                                <?php // ajax loaded content ?>
                            </span>
                        </span>

                        <span class="dropdown dropdown-order-menu" data-product-quote-id="<?=($model->pq_id)?>" data-lead-id="<?=($model->pqProduct->pr_lead_id)?>" data-url="<?=\yii\helpers\Url::to(['/order/order/list-menu-ajax'])?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fas fa-money-check-alt"></i> Orders</a>
                            <span class="dropdown-menu" role="menu">
                                <?php // ajax loaded content ?>
                            </span>
                        </span>
                    <?php endif; ?>

                    <span style="margin: 20px 0;" class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i> Options</a>
                        <div class="dropdown-menu" role="menu">
                            <h6 class="dropdown-header">Quote Q<?=($model->pq_id)?></h6>
							<?= Html::a('<i class="fa fa-plus-circle"></i> Add option', null, [
								'class' => 'dropdown-item text-success btn-add-product-quote-option',
								//'data-product-quote-id' => $model->hq_product_quote_id,
								'data-url' => \yii\helpers\Url::to(['/product/product-quote-option/create-ajax', 'id' => $model->pq_id]),
								//'data-product-id' => $model->hqProductQuote->pq_product_id,
							]) ?>

							<?= Html::a('<i class="fa fa-plus-circle"></i> Status log', null, [
								'class' => 'dropdown-item text-success btn-product-quote-status-log',
								'data-url' => \yii\helpers\Url::to(['/product/product-quote-status-log/show', 'gid' => $model->pq_gid]),
								'data-gid' => $model->pq_gid,
							]) ?>

                            <div class="dropdown-divider"></div>
							<?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete quote', null, [
								'class' => 'dropdown-item text-danger btn-delete-product-quote',
								'data-product-quote-id' => $model->pq_id,
								'data-hotel-quote-id' => $flightQuote->fq_id,
								'data-product-id' => $model->pq_product_id,
							]) ?>
                        </div>
                    </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                <div class="quote__actions">
                    <?php Pjax::begin(['id' => 'pjax-quote_prices-'.$flightQuote->fq_id, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
                    <?= $this->render('_quote_prices', [
                        'quote' => $model,
                        'flightQuote' => $flightQuote,
                        'priceData' => $priceData
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
            <div class="col-md-2">
                <div class="quote__badges d-flex" style="width: 100%; height: 100%; padding: 15px 25px;">
					<?php $baggageInfo = FlightQuoteHelper::getBaggageInfo($flightQuote); ?>
                    <span class="quote__badge quote__badge--amenities <?php if(!$baggageInfo['hasFreeBaggage']):?>quote__badge--disabled<?php endif;?>" data-toggle="tooltip"
                          title="<?= ($baggageInfo['freeBaggageInfo'])?'Free baggage - '.$baggageInfo['freeBaggageInfo']:'No free baggage'?>"
                          data-original-title="<?= ($baggageInfo['freeBaggageInfo'])?'Free baggage - '.$baggageInfo['freeBaggageInfo']:'No free baggage'?>">
                        <i class="fa fa-suitcase"></i><span class="quote__badge-num"></span>
                    </span>

                    <span class="quote__badge quote__badge--warning <?php if(!$needRecheck):?>quote__badge--disabled<?php endif;?>" data-toggle="tooltip"
                          title="<?= ($needRecheck)?'Bag re-check may be required' : 'Bag re-check not required'?>"
                          data-original-title="<?= ($needRecheck)?'Bag re-check may be required' : 'Bag re-check not required'?>">
                        <i class="fa fa-warning"></i>
                    </span>

					<?php $hasAirportChange = FlightQuoteHelper::hasAirportChange($flightQuote); ?>
                    <span class="quote__badge <?php if($hasAirportChange):?>quote__badge--warning<?php else:?>quote__badge--disabled<?php endif;?>" data-toggle="tooltip"
                          title="<?= ($hasAirportChange) ? 'Airports Change' : 'No Airports Change'?>"
                          data-original-title="<?= ($hasAirportChange) ? 'Airports Change' : 'No Airports Change'?>">
                        <i class="fa fa-exchange"></i>
                    </span>
                </div>
            </div>
        </div>
	</div>
    <?php else: ?>
        <div class="d-flex justify-content-center align-items-center">
            <p style="margin: 20px 0;">Not found quote data</p>
        </div>
    <?php endif; ?>
</div>
