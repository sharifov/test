<?php

use common\widgets\Alert;
use modules\rentCar\src\entity\rentCar\RentCar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var RentCar $rentCar */
/* @var yii\data\ArrayDataProvider $dataProvider */
/* @var string|null $error */

?>
<div class="rent-car-quote-index">
    <div class="row">
        <div class="col-md-12">
            <?= Alert::widget() ?>
        </div>
    </div>

    <?php if ($error) : ?>
        <?php echo '<span class="text-danger">' . $error . '</span>' ?>
    <?php else : ?>
        <?php Pjax::begin(['timeout' => 15000, 'enablePushState' => false, 'enableReplaceState' => false, 'scrollTo' => false]); ?>

        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'emptyText' => '<div class="text-center">Not found any rent car</div><br>',
            'itemView' => function ($dataRentCar, $key, $index, $widget) use ($rentCar) {
                return $this->render('_list_rent_car_quotes', ['dataRentCar' => $dataRentCar, 'index' => $index, 'key' => $key, 'rentCar' => $rentCar]);
            },
            'itemOptions' => [
                'tag' => false,
            ],
        ]) ?>
        <?php Pjax::end(); ?>
    <?php endif ?>
</div>

<?php
$addQuoteUrl = Url::to(['rent-car-quote/add-quote', 'id' => $rentCar->prc_id]);
$contractRequestUrl = Url::to(['rent-car-quote/contract-request']);

$js = <<<JS
    
    $('body').off('click', '.js-add-rent-car-quote').on('click', '.js-add-rent-car-quote', function (e) {
      e.preventDefault();
      let token = $(this).data('token');      
      let btnAdd = $(this);
      
      btnAdd.addClass('disabled').prop('disabled', true);
      btnAdd.find('i').removeClass('fa-plus').addClass('fa-spin fa-spinner');

      $.ajax({
          url: '{$addQuoteUrl}',
          type: 'post',
          data: {'token': token},
          dataType: 'json'
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  createNotifyByObject({
                        title: 'Error: add rent car quote',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
                  btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-stop');
                  btnAdd.removeClass('disabled').prop('disabled', false);
                  $('#box-quote-' + token).addClass('bg-warning');                  
              } else {
                  
                  pjaxReload({
                      container: '#pjax-product-' + data.product_id,
                      push: false, replace: false, timeout: 2000
                  });
                  
                  createNotifyByObject({
                        title: 'Quote was successfully added',
                        type: 'success',
                        text: data.message,
                        hide: true
                    });
                  
                  btnAdd.html('<i class="fa fa-check"></i> Added');
                  $('#box-quote-' + token).addClass('quote-added');
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
            btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-plus');
            btnAdd.removeClass('disabled').prop('disabled', false);
            $('#box-quote-' + token).addClass('bg-danger');
        }).always(function() {
        });      
    });   
    
    $('body').off('click', '.js-contract-request').on('click', '.js-contract-request', function (e) {        
      e.preventDefault();
      let requestId = $(this).data('requestId');
      let referenceId = $(this).data('refId');
      let token = $(this).data('token');
      let btnAdd = $(this);
      
      btnAdd.addClass('disabled').prop('disabled', true);
      btnAdd.find('i').removeClass('fa-angle-double-right').addClass('fa-spin fa-spinner');

      $.ajax({
          url: '{$contractRequestUrl}',
          type: 'post',
          data: {requestId: requestId, referenceId: referenceId},
          dataType: 'json'
      })
          .done(function(data) {
              if (data.status == 1) {                  
                   createNotifyByObject({
                        title: 'Contract request success',
                        type: 'success',
                        text: data.message,
                        hide: true
                    });
                  
                  btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-check-circle-o');
                  btnAdd.removeClass('disabled').prop('disabled', false);
              } else {                  
                  createNotifyByObject({
                        title: 'Error: Contract request',
                        type: 'error',
                        text: data.message,
                        hide: true
                    });
                  btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-ban');
                  btnAdd.removeClass('disabled').prop('disabled', false);
                  $('#box-quote-' + token).addClass('bg-warning'); 
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
            btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-angle-double-right');
            btnAdd.removeClass('disabled').prop('disabled', false);
            $('#box-quote-' + token).addClass('bg-danger');
        }).always(function() {
        });      
    });  
JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'rent-car-search-quotes-js');

$css = <<<CSS
    .quote-added {
        background-color: #d0e6ca!important;
        opacity: 0.9;
    }
    .quote {
      background-color: #fff;
      border: 1px solid #c2cad8;
      border-radius: 4px;
    }
    .quote__heading {
      padding: 0 15px;
      border-bottom: 1px solid #c2cad8;
      min-height: 50px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      color: #80868c;
    }
    .quote__heading-left > *:not(:first-child) {
      margin-left: 16px;
      padding-left: 16px;
      border-left: 1px solid #c2cad8;
    }
    .quote__heading-right > *:not(:last-child) {
      margin-right: 16px;
      padding-right: 16px;
      border-right: 1px solid #c2cad8;
    }
    .quote__heading-left, .quote__heading-right {
      display: flex;
      align-items: center;
      padding: 6px 0;
    }
    .quote__vc {
      display: flex;
    }
    .quote__vc-logo {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      overflow: hidden;
      box-shadow: 0 3px 1px rgba(51, 51, 51, 0.08);
      display: inline-block;
      margin-right: 8px;
    }
    .quote__vc-img {
      width: 100%;
      height: 100%;
      display: block;
    }
    .quote__id strong {
      color: #474f58;
    }
    .quote__wrapper {
      display: flex;
      flex-flow: row wrap;
      padding: 15px;
    }
    .quote__footer {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-top: 1px solid #c2cad8;
      background: #fafafb;
      padding: 10px;
    }
    .quote__footer-left {
      display: flex;
      justify-content: flex-start;
    }
    .quote__footer-right {
      display: flex;
      justify-content: flex-end;
    }
    .quote__footer-btn {
      margin-left: 8px;
      font-size: 12px;
    }
    .quote__footer-btn:first-child {
      margin-left: 0;
    }
    .quote__footer-btn .fa:not(:only-child) {
      margin-right: 5px;
    }
    .quote__footer-btn.btn-success {
      background-color: #78c286;
      border-color: #78c286;
    }
    .quote__footer-btn.btn-success:hover, .quote__footer-btn.btn-success:active, .quote__footer-btn.btn-success:focus, .quote__footer-btn.btn-success:active:focus, .quote__footer-btn.btn-success:active:hover {
      background-color: #72b97f;
      border-color: #72b97f;
    }
    .quote__footer-btn.btn-secondary {
      background-color: #4a525f;
      border-color: #4a525f;
    }
    .quote__footer-btn.btn-secondary:hover, .quote__footer-btn.btn-secondary:active, .quote__footer-btn.btn-secondary:focus, .quote__footer-btn.btn-secondary:active:focus, .quote__footer-btn.btn-secondary:active:hover {
      background-color: #3e444f;
      border-color: #3e444f;
    }
    
    .offer {
      width: 100%;
    }
    .offer__preview img {
      width: 200px;
    }
CSS;
$this->registerCss($css);
