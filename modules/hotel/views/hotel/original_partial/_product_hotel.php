<?php

use modules\product\src\entities\product\Product;
use modules\hotel\models\search\HotelQuoteSearch;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $product Product */

$pjaxId = 'pjax-product-' . $product->pr_id;
?>
    <style>
        .x_panel_original_product_hotel {background-color: #fff3cd;}
    </style>
<?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 4000])?>
    <div class="x_panel x_panel_original_product_hotel">
        <div class="x_title">
            <h2>
                <a class="collapse-link">
                    <i class="<?= Html::encode($product->getIconClass()) ?>" title="ID: <?=$product->pr_id?>"></i> <?=Html::encode($product->prType->pt_name)?> <?=$product->pr_name ? ' - ' . Html::encode($product->pr_name) : ''?>
                    <span style="color: #53a265" class="product-quote-counter-<?= $product->pr_id ?>" data-value="<?=count($product->productQuotes)?>">
                        <?php if ($product->productQuotes) :?>
                            <sup title="Number of quotes">(<?=count($product->productQuotes)?>)</sup>
                        <?php endif;?>
                    </span>
                </a>
                <?php if ($product->pr_description) :?>
                    <a  id="product_description_<?=$product->pr_id?>"
                        class="popover-class fa fa-info-circle text-info"
                        data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top"
                        data-container="body" title="<?=Html::encode($product->pr_name)?>"
                        data-content='<?=Html::encode($product->pr_description)?>'
                    ></a>
                <?php endif; ?>
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <?php //php if ($is_manager) : ?>
                    <!--                    <li>-->
                    <!--                        --><?php //=Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>
                    <!--                    </li>-->
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">


                            <?php if ($product->hotel->ph_destination_code) :?>
                                (<b><?= Html::encode($product->hotel->ph_destination_code) ?></b>)
                                <?= Html::encode($product->hotel->ph_destination_label) ?>
                            <?php endif; ?>


                        </span>
                    </li>
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                             <?php if ($product->hotel->ph_check_in_date) :?>
                                 <b><?= Yii::$app->formatter->asDate(strtotime($product->hotel->ph_check_in_date)) ?></b>
                             <?php endif; ?>
                        </span>
                    </li>
                <li>
                    <div style="margin-right: 50px"></div>
                </li>


                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
                <?php //php endif; ?>

            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none">
            <?php //php if ((int) $product->pr_type_id === \common\models\ProductType::PRODUCT_HOTEL && $product->hotel): ?>
                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-product-search-' . $product->pr_id, 'enablePushState' => false, 'timeout' => 5000])?>
                <?= $this->render('_view_search', [
                    'model' => $product->hotel,
                    //'dataProviderQuotes' => $dataProviderQuotes
                    //'dataProviderRooms'
                ]) ?>
                <?php \yii\widgets\Pjax::end();?>
            <?php //php endif; ?>
        </div>
    </div>
<?php
\yii\widgets\Pjax::end();