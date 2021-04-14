<?php

/* @var $this yii\web\View */
/* @var $index int */
/* @var $key int */
/* @var $cruiseProduct Cruise */
/* @var $model ProductQuote */

use kartik\editable\Editable;
use modules\cruise\src\entity\cruise\Cruise;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>


    <?php
    $js = <<<JS
    $('body').off('click', '.btn-book-quote').on('click', '.btn-book-quote', function (e) {

        if(!confirm('Are you sure you want to book this quote?')) {
            return '';
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('cruise-quote-id');
        let productId = $(this).data('product-id');
        
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {'id': quoteId},
          cache: false,
          dataType: 'json',
        }).done(function(data) {
            if (parseInt(data.status) === 1) {
                new PNotify({
                    title: 'The quote was successfully booking',
                    type: 'success',
                    text: data.message,
                    hide: true
                });
                pjaxReload({
                    container: '#pjax-product-quote-list-' + productId
                });
            } else {
                new PNotify({
                    title: 'Booking failed',
                    type: 'error',
                    text: data.message,
                    hide: true
                });
                pjaxReload({
                    container: '#pjax-product-quote-list-' + productId
                });
            }
        })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
            $('#preloader').addClass('d-none');
        });
    });
    
    $('body').off('click', '.btn-cancel-book-quote').on('click', '.btn-cancel-book-quote', function (e) {

        if(!confirm('Are you sure you want to cancel book this quote?')) {
            return '';
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('cruise-quote-id');
        let productId = $(this).data('product-id');
        
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {'id': quoteId},
          cache: false,
          dataType: 'json',
        }).done(function(data) {
            if (parseInt(data.status) === 1) {
                new PNotify({
                    title: 'Booking is canceled',
                    type: 'success',
                    text: data.message,
                    hide: true
                });
                pjaxReload({
                    container: '#pjax-product-quote-list-' + productId
                });
            } else {
                new PNotify({
                    title: 'Process failed',
                    type: 'error',
                    text: data.message,
                    hide: true
                });
                pjaxReload({
                    container: '#pjax-product-quote-list-' + productId
                });
            }
        })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
            $('#preloader').addClass('d-none');
        });
    });
    
    $(document).on('click', '.btn-product-api-service-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let quoteId = $(this).data('cruise-quote-id');
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

    <?php Pjax::begin(['id' => 'pjax-product-quote-' . $model->pq_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <div class="x_panel">
        <div class="x_title">

            <span class="badge badge-white">Q<?=($model->cruiseQuote->crq_product_quote_id)?></span>
            <?= ProductQuoteStatus::asFormat($model->pq_status_id) ?>
            | "<b><?=\yii\helpers\Html::encode($model->cruiseQuote->crq_data_json['cruiseLine']['name'])?></b>"

            <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $model->pq_profit_amount ?>

            <ul class="nav navbar-right panel_toolbox">
                <li class="dropdown dropdown-offer-menu" data-product-quote-id="<?=($model->cruiseQuote->crq_product_quote_id)?>" data-lead-id="<?=($model->pqProduct->pr_lead_id)?>" data-url="<?= Url::to(['/offer/offer/list-menu-ajax'])?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="far fa-handshake"></i> Offers</a>
                    <div class="dropdown-menu" role="menu">
                        <?php // ajax loaded content ?>
                    </div>
                </li>

                <li class="dropdown dropdown-order-menu" data-product-quote-id="<?=($model->cruiseQuote->crq_product_quote_id)?>" data-lead-id="<?=($model->pqProduct->pr_lead_id)?>" data-url="<?= Url::to(['/order/order/list-menu-ajax'])?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fas fa-money-check-alt"></i> Orders</a>
                    <div class="dropdown-menu" role="menu">
                        <?php // ajax loaded content ?>
                    </div>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars text-warning"></i></a>
                    <div class="dropdown-menu" role="menu">
                        <h6 class="dropdown-header">Quote Q<?=($model->cruiseQuote->crq_product_quote_id)?></h6>


                        <?php echo Html::a('<i class="fa fa-plus-circle"></i> Add option', null, [
                            'class' => 'dropdown-item text-success btn-add-product-quote-option',
                            'data-url' => Url::to(['/product/product-quote-option/create-ajax', 'id' => $model->cruiseQuote->crq_product_quote_id]),
                        ]) ?>

                        <div class="dropdown-divider"></div>
                        <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete quote', null, [
                            'class' => 'dropdown-item text-danger btn-delete-product-quote',
                            'data-product-quote-id' => $model->cruiseQuote->crq_product_quote_id,
                            'data-cruise-quote-id' => $model->cruiseQuote->crq_id,
                            'data-product-id' => $model->pq_product_id,
                        ]) ?>
                    </div>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>

        <div class="offer__description w-100">
            <div class="offer__item-brand d-flex flex-column mb-3">
                <h5 class="mb-0">
                    <img height="20px" src="<?= $model->cruiseQuote->crq_data_json['cruiseLine']['logoImage']['standard'] ?>" alt="<?= $model->cruiseQuote->crq_data_json['cruiseLine']['name'] . ', ' . $model->cruiseQuote->crq_data_json['ship']['name'] ?>" class="cruise-line-logo">
                    <?= $model->cruiseQuote->crq_data_json['ship']['name'] ?>
                </h5>
            </div>
            <ul class="offer__option-list list-unstyled mb-4">
                <li class="offer__option mb-2">
                    <div class="d-flex">
                        <div>
                            <b class="offer-option__key text-secondary">Destination</b>: <?= $model->cruiseQuote->crq_data_json['itinerary']['destination']['destination'] ?> (<?= $model->cruiseQuote->crq_data_json['itinerary']['destination']['subDestination'] ?>)
                        </div>
                        <div class="ml-4">
                            <b class="offer-option__key text-secondary">Dates</b>:
                            <span class="offer-option__value"><?= date('F j, Y', strtotime($model->cruiseQuote->crq_data_json['departureDate'])) ?> - <?= date('F j, Y', strtotime($model->cruiseQuote->crq_data_json['returnDate']))?></span>
                        </div>
                    </div>
                </li>
                <?php if (!empty($model->cruiseQuote->crq_data_json['itinerary']['locations'])) : ?>
                    <li class="offer__option d-flex">
                        <b class="offer-option__key text-secondary">Itinerary</b>:&nbsp;
                        <ul class="offer-option__value list-unstyled d-flex offer__itinerary-list flex-wrap">
                            <?php foreach ($model->cruiseQuote->crq_data_json['itinerary']['locations'] as $location) : ?>
                                <li style="margin-right: 5px">
                                    <span> <b><?= $location['location']['name']?></b> (<?= $location['location']['countryName']?>)</span>
                                </li>
                            <?php endforeach;?>
                        </ul>
                    </li>
                <?php endif;?>
            </ul>
        </div>

        <div class="x_content" style="display: block">

            <i class="fa fa-user"></i> <?=$model->pqCreatedUser ? Html::encode($model->pqCreatedUser->username) : '-'?>,
            <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->pq_created_dt)) ?>

            <?php
            $sfs = round(($model->cruiseQuote->crq_amount + $model->cruiseQuote->crq_system_mark_up + $model->cruiseQuote->crq_agent_mark_up) * $model->cruiseQuote->crq_service_fee_percent / 100, 2);
            ?>
            <div class="overflow_auto" style="overflow: auto">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th width="150">Cabin Name</th>
                        <th>Per person, $</th>
                        <th>Adult</th>
                        <th>Children</th>
                        <th>NP, $</th>
                        <th>Mkp, $</th>
                        <th>Ex Mkp, $</th>
                        <th>SFP, %</th>
                        <th>SFS, $</th>
                        <th>SP, $</th>
                    </tr>
                    <tr>
                        <th><?= $model->cruiseQuote->crq_data_json['cabin']['name'] ?></th>
                        <th><?= $model->cruiseQuote->crq_amount_per_person ?></th>
                        <th><?= $model->cruiseQuote->crq_adults ?></th>
                        <th><?= $model->cruiseQuote->crq_children ?></th>
                        <th><?= $model->cruiseQuote->crq_amount ?></th>
                        <th><?= $model->cruiseQuote->crq_system_mark_up ?></th>
                        <td>
                            <?= Editable::widget([
                                'name' => 'extra_markup[' . $model->cruiseQuote->crq_id . ']',
                                'asPopover' => false,
                                'pjaxContainerId' => 'pjax-product-quote-' . $model->pq_id,
                                'value' => number_format($model->cruiseQuote->crq_agent_mark_up, 2),
                                'header' => 'Extra markup',
                                'size' => 'sm',
                                'inputType' => Editable::INPUT_TEXT,
                                'buttonsTemplate' => '{submit}',
                                'pluginEvents' => ['editableSuccess' => "function(event, val, form, data) { pjaxReload({container: '#pjax-product-quote-{$model->pq_id}'}); }",],
                                'inlineSettings' => [
                                    'templateBefore' => '<div class="editable-pannel">{loading}',
                                    'templateAfter' => '{buttons}{close}</div>'],
                                'options' => ['class' => 'form-control','style' => 'width:50px;', 'placeholder' => 'Enter extra markup','resetButton' => '<i class="fa fa-ban"></i>'],
                                'formOptions' => [
                                    'action' => Url::toRoute(['/cruise/cruise-quote/ajax-update-agent-markup'])
                                ]
                            ]) ?>
                        </td>
                        <th><?= $model->cruiseQuote->crq_service_fee_percent ?></th>
                        <th><?= $sfs ?></th>
                        <th><?= $total = number_format($model->cruiseQuote->crq_amount + $model->cruiseQuote->crq_system_mark_up + $model->cruiseQuote->crq_agent_mark_up + $sfs, 2)?> <?=Html::encode($model->cruiseQuote->crq_currency)?> </th>
                    </tr>
                    <tr>
                        <td class="text-right">Total: </td>
                        <td class="text-left" colspan="9"><?= $total ?></td>
                    </tr>

                </table>
            </div>

            <?= $this->render('@frontend/views/lead/quotes/partial/_quote_option_list', ['productQuote' => $model]) ?>
            <?= $this->render('@frontend/views/lead/quotes/partial/_quote_total', ['productQuote' => $model]) ?>

        </div>
    </div>
    <?php Pjax::end(); ?>

