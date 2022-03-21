<?php

use modules\product\src\entities\product\Product;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $product Product */


$pjaxId = 'pjax-product-' . $product->pr_id;
?>
<?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 4000])?>
    <div class="x_panel">
        <div class="x_title">
            <h2>
                <a class="collapse-link">
                    <i class="<?= Html::encode($product->getIconClass()) ?>" title="ID: <?=$product->pr_id?>"></i> <?=Html::encode($product->prType->pt_name)?> <?=$product->pr_name ? ' - ' . Html::encode($product->pr_name) : ''?>
                    <span style="color: #53a265" class="product-quote-counter-<?= $product->pr_id ?>" data-value="<?=count($product->productQuotes)?>">
                        <?php if ($product->productQuotes) :?>
                            <sup title="Number of quotes">(<?=count($product->productQuotes)?>)</sup>
                        <?php endif;?>
                    </span>
                </a>
                <?php if ($product->pr_description) :?>
                    <a  id="product_description_<?=$product->pr_id?>"
                        class="popover-class fa fa-info-circle text-info"
                        data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top"
                        data-container="body" title="<?=Html::encode($product->pr_name)?>"
                        data-content='<?=Html::encode($product->pr_description)?>'
                    ></a>
                <?php endif; ?>
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <?php //php if ($is_manager) : ?>
                    <!--                    <li>-->
                    <!--                        --><?php //=Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>
                    <!--                    </li>-->
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">


                            <?php if ($product->cruise->crs_destination_code) :?>
                                (<b><?= Html::encode($product->cruise->crs_destination_code) ?></b>)
                                <?= Html::encode($product->cruise->crs_destination_label) ?>
                            <?php endif; ?>


                        </span>
                    </li>
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                             <?php if ($product->cruise->crs_departure_date_from) :?>
                                 <b><?= Yii::$app->formatter->asDate(strtotime($product->cruise->crs_departure_date_from)) ?></b>
                             <?php endif; ?>
                        </span>
                    </li>
                <li>
                    <div style="margin-right: 50px"></div>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars warning"></i> <span class="text-warning">Actions</span></a>
                    <div class="dropdown-menu" role="menu">
                        <?php /*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                            'class' => 'dropdown-item text-danger btn-update-product',
                            'data-product-id' => $product->pr_id
                        ])*/ ?>

                        <h6 class="dropdown-header">P<?=$product->pr_id?> - C<?=$product->cruise->crs_id?></h6>

                        <?= Html::a('<i class="fa fa-edit"></i> Update Request', null, [
                            'data-url' => \yii\helpers\Url::to([
                                '/cruise/cruise/update-ajax',
                                'id' => $product->cruise->crs_id,
                            ]),
                                                     'data-product-id' => $product->pr_id,
                                                     'data-cruise-id' => $product->cruise->crs_id,
                                                     'class' => 'dropdown-item text-warning btn-update-cruise-request btn-update-request'
                        ]) ?>

                        <?= Html::a('<i class="fa fa-search"></i> Search Quotes', null, [
                            'data-url' => \yii\helpers\Url::to([
                                '/cruise/cruise-quote/search-ajax',
                                'id' => $product->cruise->crs_id
                            ]),
                            'data-cruise-id' => $product->cruise->crs_id,
                            'class' => 'dropdown-item text-success btn-search-cruise-quotes'
                        ]) ?>

                        <?php if (!$product->cruise->cabins) : ?>
                            <div class="menu-btn-add-cabin">
                                <?= Html::a('<i class="fa fa-plus"></i> Add Cabin', null, [
                                    'data-url' => \yii\helpers\Url::to([
                                        '/cruise/cruise-cabin/create-ajax',
                                        'id' => $product->cruise->crs_id,
                                    ]),
                                    'data-cruise-id' => $product->cruise->crs_id,
                                    'data-product-id' => $product->pr_id,
                                    'class' => 'dropdown-item btn-add-cruise-cabin btn-add-cabin'
                                ]) ?>
                            </div>
                        <?php endif; ?>

                        <div class="dropdown-divider"></div>
                        <?= Html::a('<i class="fa fa-edit"></i> Update Product', null, [
                            'class' => 'dropdown-item text-warning btn-update-product',
                            'data-product-id' => $product->pr_id,
                        ]) ?>
                        <?php if ($product->isDeletable()) : ?>
                            <?= Html::a(
                                '<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete Cruise',
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
                <?php //php endif; ?>

            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none">
            <?php //php if ((int) $product->pr_type_id === \common\models\ProductType::PRODUCT_CRUISE && $product->cruise): ?>
                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-product-search-' . $product->pr_id, 'enablePushState' => false, 'timeout' => 5000])?>
                <?= $this->render('_view_search', [
                    'model' => $product->cruise,
                    //'dataProviderQuotes' => $dataProviderQuotes
                    //'dataProviderCabins'
                ]) ?>
                <?php \yii\widgets\Pjax::end();?>
            <?php //php endif; ?>
        </div>
    </div>
<?php \yii\widgets\Pjax::end()?>



<?php

$js = <<<JS

    $('body').off('click', '.btn-update-cruise-request').on('click', '.btn-update-cruise-request', function (e) {
        e.preventDefault();
        let updateCruiseRequestUrl = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-sm');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Cruise request');
        modal.find('.modal-body').load(updateCruiseRequestUrl, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
    });

    $('body').off('click', '.btn-add-cruise-cabin').on('click', '.btn-add-cruise-cabin', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Add Cabin request');
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
    
    $('body').off('click', '.btn-update-cruise-cabin').on('click', '.btn-update-cruise-cabin', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
                
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Cabin request');
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
    
    
     $('body').off('click', '.btn-search-cruise-quotes').on('click', '.btn-search-cruise-quotes', function (e) {
        e.preventDefault();
        $('#preloader').removeClass('d-none');          
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Search Cruise Quotes');
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
    
    
    $('body').off('click', '.btn-delete-cruise-cabin').on('click', '.btn-delete-cruise-cabin', function(e) {
        
        if(!confirm('Are you sure you want to delete this cabin?')) {
            return '';
        }
        
      e.preventDefault();
      $('#preloader').removeClass('d-none');
      let cabinId = $(this).data('cabin-id');
      let cruiseId = $(this).data('cruise-id');
      let url = $(this).data('url');
     
      /*alert(productId);
      
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');*/

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',
          data: {'id': cabinId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  createNotifyByObject({
                        title: 'Error: delete cabin',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  pjaxReload({
                      container: '#pjax-cruise-cabins-' + cruiseId
                  });
                  createNotifyByObject({
                        title: 'The cabin was successfully removed',
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

$this->registerJs($js, \yii\web\View::POS_READY, 'update-cruise-request-js');
