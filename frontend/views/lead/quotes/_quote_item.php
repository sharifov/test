<?php

/**
 * @var $model Quote
 * @var $appliedQuote bool
 * @var $leadId int
 * @var $leadForm LeadForm
 * @var $isManager bool
 * @var $index int
 */

use common\models\Currency;
use common\models\Quote;
use common\models\Airline;
use common\components\SearchService;
use frontend\helpers\QuoteHelper;
use frontend\models\LeadForm;
use src\helpers\app\AppHelper;
use src\helpers\quote\ImageHelper;
use src\services\quote\quotePriceService\ClientQuotePriceService;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$user = Yii::$app->user->identity;
$showGdsOfferId = ($user->isAdmin() || $user->isSuperAdmin() || $user->isQa());
$airlineName = $model->mainAirline ? $model->mainAirline->name : '';
$currency = empty($model->q_client_currency) ? Currency::getDefaultCurrencyCode() : $model->q_client_currency;


if ($model->isClientCurrencyDefault()) {
    $priceData = $model->getPricesData();
} else {
    try {
        $priceData = (new ClientQuotePriceService($model))->getClientPricesData();
    } catch (\Throwable $throwable) {
        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['quoteId' => $model->id]);
        \Yii::error($message, 'QuotePrices:PriceData:Throwable');
        $priceData = $model->getPricesData();
        $currency = Currency::getDefaultCurrencyCode();
    }
}

?>
<div class="quote quote--highlight" id="quote-<?=$model->uid?>">
    <?php $tripsInfo = []?>
    <?php foreach ($model->quoteTrips as $trip) :?>
        <?php
        $segments = $trip->quoteSegments;
        $segmentsCnt = count($segments);
        $firstSegment = $segments[0];
        $lastSegment = $segments[$segmentsCnt - 1];
        $tripsInfo[] = ($firstSegment->departureAirport && $lastSegment->arrivalAirport) ?
                    $firstSegment->departureAirport->city . ' → ' . $lastSegment->arrivalAirport->city :
                    $firstSegment->qs_departure_airport_code . ' → ' . $lastSegment->qs_arrival_airport_code;
        ?>
    <?php endforeach;?>
    <div class="quote__heading" <?=$model->isAlternative() ? 'style="background-color: #fdffe5;"' : ''?>>
        <div class="quote__heading-left">
            <span class="quote__id">
                <span class="badge badge-white"><?= ($index + 1) ?></span>
            </span>

            <?php if ($model->isOriginal()) : ?>
                <span class="label label-primary"><?= Quote::getTypeName($model->type_id) ?></span>
            <?php elseif (($leadForm->mode !== $leadForm::VIEW_MODE || $isManager) && in_array($model->status, [Quote::STATUS_CREATED, Quote::STATUS_SENT, Quote::STATUS_OPENED])) : ?>
                <div class="custom-checkbox">
                    <input class="quotes-uid" id="q<?= $model->uid ?>" value="<?= $model->uid ?>" data-id="<?=$model->id?>" type="checkbox" name="quote[<?= $model->uid ?>]">
                    <label for="q<?= $model->uid ?>"></label>
                </div>
            <?php endif; ?>

            <?=$model->isAlternative() ? Html::tag(
            'i',
            '',
            ['class' => 'fa fa-font', 'title' => 'Alternative quote']
        ) : ''?>

            <span class="quote__id" title="Quote ID: <?=$model->id ?>, UID: <?= Html::decode($model->uid) ?>" data-toggle="tooltip">
                <strong><?= Html::decode($model->uid) ?></strong>
            </span>

            <?= $model->getStatusSpan()?>

            <span class="quote__id" title="Created Date Time">
                <?php if ($model->created): ?>
                    <small><i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDatetime(strtotime($model->created)) ?></small>
                <?php endif; ?>
            </span>


            <?php

            if ($ticketSegments = $model->getTicketSegments()) :?>
                <span title="Separate Ticket">
                    <i class="fa fa-ticket warning"></i> (<?=count($ticketSegments)?>)
                </span>
            <?php endif; ?>

        </div>
        <div class="quote__heading-right">
            <span class="label label-info" style="font-size: 15px">
                <b><?= number_format($priceData['total']['selling'], 2)?></b>
                <?= Html::encode($currency)?>
            </span>
            <div class="dropdown"  title="Menu">
                    <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown">
                        <span class="fas fa-list-ul"></span>
                        <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu">

                            <?= Html::a('<i class="fa fa-search"></i> Details', null, [
                                'class' => 'btn-quote-details dropdown-item',
                                'data-id' => $model->id,
                                'data-title' => implode(', ', $tripsInfo),
                                'data-url' => Url::to(['quotes/ajax-details', 'id' => $model->id]),
                                //'data-target' => '#quote_detail_'.$model->uid,
                                'title' => 'Details'
                            ]) ?>

                            <?= Html::a('<i class="fa fa-list"></i> Status logs', null, [
                                'class' => 'view-status-log sl-quote__status-log dropdown-item',
                                'data-id' => $model->id,
                                'title' => 'View status log'
                            ]) ?>

                            <?= Html::a('<i class="fa fa-list-alt"></i> Reserv. dump', null, [
                                'class' => 'btn-reservation-dump dropdown-item',
                                'title' => 'Reservation Dump quote: ' . $model->uid,
                                'data-content' => \yii\helpers\Html::encode($model->reservation_dump)
                            ]) ?>

                            <?= Html::a('<i class="fa fa-camera"></i> Email capture', null, [
                                'class' => 'btn-capture dropdown-item',
                                //'data-id' => $model->id,
                                'data-url' => Url::to(['quotes/ajax-capture', 'id' => $model->id, 'gid' => $model->lead->gid]),
                                'title' => 'Email Capture link'
                            ]) ?>


                        <?php if (!$appliedQuote) : ?>
                                <?php  echo Html::a('<i class="fa fa-copy"></i> Clone', null, [
                                    'class' => 'clone-quote-by-uid-self dropdown-item',
                                    'data-uid' => $model->uid,
                                   // 'data-url' => Url::to(['quote/clone', 'leadId' => $leadId, 'qId' => $model->id]),
                                    'title' => 'Clone'
                                ]);
                                ?>

                        <?php endif; ?>

                        <?php if (!$model->isDeclined() && !$model->isAlternative() && !$model->isOriginal()) :?>
                            <?php  echo Html::a('<i class="fa fa-eye"></i> Checkout Page', $model->getCheckoutUrlPage(), [
                                    'class' => 'dropdown-item',
                                'target'    => '_blank',
                                'title'     => 'View checkout',
                                'data-pjax' => 0
                            ]);
                            ?>
                            <?= Html::a('<i class="fa fa-camera"></i> Copy Checkout Link', null, [
                                'class' => 'btn-copy-checkout-link dropdown-item',
                                'data-url' => $model->getCheckoutUrlPage(),
                                'title' => 'Copy To Clipboard'
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
        </div>
    </div>

    <div style="padding: 5px">

        <div class="row" style="margin-top: 5px;">
            <div class="col-md-12">
                <div class="col-md-12">

                    <i class="fa fa-plane"></i> Main Airline:
                    <span title="Main Airline: <?= Html::encode($airlineName)?>
                    <?= Html::decode($model->main_airline_code)?>" data-toggle="tooltip">
                        <?php if ($airlineLogo = $model::getAirlineLogo($model->main_airline_code)) : ?>
                            <span class="quote__vc-logo">
                                <?php echo Html::img($airlineLogo, ['class' => 'quote__vc-img', 'alt' => $model->main_airline_code]); ?>
                            </span>
                        <?php endif ?>
                            <?= Html::encode($airlineName)?>
                        <strong><?= Html::encode($model->main_airline_code)?></strong>
                    </span>
                        &nbsp; | &nbsp;
                    <span title="<?= $model->created_by_seller ? 'Agent' : 'Expert'?>: <?= Html::encode($model->employee_name)?>">
                        <?php echo Html::encode($model->created_by_seller) ? '<i class="fa fa-user"></i>' :
                            '<i class="fa fa-user-secret text-warning"></i>'; ?>
                        <strong><?= Html::encode($model->employee_name)?></strong>
                    </span>

                    <?php /*
                    &nbsp; | &nbsp;
                    */ ?>
                    <?php /* ?>
            <div class="quote__gds" title="GDS / <?php if ($showGdsOfferId && !empty($model->gds_offer_id)) :
                echo 'GDS Offer ID: ' . \yii\helpers\Html::encode($model->gds_offer_id) . ' /';
                                                 endif; ?> PCC">
                <strong><?= SearchService::getGDSName($model->gds)?></strong>
                <?php if ($showGdsOfferId && !empty($model->gds_offer_id)) : ?>
                    <i class="fas fa-passport success"></i>
                <?php endif; ?>
                / <i><?= $model->pcc?></i>

            </div>
            <?php */ ?>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="quote__trip">
            <?php
                $needRecheck = false;
                $firstSegment = null;
                $lastSegment = null;
            ?>
            <?php foreach ($model->quoteTrips as $trip) :?>
                <?php

                $segments = $trip->quoteSegments;
                if ($segments) {
                    $segmentsCnt = count($segments);
                    $stopCnt = $segmentsCnt - 1;
                    $firstSegment = $segments[0];
                    $lastSegment = end($segments);
                    $cabins = [];
                    $marketingAirlines = [];
                    $airlineNames = [];
                    foreach ($segments as $segment) {
                        if (!in_array(SearchService::getCabin($segment->qs_cabin), $cabins)) {
                            $cabins[] = SearchService::getCabin($segment->qs_cabin, $segment->qs_cabin_basic);
                        }
                        if (!empty($segment->qs_recheck_baggage)) {
                            $needRecheck = true;
                        }
                        if (!empty($segment->qs_stop)) {
                            $stopCnt += $segment->qs_stop;
                        }
                        if (!in_array($segment->qs_marketing_airline, $marketingAirlines)) {
                            $marketingAirlines[] = $segment->qs_marketing_airline;

                            $airlineNames[] = $segment->marketingAirline ?
                                $segment->marketingAirline->name : $segment->qs_marketing_airline;
                        }
                    }
                } else {
                    continue;
                }
                ?>
            <div class="quote__segment">
                <div class="quote__info">
                    <?php if (count($marketingAirlines) === 1) :?>
                        <?php if (!empty($marketingAirlines[0]) && $airlineLogo = $model::getAirlineLogo($marketingAirlines[0])) : ?>
                            <?php echo Html::img($airlineLogo, ['class' => 'quote__airline-logo img-thumbnail', 'alt' => $model->main_airline_code]); ?>
                        <?php else : ?>
                            <?php echo Html::img("/img/_blank.png", ['class' => 'quote__airline-logo', 'alt' => 'No logo']); ?>
                        <?php endif ?>
                    <?php else :?>
                        <?php echo Html::img(
                    "/img/multiple_airlines.png",
                    ['class' => 'quote__airline-logo', 'alt' => implode(', ', $marketingAirlines)]
                ); ?>
                    <?php endif;?>

                    <div class="quote__info-options">
                        <div class="quote__duration" title="Duration"><i class="fa fa-clock-o"></i> <?= SearchService::durationInMinutes($trip->qt_duration)?></div>
                        <div class="quote__airline-name" title="Airline"><?= implode(', ', $airlineNames);?></div>
                    </div>
                </div>
                <div class="quote__itinerary">
                    <div class="quote__itinerary-col quote__itinerary-col--from">
                        <div class="quote__datetime">
                            <span class="quote__time">
                                <?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->qs_departure_time), 'h:mm a')?>
                            </span>
                            <span class="quote__date">
                                <?= Yii::$app->formatter_search->asDatetime(strtotime($firstSegment->qs_departure_time), 'MMM d')?>
                            </span>
                        </div>
                        <div class="quote__location">
                            <div class="quote__airport">
                                <span class="quote__city">
                                    <?= ($firstSegment->departureAirport) ? $firstSegment->departureAirport->city : $firstSegment->qs_departure_airport_code?>
                                </span>
                                (<span class="quote__iata">
                                    <?= Html::encode($firstSegment->qs_departure_airport_code)?>
                                </span>)
                            </div>
                        </div>
                    </div>
                    <div class="quote__itinerary-col quote__itinerary-col--to">
                        <div class="quote__datetime">
                            <span class="quote__time">
                                <?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->qs_arrival_time), 'h:mm a')?>
                            </span>
                            <span class="quote__date">
                                <?= Yii::$app->formatter_search->asDatetime(strtotime($lastSegment->qs_arrival_time), 'MMM d')?>
                            </span>
                        </div>
                        <div class="quote__location">
                            <div class="quote__airport">
                                <span class="quote__city">
                                    <?= ($lastSegment->arrivalAirport) ? $lastSegment->arrivalAirport->city : $lastSegment->qs_arrival_airport_code?>
                                </span>
                                (<span class="quote__iata">
                                    <?= Html::encode($lastSegment->qs_arrival_airport_code)?>
                                </span>)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="quote__additional-info">
                    <div class="quote__stops">
                        <span class="quote__stop-quantity">
                            <?= \Yii::t('search', '{n, plural, =0{Nonstop} one{# stop} other{# stops}}', ['n' => $stopCnt]);?>
                        </span>
                    </div>
                    <div class="quote__cabin">
                        <?= implode(', ', $cabins)?>
                    </div>
                </div>
            </div>

            <?php endforeach;?>

        </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="quote__badges text-left">

                    <?php echo QuoteHelper::formattedFreeBaggage($model->getMetaInfo()) ?>

                    <span class="quote__badge quote__badge--warning <?php if (!$needRecheck) :
                        ?>quote__badge--disabled<?php
                                                                    endif;?>" data-toggle="tooltip"
                          title="<?= ($needRecheck) ? 'Bag re-check may be required' : 'Bag re-check not required'?>"
                          data-original-title="<?= ($needRecheck) ? 'Bag re-check may be required' : 'Bag re-check not required'?>">
                        <i class="fa fa-warning"></i>
                    </span>

                    <span class="quote__badge <?php if ($model->hasAirportChange) :
                        ?>quote__badge--warning<?php
                                              else :
                                                    ?>quote__badge--disabled<?php
                                              endif;?>" data-toggle="tooltip"
                          title="<?= ($model->hasAirportChange) ? 'Airports Change' : 'No Airports Change'?>"
                          data-original-title="<?= ($model->hasAirportChange) ? 'Airports Change' : 'No Airports Change'?>">
                        <i class="fa fa-exchange"></i>
                    </span>

                    <?php echo QuoteHelper::formattedPenalties($model->getPenaltiesInfo()) ?>

                    <?php echo QuoteHelper::formattedMetaRank($model->getMetaInfo())?>

                    <?php echo QuoteHelper::formattedProviderProject($model) ?>
                </div>
            </div>
            <div class="col-md-5">
                <table class="table table-bordered">
                    <tr>
                        <th>
                            <?php Pjax::begin(['id' => 'pjax-quote_estimation_profit-' . $model->id,
                                'enablePushState' => false, 'enableReplaceState' => false]); ?>

                                <?php /*$priceData = $model->getPricesData();*/ ?>

                                <?php if ($model->isApplied() && $model->lead->final_profit !== null) :?>
                                    <a id="quote_profit_<?= $model->id?>" data-toggle="popover" data-html="true" data-trigger="hover"
                                       data-placement="top" data-container="body" title="Final Profit"
                                       class="popover-class quote__profit "
                                       data-content='<?= $model->getEstimationProfitText();?>'>
                                        <?= '$' . $model->lead->getFinalProfit();?>
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                <?php else :?>
                                    <a id="quote_profit_<?= $model->id?>" data-toggle="popover" data-html="true" data-trigger="hover"
                                       data-placement="top" data-container="body" title="Estimation Profit"
                                       class="popover-class quote__profit"
                                       data-content='<?= $model->getEstimationProfitText();?>'>
                                        <?php if (isset($priceData['total'])) :?>
                                            <?=number_format($model->getEstimationProfit(), 2);?>$
                                        <?php endif;?>
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                <?php endif;?>
                            <?php Pjax::end(); ?>
                        </th>
                        <td>
                            <?php if ($model->quoteLabel) : ?>
                                <?php $quoteLabels = '' ?>
                                <?php foreach ($model->quoteLabel as $quoteLabel) : ?>
                                    <?php $quoteLabels .= $quoteLabel->getDescription() . '<br />' ?>
                                <?php endforeach ?>
                                <a class="popover-class"
                                   data-toggle="popover"
                                   data-html="true"
                                   data-placement="top"
                                   data-container="body"
                                   data-trigger="hover"
                                   title="Quote Label"
                                   data-content='<?= Html::encode($quoteLabels) ?>'>
                                    <i class="fa fa-tags"></i> Labels
                                </a>
                            <?php endif ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row" style="margin-top: 5px;">
            <div class="col-md-12">
                <span data-toggle="tooltip" title="1 <?= Html::encode($currency) ?> =
                    <?= round($model->q_client_currency_rate, 5) ?> <?= Currency::getDefaultCurrencyCode()?> ">
                        &nbsp;Currency: <strong><?= Html::encode($currency) ?></strong>
                    <?php if (!$model->isClientCurrencyDefault()) : ?>
                        <i class="fa fa-exclamation-circle warning"></i>
                    <?php endif ?>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php Pjax::begin(['id' => 'pjax-quote_prices-' . $model->id,
                    'enablePushState' => false, 'enableReplaceState' => false]); ?>
                <?= $this->render('_quote_prices', [
                        'quote' => $model,
                        'priceData' => $priceData
                    ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>

    </div>
</div>

<?php
$css = <<<CSS
    .tooltip_quote_info_box {
        text-align: left;
        padding-top: 12px;
    }     
    .tooltip_quote_info_box ul {
        padding-left: 16px;
    } 
    .tooltip_quote_info_box p {
        margin-bottom: 0;
    }
    .quote__additional-info {
        /*max-width: 86px;*/
    }
    .quote_exclamation_currency {
        text-align: left;
        margin-top: 3px;
    }
CSS;
$this->registerCss($css);
