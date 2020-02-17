<?php
/* @var $this yii\web\View */
/* @var $dataHotel array */
/* @var $index int */
/* @var $key int */
/* @var $hotelSearch Hotel */

use modules\hotel\models\Hotel;
use yii\data\ArrayDataProvider;
use yii\web\View;

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


?>
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