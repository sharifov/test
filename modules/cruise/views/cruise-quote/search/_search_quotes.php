<?php

use common\widgets\Alert;
use modules\cruise\src\entity\cruise\Cruise;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $cruise Cruise */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $existsQuote array */

?>
<style>
    body {
        font-size: 13px;
        font-weight: 400;
        line-height: 1.471;
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



</style>
<div class="cruise-quote-index">
        <div class="row">
        <div class="col-md-12">
            <?= Alert::widget() ?>
        </div>
    </div>

    <?php Pjax::begin(['timeout' => 15000, 'enablePushState' => false, 'enableReplaceState' => false, 'scrollTo' => false]); ?>

    <?= \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        /*'options' => [
            'tag' => 'table',
            'class' => 'table table-bordered',
        ],*/
        'emptyText' => '<div class="text-center">Not found any cruises</div><br>',
        'itemView' => function ($dataCruise, $key, $index, $widget) use ($cruise, $existsQuote) {
            return $this->render('_list_cruise_quotes', ['dataCruise' => $dataCruise, 'index' => $index, 'key' => $key, 'cruise' => $cruise, 'existsQuote' => $existsQuote]);
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
    $('body').off('click', '.btn-add-cruise-quote').on('click', '.btn-add-cruise-quote', function (e) {
        
      e.preventDefault();
      //$('#preloader').removeClass('d-none');
      let quoteId = $(this).data('cruise-quote-id');
      let cabinCode = $(this).data('cabin-code');
      let url = $(this).data('url');
      
      let btnAdd = $(this);
      btnAdd.addClass('disabled').prop('disabled', true);
      btnAdd.find('i').removeClass('fa-plus').addClass('fa-spin fa-spinner');

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',
          data: {'quoteId': quoteId, 'cabinCode': cabinCode},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  //alert(data.error);
                  createNotifyByObject({
                        title: 'Error: add cruise quote',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
                  btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-stop');
                  btnAdd.removeClass('disabled').prop('disabled', false);
                  $('#tr-cruise-quote-' + quoteId + cabinCode).addClass('bg-warning');
              } else {
                  
                  pjaxReload({
                      container: '#pjax-product-quote-list-' + data.product_id,
                      push: false, replace: false, timeout: 2000
                  });

                  createNotifyByObject({
                        title: 'Quote was successfully added',
                        type: 'success',
                        text: data.message,
                        hide: true
                    });
                  
                  btnAdd.html('<i class="fa fa-check"></i> Added');//.find('i').text('Added').removeClass('fa-spin fa-spinner').addClass('fa-check');
                  $('#tr-cruise-quote-' + quoteId + cabinCode).addClass('table-success');
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
            btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-plus');
            btnAdd.removeClass('disabled').prop('disabled', false);
            $('#tr-cruise-quote-' + quoteId + cabinCode).addClass('bg-danger');
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