<?php

use kartik\editable\Editable;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $index int */
/* @var $key int */
/* @var $rentCar \modules\rentCar\src\entity\rentCar\RentCar */
/* @var $modelQuote RentCarQuote */

?>

<?php if ($modelQuote->rcqProductQuote) : ?>
    <?php
    $js = <<<JS
        
    $(document).on('click', '.btn-product-api-service-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let quoteId = $(this).data('hotel-quote-id');
        let modal = $('#modal-lg');
          
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Api service log [' + quoteId + ']');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
     });
JS;

    $this->registerJs($js, \yii\web\View::POS_READY);
    ?>

    <?php Pjax::begin(['id' => 'pjax-product-quote-' . $modelQuote->rcqProductQuote->pq_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="x_panel">
    <div class="x_title">

        <span class="badge badge-white">Q<?=($modelQuote->rcq_product_quote_id)?></span>
            | <?=Html::encode($modelQuote->rcqProductQuote->pq_name)?>

            | <?= ProductQuoteStatus::asFormat($modelQuote->rcqProductQuote->pq_status_id) ?>

        <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $modelQuote->rcqProductQuote->pq_profit_amount ?>

        <ul class="nav navbar-right panel_toolbox">

            <li
                class="dropdown dropdown-offer-menu"
                data-product-quote-id="<?=($modelQuote->rcq_product_quote_id)?>"
                data-lead-id="<?=($rentCar->prcProduct->pr_lead_id)?>"
                data-url="<?= Url::to(['/offer/offer/list-menu-ajax'])?>"
            >
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="far fa-handshake"></i> Offers</a>
                <div class="dropdown-menu" role="menu">
                    <?php // ajax loaded content?>
                </div>
            </li>

            <li class="dropdown dropdown-order-menu" data-product-quote-id="<?=($modelQuote->rcq_product_quote_id)?>" data-lead-id="<?=($rentCar->prcProduct->pr_lead_id)?>" data-url="<?= Url::to(['/order/order/list-menu-ajax'])?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fas fa-money-check-alt"></i> Orders</a>
                <div class="dropdown-menu" role="menu">
                    <?php // ajax loaded content?>
                </div>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars text-warning"></i></a>
                <div class="dropdown-menu" role="menu">
                    <h6 class="dropdown-header">Quote Q<?=($modelQuote->rcq_product_quote_id)?></h6>

                    <?= Html::a('<i class="fa fa-list"></i> Status log', null, [
                        'class' => 'dropdown-item text-secondary btn-product-quote-status-log',
                        'data-url' => Url::to(['/product/product-quote-status-log/show', 'gid' => $modelQuote->rcqProductQuote->pq_gid]),
                        'data-gid' => $modelQuote->rcqProductQuote->pq_gid,
                    ]) ?>

                    <div class="dropdown-divider"></div>
                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete quote', null, [
                        'class' => 'dropdown-item text-danger btn-delete-product-quote',
                        'data-product-quote-id' => $modelQuote->rcq_product_quote_id,
                        'data-model-quote-id' => $modelQuote->rcq_id,
                        'data-product-id' => $modelQuote->rcqProductQuote->pq_product_id,
                    ]) ?>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">
        <div class="row">
             <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $modelQuote,
                    'attributes' => [
                        [
                            'attribute' => 'rcq_model_name',
                            'value' => function (RentCarQuote $model) {
                                if (!$model->rcq_model_name) {
                                    return Yii::$app->formatter->nullDisplay;
                                }
                                $result = $model->rcq_model_name;
                                if ($model->rcq_image_url) {
                                    $result .= ' <img src="' . $model->rcq_image_url . '" height="21" />';
                                }
                                return $result;
                            },
                            'format' => 'raw',
                        ],
                        'rcq_category',
                        [
                            'attribute' => 'rcq_vendor_name',
                            'value' => function (RentCarQuote $model) {
                                if (!$model->rcq_vendor_name) {
                                    return Yii::$app->formatter->nullDisplay;
                                }
                                $result = $model->rcq_vendor_name;
                                if ($model->rcq_vendor_logo_url) {
                                    $result .= ' <img src="' . $model->rcq_vendor_logo_url . '" height="14" />';
                                }
                                return $result;
                            },
                            'format' => 'raw',
                        ],
                        'rcq_price_per_day',
                        'rcq_currency',
                    ],
                ]) ?>
             </div>
             <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $modelQuote,
                    'attributes' => [
                        [
                            'attribute' => 'rcq_options',
                            'value' => static function (RentCarQuote $model) {
                                if (!$model->rcq_options) {
                                    return Yii::$app->formatter->nullDisplay;
                                }
                                $resultOption = '';
                                foreach ($model->rcq_options as $key => $option) {
                                    $resultOption .= ucfirst($key) . ' : <b>' . $option . '</b><br />';
                                }
                                return $resultOption;
                            },
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'rcq_advantages',
                            'value' => static function (RentCarQuote $model) {
                                if (!$model->rcq_advantages) {
                                    return Yii::$app->formatter->nullDisplay;
                                }
                                $result = '';
                                foreach ($model->rcq_advantages as $key => $advantage) {
                                    $result .= '<span class="text-success">' . $advantage . '</span><br />';
                                }
                                return $result;
                            },
                            'format' => 'raw',
                        ],
                    ],
                ]) ?>
            </div>
        </div>

        <i class="fa fa-user"></i> <?=$modelQuote->rcqProductQuote->pqCreatedUser ? Html::encode($modelQuote->rcqProductQuote->pqCreatedUser->username) : '-'?>,
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($modelQuote->rcqProductQuote->pq_created_dt)) ?>

        <?= $this->render('@frontend/views/lead/quotes/partial/_quote_option_list', ['productQuote' => $modelQuote->rcqProductQuote]) ?>
        <?= $this->render('@frontend/views/lead/quotes/partial/_quote_total', ['productQuote' => $modelQuote->rcqProductQuote]) ?>

    </div>
</div>
    <?php Pjax::end(); ?>

<?php endif; ?>