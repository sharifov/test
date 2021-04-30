<?php

use yii\helpers\Html;
use yii\helpers\VarDumper;
use modules\hotel\assets\HotelAsset;
use yii\helpers\Inflector;

/**
 * @var $rentCarQuote \modules\rentCar\src\entity\rentCarQuote\RentCarQuote
 */

HotelAsset::register($this);
?>

<div class="quote__details">
    <h4 class="trip__subtitle">
        <span class="trip__leg-type">Pick Up</span>
        <span class="trip__leg-date"> <?= Yii::$app->formatter_search->asDatetime(strtotime($rentCarQuote->rcq_pick_up_dt), 'EEE d MMM HH:mm')?></span> /
        <span class="trip__leg-type">Drop Off</span>
        <span class="trip__leg-date"><?= Yii::$app->formatter_search->asDatetime(strtotime($rentCarQuote->rcq_drop_off_dt), 'EEE d MMM H:mm')?></span>
    </h4>
    <div class="quote">
        <div class="quote__wrapper">
            <div class="row">
                <div class="col-4">
                    <?php if (!empty($rentCarQuote->rcq_json_response['car']['images']['SIZE268X144'])) : ?>
                        <img src="<?= $rentCarQuote->rcq_json_response['car']['images']['SIZE268X144'] ?>"
                             alt="<?= Html::encode($rentCarQuote->rcq_json_response['car']['example']) ?>" class="img-thumbnail">
                    <?php endif; ?>
                </div>
                <div class="col-4">
                    <div class="offer__description">
                        <div class="offer__item-brand d-flex flex-column mb-2">
                            <span class="text-lg" title="Category">
                              <h5 class="mb-0">
                                <?= Html::encode($rentCarQuote->rcq_category) ?>
                              </h5>
                            </span>
                            <span class="text-md text-secondary" title="Model Name"><?= Html::encode($rentCarQuote->rcq_model_name) ?></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <i class="fas fa-calendar-day mr-1 text-info"></i><span><b>Rental days</b>: <?= Html::encode($rentCarQuote->rcq_json_response['price_details']['num_rental_days']) ?></span><br>
                        <i class="fa fa-map-marker mr-2 text-info"></i><span><b>Pick Up</b>: <?= Html::encode($rentCarQuote->rcq_pick_up_location) ?></span><br>
                        <i class="fa fa-map-marker mr-2 text-info"></i><span><b>Drop Off</b>: <?= Html::encode($rentCarQuote->rcq_drop_of_location) ?></span>
                    </div>
                </div>
                <div class="col-4">
                    <ul class="offer__option-list list-unstyled">
                        <?php foreach ($rentCarQuote->rcq_options as $name => $value) : ?>
                            <?php if (empty($value)) : ?>
                                <?php continue; ?>
                            <?php endif ?>
                            <li class="offer__option">
                                <b class="offer-option__key"><?php echo Inflector::humanize($name) ?></b>:
                                <span class="offer-option__value"><?php echo $value ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
