<?php
/* @var $this yii\web\View */
/* @var $dataRoom array */
/* @var $dataHotel array */
/* @var $index int */
/* @var $key int */
/* @var $hotelSearch Hotel */

use modules\hotel\models\Hotel;
use yii\data\ArrayDataProvider;
use yii\web\View;

$quoteRooms = $dataRoom['rates'];

$quoteExist = $hotelSearch->quoteExist($dataRoom['groupKey']);
//$room = $dataRoom['rates'][0];

?>
<tr id="tr-hotel-quote-<?=($dataRoom['groupKey'])?>" <?=$quoteExist ? 'class="bg-success"' : ''?>>
    <td style="width: 50px">
        <?=($key + 1)?>
    </td>
    <td style="width: 100px" title="<?=md5($dataRoom['groupKey'])?>">
        <?=($dataRoom['groupKey'])?><br>
        <?=md5($dataRoom['groupKey'])?>
    </td>
    <td>
        <table class="table table-striped table-bordered">
            <?php foreach ($quoteRooms as $room):?>
                 <tr>
                    <td title="<?=($room['key'])?>">
                        <b><?=\yii\helpers\Html::encode($room['name'])?></b>
                    </td>
                    <td>
                        class: <?=(\yii\helpers\Html::encode($room['class']))?>
                    </td>
                    <td>
                        code: <?=(\yii\helpers\Html::encode($room['code']))?>
                    </td>
                    <td title="<?=(\yii\helpers\Html::encode($room['boardCode']))?>">
                        <?=(\yii\helpers\Html::encode($room['boardName']))?>
                    </td>
                    <td>
                        type: <?=(\yii\helpers\Html::encode($room['type']))?>
                    </td>
                    <td>
                        rooms: <?=(\yii\helpers\Html::encode($room['rooms']))?>
                    </td>
                    <td>
                        <i class="fa fa-user"></i> <?=(\yii\helpers\Html::encode($room['adults'] ?? 0))?>
                    </td>
                    <td>
                        <i class="fa fa-child"></i> <?=(\yii\helpers\Html::encode($room['children'] ?? 0))?>
                    </td>
            <!--        <td>-->
            <!--            ages: --><?php //=(\yii\helpers\Html::encode($room['childrenAges'] ?? ''))?>
            <!--        </td>-->
                    <td>
                        <?=number_format(\yii\helpers\Html::encode($room['amount']), 2)?> $
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    </td>
    <td>
        <?php if ($quoteExist):?>
            <span class="badge badge-white">Added</span>
        <?php else: ?>
            <?= \yii\bootstrap4\Html::a('<i class="fa fa-plus"></i> add Quote', null, ['data-url' => \yii\helpers\Url::to(['/hotel/hotel-quote/add-ajax', 'ph_id' => $hotelSearch->ph_id]),
            'data-hotel-code' => $dataHotel['code'],
            'data-quote-key' => $dataRoom['groupKey'],
            'class' => 'btn btn-success btn-add-hotel-quote']) ?>
        <?php endif; ?>
    </td>
</tr>
<!--    <tr>-->
<!--        <td colspan="10">-->
<!--            --><?php ////\yii\helpers\VarDumper::dump($model, 5, true)?>
<!--        </td>-->
<!--    </tr>-->
<!--</table>-->