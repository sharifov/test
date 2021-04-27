<?php

/* @var $this yii\web\View */
/* @var $index int */
/* @var $key int */
/* @var $hotelProduct Hotel */
/* @var $model HotelQuote */
/* @var $lead \common\models\Lead */

use kartik\editable\Editable;
use modules\hotel\models\Hotel;
use modules\hotel\models\HotelQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use sales\auth\Auth;
use sales\services\CurrencyHelper;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>
<?php if ($model->hqProductQuote) : ?>
    <?php Pjax::begin(['id' => 'pjax-product-quote-' . $model->hqProductQuote->pq_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="x_panel">
    <div class="x_title">

        <span class="badge badge-white">Q<?=($model->hq_product_quote_id)?></span> Hotel "<b><?=\yii\helpers\Html::encode($model->hqHotelList->hl_name)?></b>"
            (<?=\yii\helpers\Html::encode($model->hqHotelList->hl_star)?>),
            <?php //=\yii\helpers\Html::encode($model->hqProductQuote->pq_name)?>
            <?=\yii\helpers\Html::encode($model->hq_destination_name ?? '')?>
             <?php //=\yii\helpers\Html::encode($model->hqProductQuote->pq_gid)?>

        | <?= ProductQuoteStatus::asFormat($model->hqProductQuote->pq_status_id) ?>

        <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $model->hqProductQuote->pq_profit_amount ?>

        <?php if ($model->hqProductQuote->pq_clone_id) : ?>
            <span class="badge badge-warning" style="padding-left: 5px">CLONE</span>
        <?php endif;?>

        <ul class="nav navbar-right panel_toolbox">
            <li class="create-product-alternative" data-product-quote-id="<?=($model->hq_product_quote_id)?>" data-lead-id="<?= $lead->id ?>" data-url="<?= Url::to(['/hotel/hotel/ajax-create-alternative-product'])?>">
                <a href="#" title="Create a product to search for an alternative offer"><i class="fa fa-hotel"></i></a>
            </li>
        </ul>

        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <?php /*= $this->render('../quotes/quote_list', [
            'dataProvider' => $quotesProvider,
            'lead' => $lead,
            'leadForm' => $leadForm,
            'is_manager' => $is_manager,
        ])*/ ?>
        <i class="fa fa-user"></i> <?=$model->hqProductQuote->pqCreatedUser ? Html::encode($model->hqProductQuote->pqCreatedUser->username) : '-'?>,
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->hqProductQuote->pq_created_dt)) ?>,
        <i title="code: <?=\yii\helpers\Html::encode($model->hq_hash_key)?>">Hash: <?=\yii\helpers\Html::encode($model->hq_hash_key)?></i>

        <?php if ($model->hotelQuoteRooms) :
            $totalAmountRoom = 0;
            $adlTotalCount = 0;
            $chdTotalCount = 0;
            $totalNp = 0;
            $totalMkp = 0;
            $totalExMkp = 0;
            $totalSfs = 0;
            $totalSp = 0;
            ?>
            <div class="overflow_auto" style="overflow: auto">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>Room Name</th>
                        <th>Board Name</th>
                        <th>Pax</th>
                        <th>Cancel Amount</th>
                        <th>NP, $</th>
                        <th>Mkp, $</th>
                        <th>Ex Mkp, $</th>
                        <th>SFP, %</th>
                        <th>SFS, $</th>
                        <th>SP, $</th>
                    </tr>
                    <?php foreach ($model->hotelQuoteRooms as $room) :
                        $totalAmountRoom += (float) $room->hqr_amount;
                        $adlTotalCount += $room->hqr_adults;
                        $chdTotalCount += $room->hqr_children;
                        $totalNp += $room->hqr_amount;
                        $totalMkp += $room->hqr_system_mark_up;
                        $totalExMkp += $room->hqr_agent_mark_up;

                        $sfs = round(($room->hqr_amount + $room->hqr_system_mark_up + $room->hqr_agent_mark_up) * $room->hqr_service_fee_percent / 100, 2);
                        $totalSfs += $sfs;

                        $sp = CurrencyHelper::roundUp($room->hqr_amount + $room->hqr_system_mark_up + $room->hqr_agent_mark_up + $sfs);
                        $totalSp += $sp;
                        ?>

                    <tr>

                        <td title="<?=Html::encode($room->hqr_key)?>"><?=Html::encode($room->hqr_id)?></td>
                        <td title="code: <?=Html::encode($room->hqr_code)?>, class: <?=Html::encode($room->hqr_class)?>"><?=Html::encode($room->hqr_room_name)?></td>

                        <td title="code: <?=Html::encode($room->hqr_board_code)?>">
                            <?=Html::encode($room->hqr_board_name)?>
                            <?php if ($room->hqr_rate_comments) :?>
                                <i class="fa fa-info-circle green" title="Rate Comments: <?=Html::encode($room->hqr_rate_comments)?>"></i>
                            <?php endif;?>
                        </td>
                        <td class="text-center">
                            <?=$room->hqr_adults ? '<i class="fa fa-user"></i> ' . ($room->hqr_adults) : '-'?>
                            <?=$room->hqr_children ? ', <i class="fa fa-child"></i> ' . ($room->hqr_children) : '-'?>
                        </td>
                        <td>
                            <?php if ($room->getActualCancelAmount()) : ?>
                                <?=Html::encode($room->getActualCancelAmount())?>, <?=Html::encode($room->getActualCancelDate())?>
                            <?php endif; ?>
                        </td>
                        <td><?= Html::encode($room->hqr_amount) ?></td>
                        <td><?= Html::encode($room->hqr_system_mark_up) ?></td>
                        <td><?= Html::encode(number_format($room->hqr_agent_mark_up, 2)) ?></td>
                        <td><?= Html::encode($room->hqr_service_fee_percent) ?>%</td>
                        <td><?= $sfs ?></td>
    <!--                    <td>--><?php ////=Html::encode($room->hqr_id)?><!--</td>-->
                        <td class="text-right"><?= $sp ?> <?=Html::encode($room->hqr_currency)?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-right">Room Total: </td>
                        <td class="text-center">
                            <?=$adlTotalCount ? '<i class="fa fa-user"></i> ' . $adlTotalCount : '-'?>
                            <?=$chdTotalCount ? ', <i class="fa fa-child"></i> ' . $chdTotalCount : '-'?>
                        </td>
                        <td class="text-right"></td>
                        <td class="text-right"><?= number_format($totalNp, 2) ?></td>
                        <td class="text-right"><?= number_format($totalMkp, 2) ?></td>
                        <td class="text-right"><?= number_format($totalExMkp, 2) ?></td>
                        <td class="text-right"></td>
                        <td class="text-right"><?= number_format($totalSfs, 2) ?></td>

                        <?php
                            $price = round((float) $model->hqProductQuote->pq_price, 2);
                            $totalAmountRoom = round($totalAmountRoom, 2);
                        ?>

                        <td class="text-right">
                            <b><?=number_format($totalSp, 2)?> USD</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right">Total: </td>
                        <td class="text-center">
                            <?=$adlTotalCount ? '<i class="fa fa-user"></i> ' . $adlTotalCount : '-'?>
                            <?=$chdTotalCount ? ', <i class="fa fa-child"></i> ' . $chdTotalCount : '-'?>
                        </td>
                        <td class="text-right"></td>
                        <td class="text-right"><?= number_format($totalNp * $model->getCountDays(), 2) ?></td>
                        <td class="text-right"><?= number_format($totalMkp * $model->getCountDays(), 2) ?></td>
                        <td class="text-right"><?= number_format($totalExMkp * $model->getCountDays(), 2) ?></td>
                        <td class="text-right"></td>
                        <?php
                            $totalFeeSum = round($totalSfs * $model->getCountDays(), 2);
                            $feeSum = round((float) $model->hqProductQuote->pq_service_fee_sum, 2);
                        ?>

                        <td class="text-right <?=($totalFeeSum !== $feeSum) ? 'danger' : ''?>">
                            <b title="<?=$totalFeeSum?> & <?=$feeSum?>"><?=number_format($feeSum, 2)?> USD</b>
                        </td>

                        <?php
                            $totalPrice = round($totalSp * $model->getCountDays(), 2);
                            $price = round((float) $model->hqProductQuote->pq_price, 2);
                        ?>

                        <td class="text-right <?=($totalPrice !== $price) ? 'danger' : ''?>">
                            <b title="<?=$totalPrice?> & <?=$price?>"><?=number_format($price, 2)?> USD</b>
                        </td>
                    </tr>

                </table>
            </div>
        <?php endif; ?>

        <?= $this->render('@frontend/views/lead/quotes/partial/_quote_option_list', ['productQuote' => $model->hqProductQuote]) ?>
        <?= $this->render('@frontend/views/lead/quotes/partial/_quote_total', ['productQuote' => $model->hqProductQuote]) ?>

    </div>
</div>
    <?php Pjax::end(); ?>

<?php endif; ?>