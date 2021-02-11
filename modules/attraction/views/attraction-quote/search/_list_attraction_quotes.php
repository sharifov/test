<?php

/* @var $this yii\web\View */
/* @var $dataHotel array */
/* @var $index int */
/* @var $key int */

/* @var $hotelSearch Hotel */

use modules\hotel\models\Hotel;

//var_dump($hotelSearch); die();
?>


<tr>
    <td>
        <?= $key + 1 ?>
    </td>
    <td>
        <b><?=\yii\helpers\Html::encode($dataHotel[$key]['name'])?></b>
    </td>
    <td>
        type: <?=(\yii\helpers\Html::encode($dataHotel[$key]['__typename']))?>
    </td>
    <td>
        duration: <?=(\yii\helpers\Html::encode($dataHotel[$key]['duration']['formatted']))?>
    </td>
    <td title="<?php //=(\yii\helpers\Html::encode($attraction['boardCode']))?>">
        <?php //=(\yii\helpers\Html::encode($attraction['boardName']))?>
    </td>
    <td>
        rooms: <?php //=(\yii\helpers\Html::encode($attraction['rooms']))?>
    </td>
    <td>
        <i class="fa fa-user"></i> <?php //=(\yii\helpers\Html::encode($attraction['adults'] ?? 0))?>
    </td>
    <td>
        <i class="fa fa-child"></i> <?php //=(\yii\helpers\Html::encode($attraction['children'] ?? 0))?>
    </td>

    <td>
        <?= \yii\helpers\Html::encode($dataHotel[$key]['leadTicket']['price']['lead']['formatted'] ?? '0')?>
    </td>
    <td>
        <?php if (false) : ?>
            <span class="badge badge-white">Added</span>
        <?php else : ?>
            <?= \yii\bootstrap4\Html::a('<i class="fa fa-plus"></i> add Quote', null, [
                'data-url' => \yii\helpers\Url::to(['/attraction/attraction-quote/add-ajax', 'atn_id' => $hotelSearch->atn_id]),
                //'data-hotel-code' => $dataHotel['code'],
                'data-quote-key' => $dataRoom['id'],
                'class' => 'btn btn-success btn-add-hotel-quote'
            ]) ?>
        <?php endif; ?>
    </td>
</tr>


