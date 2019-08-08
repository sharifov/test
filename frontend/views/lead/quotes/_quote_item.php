<?php
/**
 * @var $model Quote
 * @var $appliedQuote obj
 * @var $leadId int
 * @var $leadForm \frontend\models\LeadForm
 * @var $isManager bool
 */

use common\models\Quote;
use common\models\Airline;
use common\components\SearchService;
use yii\bootstrap\Html;
use yii\helpers\Url;
?>
<div class="quote quote--highlight" id="quote-<?=$model->uid?>">

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
	<div class="quote__heading" <?=$model->isAlternative() ? 'style="background-color: #fdffe5;"' : ''?>>
		<div class="quote__heading-left">
			<?php if (($leadForm->mode !== $leadForm::VIEW_MODE || $isManager) && in_array($model->status, [Quote::STATUS_CREATED, Quote::STATUS_SEND, Quote::STATUS_OPENED])) : ?>
			<div class="custom-checkbox">
				<input class="quotes-uid" id="q<?= $model->uid ?>" value="<?= $model->uid ?>" data-id="<?=$model->id?>" type="checkbox" name="quote[<?= $model->uid ?>]">
                <label for="q<?= $model->uid ?>"></label>
			</div>
            <?php endif; ?>

            <?=$model->isAlternative() ? \yii\helpers\Html::tag('i', '', ['class' => 'fa fa-font', 'title' => 'Alternative quote']) : ''?>

            <?= $model->getStatusSpan()?>

			<span class="quote__id">QUID: <strong><?= $model->uid ?></strong></span>
			<span class="quote__vc" title="Main Airline">
				<span class="quote__vc-logo">
                    <img src="//www.gstatic.com/flights/airline_logos/70px/<?= $model->main_airline_code?>.png" alt="" class="quote__vc-img">
                </span>

                <?php $airline = $model->mainAirline;
                    if($airline) { echo \yii\helpers\Html::encode($airline->name); }
                ?> &nbsp;[<strong><?= $model->main_airline_code?></strong>]
            </span>

			<div class="quote__gds" title="GDS / PCC">
				<strong><?= SearchService::getGDSName($model->gds)?></strong> / <i><?= $model->pcc?></i>
			</div>
			<span title="<?= $model->created_by_seller ? 'Agent' : 'Expert'?>: <?= \yii\helpers\Html::encode($model->employee_name)?>">
                <?php echo $model->created_by_seller ? '<i class="fa fa-user text-info"></i>' : '<i class="fa fa-user-secret text-warning"></i>'; ?>
                <strong><?= $model->employee_name?></strong>
            </span>
			<?php \yii\widgets\Pjax::begin(['id' => 'pjax-quote_estimation_profit-'.$model->id, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
			<?php $priceData = $model->getPricesData();	?>

			<?php if($model->status == $model::STATUS_APPLIED && $model->lead->final_profit !== null):?>
                <button id="quote_profit_<?= $model->id?>" data-toggle="popover" data-html="true" data-trigger="click" data-placement="top" data-container="body" title="Final Profit" class="popover-class quote__profit btn btn-info"
                 data-content='<?= $model->getEstimationProfitText();?>'>
                    <?= '$'.$model->getFinalProfit();?>
                </button>
			<?php else:?>

                <a id="quote_profit_<?= $model->id?>" data-toggle="popover" data-html="true" data-trigger="click" data-placement="top" data-container="body" title="Estimation Profit" class="popover-class quote__profit"
			 data-content='<?= $model->getEstimationProfitText();?>'>
                    <?php if(isset($priceData['total'])):?>
                        <?=number_format($model->getEstimationProfit(),2);?>$
                    <?php endif;?>
                </a>
			<?php endif;?>

            <?php \yii\widgets\Pjax::end(); ?>
		</div>
		<div class="quote__heading-right">


            <div class="btn-group" role="group" aria-label="...">

                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="fa fa-list-ul"></span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <?/*<li>
                            <?= Html::a('<i class="fa fa-eye"></i> Details', null, [
                                'class' => 'quote_details__btn',
                                'data-title' => implode(', ',$tripsInfo),
                                'data-target' => '#quote_detail_'.$model->uid,
                                'title' => 'Details'
                            ]) ?>
                        </li>*/?>

                        <li>
                            <?= Html::a('<i class="fa fa-search"></i> Details', null, [
                                'class' => 'btn-quote-details',
                                'data-id' => $model->id,
                                'data-title' => implode(', ',$tripsInfo),
                                'data-url' => Url::to(['quotes/ajax-details', 'id' => $model->id]),
                                //'data-target' => '#quote_detail_'.$model->uid,
                                'title' => 'Details'
                            ]) ?>
                        </li>


                        <li>
                            <?= Html::a('<i class="fa fa-list"></i> Status logs', null, [
                                'class' => 'view-status-log sl-quote__status-log',
                                'data-id' => $model->id,
                                'title' => 'View status log'
                            ]) ?>
                        </li>
                        <li>
                            <?= Html::a('<i title="" class="fa fa-list-alt"></i> Reserv. dump', null, [
                                'class' => 'popover-class',
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
                        </li>

                        <?php if ($appliedQuote === null): ?>
                            <li>
                                <?php  echo Html::a('<i class="fa fa-copy"></i> Clone', null, [
                                    'class' => 'add-clone-alt-quote',
                                    'data-uid' => $model->uid,
                                    'data-url' => Url::to(['quote/clone', 'leadId' => $leadId, 'qId' => $model->id]),
                                    'title' => 'Clone'
                                ]);
                                ?>
                            </li>
                        <?php endif; ?>

                        <?php if($model->status != Quote::STATUS_DECLINED):?>
                        <li>
                            <?php  echo Html::a('<i class="fa fa-eye"></i> Checkout Page', $model->getCheckoutUrlPage(), [
                                'target'    => '_blank',
                                'title'     => 'View checkout',
                                'data-pjax' => 0
                            ]);
                            ?>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <?/*

			<div class="btn-group">
				<?php if ($appliedQuote === null) {
                    echo Html::button('<i class="fa fa-copy"></i>', [
                        'class' => 'btn btn-primary add-clone-alt-quote',
                        'data-uid' => $model->uid,
                        'data-url' => Url::to(['quote/clone', 'leadId' => $leadId, 'qId' => $model->id]),
                        'title' => 'Clone'
                    ]);
                } ?>
				<?= Html::button('<i class="fa fa-list"></i>',[
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
            */?>

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

                        $airlineNames[] = $segment->marketingAirline ? $segment->marketingAirline->name : $segment->qs_marketing_airline;

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