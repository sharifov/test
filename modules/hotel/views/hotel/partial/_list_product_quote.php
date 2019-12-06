<?php
/* @var $this yii\web\View */
/* @var $index int */
/* @var $key int */
/* @var $hotelProduct Hotel */
/* @var $model HotelQuote */

use modules\hotel\models\Hotel;
use modules\hotel\models\HotelQuote;
use yii\data\ArrayDataProvider;
use yii\web\View;
use yii\helpers\Html;

?>

<?php if ($model->hqProductQuote): ?>

<div class="x_panel">
    <div class="x_title">
        <h2>
            Q<?=($key + 1)?>.  <?=\yii\helpers\Html::encode($model->hqProductQuote->pq_name)?>, <?=\yii\helpers\Html::encode($model->hq_destination_name ?? '')?>
             <?//=\yii\helpers\Html::encode($model->hqProductQuote->pq_gid)?>
        </h2>
        <ul class="nav navbar-right panel_toolbox">
<!--            <li>-->
<!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
<!--            </li>-->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                <div class="dropdown-menu" role="menu">
                    <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete quote', null, [
                        'class' => 'dropdown-item text-danger btn-delete-product-quote',
                        'data-product-quote-id' => $model->hq_product_quote_id,
                        'data-hotel-quote-id' => $model->hq_id,
                        'data-product-id' => $model->hqProductQuote->pq_product_id,
                    ]) ?>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <?/*= $this->render('../quotes/quote_list', [
            'dataProvider' => $quotesProvider,
            'lead' => $lead,
            'leadForm' => $leadForm,
            'is_manager' => $is_manager,
        ])*/ ?>
        <i title="code: <?=\yii\helpers\Html::encode($model->hq_hash_key)?>">Hash: <?=\yii\helpers\Html::encode($model->hq_hash_key)?></i>

        <?php if ($model->hotelQuoteRooms):
            $totalAmount = 0;
            $adlTotalCount = 0;
            $chdTotalCount = 0;
            ?>
            <table class="table table-striped table-bordered">
                <?php foreach ($model->hotelQuoteRooms as $room):

                    $totalAmount += (float) $room->hqr_amount;
                    $adlTotalCount += $room->hqr_adults;
                    $chdTotalCount += $room->hqr_children;
                    ?>

<!--                    'hqr_id',-->
<!--                    'hqr_hotel_quote_id',-->
<!--                    'hqr_room_name',-->
<!--                    'hqr_key',-->
<!--                    'hqr_code',-->
<!--                    'hqr_class',-->
<!--                    'hqr_amount',-->
<!--                    'hqr_currency',-->
<!--                    'hqr_cancel_amount',-->
<!--                    'hqr_cancel_from_dt',-->
<!--                    'hqr_payment_type',-->
<!--                    'hqr_board_code',-->
<!--                    'hqr_board_name',-->
<!--                    'hqr_rooms',-->
<!--                    'hqr_adults',-->
<!--                    'hqr_children',-->

                <tr>
<!--                    <td>-->
<!--                        --><?////=Html::encode($room->hqr_id)?>
<!--                        --><?php
//                            //\yii\helpers\VarDumper::dump($room->attributes, 10, true);
//                        ?>
<!--                    </td>-->

                    <td title="<?=Html::encode($room->hqr_key)?>"><?=Html::encode($room->hqr_id)?></td>
                    <td title="code: <?=Html::encode($room->hqr_code)?>, class: <?=Html::encode($room->hqr_class)?>"><?=Html::encode($room->hqr_room_name)?></td>


<!--                    <td>--><?//=Html::encode($room->hqr_payment_type)?><!--</td>-->
                    <td title="code: <?=Html::encode($room->hqr_board_code)?>"><?=Html::encode($room->hqr_board_name)?></td>
                    <td class="text-center"><?=$room->hqr_adults ? '<i class="fa fa-user"></i> ' . ($room->hqr_adults) : '-'?></td>
                    <td class="text-center"><?=$room->hqr_children ? '<i class="fa fa-child"></i> ' . ($room->hqr_children) : '-'?></td>
                    <td>
                        <?php if ($room->hqr_cancel_amount): ?>
                        <?=Html::encode($room->hqr_cancel_amount)?>, <?=Html::encode($room->hqr_cancel_from_dt)?>
                        <?php endif; ?>
                    </td>
<!--                    <td>--><?////=Html::encode($room->hqr_id)?><!--</td>-->
                    <td class="text-right"><?=number_format($room->hqr_amount, 2)?> <?=Html::encode($room->hqr_currency)?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-right">Total: </td>
                    <td class="text-center"><?=$adlTotalCount ? '<i class="fa fa-user"></i> '. $adlTotalCount : '-'?></td>
                    <td class="text-center"><?=$chdTotalCount ? '<i class="fa fa-child"></i> '. $chdTotalCount : '-'?></td>
                    <td class="text-right"></td>

                    <?php
                        $originPrice = round((float) $model->hqProductQuote->pq_origin_price, 2);
                        $totalAmount = round($totalAmount, 2);
                    ?>

                    <td class="text-right <?=( $totalAmount !== $originPrice) ? 'danger': ''?>">
                        <b title="<?=$totalAmount?> & <?=$originPrice?>"><?=number_format($originPrice, 2)?> USD</b>
                    </td>
                </tr>
            </table>
        <?php endif; ?>

    </div>
</div>

<?php endif; ?>