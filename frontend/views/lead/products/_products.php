<?php
/**
 * @var $this \yii\web\View
 * @var $lead Lead
 * @var $leadForm LeadForm
 * @var $itineraryForm ItineraryEditForm
 * @var $is_manager bool
 * @var $quotesProvider \yii\data\ActiveDataProvider
 *
 */

use common\models\Lead;
use frontend\models\LeadForm;
use sales\forms\lead\ItineraryEditForm;
use yii\helpers\Html;

?>

<?php

    $products = $lead->products;
    //\yii\helpers\VarDumper::dump($products);
    $items = [];
?>

<?php \yii\widgets\Pjax::begin(['id' => 'product-accordion', 'enablePushState' => false, 'enableReplaceState' => false])?>

<div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-plane"></i> Flight - default</h2>
            <ul class="nav navbar-right panel_toolbox">
                <?/*php if ($is_manager) : ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                        <div class="dropdown-menu" role="menu">
                            <?= Html::a('<i class="fa fa-remove text-danger"></i> Delete product', null, [
                                'class' => 'dropdown-item text-danger bt-delete-product',
                                'data-product-id' => 1
                            ]) ?>
                        </div>
                    </li>
                <?php endif;*/ ?>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: <?=$products ? 'none' : 'block'?>">
			<?php
			$js = <<<JS
pjaxOffFormSubmit('#pj-itinerary');
JS;
			$this->registerJs($js);
			?>
            <?php \yii\widgets\Pjax::begin(['id' => 'pj-itinerary', 'enablePushState' => false, 'enableReplaceState' => false])?>
            <?= $this->render('../partial/_flightDetails', [
                'itineraryForm' => $itineraryForm,
            ]) ?>
            <?php \yii\widgets\Pjax::end();?>

            <?= $this->render('../quotes/quote_list', [
                'dataProvider' => $quotesProvider,
                'lead' => $lead,
                'leadForm' => $leadForm,
                'is_manager' => $is_manager,
            ]) ?>
        </div>
    </div>

<?php foreach ($products as $product):?>

    <?php if ((int) $product->isHotel() && $product->hotel): ?>
        <?= $this->render('@modules/hotel/views/hotel/partial/_product_hotel', [
            'product' => $product,
        ]) ?>
    <?php endif; ?>

    <?php if ((int) $product->isFlight() && $product->flight): ?>
        <?= $this->render('@modules/flight/views/flight/partial/_product_flight', [
            'product' => $product,
        ]) ?>
    <?php endif; ?>

<?php endforeach; ?>

<?php \yii\widgets\Pjax::end()?>

<?php

$ajaxDeleteProductUrl = \yii\helpers\Url::to(['/product/product/delete-ajax']);
$ajaxDeleteProductQuoteUrl = \yii\helpers\Url::to(['/product/product-quote/delete-ajax']);


$js = <<<JS

     $(document).on('click', '.btn-product-quote-status-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let gid = $(this).data('gid');
        let modal = $('#modal-lg');
          
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Product quote [' + gid + '] status history');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
     });

    $('body').on('click', '.btn-delete-product', function(e) {
        
        if(!confirm('Are you sure you want to delete this product?')) {
            return '';
        }
        
      e.preventDefault();
      $('#preloader').removeClass('d-none');
      let productId = $(this).data('product-id');
      
      /*alert(productId);
      
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');*/

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: '$ajaxDeleteProductUrl',
          type: 'post',
          data: {'id': productId},
  //        contentType: false,
  //        cache: false,
//          processData: false,
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  new PNotify({
                        title: 'Error: delete product',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  $.pjax.reload({
                      container: '#product-accordion'
                  });
                  new PNotify({
                        title: 'The product was successfully removed',
                        type: 'success',
                        text: data.message,
                        hide: true
                    });
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
            //btnSubmit.prop('disabled', false);
            //btnSubmit.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
            //alert( "complete" );
            $('#preloader').addClass('d-none');
        });
      // return false;
    });


    $('body').on('click', '.btn-delete-product-quote', function(e) {
        
        let productQuoteId = $(this).data('product-quote-id');
        let productId = $(this).data('product-id');
        
      if(!confirm('Are you sure you want to delete quote ('+ productQuoteId +') ?')) {
        return '';
      }
        
      e.preventDefault();
      $('#preloader').removeClass('d-none');
      
      
      /*alert(productId);
      
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');*/

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: '$ajaxDeleteProductQuoteUrl',
          type: 'post',
          data: {'id': productQuoteId},
  //        contentType: false,
  //        cache: false,
//          processData: false,
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  new PNotify({
                        title: 'Error: delete product quote',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  $.pjax.reload({
                      container: '#pjax-product-quote-list-' + productId
                  });
                  new PNotify({
                        title: 'The product quote was successfully removed',
                        type: 'success',
                        text: data.message,
                        hide: true
                    });
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
            //btnSubmit.prop('disabled', false);
            //btnSubmit.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
            //alert( "complete" );
            $('#preloader').addClass('d-none');
        });
      // return false;
    });
    
    
    $('body').off('click', '.btn-add-product-quote-option').on('click', '.btn-add-product-quote-option', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Add quote option');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
    });
    
    $('body').off('click', '.btn-update-product-quote-option').on('click', '.btn-update-product-quote-option', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update quote option');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
    });
    
    $('body').off('click', '.btn-delete-product-quote-option').on('click', '.btn-delete-product-quote-option', function(e) {
        
        if(!confirm('Are you sure you want to delete this option?')) {
            return '';
        }
        
      e.preventDefault();
      $('#preloader').removeClass('d-none');
      let optionId = $(this).data('pqo-id');
      let productId = $(this).data('product-id');
      let url = $(this).data('url');
     
      /*alert(productId);
      
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');*/

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',
          data: {'id': optionId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  new PNotify({
                        title: 'Error: delete quote option',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  
                  new PNotify({
                        title: 'The option was successfully removed',
                        type: 'success',
                        text: data.message,
                        hide: true
                  });
                  
                  $.pjax.reload({
                      container: '#pjax-product-quote-list-' + productId
                  });
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
            $('#preloader').addClass('d-none');
        });
      // return false;
    });
    
    /*$("#product-accordion").on("pjax:start", function () {            
        $('#preloader').removeClass('d-none');
    });

    $("#product-accordion").on("pjax:end", function () {           
       $('#preloader').addClass('d-none');
    }); */
    
    
JS;

$this->registerJs($js, \yii\web\View::POS_READY);

    //echo \yii\bootstrap4\Accordion::widget(['items' => $items]);

