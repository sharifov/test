<?php
/**
 * @var $lead Lead
 * @var $quote Quote
 * @var $prices QuotePrice[]
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
    $('#cancel-alt-quote').click(function (e) {
        e.preventDefault();
        var editBlock = $('#$formID');
        editBlock.parent().parent().removeClass('in');
        editBlock.parent().html('');
        $('#create-quote').modal('hide');
        if ($(this).data('type') == 'search') {
            $('#quick-search').modal('show');
        }
    });
    $('#cancel-confirm-quote').click(function (e) {
        e.preventDefault();
        $('#modal-confirm-alt-itinerary').modal('hide');
    });
JS;
$this->registerJs($js);

?>

<?php $form = ActiveForm::begin([
    'errorCssClass' => '',
    'successCssClass' => '',
    'id' => $formID
]) ?>
<div class="alternatives__item">
    <div class="btn-wrapper">
        <?= Html::button('<span class="btn-icon"><i class="glyphicon glyphicon-remove-circle"></i></span><span>Cancel</span>', [
            'id' => 'cancel-alt-quote',
            'class' => 'btn btn-danger btn-with-icon'
        ]) ?>
        <?= Html::submitButton('<span class="btn-icon"><i class="fa fa-save"></i></span><span>Confirm</span>', [
                'id' => 'save-alt-quote',
                'class' => 'btn btn-primary btn-with-icon'
            ]) ?>
    </div>
</div>
<?php ActiveForm::end() ?>