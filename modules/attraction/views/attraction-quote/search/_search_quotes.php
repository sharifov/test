<?php

use common\widgets\Alert;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $hotelSearch \modules\hotel\models\Hotel */
/* @var $dataProvider yii\data\ArrayDataProvider */

//var_dump($dataProvider->getModels()); die();
//\yii\helpers\VarDumper::dump($dataProvider->getModels(), 10, true); exit;
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
        'itemView' => function ($dataHotel, $key, $index, $widget) use ($hotelSearch) {
            //\yii\helpers\VarDumper::dump($dataHotel, 10, true); exit;
            return $this->render('_list_attraction_quotes', ['dataHotel' => $dataHotel, 'index' => $index, 'key' => $key, 'hotelSearch' => $hotelSearch]);
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

    $('body').off('click', '.btn-availability-quote').on('click', '.btn-availability-quote', function (e) {        
      e.preventDefault();
      //$('#preloader').removeClass('d-none');
      let url = $(this).data('url');
      let atnId = $(this).data('atn-id');
      let attractionKey = $(this).data('attraction-key');       
      let btnAdd = $(this);
      
      btnAdd.addClass('disabled').prop('disabled', true);
      btnAdd.find('i').removeClass('fa-plus').addClass('fa-spin fa-spinner');

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',          
          data: {'attraction_key': attractionKey, 'atn_id': atnId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  new PNotify({
                        title: 'Error: fail availability check',
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
                  $('#' + attractionKey).html(data);
                  new PNotify({
                        title: 'Availability was successfully checked',
                        type: 'success',
                        text: 'Attraction ID ' + atnId,
                        hide: true
                    });
                  
                  btnAdd.html('<i class="fa fa-check"></i>  Checked Availability');;
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
          data: {'quote_key': quoteKey},
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
                  
                  pjaxReload({
                      container: '#pjax-product-quote-list-' + data.product_id,
                      push: false, replace: false, timeout: 2000
                  });
                  
                  new PNotify({
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