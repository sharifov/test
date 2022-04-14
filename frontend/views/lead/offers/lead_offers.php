<?php

/**
 * @var $this View
 * @var $lead Lead
 * @var $dataProviderOffers \yii\data\ActiveDataProvider
 */

use common\models\Lead;
use yii\helpers\Url;
use yii\web\View;
use yii\bootstrap4\Html;

?>
<style>
    .x_panel_offers {background-color: #d0e6ca;}
</style>

<?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-offers', 'enablePushState' => false, 'timeout' => 10000]) ?>
<div class="x_panel x_panel_offers">
    <div class="x_title">

        <h2><i class="far fa-handshake"></i> Offers (<?=$dataProviderOffers->totalCount?>)</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <?= Html::a('<i class="fa fa-plus-circle success"></i> add Offer', null, [
                    'data-url' => \yii\helpers\Url::to(['/offer/offer/create-ajax', 'id' => $lead->id]),
                    'class' => 'btn btn-light btn-create-offer'
                ])?>
            </li>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProviderOffers,

                    /*'options' => [
                        'tag' => 'table',
                        'class' => 'table table-bordered',
                    ],*/
                    'emptyText' => '<div class="text-center">Not found offers</div>',
                    //'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
                    'itemView' => function ($model, $key, $index, $widget) {
                        return $this->render('_list_item', ['offer' => $model, 'index' => $index]);
                    },

                    'itemOptions' => [
                        //'class' => 'item',
                        //'tag' => false,
                    ],
                ]) ?>

    </div>
</div>
<?php yii\widgets\Pjax::end() ?>

<?php

$js = <<<JS

    $('body').off('click', '.btn-create-offer').on('click', '.btn-create-offer', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Add offer');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            if (status == 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });
    
    $('body').off('click', '.btn-update-offer').on('click', '.btn-update-offer', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
                
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update offer');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            if (status == 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });
    
    
    
    $('body').off('click', '.btn-delete-offer').on('click', '.btn-delete-offer', function(e) {
        
        if(!confirm('Are you sure you want to delete this offer?')) {
            return '';
        }
        
      e.preventDefault();
      $('#preloader').removeClass('d-none');
      let offerId = $(this).data('offer-id');
      let url = $(this).data('url');
           
      /*alert(productId);
      
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');*/

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',
          data: {'id': offerId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  createNotifyByObject({
                        title: 'Error: delete offer',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  $.pjax.reload({container: '#pjax-lead-offers', timout: 8000});
                  createNotifyByObject({
                        title: 'The offer was successfully removed',
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
    
     $(document).on('click', '.btn-offer-status-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let gid = $(this).data('gid');
        let modal = $('#modal-lg');
          
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Offer [' + gid + '] status history');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status == 'error') {
                alert(response);
            } else {
            //$('#preloader').addClass('d-none');
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
     });
    
     $(document).on('click', '.btn-offer-send-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let gid = $(this).data('gid');
        let modal = $('#modal-md');
          
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Offer [' + gid + '] send history');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            if (status == 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
     });
    
     $(document).on('click', '.btn-offer-view-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let gid = $(this).data('gid');
        let modal = $('#modal-lg');
          
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Offer [' + gid + '] view history');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            if (status == 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
     });
     
     $('body').off('click', '.btn-action-alternative-offer').on('click', '.btn-action-alternative-offer', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let id = $(this).data('offer-id');
        let offerBtnAction = $(this).closest('.offer-li-action').find('.offer-btn-action');
        let offerBtnActionHtml = offerBtnAction.html();
          
        $.ajax({
            url: url,
            data: {offerId: id},
            type: 'post',
            dataType: 'json',
            cache: false,
            beforeSend: function () {
                offerBtnAction.html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function (data) {
                if (data.error) {
                    createNotify('Error', data.message, 'error');
                } else {
                    createNotify('Success', 'Offer alternative successfully confirmed', 'success');
                    pjaxReload({container: "#pjax-lead-orders", async: false, timeout: 5000});
                    pjaxReload({container: "#pjax-lead-offers", async: false, timeout: 5000});
                }
            },
            error: function (xhr) {
                createNotify('Error', xhr.responseText, 'error');
            },
            complete: function () {
                offerBtnAction.html(offerBtnActionHtml);
            }
        })
     });
     
JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'lead-offer-js');
