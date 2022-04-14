<?php

use modules\product\src\entities\product\Product;
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

$pjaxId = 'pjax-product-' . $product->pr_id;
?>
<?php Pjax::begin([
    'id' => $pjaxId,
    'enablePushState' => false,
    'enableReplaceState' => false,
    'timeout' => 5000
]) ?>
    <div class="x_panel">
        <div class="x_title">
            <h2>
                <a class="collapse-link">
                    <i class="<?= Html::encode($product->getIconClass()) ?>"
                       title="ID: <?= $product->pr_id ?>"></i> <?= Html::encode($product->prType->pt_name) ?> <?= $product->pr_name ? ' - ' . Html::encode($product->pr_name) : '' ?>
                    <span style="color: #53a265" class="product-quote-counter-<?= $product->pr_id ?>"
                          data-value="<?= count($product->productQuotes) ?>">
                        <?php if ($product->productQuotes) : ?>
                            <sup title="Number of quotes">(<?= count($product->attraction->attractionQuotes) ?>)</sup>
                        <?php endif; ?>
                    </span>
                </a>
                <?php if ($product->pr_description) : ?>
                    <a id="product_description_<?= $product->pr_id ?>"
                       class="popover-class fa fa-info-circle text-info"
                       data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top"
                       data-container="body" title="<?= Html::encode($product->pr_name) ?>"
                       data-content='<?= Html::encode($product->pr_description) ?>'
                    ></a>
                <?php endif; ?>
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <?php //php if ($is_manager) :?>
                <?php //=Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>

                <li title="Destination">
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                            <?php if ($product->attraction->atn_destination_code) : ?>
                                (<b><?= Html::encode($product->attraction->atn_destination_code) ?></b>)
                                <?= Html::encode($product->attraction->atn_destination) ?>
                            <?php endif; ?>
                        </span>
                </li>
                <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                             <?php if ($product->attraction->atn_date_from) : ?>
                                 <b><?= Yii::$app->formatter->asDate(strtotime($product->attraction->atn_date_from)) ?></b>
                             <?php endif; ?>
                        </span>
                </li>
                <li>
                    <div style="margin-right: 50px"></div>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i
                                class="fa fa-bars warning"></i> <span class="text-warning">Actions</span></a>
                    <div class="dropdown-menu" role="menu">
                        <?php /*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                            'class' => 'dropdown-item text-danger btn-update-product',
                            'data-product-id' => $product->pr_id
                        ])*/ ?>

                        <h6 class="dropdown-header">P<?= $product->pr_id ?> - A<?= $product->attraction->atn_id ?></h6>

                        <?= Html::a('<i class="fa fa-edit"></i> Update Request', null, [
                            'data-url' => \yii\helpers\Url::to([
                                '/attraction/attraction/update-ajax',
                                'id' => $product->attraction->atn_id
                            ]),
                                                     'data-hotel-id' => $product->attraction->atn_id,
                                                     'class' => 'dropdown-item text-warning btn-update-attraction-request btn-update-request',
                                                     'data-product-id' => $product->pr_id
                        ]) ?>

                        <?= Html::a('<i class="fa fa-search"></i> Search Quotes', null, [
                            'data-url' => \yii\helpers\Url::to([
                                '/attraction/attraction-quote/search-ajax',
                                'id' => $product->attraction->atn_id
                            ]),
                            'data-hotel-id' => $product->attraction->atn_id,
                            'class' => 'dropdown-item text-success btn-search-attraction-quotes'
                        ]) ?>

                        <!--<?php /*if (!$product->attraction->attractionPaxes) : */?>
                            <?/*= Html::a('<i class="fa fa-plus"></i> Add Travelers', null, [
                                'data-url' => \yii\helpers\Url::to([
                                    '/attraction/attraction-pax/create-ajax',
                                    'id' => $product->attraction->atn_id,
                                ]),
                                'data-hotel-id' => $product->attraction->atn_id,
                                'class' => 'dropdown-item btn-add-attraction-travelers'
                            ]) */?>
                        <?php /*endif; */?>-->

                        <div class="dropdown-divider"></div>
                        <?= Html::a('<i class="fa fa-edit"></i> Update Product', null, [
                            'class' => 'dropdown-item text-warning btn-update-product',
                            'data-product-id' => $product->pr_id,
                        ]) ?>
                        <?php if ($product->isDeletable()) : ?>
                            <?= Html::a(
                                '<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete Attraction',
                                null,
                                [
                                    'class' => 'dropdown-item text-danger btn-delete-product',
                                    'data-product-id' => $product->pr_id
                                ]
                            ) ?>
                        <?php endif ?>

                    </div>
                </li>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
                <?php //php endif;?>

            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: <?=(Yii::$app->request->isPjax && Yii::$app->request->get('_pjax') == '#' . $pjaxId) ? 'block' : 'none'?>">
            <?php Pjax::begin([
                'id' => 'pjax-product-search-' . $product->pr_id,
                'enablePushState' => false,
                'timeout' => 5000
            ]) ?>
            <?= $this->render('_view_search', [
                'model' => $product->attraction,
                //'dataProviderQuotes' => $dataProviderQuotes
                //'dataProviderRooms'
            ]) ?>
            <?php Pjax::end(); ?>

        </div>
    </div>
<?php Pjax::end() ?>


<?php

$js = <<<JS

    $('body').off('click', '.btn-update-attraction-request').on('click', '.btn-update-attraction-request', function (e) {
        e.preventDefault();
        let updateHotelRequestUrl = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-sm');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Attraction request');
        modal.find('.modal-body').load(updateHotelRequestUrl, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
    });

    $('body').off('click', '.btn-add-attraction-travelers').on('click', '.btn-add-attraction-travelers', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Add Travelers');
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
    
    $('body').off('click', '.btn-update-attraction-travelers').on('click', '.btn-update-attraction-travelers', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
                
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Travelers');
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
    
    
     $('body').off('click', '.btn-search-attraction-quotes').on('click', '.btn-search-attraction-quotes', function (e) {
        e.preventDefault();
        $('#preloader').removeClass('d-none');          
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Search Attraction Quotes');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status == 'error') {
                alert(response);
            } else {
                $('#preloader').addClass('d-none');
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });
    
    
    /*$('body').off('click', '.btn-delete-hotel-room').on('click', '.btn-delete-hotel-room', function(e) {
        
        if(!confirm('Are you sure you want to delete this room?')) {
            return '';
        }
        
      e.preventDefault();
      $('#preloader').removeClass('d-none');
      let roomId = $(this).data('room-id');
      let hotelId = $(this).data('hotel-id');
      let url = $(this).data('url');
           

      $.ajax({
          url: url,
          type: 'post',
          data: {'id': roomId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  createNotifyByObject({
                        title: 'Error: delete room',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  pjaxReload({
                      container: '#pjax-hotel-rooms-' + hotelId
                  });
                  createNotifyByObject({
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
    });*/
    
    
    
JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'update-attraction-request-js');
