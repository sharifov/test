<?php
use yii\widgets\ActiveForm;
use common\models\Lead;
use yii\helpers\Html;

/**
 * @var $discountId string
 * @var $activity []
 * @var $form ActiveForm
 * @var $lead Lead
 * @var $this \yii\web\View
 */

$js = <<<JS
$('#search-by-discount-id').click(function() {
    var discount = $('input[name="discountId"]');
    if (discount.val().length == 0) {
        discount.parent().addClass('has-error');
    } else {
        discount.parent().removeClass('has-error');
        var form = $('#clientRequestLog');
        var tr = '<tr><th class="text-bold text-center">Not found info by Discount ID. Search by other Discount ID!</th></tr>';
        $('#log-events tbody').html('');
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serializeArray(),
            success: function (data) {
                if(data.length > 0) {
                    console.log(data);
                    $.each(data, function(i, entry){
                        tr = '<tr>' +
                            '<th class="text-bold">'+entry.created+'</th>' +
                            '<td><a href="'+entry.referer+'" target="_blank">'+entry.referer+'</a></td>' +
                            '</tr>';
                        $('#log-events tbody').append(tr);
                    });
                } else {
                    $('#log-events tbody').append(tr);
                }
            },
            error: function (error) {
                $('#log-events tbody').append(tr);
                console.log('Error: ' , error);
            }
        });
    }
});
JS;
$this->registerJs($js);
?>

<div class="sl-events-log">
    <?php $form = ActiveForm::begin([
        'action' => \yii\helpers\Url::to(['lead/get-user-actions', 'id' => $lead->id]),
        'errorCssClass' => '',
        'successCssClass' => '',
        'id' => 'clientRequestLog'
    ]); ?>
    <div class="row" style="margin: 0 0 20px;">
        <div class="form-inline col-sm-7">
            <?= Html::label('Discount Id:') ?>
            <?= Html::textInput('discountId', $discountId, [
                'class' => 'form-control'
            ]) ?>
        </div>
        <div class="col-sm-offset-2 col-sm-3 text-right">
            <?= Html::button('Search', [
                'id' => 'search-by-discount-id',
                'class' => 'btn btn-primary'
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end() ?>
    <table class="table table-neutral">
        <tbody>
        <?php if (!empty($activity)) :
            foreach ($activity as $item) : ?>
                <tr>
                    <th class="text-bold"><?= $item->created ?></th>
                    <td><?= Html::a($item->referer, $item->referer) ?></td>
                </tr>
            <?php endforeach;
        else : ?>
            <tr>
                <th class="text-bold text-center">Not found info by Discount ID. Search by other Discount ID!</th>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
