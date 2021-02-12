<?php

use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\helpers\RentCarDataParser;
use yii\data\ArrayDataProvider;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataRentCar array */
/* @var $index int */
/* @var $key int */
/* @var $rentCar rentCar */

?>

<div class="quote" id="box-quote-<?php echo RentCarDataParser::getOfferToken($dataRentCar, $rentCar->prc_request_hash_key) ?>">
    <div class="quote__heading">
      <div class="quote__heading-left">
        <span class="quote__id">
          <strong>#<?=($key + 1)?></strong>
        </span>
        <span class="quote__vc">
          <div class="quote__vc-logo">
            <img src="<?php echo RentCarDataParser::getVendorLogo($dataRentCar) ?>" alt="DY" class="quote__vc-img">
          </div>
          <div class="quote__vc-name">
            <?php echo RentCarDataParser::getVendorName($dataRentCar) ?>
          </div>
        </span>
      </div>
      <div class="quote__heading-right">
        <span class="quote__vc">
          <span class="mr-1">
            Price per day:
          </span>
          <strong class="text-dark">
            <?php
               $perDay = RentCarDataParser::getPricePerDay($dataRentCar);
            ?>
            <?php echo RentCarDataParser::getPriceCurrencySymbol($dataRentCar) ?><?php echo $perDay ?>
          </strong>
        </span>
        <span class="quote__vc">
          <span class="mr-1">
            Days:
          </span>
          <strong class="text-dark">
            <?php
               $days = $rentCar->calculateDays();
            ?>
            <?php echo $days ?>
          </strong>
        </span>
        <span class="quote__vc">
          <span class="mr-1">
            <strong>
              Total:
            </strong>
          </span>
          <strong class="text-success">
            <?php
                $total = (int) $perDay * $days;
            ?>
            <?php echo RentCarDataParser::getPriceCurrencySymbol($dataRentCar) ?><?php echo $total ?>
          </strong>
        </span>
      </div>

    </div>

    <div class="quote__wrapper">
      <div class="offer">
        <div class="row">
          <div class="col col-6">
            <div class="d-flex">
            <div class="offer__preview px-3">
              <img
                src="<?php echo RentCarDataParser::getModelImg($dataRentCar) ?>"
                alt="car-model" class="img-thumbnail">
            </div>
            <div class="offer__description">
              <div class="offer__item-brand d-flex flex-column mb-2">
                <span class="text-lg">
                  <h5 class="mb-0">
                    <?php echo RentCarDataParser::getModelCategory($dataRentCar) ?>
                  </h5>
                </span>
                <span class="text-md text-secondary"><?php echo RentCarDataParser::getModelName($dataRentCar) ?></span>
              </div>
              <ul class="offer__option-list list-unstyled">
                <?php foreach (RentCarDataParser::getOptions($dataRentCar) as $name => $value) : ?>
                    <li class="offer__option">
                      <b class="offer-option__key"><?php echo ucfirst($name) ?></b>:
                      <span class="offer-option__value"><?php echo $value ?></span>
                    </li>
                <?php endforeach; ?>

              </ul>
            </div>
            </div>
          </div>
          <div class="col col-6">
            <div class="row align-items-center h-100">
              <div class="col col-4">
                <ul class="offer__perks-list list-unstyled mb-0">
                  <?php foreach (RentCarDataParser::getActionable($dataRentCar) as $value) : ?>
                  <li>
                    <span class="text-success"><?php echo $value ?></span>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <div class="col col-8">
                <div class="alert alert-info mb-0" role="alert" style="background-color: #fafafb; border: 1px solid #c2cad8;">
                  <div class="text-dark">
                    <div class="list-unstyled">
                      <li>
                        <strong>Pick-up: </strong>
                        <span> <?php echo RentCarDataParser::getPickUpLocation($dataRentCar) ?></span>
                      </li>
                      <li>
                        <strong>Drop-off: </strong>
                        <span> <?php echo RentCarDataParser::getDropOffLocation($dataRentCar) ?></span>
                      </li>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="quote__footer">
      <div class="quote__footer-left"></div>
      <div class="quote__footer-right">
        <button
            type="button"
            class="btn btn-success quote__footer-btn js-add-rent-car-quote"
            data-request-id="<?php echo $rentCar->prc_id ?>"
            data-token="<?php echo RentCarDataParser::getOfferToken($dataRentCar, $rentCar->prc_request_hash_key) ?>">
                <i class="fa fa-plus"></i>&nbsp; <span>Add Quote</span>
        </button>
      </div>
    </div>
  </div>
