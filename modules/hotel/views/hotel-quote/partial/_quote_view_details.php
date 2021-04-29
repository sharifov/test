<?php

use yii\helpers\VarDumper;
use yii\helpers\Html;
use modules\hotel\assets\HotelAsset;

/**
 * @var $hotelQuote \modules\hotel\models\HotelQuote
 */
HotelAsset::register($this);
?>

<div class="quote__details">
    <h4 class="trip__subtitle">
        <span class="trip__leg-type">Check In</span>
        <span class="trip__leg-date"> <?= Yii::$app->formatter_search->asDatetime(strtotime($hotelQuote->hqHotel->ph_check_in_date), 'EEE d MMM')?></span> /
        <span class="trip__leg-type">Check Out</span>
        <span class="trip__leg-date"><?= Yii::$app->formatter_search->asDatetime(strtotime($hotelQuote->hqHotel->ph_check_out_date), 'EEE d MMM')?></span>
    </h4>
    <div class="quote">
        <div class="quote__wrapper">
            <div class="row">
                <div class="col-sm-3">
                    <?php if (!empty($hotelQuote->hq_origin_search_data['images'][0]['url'])) : ?>
                        <img src="https://www.gttglobal.com/hotel/img/<?= $hotelQuote->hq_origin_search_data['images'][0]['url'] ?>"
                             alt="<?= Html::encode($hotelQuote->hq_hotel_name) ?>" class="img-thumbnail">
                    <?php endif; ?>
                </div>
                <div class="col-9">
                    <h5 class="mb-2">
                        <span class="mr-1"><?= Html::encode($hotelQuote->hq_hotel_name) ?></span>
                        <?php if (!empty($hotelQuote->hq_origin_search_data['categoryCode'])) : ?>
                            <img alt="stars" src="https://cdn4.hotelopia.com/freya/img/stars/<?= $hotelQuote->hq_origin_search_data['categoryCode'] ?>.gif">
                        <?php endif; ?>
                    </h5>
                    <div class="mb-4">
                        <i class="fa fa-map-marker mr-1 text-info"></i>
                        <span><?= Html::encode($hotelQuote->hq_origin_search_data['city'] ?? '') ?>, <?= Html::encode($hotelQuote->hq_origin_search_data['address'] ?? '') ?></span>
                        <?php if ($hotelQuote->hq_origin_search_data['email']) : ?>
                            <br>
                            <i class="fa fa-envelope mr-1 text-info"></i> <?= Html::encode($hotelQuote->hq_origin_search_data['email'] ?? '') ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p>
                            <?= Html::encode($hotelQuote->hq_origin_search_data['description'] ?? '') ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php if ($quoteRooms = $hotelQuote->hotelQuoteRooms) : ?>
            <table class="table table-bordered mt-3">
                <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Room</th>
                    <th>Board</th>
                    <th>Guests</th>
                    <th>Cancellation Policies</th>
                    <th>Price</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($quoteRooms as $quoteRoomKey => $quoteRoom) : ?>
                    <tr>
                        <th><?php echo $quoteRoomKey + 1 ?></th>
                        <td>
                            <div><?= Html::encode($quoteRoom->hqr_room_name) ?></div>
                        </td>
                        <td><span class="badge badge-secondary"><?= Html::encode($quoteRoom->hqr_board_name) ?></span></td>
                        <td>
                            <span class="ml-2"><i class="fa fa-user"></i> <?=(Html::encode($quoteRoom->hqr_adults ?? 0))?></span>
                            <span class="ml-2"><i class="fa fa-child"></i> <?=(Html::encode($quoteRoom->hqr_children ?? 0))?></span>
                        </td>
                        <td>
                            <span class="ml-2"><i class="fa fa-clock-o"></i> <?=(Html::encode(Yii::$app->formatter->asDatetime(strtotime($quoteRoom->hqr_cancellation_policies[0]['from']), 'php: d-M-Y [H:i]')))?></span>
                            <span class="ml-2"><i class="fa fa-money"></i> <?=(Html::encode($quoteRoom->hqr_cancellation_policies[0]['amount']))?></span>
                        </td>
                        <td>$<?=number_format(Html::encode($quoteRoom->hqr_amount), 2)?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
