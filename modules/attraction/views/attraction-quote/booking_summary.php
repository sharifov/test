<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $bookingDetails array
 */

//$answers = !empty($bookingDetails['questionList']['nodes']) ? $bookingDetails['questionList']['nodes'] : $bookingDetails['availabilityList']['nodes'][0]['questionList']['nodes'];
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

            <?php if (!empty($bookingDetails['questionList']['nodes'])) : ?>
                <div class="x_panel rounded">
                    <div class="x_title">
                        <h2>Lead Person Details</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="display: block">
                        <?php foreach ($bookingDetails['questionList']['nodes'] as $answer) : ?>
                            <?= '<span class="badge badge-white">' . $answer['label'] . '</span>: <b>' . $answer['answerValue'] . '</b><br>' ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($bookingDetails['availabilityList']['nodes'][0]['questionList']['nodes'])) : ?>
                <div class="x_panel rounded">
                    <div class="x_title">
                        <h2>Additional Details</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="display: block">
                        <?php foreach ($bookingDetails['availabilityList']['nodes'][0]['questionList']['nodes'] as $answer) : ?>
                            <?= '<span class="badge badge-white">' . $answer['label'] . '</span>: <b>' . $answer['answerValue'] . '</b><br>' ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

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
                                <?php if (isset($bookingDetails['availabilityList']['nodes'][0]['product']['previewImage']['url'])) : ?>
                                    <img src="<?= $bookingDetails['availabilityList']['nodes'][0]['product']['previewImage']['url'] ?>" class="img-thumbnail" alt="Preview">
                                <?php else : ?>
                                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <g transform="translate(1 1)"> <g>
                                                <g>
                                                    <path d="M255-1C114.2-1-1,114.2-1,255s115.2,256,256,256s256-115.2,256-256S395.8-1,255-1z M255,16.067
                                c63.054,0,120.598,24.764,163.413,65.033l-65.336,64.802L334.36,97.987c-0.853-2.56-4.267-5.12-7.68-5.12H185.027
                                c-3.413,0-5.973,1.707-7.68,5.12L156.013,152.6h-48.64c-17.067,0-30.72,13.653-30.72,30.72v168.96
                                c0,17.067,13.653,30.72,30.72,30.72h6.653l-34.26,33.981C40.285,374.319,16.067,317.354,16.067,255
                                C16.067,123.587,123.587,16.067,255,16.067z M314.733,255c0,33.28-26.453,59.733-59.733,59.733
                                c-13.563,0-25.99-4.396-35.957-11.854l84.125-83.438C310.449,229.34,314.733,241.616,314.733,255z M195.267,255
                                c0-33.28,26.453-59.733,59.733-59.733c13.665,0,26.174,4.467,36.179,12.028l-84.183,83.495
                                C199.613,280.852,195.267,268.487,195.267,255z M303.374,195.199C290.201,184.558,273.399,178.2,255,178.2
                                c-42.667,0-76.8,34.133-76.8,76.8c0,18.17,6.206,34.779,16.61,47.877l-63.576,63.057H106.52c-7.68,0-13.653-5.973-13.653-13.653
                                V183.32c0-7.68,5.973-13.653,13.653-13.653h54.613c3.413,0,6.827-2.56,7.68-5.12l21.333-54.613h129.707l19.404,49.675
                                L303.374,195.199z M206.848,314.974C219.987,325.509,236.703,331.8,255,331.8c42.667,0,76.8-34.133,76.8-76.8
                                c0-18.068-6.138-34.592-16.436-47.655l37.988-37.678h49.274c7.68,0,13.653,5.973,13.653,13.653v168.96
                                c0,7.68-5.973,13.653-13.653,13.653H155.469L206.848,314.974z M255,493.933c-62.954,0-120.415-24.686-163.208-64.843L138.262,383
                                H403.48c17.067,0,30.72-13.653,31.573-30.72V183.32c0-17.067-13.653-30.72-30.72-30.72H370.56l59.865-59.376
                                c39.368,42.639,63.509,99.521,63.509,161.776C493.933,386.413,386.413,493.933,255,493.933z"/>
                                                    <path d="M383,186.733c-9.387,0-17.067,7.68-17.067,17.067c0,9.387,7.68,17.067,17.067,17.067s17.067-7.68,17.067-17.067
                                C400.067,194.413,392.387,186.733,383,186.733z"/> </g> </g> </g> <g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                        </svg>
                                <?php endif; ?>
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