<?php
/**
 * @var $lead Lead
 * @var $quote Quote
 * @var $prices QuotePrice[]
 * @var $errors []
 */

use common\models\Lead;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\Quote;
use common\models\QuotePrice;

$quotePriceUrl = \yii\helpers\Url::to(['quote/calc-price', 'quoteId' => $quote->id]);
$formID = sprintf('alt-quote-info-form-%d', $quote->id);

$js = <<<JS
    /***  Cancel card  ***/
    $('#cancel-alt-quote').on('click', function (e) {
        e.preventDefault();
        let editBlock = $('#$formID');
        editBlock.parent().parent().removeClass('show');
        editBlock.parent().html('');
        $('#modal-lg').modal('hide');
        if ($(this).data('type') == 'search') {
            //$('#quick-search').modal('show');
        }
    });
    $('#cancel-confirm-quote').on('click', function (e) {
        e.preventDefault();
        $('#modal-confirm-alt-itinerary').modal('hide');
    });
JS;
$this->registerJs($js);

?>
<?php if(!empty($errors)):?><div class="alert alert-danger">Some errors happened!</div><?php endif;?>

<?php $form = ActiveForm::begin([
    'errorCssClass' => '',
    'successCssClass' => '',
    'id' => $formID
]) ?>
<div class="alternatives__item">
    <div class="btn-wrapper">
        <?= Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Cancel', [
            'id' => 'cancel-alt-quote',
            'class' => 'btn btn-danger'
        ]) ?>
        <?= Html::submitButton('<i class="fa fa-save"></i> Confirm', [
                'id' => 'save-alt-quote',
                'class' => 'btn btn-primary'
            ]) ?>
    </div>
</div>
<?php ActiveForm::end() ?>