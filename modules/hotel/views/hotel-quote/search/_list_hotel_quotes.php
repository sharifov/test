<?php

/* @var $this yii\web\View */
/* @var $dataHotel array */
/* @var $index int */
/* @var $key int */
/* @var $hotelSearch Hotel */

use modules\hotel\assets\HotelAsset;
use modules\hotel\models\Hotel;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/*
 *
 * 'categoryName' => '4 STARS'
'destinationName' => 'Cadiz / Jerez'
'zoneName' => 'Cádiz'
'minRate' => 102.27
'maxRate' => 517.46
'currency' => 'USD'
'rooms' => [...]
'code' => 58197
'name' => 'Senator Cadiz Spa'
'description' => 'This striking hotel is wonderfully located in the historical centre of Cádiz, close to the best shopping and historical areas of the capital. It is situated just 5 minutes from the train station and 40 km from Jerez airport. This establishment provides guests with a wide array of facilities such as WiFi access throughout the premises, perfect for those who want to stay connected. The spacious rooms have an en suite bathroom with hairdryer, soundproof windows and necessary amenities to allow guests to feel at home. A gym and an outdoor pool with magnificent views of Cadiz are also available to guests (Please note the swimming pool is open from 16 April to 13 October.) Courtesy bottle of water.
The SPA service at Christmas is closed on December 25 and January 1.'
'countryCode' => 'ES'
'stateCode' => '11'
'destinationCode' => 'CAD'
'zoneCode' => 99
'latitude' => 36.532532
'longitude' => -6.293758
'categoryCode' => '4EST'
'categoryGroupCode' => 'GRUPO4'
'chainCode' => 'SENAT'
'boardCodes' => [...]
'segmentCodes' => [...]
'address' => 'Calle Rubio Y Diaz,1  '
'postalCode' => '11004'
'city' => 'CADIZ'
'email' => 'reservas@senatorhr.com'
'license' => 'H/CA/01196'
'phones' => [...]
'images' => [...]
'lastUpdate' => '2019-11-21'
's2C' => '4*'
'ranking' => 2
'serviceType' => 'HOTELBEDS'
 *
 */

$roomDataProvider = new ArrayDataProvider([
    'allModels' => $dataHotel['rooms'] ?? [],
    'pagination' => [
        'pageSize' => 15,
        'pageParam' => 'qh-page' . $key
    ],
    /*'sort' => [
        'attributes' => ['ranking', 'name', 's2C'],
    ],*/
]);

HotelAsset::register($this);
?>
<?php /*
<table class="table table-striped table-bordered">
    <tr>
        <td style="width: 70px">
            <?=($key + 1)?>
        </td>
        <td title="code: <?=\yii\helpers\Html::encode($dataHotel['code'])?>">
            <i class="fa fa-hotel"></i> <b><?=\yii\helpers\Html::encode($dataHotel['name'])?>, <?=\yii\helpers\Html::encode($dataHotel['s2C'] ?? '')?></b>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?php //php \yii\helpers\VarDumper::dump($model, 3, true)?>

            <?php \yii\widgets\Pjax::begin(['timeout' => 15000, 'enablePushState' => false, 'enableReplaceState' => false, 'scrollTo' => false]); ?>
            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $roomDataProvider,
                'options' => [
                    'tag' => 'table',
                    'class' => 'table table-bordered',
                ],
                'emptyText' => '<div class="text-center">Not found rooms</div><br>',
                'itemView' => function ($modelRoom, $key, $index, $widget) use ($dataHotel, $hotelSearch) {
                    return $this->render('_list_hotel_room_quotes', ['dataRoom' => $modelRoom, 'dataHotel' => $dataHotel, 'index' => $index, 'key' => $key, 'hotelSearch' => $hotelSearch]);
                },
                //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
                'itemOptions' => [
                    //'class' => 'item',
                    'tag' => false,
                ],
            ]) ?>





<!--            --><?php
//                if ($model['rooms']) {
//                    foreach ($model['rooms'] as $room) {
//                        \yii\helpers\VarDumper::dump($room, 3, true);
//                    }
//                }
//            ?>
            <?php \yii\widgets\Pjax::end(); ?>
        </td>
    </tr>
</table>
 */ ?>

<div class="quote">
  <div class="quote__heading">
    <div class="quote__heading-left">
        <span class="quote__id">
          <strong># <?= $key + 1 ?></strong>
        </span>
      <span class="quote__vc">
        <?= Html::encode($dataHotel['accomodationType']['code'] ?? '') ?>
        </span>
    </div>
    <div class="quote__heading-right">
        <span class="quote__vc">
          <span class="mr-1">
            <strong>
              From:
            </strong>
          </span>
          <strong class="text-success">
            $<?=number_format(Html::encode($dataHotel['rooms'][0]['totalAmount'] - ($dataHotel['rooms'][0]['totalMarkup'] ?? 0)), 2)?>
          </strong>
        </span>
    </div>

  </div>

  <div class="quote__wrapper">
    <div class="">
      <div class="row">
        <div class="col-3">
          <?php if (isset($dataHotel['images'][0]['url'])) : ?>
          <img src="https://www.gttglobal.com/hotel/img/<?= $dataHotel['images'][0]['url'] ?>" alt="<?= Html::encode($dataHotel['name']) ?>" class="img-thumbnail">
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
          <h5 class="mb-2">
            <span class="mr-1"><?= Html::encode($dataHotel['name']) ?></span>
            <?php if (isset($dataHotel['s2C'])) : ?>
              <span class="text-warning">
                <small>
                  <?php for ($i = 0; $i < (int)$dataHotel['s2C']; $i++) : ?>
                    <i class="fa fa-star"></i>
                  <?php endfor; ?>
                </small>
              </span>
            <?php endif; ?>
          </h5>
          <div class="mb-4">
            <i class="fa fa-map-marker mr-1 text-info"></i>
            <span><?= Html::encode($dataHotel['city'] ?? '') ?>, <?= Html::encode($dataHotel['address'] ?? '') ?></span>
            <?php if (isset($dataHotel['email'])) : ?>
            <br>
            <i class="fa fa-envelope mr-1 text-info"></i> <?= Html::encode($dataHotel['email']) ?>
            <?php endif; ?>
          </div>
          <div><p>
              <?= Html::encode($dataHotel['description']) ?>
            </p>
          </div>
          <div>
          </div>
        </div>
      </div>
        <?php Pjax::begin(['timeout' => 15000, 'enablePushState' => false, 'enableReplaceState' => false, 'scrollTo' => false]); ?>
        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $roomDataProvider,
            'options' => [
                'tag' => 'table',
                'class' => 'table table-bordered',
            ],
            'emptyText' => '<div class="text-center">Not found rooms</div><br>',
            'itemView' => function ($modelRoom, $key, $index, $widget) use ($dataHotel, $hotelSearch) {
                return $this->render('_list_hotel_room_quotes', ['dataRoom' => $modelRoom, 'dataHotel' => $dataHotel, 'index' => $index, 'key' => $key, 'hotelSearch' => $hotelSearch]);
            },
            //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
            'itemOptions' => [
                //'class' => 'item',
                'tag' => false,
            ],
        ]) ?>
        <?php Pjax::end(); ?>
    </div>

  </div>
</div>