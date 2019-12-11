<?php
/* @var $this yii\web\View */
/* @var $model Order */
/* @var $index integer */

use common\models\Order;
use yii\bootstrap4\Html;

?>

<div class="x_panel">
    <div class="x_title">

        <small><span class="badge badge-white">OR<?=($model->or_id)?></span></small>
        "<b><?=\yii\helpers\Html::encode($model->or_name)?></b>"
        (<span title="UID"><?=\yii\helpers\Html::encode($model->or_uid)?></span>)
        <?=$model->getStatusLabel()?>
        <?=$model->getPayStatusLabel()?>

        <ul class="nav navbar-right panel_toolbox">
            <!--            <li>-->
            <!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
            <!--            </li>-->
            <li>
                <?= Html::a('<i class="fa fa-edit warning"></i> Update order', null, [
                    'data-url' => \yii\helpers\Url::to(['/order/update-ajax', 'id' => $model->or_id]),
                    'class' => 'btn-update-order'
                ])?>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                <div class="dropdown-menu" role="menu">
                    <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete order', null, [
                        'class' => 'dropdown-item text-danger btn-delete-order',
                        'data-order-id' => $model->or_id,
                        'data-url' => \yii\helpers\Url::to(['/order/delete-ajax']),
                    ]) ?>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <table class="table table-bordered">
            <?php if ($model->orderProducts):

                $originTotalPrice = 0;
                $clientTotalPrice = 0;

                ?>
                <tr>
                    <th>Quote ID</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th title="Service FEE">FEE</th>
                    <th title="Origin Price, USD">Price, USD</th>
                    <th>Client Price</th>
                    <th></th>
                </tr>
                <?php if ($model->orderProducts):?>
                <?php foreach ($model->orderProducts as $product):
                    $quote = $product->orpProductQuote;
                    $originTotalPrice += $quote->pq_price;
                    $clientTotalPrice += $quote->pq_client_price;
                    ?>
                    <tr>
                        <td title="Product Quote ID"><?=Html::encode($quote->pq_id)?></td>
                        <td title="<?=Html::encode($quote->pq_product_id)?>">
                            <?=Html::encode($quote->pqProduct->prType->pt_name)?>
                            <?=$quote->pqProduct->pr_name ? ' - ' . Html::encode($quote->pqProduct->pr_name) : ''?>
                        </td>

                        <!--                    <td>--><?//=\yii\helpers\VarDumper::dumpAsString($quote->attributes, 10, true)?><!--</td>-->

                        <td><?=Html::encode($quote->pq_name)?></td>
                        <td><?=$quote->getStatusLabel()?></td>
                        <td><?=$quote->pq_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($quote->pq_created_dt)) : '-'?></td>
                        <td class="text-right"><?=number_format($quote->pq_service_fee_sum, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_price, 2)?></td>
                        <td class="text-right"><?=number_format($quote->pq_client_price, 2)?> <?=Html::encode($quote->pq_client_currency)?></td>
                        <td>
                            <?php
                            echo Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i>', null, [
                                'data-order-id' => $model->or_id,
                                'data-product-quote-id' => $quote->pq_id,
                                'class' => 'btn-delete-quote-from-order',
                                'data-url' => \yii\helpers\Url::to(['order-product/delete-ajax'])
                            ]);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th class="text-right" colspan="5">Total: </th>
                    <th class="text-right"></th>
                    <th class="text-right"><?=number_format($originTotalPrice, 2)?></th>
                    <th class="text-right"><?=number_format($clientTotalPrice, 2)?> <?=Html::encode($quote->pq_client_currency)?></th>
                    <th></th>
                </tr>
            <?php endif; ?>
            <?php endif; ?>
        </table>

        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->or_created_dt)) ?>
        <i class="fa fa-user"></i> <?=$model->orCreatedUser ? Html::encode($model->orCreatedUser->username) : '-'?>
    </div>
</div>