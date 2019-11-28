<?php
/**
 * @var $this \yii\web\View
 * @var $lead Lead
 * @var $leadForm LeadForm
 * @var $itineraryForm ItineraryEditForm
 * @var $is_manager bool
 * @var $products \common\models\Product[]
 * @var $quotesProvider \yii\data\ActiveDataProvider
 *
 */

use common\models\Lead;
use frontend\models\LeadForm;
use sales\forms\lead\ItineraryEditForm;
use yii\helpers\Html;

?>

<?php
    $products = \common\models\Product::find()->where(['pr_lead_id' => $lead->id])->all();

    //\yii\helpers\VarDumper::dump($products);

    $items = [];
?>


<?php \yii\widgets\Pjax::begin(['id' => 'product-accordion', 'enablePushState' => false, 'timeout' => 10000])?>

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
        <div class="x_content" style="display: block">
            <?php \yii\widgets\Pjax::begin(['id' => 'pj-itinerary', 'enablePushState' => false, 'timeout' => 10000])?>
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
    <div class="x_panel">
        <div class="x_title">
            <h2>
                <i class="fa fa-hotel" title="ID: <?=$product->pr_id?>"></i> <?=Html::encode($product->prType->pt_name)?> <?=$product->pr_name ? ' - ' . Html::encode($product->pr_name) : ''?>
                <?php if ($product->pr_description):?>
                    <i class="fa fa-info-circle text-info" title="<?=Html::encode($product->pr_description)?>"></i>
                <?php endif;?>
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <?php if ($is_manager) : ?>
                    <!--                    <li>-->
                    <!--                        --><?//=Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>
                    <!--                    </li>-->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                        <div class="dropdown-menu" role="menu">
                            <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete product', null, [
                                'class' => 'dropdown-item text-danger btn-delete-product',
                                'data-product-id' => $product->pr_id
                            ]) ?>
                        </div>
                    </li>
                <?php endif; ?>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none">

        </div>
    </div>
<?php endforeach; ?>

<?php \yii\widgets\Pjax::end()?>

<?php

$ajaxDeleteProductUrl = \yii\helpers\Url::to(['product/delete-ajax']);

$js = <<<JS

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


    $("#product-accordion").on("pjax:start", function () {            
        $('#preloader').removeClass('d-none');
    });

    $("#product-accordion").on("pjax:end", function () {           
       $('#preloader').addClass('d-none');
    }); 
JS;

$this->registerJs($js, \yii\web\View::POS_READY);

    //echo \yii\bootstrap4\Accordion::widget(['items' => $items]);

