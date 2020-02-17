<?php

use common\widgets\Alert;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $hotelSearch \modules\hotel\models\Hotel */
/* @var $dataProvider yii\data\ArrayDataProvider */

//$this->title = 'Hotel Quotes';

?>
<div class="hotel-quote-index">

<!--    <h1>--><?php //= Html::encode($this->title) ?><!--</h1>-->
<!---->
<!--    <p>-->
<!--        --><?php //= Html::a('Create Hotel Quote', ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?php
        /*
         *
         * 'categoryName' => '4 STARS'
        'destinationName' => 'Cadiz / Jerez'
        'zoneName' => 'Cádiz'
        'minRate' => 102.27
        'maxRate' => 517.46
        'currency' => 'USD'
        'rooms' => [...]
        'code' => 58197
        'name' => 'Senator Cadiz Spa'
        'description' => 'This striking hotel is wonderfully located in the historical centre of Cádiz, close to the best shopping and historical areas of the capital. It is situated just 5 minutes from the train station and 40 km from Jerez airport. This establishment provides guests with a wide array of facilities such as WiFi access throughout the premises, perfect for those who want to stay connected. The spacious rooms have an en suite bathroom with hairdryer, soundproof windows and necessary amenities to allow guests to feel at home. A gym and an outdoor pool with magnificent views of Cadiz are also available to guests (Please note the swimming pool is open from 16 April to 13 October.) Courtesy bottle of water.
The SPA service at Christmas is closed on December 25 and January 1.'
        'countryCode' => 'ES'
        'stateCode' => '11'
        'destinationCode' => 'CAD'
        'zoneCode' => 99
        'latitude' => 36.532532
        'longitude' => -6.293758
        'categoryCode' => '4EST'
        'categoryGroupCode' => 'GRUPO4'
        'chainCode' => 'SENAT'
        'boardCodes' => [...]
        'segmentCodes' => [...]
        'address' => 'Calle Rubio Y Diaz,1  '
        'postalCode' => '11004'
        'city' => 'CADIZ'
        'email' => 'reservas@senatorhr.com'
        'license' => 'H/CA/01196'
        'phones' => [...]
        'images' => [...]
        'lastUpdate' => '2019-11-21'
        's2C' => '4*'
        'ranking' => 2
        'serviceType' => 'HOTELBEDS'
         *
         */
    ?>

    <div class="row">
        <div class="col-md-12">
			<?= Alert::widget() ?>
        </div>
    </div>

    <?php Pjax::begin(['timeout' => 15000, 'enablePushState' => false, 'enableReplaceState' => false, 'scrollTo' => false]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        /*'options' => [
            'tag' => 'table',
            'class' => 'table table-bordered',
        ],*/
        'emptyText' => '<div class="text-center">Not found any hotels</div><br>',
        'itemView' => function ($dataHotel, $key, $index, $widget) use ($hotelSearch) {
            return $this->render('_list_hotel_quotes', ['dataHotel' => $dataHotel, 'index' => $index, 'key' => $key, 'hotelSearch' => $hotelSearch]);
        },
        //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
        'itemOptions' => [
            //'class' => 'item',
            'tag' => false,
        ],
    ]) ?>


    <?php /*= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           'categoryName',
            'destinationName',
            'zoneName',
            'minRate',
            'maxRate',
            'currency',
            //'rooms',
            'code',
            'name',
            //'description',
            'countryCode',
            'stateCode',
            'destinationCode',
            'zoneCode',
            //'latitude',
            // 'longitude',
            'categoryCode',
            'categoryGroupCode',
            'chainCode',
            //'boardCodes',
            //'segmentCodes',
            'address',
            'postalCode',
            'city',
            //'email',
            'license',
            //'phones' => [...]
            //'images' => [...]
            'lastUpdate',
            's2C',
            'ranking',
            //'serviceType',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]);*/ ?>

    <?php Pjax::end(); ?>

</div>

<?php
//$updateHotelRequestUrl = \yii\helpers\Url::to();

//$deleteRoomUrl = \yii\helpers\Url::to(['/hotel/hotel-room/delete-ajax']);

$js = <<<JS



    
    
     /*$('body').off('click', '.btn-add-hotel-quote').on('click', '.btn-add-hotel-quote', function (e) {
        e.preventDefault();
        //$('#preloader').removeClass('d-none');          
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Search Hotel Quotes');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            $('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
        return false;
    });*/
    
    
    
    
    
    
    $('body').off('click', '.btn-add-hotel-quote').on('click', '.btn-add-hotel-quote', function (e) {
        
      e.preventDefault();
      //$('#preloader').removeClass('d-none');
      let quoteKey = $(this).data('quote-key');
      let hotelCode = $(this).data('hotel-code');
      let url = $(this).data('url');
      //let productId = $(this).data('product-id');
     
      /*alert(productId);*/
      
      let btnAdd = $(this);
      btnAdd.addClass('disabled').prop('disabled', true);
      btnAdd.find('i').removeClass('fa-plus').addClass('fa-spin fa-spinner');

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',
          data: {'hotel_code': hotelCode, 'quote_key': quoteKey},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  new PNotify({
                        title: 'Error: add hotel quote',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
                  btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-stop');
                  btnAdd.removeClass('disabled').prop('disabled', false);
                  $('#tr-hotel-quote-' + quoteKey).addClass('bg-warning');
              } else {
                  
                  $.pjax.reload({
                      container: '#pjax-product-quote-list-' + data.product_id,
                      push: false, replace: false, timeout: 2000
                  });
                  
                  new PNotify({
                        title: 'Quote was successfully added',
                        type: 'success',
                        text: data.message,
                        hide: true
                    });
                  
                  btnAdd.html('<i class="fa fa-check"></i> Added');//.find('i').text('Added').removeClass('fa-spin fa-spinner').addClass('fa-check');
                  $('#tr-hotel-quote-' + quoteKey).addClass('bg-success');
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
            btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-plus');
            btnAdd.removeClass('disabled').prop('disabled', false);
            $('#tr-hotel-quote-' + quoteKey).addClass('bg-danger');
        }).always(function() {
            //btnAdd.prop('disabled', false);
            //btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-check');
            //alert( "complete" );
            //$('#preloader').addClass('d-none');
        });
      // return false;
    });
    
JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'search-quotes-js');