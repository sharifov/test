<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $bookingDetails array
 */
?>
<div class="container border border-dark rounded p-3">
    <div class="row">
        <div class="col-4">

            <div class="x_panel rounded">
                <div class="x_title">
                    <h2>Booking</h2>

                    <ul class="nav navbar-right panel_toolbox">
                        <?php if (strtolower($bookingDetails['paymentState']) !== 'on_account') : ?>
                        <li>
                            <a href="<?= $bookingDetails['partnerChannelBookingUrl'] . '/booking/' . $bookingDetails['id']?>" target="_blank" class="text-success"><i class="fas fa-credit-card"></i> Payment </a>
                        </li>
                        <?php endif; ?>
                        <li>
                            <?= Html::a(
                                '<i class="fa fa-refresh"></i> Check Confirmation',
                                null,
                                [
                                    'class' => 'text-info js-btn-booking-confirmation',
                                    'data-url' => Url::to('/attraction/attraction-quote/check-booking-confirmation'),
                                    'data-book-id' => $bookingDetails['id'],
                                ]
                            ) ?>
                        </li>
                    </ul>

                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">
                    <span class="badge badge-white">Lead Person Name</span>: <?= !empty($bookingDetails['leadPassengerName']) ? '<b>' . $bookingDetails['leadPassengerName'] . '</b>' : 'Not specified'?><br>
                    <span class="badge badge-white">Your Reference</span>: <?= !empty($bookingDetails['reference']) ? '<b>' . $bookingDetails['reference'] . '</b>' : 'Not specified'?><br>
                    <span class="badge badge-white">Holibob Booking Reference</span>: <?= !empty($bookingDetails['code']) ? '<b>' . $bookingDetails['code'] . '</b>' : 'Not specified'?><br>
                    <span class="badge badge-white">Payment State</span>: <?= !empty($bookingDetails['paymentState']) ? '<b>' . $bookingDetails['paymentState'] . '</b>' : 'Not specified'?><br>
                    <span class="badge badge-white">Booking Status</span>: <?= !empty($bookingDetails['state']) ? '<b>' . $bookingDetails['state'] . '</b>' : 'Not specified'?>
                </div>
            </div>

            <div class="x_panel rounded">
                <div class="x_title">
                    <h2>Lead Person Details</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">
                    <?php foreach ($bookingDetails['questionList']['nodes'] as $question) : ?>
                        <?= '<span class="badge badge-white">' . $question['label'] . '</span>: <b>' . $question['answerValue'] . '</b><br>' ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="x_panel rounded">
                <div class="x_title">
                    <h2>Summary</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="display: block">
                    <h5><?= $bookingDetails['availabilityList']['nodes'][0]['product']['name'] ?></h5>
                    <div class="container">
                        <div class="row">
                            <div class="col-3">
                                <img src="<?= $bookingDetails['availabilityList']['nodes'][0]['product']['previewImage']['url'] ?>" class="img-thumbnail" alt="Preview">
                            </div>
                            <div class="col-9">
                                <?= '<span class="badge badge-white">Date</span>: <b>' . $bookingDetails['availabilityList']['nodes'][0]['date'] . '</b><br>' ?>
                                <?php foreach ($bookingDetails['availabilityList']['nodes'][0]['optionList']['nodes'] as $option) : ?>
                                    <?= '<span class="badge badge-white">' . $option['label'] . '</span>: <b>' . $option['answerFormattedText'] . '</b><br>' ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>