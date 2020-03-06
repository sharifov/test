<?php

use modules\product\src\entities\productType\ProductTypeQuery;
use modules\product\src\useCases\product\create\ProductCreateForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProductCreateForm */
/* @var $form ActiveForm */

$pjaxId = 'add-product-pjax'; // . uniqid();

?>


<div class="product-form">
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <?php
       // $form = ActiveForm::begin(['']);
        $form = ActiveForm::begin([
                'id' => 'product-form',
                //'options' => ['data-pjax' => true],
            'action' => ['/product/product/create-ajax'],
            'method' => 'POST'
        ]);
    ?>
    <?php //php $this->registerJs('$(document).off("submit", "#add-product-pjax form[data-pjax]");alert(12);', \yii\web\View::POS_READY)?>

    <?= $form->field($model, 'pr_type_id')->dropDownList($model->pr_type_id ? [$model->pr_type_id => ProductTypeQuery::getListEnabled()[$model->pr_type_id]] : ProductTypeQuery::getListEnabled()) ?>

    <?= $form->field($model, 'pr_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pr_lead_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'pr_description')->textarea(['rows' => 4]) ?>


    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>


<?php
//$js = '$(document).on("submit", "#add-product-pjax form[data-pjax]", function (event) {$.pjax.submit(event, {"push":false,"replace":false,"timeout":5000,"scrollTo":false,"container":"#add-product-pjax"});});';
//$this->registerJs($js);

$js = <<<JS
  //$("#product-form").unbind('submit');
  $('#product-form').off().on('submit', function(e) {
      e.preventDefault();
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');
      
     // $('#preloader').removeClass('d-none');
      
      $.ajax({
          url: $(this).attr('action'),
          type: $(this).attr('method'),
          data: new FormData($(this)[0]),
          //mimeType: 'multipart/form-data',
          contentType: false,
          cache: false,
          processData: false,
          dataType: 'json',
      })
          .done(function(data) {
              if (data.errors) {
                  $.each(data.errors, function(key, val) {
                      let el = $('#' + key);
                      el.parent('.form-group').addClass('has-error');
                      el.next('.help-block').html(val);
                      el.next('.invalid-feedback').show().html(val);
                  });
              } else {
                  //reload pjax and close boostrap modal
                  pjaxReload({
                      container: '#pjax-lead-products-wrap',
                      timeout: 5000,
                      async: false,
                  });
                  pjaxReload({ container: '#pjax-lead-call-expert', timeout: 5000, async: false });
                  
                  $('#modal-sm').modal('hide');
              }
              // btnSubmit.prop('disabled', false);
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
            btnSubmit.prop('disabled', false);
            btnSubmit.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
            //alert( "complete" );
            //$('#preloader').removeClass('d-none');
        });
      return false;
    });
 
  
JS;

$this->registerJs($js, \yii\web\View::POS_READY);

//$js = <<<JS
//
//  jQuery('#product-form').submit(function(e) {
//
//      e.preventDefault();
//
//      jQuery.ajax({
//          url: jQuery(this).attr('action'),
//          type: jQuery(this).attr('method'),
//          data: new FormData(form[0]),
//          mimeType: 'multipart/form-data',
//          contentType: false,
//          cache: false,
//          processData: false,
//          dataType: 'json',
//          success: function(data) {
//              alert(123);
//              //if there are serverside errors then ajax show them on the page
//              if (data.errors) {
//                  jQuery.each(data.errors, function(key, val) {
//                      var el = jQuery('#' + key);
//                      el.parent('.form-group').addClass('has-error');
//                      el.next('.help-block').html(val);
//                  });
//              } else {
//                  //reload pjax and close boostrap modal
//                  /*jQuery.pjax.reload({
//                      container: '#countries'
//                  });*/
//                  jQuery('#modal-sm').modal('hide');
//              }
//          }
//      });
//      return false;
//});
//
///*$("document").ready(function(){
//    $("#$$pjaxId").on("pjax:end", function() {
//        //pjaxReload({container:"#dictionary-grid", timeout : false });
//        alert(123);
//    });
//});*/
//JS;
//Pjax form in modal updating
//$this->registerJs($js);