<?php

/**
 * @var $lead Lead
 * @var $this \yii\web\View
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use common\models\Lead;

$formID = 'getOnlineQuotes';

$js = <<<JS
    $('#search-quotes-by-gds').click(function (e) {
        e.preventDefault();
        $('#itinerary-key-id').val('');
        var form = $('#$formID');
        $('#preloader').removeClass('hidden');
        $('#quick-search').modal('hide');
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: form.serialize(),
            success: function (data) {
                $('#preloader').addClass('hidden');
                $('#quick-search').find('.modal-body #quick-search_quotes-result').html(data.body);
                $('#quick-search').modal('show');
            },
            error: function (error) {	
                $('#preloader').addClass('hidden');
                console.log('Error: ' + error);			
            }
        });
    });
JS;

$this->registerJs($js);
?>


<div class="sl-events-log">
    <?php $form = ActiveForm::begin([
        'errorCssClass' => '',
        'successCssClass' => '',
        'id' => $formID
    ]); ?>
    <?= Html::hiddenInput('itinerary-key', null, [
        'id' => 'itinerary-key-id'
    ]) ?>
    <div class="row" style="margin: 0 0 20px;">
        <div class="form-inline col-sm-7">
            <?= Html::label('Select GDS:') ?>
            <?= Html::dropDownList('gds', null, \common\components\GTTGlobal::getGDSName(), [
                'class' => 'form-control'
            ]) ?>
        </div>
        <div class="col-sm-offset-2 col-sm-3 text-right">
            <?= Html::button('Search', [
                'id' => 'search-quotes-by-gds',
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>
    <div id="quick-search_quotes-result">

    </div>
</div>