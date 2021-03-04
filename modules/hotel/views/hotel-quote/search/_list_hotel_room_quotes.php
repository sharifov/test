<?php

/* @var $this yii\web\View */
/* @var $dataRoom array */
/* @var $dataHotel array */
/* @var $index int */
/* @var $key int */
/* @var $hotelSearch Hotel */

use modules\hotel\models\Hotel;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\web\View;

$quoteRooms = $dataRoom['rates'];

$quoteExist = $hotelSearch->quoteExist($dataRoom['groupKey']);
//$room = $dataRoom['rates'][0];

?>
  <thead class="thead-light">
    <tr>
      <th>#</th>
      <th>Room</th>
      <th>Board</th>
      <th>Guests</th>
      <th>Price</th>
      <th class="text-right">
          <?php if ($quoteExist) :?>
            <span class="badge badge-white">Added</span>
          <?php else : ?>
              <?= \yii\bootstrap4\Html::a('<i class="fa fa-plus"></i> add Quote', null, ['data-url' => \yii\helpers\Url::to(['/hotel/hotel-quote/add-ajax', 'ph_id' => $hotelSearch->ph_id]),
                  'data-hotel-code' => $dataHotel['code'],
                  'data-quote-key' => $dataRoom['groupKey'],
                  'class' => 'btn btn-success btn-add-hotel-quote']) ?>
          <?php endif; ?>
      </th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($quoteRooms as $quoteRoomKey => $quoteRoom) : ?>
    <tr id="tr-hotel-quote-<?=($dataRoom['groupKey'])?>" class="tr-hotel-quote-<?=($dataRoom['groupKey'])?> <?= $quoteExist ? 'bg-success' : '' ?>">
      <th><?= $quoteRoomKey + 1 ?></th>
      <td>
        <div><?= Html::encode($quoteRoom['name']) ?></div>
      </td>
      <td><span class="badge badge-secondary"><?= Html::encode($quoteRoom['boardName']) ?></span></td>
      <td>
        <span class="ml-2"><i class="fa fa-user"></i> <?=(Html::encode($quoteRoom['adults'] ?? 0))?></span>
        <span class="ml-2"><i class="fa fa-child"></i> <?=(Html::encode($quoteRoom['children'] ?? 0))?></span>
      </td>
      <td>$<?=number_format(Html::encode($quoteRoom['amount'] - ($quoteRoom['markup'] ?? 0)), 2)?></td>
      <td class="text-right"><?php echo $quoteRoom['type'] ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>