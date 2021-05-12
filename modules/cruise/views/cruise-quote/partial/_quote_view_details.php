<?php

use yii\helpers\Html;
use modules\hotel\assets\HotelAsset;

/**
 * @var $cruiseQuote \modules\cruise\src\entity\cruiseQuote\CruiseQuote
 */
HotelAsset::register($this);
?>

<div class="quote__details">
    <h4 class="trip__subtitle">
        <span class="trip__leg-type">Departure</span>
        <span class="trip__leg-date"> <?= Yii::$app->formatter_search->asDatetime(strtotime($cruiseQuote->crq_data_json['departureDate']), 'EEE d MMM')?></span> /
        <span class="trip__leg-type">Return</span>
        <span class="trip__leg-date"><?= Yii::$app->formatter_search->asDatetime(strtotime($cruiseQuote->crq_data_json['returnDate']), 'EEE d MMM')?></span>
    </h4>

    <div class="quote">
        <div class="quote__wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-sm-2">
                        <img src="<?= $cruiseQuote->crq_data_json['ship']['shipImage']['standard'] ?>"
                             alt="<?= Html::encode($cruiseQuote->crq_data_json['ship']['name']) ?>" class="img-thumbnail">
                    </div>
                    <div class="col-3">
                        <h5 class="mb-2">
                            <span class="mr-1"><?= Html::encode($cruiseQuote->crq_data_json['ship']['name']) ?></span>
                        </h5>
                        <div class="mb-4">
                            <i class="fa fa-map-marker mr-1 text-info"></i>
                            <span><?= Html::encode($cruiseQuote->crq_data_json['itinerary']['destination']['destination'] ?? '') ?> (<?= Html::encode($cruiseQuote->crq_data_json['itinerary']['destination']['subDestination'] ?? '') ?>)</span>

                        </div>
                    </div>
                    <div class="col-7">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                            <th colspan="5" class="text-center">Cruise Itinerary</th>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Port / At Sea</th>
                                <th>Arrive</th>
                                <th>Depart</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($cruiseQuote->crq_data_json['itinerary']['locations'] as $locationKey => $location) : ?>
                                <tr>
                                    <th><?php echo $locationKey + 1 ?></th>
                                    <td>
                                        <?= Yii::$app->formatter_search->asDatetime(strtotime($cruiseQuote->crq_data_json['departureDate'] . '+' . $locationKey . ' day'), 'EEE d MMM')?>
                                    </td>
                                    <td><span class="badge badge-secondary"><?= Html::encode($location['location']['name']) ?></span></td>
                                    <td>
                                        <span class="ml-2"><i class="fa fa-clock"></i> <?=(Html::encode($location['arrivalHrMin']))?></span>
                                    </td>
                                    <td>
                                        <span class="ml-2"><i class="fa fa-clock"></i> <?=(Html::encode($location['departureHrMin']))?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($cruiseQuote->crq_data_json['cabin']) : ?>
                    <table class="table table-bordered table-sm" style="font-size: 14px">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Cabin Preview</th>
                            <th>Cabin</th>
                            <th>Experience</th>
                            <th>Travelers</th>
                            <th>Price</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th><?=  1 ?></th>
                                <td>
                                    <img src="<?= $cruiseQuote->crq_data_json['cabin']['imgUrl'] ?>" class="img-thumbnail"
                                         style="max-width: 70px; max-height: 70px;">
                                </td>
                                <td>
                                    <div><?= $cruiseQuote->crq_data_json['cabin']['name'] ?></div>
                                </td>
                                <td><?= $cruiseQuote->crq_data_json['cabin']['experience'] ?></td>
                                <td>
                                    <?php if ($cruiseQuote->getAdults()) : ?>
                                        <span class="ml-2"><?= $cruiseQuote->getAdults() ?> <i
                                                    class="fa fa-user text-secondary"></i></span>
                                    <?php endif; ?>
                                    <?php if ($cruiseQuote->getChildren()) : ?>
                                        <span class="ml-2"><?= $cruiseQuote->getChildren() ?> <i
                                                    class="fa fa-child text-secondary"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td>$<?= $cruiseQuote->crq_data_json['cabin']['price'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>


            </div>
        </div>
    </div>

</div>