<?php

use modules\product\src\entities\product\Product;
use modules\product\src\forms\ProductUpdateForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var ProductUpdateForm $formModel */
/* @var ActiveForm $form */
/* @var Product $model */

$pjaxId = 'update-product-pjax';
?>

<div class="product-form">
    <?php Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <?php
        $form = ActiveForm::begin([
            'id' => 'product-update-form',
            'action' => ['/product/product/update-ajax?id=' . $model->pr_id],
            'method' => 'POST'
        ]);
    ?>
    <?= $form->field($formModel, 'pr_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($formModel, 'pr_description')->textarea(['rows' => 4]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>

<?php

$js = <<<JS
  $('#product-update-form').off().on('submit', function(e) {
      e.preventDefault();
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');
      
      $.ajax({
          url: $(this).attr('action'),
          type: $(this).attr('method'),
          data: new FormData($(this)[0]),
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
                  $.pjax.reload({
                      container: '#pjax-lead-products-wrap',
                      timeout: 5000,
                      async: false,
                  });
                  $('#modal-sm').modal('hide');
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
            btnSubmit.prop('disabled', false);
            btnSubmit.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
        });
      return false;
  });
JS;

$this->registerJs($js, \yii\web\View::POS_READY);
