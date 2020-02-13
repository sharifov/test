<?php

use modules\flight\models\Flight;
use modules\flight\models\forms\ItineraryEditForm;
use modules\flight\src\helpers\FlightFormatHelper;
use modules\flight\src\helpers\FlightSegmentHelper;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var  $itineraryForm ItineraryEditForm
 */
?>

<div class="col-sm-12">
	<div class="">
		<div class="request-overview" style="">
<!--			<div style="letter-spacing: 0.8px; border-bottom: 1px dotted rgb(165, 177, 197); padding-bottom: 13px;"-->
<!--				 class="row-flex row-flex-justify">-->
<!--				<span style="font-weight: 600; font-size: 16px;">Flight Request</span>-->
<!--				<span style="font-size: 13px; padding: 0 7px">-->
<!--                        --><?php
//						switch ($itineraryForm->tripType) {
//							case Flight::TRIP_TYPE_ONE_WAY : $iconClass = 'fa fa-long-arrow-right';
//								break;
//							case Flight::TRIP_TYPE_ROUND_TRIP : $iconClass = 'fa fa-exchange';
//								break;
//							case Flight::TRIP_TYPE_MULTI_DESTINATION : $iconClass = 'fa fa-random';
//								break;
//							default: $iconClass = '';
//						}
//						?>
<!--                        <i class="--><?//=$iconClass?><!-- text-success" aria-hidden="true"></i>-->
<!--                        --><?//= FlightFormatHelper::tripTypeName($itineraryForm->tripType) ?><!-- •-->
<!--                        <b>--><?//= FlightFormatHelper::cabinName($itineraryForm->cabin) ?><!--</b> •-->
<!--                        --><?//= (int)$itineraryForm->adults + (int)$itineraryForm->children + (int)$itineraryForm->infants ?><!-- pax</span>-->
<!--				<span>-->
<!--                        --><?php //if ($itineraryForm->adults): ?>
<!--							<span><strong class="label label-success"-->
<!--										  style="margin-left: 7px;">--><?//= $itineraryForm->adults ?><!--</strong> ADT</span>-->
<!--						--><?php //endif; ?>
<!--					--><?php //if ($itineraryForm->children): ?>
<!--						<span><strong class="label label-success"-->
<!--									  style="margin-left: 7px;">--><?//= $itineraryForm->children ?><!--</strong> CHD</span>-->
<!--					--><?php //endif; ?>
<!--					--><?php //if ($itineraryForm->infants): ?>
<!--						<span><strong class="label label-success"-->
<!--									  style="margin-left: 7px;">--><?//= $itineraryForm->infants ?><!--</strong> INF</span>-->
<!--					--><?php //endif; ?>
<!--                    </span>-->
<!--			</div>-->
			<div class="">
				<div>
					<table class="table">
						<tr>
							<th>Nr</th>
							<th>Origin</th>
							<th></th>
							<th>Destination</th>
							<th>Departure</th>
							<th>Flex</th>
						</tr>
						<?php foreach ($itineraryForm->segments as $keySegment => $segment): ?>
							<tr>
								<td>
									<?= $keySegment + 1 ?>.
								</td>
								<td>

									<b><?= Html::encode($segment->fs_origin_iata_label) ?></b>

								</td>
								<td>
									<i class="fa fa-long-arrow-right"></i>
								</td>
								<td>

									<b><?= Html::encode($segment->fs_destination_iata_label) ?></b>

								</td>
								<td style="<?=time() > strtotime($segment->fs_departure_date) ? 'color: red;' : ''?>">
									<i class="fa fa-calendar"></i> <?= date('d-M-Y', strtotime($segment->fs_departure_date)) ?>
								</td>
								<td>
									<?= $segment->fs_flex_days ? '<strong class="text-success">' . FlightSegmentHelper::flexibilityTypeName($segment->fs_flex_type_id) . ' ' . $segment->fs_flex_days . ' days</strong>' : 'exact' ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>