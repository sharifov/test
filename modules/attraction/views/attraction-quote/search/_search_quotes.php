<?php

use common\widgets\Alert;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $attraction \modules\attraction\models\Attraction */
/* @var $dataProvider yii\data\ArrayDataProvider */

?>
<div class="hotel-quote-index">

<!--    <h1>--><?php //= Html::encode($this->title) ?><!--</h1>-->
<!---->
<!--    <p>-->
<!--        --><?php //= Html::a('Create Hotel Quote', ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

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
        'summary' => false,
        'emptyText' => '<div class="text-center">Not found any hotels</div><br>',
        'itemView' => function ($dataAttraction, $key, $index, $widget) use ($attraction) {
            //\yii\helpers\VarDumper::dump($dataHotel, 10, true); exit;
            return $this->render('_list_attraction_quotes', ['dataAttraction' => $dataAttraction, 'index' => $index, 'key' => $key, 'attraction' => $attraction]);
        },
        //'layout' => "{items}<div class=\"text-center\" style='margin-top: -20px; margin-bottom: -25px'>{pager}</div>", // {summary}\n<div class="text-center">{pager}</div>
        'itemOptions' => [
            //'class' => 'item',
            'tag' => false,
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>

<?php

$js = <<<JS

    $('body').off('click', '.btn-availability-list-quote').on('click', '.btn-availability-list-quote', function (e) {        
      e.preventDefault();
      //$('#preloader').removeClass('d-none');
      let url = $(this).data('url');
      let atnId = $(this).data('atn-id');
      let productKey = $(this).data('attraction-key');       
      let btnAdd = $(this);
      
      btnAdd.addClass('disabled').prop('disabled', true);
      btnAdd.find('i').removeClass('fa-plus').addClass('fa-spin fa-spinner');

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',          
          data: {'product_key': productKey, 'atn_id': atnId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  createNotifyByObject({
                        title: 'Error: fail to get Quotes',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
                  btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-stop');
                  btnAdd.removeClass('disabled').prop('disabled', false);
                  $('#tr-hotel-quote-' + quoteKey).addClass('bg-warning');
              } else {                  
                  /*pjaxReload({
                      container: '#pjax-product-quote-list-' + data.product_id,
                      push: false, replace: false, timeout: 2000
                  });*/
                  
                  $('#' + productKey).html(data.html);
                  if (data.result) {
                      createNotifyByObject({
                        title: 'Check Availability',
                        type: 'warning',
                        text: 'No available quotes. For Attraction with ID ' + atnId +'.',
                        hide: true
                      });
                      
                      btnAdd.addClass('btn-warning').html('<i class="fa fa-stop"></i>  Quotes are not available');
                  } else {
                      createNotifyByObject({
                        title: 'Check Availability',
                        type: 'success',
                        text: 'Availability was successfully checked. For Attraction with ID ' + atnId +'.',
                        hide: true
                      });
                  
                     btnAdd.html('<i class="fa fa-check"></i>  Quotes Obtained');
                  }                  
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
    });


$('body').off('click', '.btn-availability-quote').on('click', '.btn-availability-quote', function (e) {        
      e.preventDefault();
      //$('#preloader').removeClass('d-none');
      let url = $(this).data('url');
      let atnId = $(this).data('atn-id');
      let availabilityKey = $(this).data('availability-key');       
      let btnAdd = $(this);      
      btnAdd.addClass('disabled').prop('disabled', true);
      btnAdd.find('i').removeClass('fa-plus').addClass('fa-spin fa-spinner');

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',          
          data: {'availability_key': availabilityKey, 'atn_id': atnId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  //alert(data.error);
                  createNotifyByObject({
                        title: 'Info: fail to get availability data',
                        type: 'info',
                        text: data.message,
                        hide: true
                    });
                  btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-stop');
                  btnAdd.removeClass('disabled').prop('disabled', false);
                  $('#tr-hotel-quote-' + quoteKey).addClass('bg-warning');
              } else {                  
                  /*pjaxReload({
                      container: '#pjax-product-quote-list-' + data.product_id,
                      push: false, replace: false, timeout: 2000
                  });*/
                  $('#' + availabilityKey).html(data);
                  createNotifyByObject({
                        title: 'Availability was successfully checked',
                        type: 'success',
                        text: 'Attraction ID ' + atnId,
                        hide: true
                    });
                  
                  btnAdd.html('<i class="fa fa-check"></i>  Options Obtained');
                  //$('#tr-hotel-quote-' + quoteKey).addClass('bg-success');
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
            btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-plus');
            btnAdd.removeClass('disabled').prop('disabled', false);
            //$('#tr-hotel-quote-' + quoteKey).addClass('bg-danger');
        }).always(function() {
            //btnAdd.prop('disabled', false);
            //btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-check');
            //alert( "complete" );
            //$('#preloader').addClass('d-none');
        });      
    });


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
      
    $('body').off('click', '.btn-add-attraction-quote').on('click', '.btn-add-attraction-quote', function (e) {
        
      e.preventDefault();
      //$('#preloader').removeClass('d-none');
      let quoteKey = $(this).data('quote-key');
      //let hotelCode = $(this).data('hotel-code');
      let date = $(this).data('date');
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
          //data: {'hotel_code': hotelCode, 'quote_key': quoteKey},
          data: {'quote_key': quoteKey, 'date': date},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  createNotifyByObject({
                        title: 'Error: add hotel quote',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
                  btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-stop');
                  btnAdd.removeClass('disabled').prop('disabled', false);
                  $('#tr-hotel-quote-' + quoteKey).addClass('bg-warning');
              } else {
                  
                  pjaxReload({
                      container: '#pjax-product-quote-list-' + data.product_id,
                      push: false, replace: false, timeout: 2000
                  });
                  
                  createNotifyByObject({
                        title: 'Quote successfully added',
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