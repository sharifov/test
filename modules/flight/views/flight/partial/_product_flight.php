<?php

use common\models\Product;
use modules\hotel\models\search\HotelQuoteSearch;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $product Product */



/*\yii\web\YiiAsset::register($this);


$searchModel = new HotelQuoteSearch();
$params = Yii::$app->request->queryParams;
$params['HotelQuoteSearch']['hq_hotel_id'] = $model->ph_id;
$dataProviderQuotes = $searchModel->searchProduct($params);*/

?>
<?//php \yii\widgets\Pjax::begin(['id' => 'pjax-product-' . $product->pr_id, 'enablePushState' => false, 'timeout' => 10000])?>
    <div class="x_panel">
        <div class="x_title">
            <h2>
                <i class="fas fa-plane" title="ID: <?=$product->pr_id?>"></i> <?=Html::encode($product->prType->pt_name)?> <?=$product->pr_name ? ' - ' . Html::encode($product->pr_name) : ''?>
                <?php if ($product->pr_description):?>
                    <i class="fa fa-info-circle text-info" title="<?=Html::encode($product->pr_description)?>"></i>
                <?php endif;?>
                (<?=count($product->productQuotes)?>)
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <?//php if ($is_manager) : ?>
                    <!--                    <li>-->
                    <!--                        --><?//=Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>
                    <!--                    </li>-->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                        <div class="dropdown-menu" role="menu">
                            <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                            <?= Html::a('<i class="fa fa-search"></i> Search Quotes', null, [
                                'data-url' => \yii\helpers\Url::to([
                                    '/flight/flight-quote/search-ajax',
                                    'id' => $product->flight->fl_id
                                ]),
                                'data-hotel-id' => $product->flight->fl_id,
                                'class' => 'dropdown-item text-success btn-search-flight-quotes'
                            ]) ?>

                            <?= Html::a('<i class="fa fa-edit"></i> Update Request', null, [
                                'data-url' => \yii\helpers\Url::to([
                                    '/flight/flight/update-ajax',
                                    'id' => $product->flight->fl_id
                                ]),
                                'data-hotel-id' => $product->flight->fl_id,
                                'class' => 'dropdown-item text-warning btn-update-flight-request'
                            ]) ?>


                            <?= Html::a('<i class="fa fa-plus"></i> Add Room', null, [
                                'data-url' => \yii\helpers\Url::to([
                                    '/flight/flight-room/create-ajax',
                                    'id' => $product->flight->fl_id,
                                ]),
                                'data-hotel-id' => $product->flight->fl_id,
                                'class' => 'dropdown-item btn-add-flight-room'
                            ]) ?>


                            <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete product',
                                null, [
                                    'class' => 'dropdown-item text-danger btn-delete-product',
                                    'data-product-id' => $product->pr_id
                                ]) ?>

                        </div>
                    </li>
                <?//php endif; ?>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none">
            <?//php if ((int) $product->pr_type_id === \common\models\ProductType::PRODUCT_HOTEL && $product->hotel): ?>
                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-product-search-' . $product->pr_id, 'enablePushState' => false, 'timeout' => 5000])?>
<!--                --><?//= $this->render('_view_search', [
//                    'model' => $product->hotel,
//                    //'dataProviderQuotes' => $dataProviderQuotes
//                    //'dataProviderRooms'
//                ]) ?>
                <?php \yii\widgets\Pjax::end();?>
            <?//php endif; ?>
        </div>
    </div>
<?//php \yii\widgets\Pjax::end()?>



<?php

$js = <<<JS

    $('body').off('click', '.btn-update-flight-request').on('click', '.btn-update-flight-request', function (e) {
        e.preventDefault();
        let updateHotelRequestUrl = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-sm');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update flight request');
        modal.find('.modal-body').load(updateHotelRequestUrl, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
    });

    $('body').off('click', '.btn-add-flight-room').on('click', '.btn-add-flight-room', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Add Room request');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
    });
    
    $('body').off('click', '.btn-update-flight-room').on('click', '.btn-update-flight-room', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
                
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Room request');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
    });
    
    
     $('body').off('click', '.btn-search-flight-quotes').on('click', '.btn-search-flight-quotes', function (e) {
        e.preventDefault();
        $('#preloader').removeClass('d-none');          
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Search flight Quotes');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            $('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
    });
    
    
    $('body').off('click', '.btn-delete-flight-room').on('click', '.btn-delete-flight-room', function(e) {
        
        if(!confirm('Are you sure you want to delete this room?')) {
            return '';
        }
        
      e.preventDefault();
      $('#preloader').removeClass('d-none');
      let roomId = $(this).data('room-id');
      let hotelId = $(this).data('hotel-id');
      let url = $(this).data('url');
     
      /*alert(productId);
      
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');*/

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',
          data: {'id': roomId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  new PNotify({
                        title: 'Error: delete room',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  $.pjax.reload({
                      container: '#pjax-flight-rooms-' + hotelId
                  });
                  new PNotify({
                        title: 'The room was successfully removed',
                        type: 'success',
                        text: data.message,
                        hide: true
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
    
    
JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'update-hotel-request-js');
