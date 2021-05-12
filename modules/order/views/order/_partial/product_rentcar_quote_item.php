<?php

use common\models\Airports;
use kartik\editable\Editable;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use sales\auth\Auth;
use yii\data\ArrayDataProvider;
use yii\helpers\Inflector;
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
/**
 * @var $model ProductQuote
 */

?>

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

    $('body').on('click','.btn-rentcar-quote-details', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-lg');       
        $('#modal-lg-label').html($(this).data('title'));        
        modal.find('.modal-body').html('');
        $('#preloader').removeClass('hidden');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status == 'error') {
                alert(response);
            } else {
                $('#preloader').addClass('hidden');
                modal.modal('show');
            }
        });
    });
JS;

    $this->registerJs($js, \yii\web\View::POS_READY);
    ?>

    <?php Pjax::begin(['id' => 'pjax-product-quote-' . $model->pq_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <div class="x_panel">
        <div class="x_title">

            <span class="badge badge-white">Q<?=($model->pq_id)?></span>
            | <?= ProductQuoteStatus::asFormat($model->pq_status_id) ?>
            <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $model->pq_profit_amount ?>
            <ul class="nav navbar-right panel_toolbox">

                <!--<li
                    class="dropdown dropdown-offer-menu"
                    data-product-quote-id="<?/*=($model->pq_id)*/?>"
                    data-lead-id="<?/*=($model->pqProduct->pr_lead_id)*/?>"
                    data-url="<?/*= Url::to(['/offer/offer/list-menu-ajax'])*/?>"
                >
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="far fa-handshake"></i> Offers</a>
                    <div class="dropdown-menu" role="menu">
                        <?php /*// ajax loaded content*/?>
                    </div>
                </li>-->

                <!--<li class="dropdown dropdown-order-menu" data-product-quote-id="<?/*=($model->pq_id)*/?>" data-lead-id="<?/*=($model->pqProduct->pr_lead_id)*/?>" data-url="<?/*= Url::to(['/order/order/list-menu-ajax'])*/?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fas fa-money-check-alt"></i> Orders</a>
                    <div class="dropdown-menu" role="menu">
                        <?php /*// ajax loaded content*/?>
                    </div>
                </li>-->

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars text-warning"></i></a>
                    <div class="dropdown-menu" role="menu">
                        <h6 title="RCQuoteID: <?php echo $model->rentCarQuote->rcq_id ?>" class="dropdown-header">Quote Q<?=($model->pq_id)?></h6>
                        <?= Html::a('<i class="fa fa-search"></i> Details', null, [
                            'class' => 'btn-rentcar-quote-details dropdown-item',
                            'data-id' => $model->pq_id,
                            'data-title' => '<i class="fa fa-car"></i> ' . $model->rentCarQuote->rcq_vendor_name,
                            'data-url' => Url::to(['/rent-car/rent-car-quote/ajax-quote-details', 'id' => $model->pq_id]),
                            'title' => 'Details'
                        ]) ?>

                        <!--<?/*= Html::a('<i class="fa fa-plus-circle"></i> Add option', null, [
                            'class' => 'dropdown-item text-success btn-add-product-quote-option',
                            'data-url' => Url::to(['/product/product-quote-option/create-ajax', 'id' => $model->pq_id]),
                        ]) */?>

                        <?/*= Html::a('<i class="fa fa-list"></i> Status log', null, [
                            'class' => 'dropdown-item text-secondary btn-product-quote-status-log',
                            'data-url' => Url::to(['/product/product-quote-status-log/show', 'gid' => $model->pq_gid]),
                            'data-gid' => $model->pq_gid,
                        ]) */?>

                        <?php /*if ($model->isBookable() && Auth::can('/rent-car/rent-car-quote/book')) : */?>
                            <?php /*echo Html::a('<i class="fa fa-share-square"></i> Book', null, [
                                'class' => 'dropdown-item js-btn-book-rent-car',
                                'data-url' => Url::to('/rent-car/rent-car-quote/book'),
                                'data-rent-car-quote-id' => $model->rentCarQuote->rcq_id,
                                'data-product-id' => $model->pq_product_id,
                            ]) */?>
                        <?php /*endif */?>
                        <?php /*if ($model->isBooked() && Auth::can('/rent-car/rent-car-quote/file-generate')) : */?>
                            <?php /*echo Html::a('<i class="fa fa-file-pdf-o"></i> Generate PDF', null, [
                                'class' => 'dropdown-item js-btn-generate-pdf-rent-car',
                                'data-url' => Url::to('/rent-car/rent-car-quote/file-generate'),
                                'data-rent-car-quote-id' => $model->rentCarQuote->rcq_id,
                                'data-product-id' => $model->pq_product_id,
                            ]) */?>
                        <?php /*endif */?>

                        <?php /*if ($model->isBooked() && Auth::can('/rent-car/rent-car-quote/cancel-book')) : */?>
                            <?php /*echo Html::a('<i class="fa fa-share-square"></i> Cancel Book', null, [
                                'class' => 'dropdown-item js-btn-cancel-book-rent-car',
                                'data-url' => Url::to('/rent-car/rent-car-quote/cancel-book'),
                                'data-rent-car-quote-id' => $model->rentCarQuote->rcq_id,
                                'data-product-id' => $model->pq_product_id,
                            ]) */?>
                        <?php /*endif */?>

                        <div class="dropdown-divider"></div>
                        <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete quote', null, [
                            'class' => 'dropdown-item text-danger btn-delete-product-quote',
                            'data-product-quote-id' => $model->rentCarQuote->rcq_product_quote_id,
                            'data-model-quote-id' => $model->rentCarQuote->rcq_id,
                            'data-product-id' => $model->pq_product_id,
                        ]) */?>-->

                    </div>
                </li>
            </ul>

            <div class="clearfix"></div>
            &nbsp;<?=Html::encode($model->pq_name)?> | <?php echo $model->rentCarQuote->rcq_vendor_name ?>

        </div>
        <div class="x_content" style="display: block">
            <div class="row">
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model->rentCarQuote,
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
                            'rcq_days',
                            'rcq_price_per_day',
                            'rcq_currency',
                        ],
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= DetailView::widget([
                        'model' => $model->rentCarQuote,
                        'attributes' => [
                            [
                                'attribute' => 'rcq_options',
                                'value' => static function (RentCarQuote $model) {
                                    if (!$model->rcq_options) {
                                        return Yii::$app->formatter->nullDisplay;
                                    }
                                    $resultOption = '';
                                    foreach ($model->rcq_options as $key => $option) {
                                        $resultOption .= Inflector::humanize($key) . ' : <b>' . $option . '</b><br />';
                                    }
                                    return $resultOption;
                                },
                                'format' => 'raw',
                            ],
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="row box-rc-prices">
                <div class="col-md-12">
                    <?php Pjax::begin(['id' => 'pjax-quote_prices-' . $model->rentCarQuote->getId(), 'enablePushState' => false, 'enableReplaceState' => false]); ?>
                    <?= $this->render('rentcar_quote_item_prices', [
                        'productQuote' => $model,
                    ]); ?>

                    <?= $this->render('@frontend/views/lead/quotes/partial/_quote_option_list', ['productQuote' => $model]) ?>
                    <?= $this->render('@frontend/views/lead/quotes/partial/_quote_total', ['productQuote' => $model]) ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>

            <i class="fa fa-user"></i> <?=$model->pqOwnerUser ? Html::encode($model->pqOwnerUser->username) : '-'?>,
            <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->pq_created_dt)) ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
