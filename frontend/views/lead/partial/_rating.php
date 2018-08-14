<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 */

$disabled = ($lead->employee_id == Yii::$app->user->identity->getId() && $lead->status == \common\models\Lead::STATUS_PROCESSING)
    ? '' : ' disabled';
$ratingUrl = \yii\helpers\Url::to([
    '/lead/set-rating',
    'id' => $lead->id
]);
$js = <<<JS
$('input[name="rate"]').click(function() {
    var rating = $(this);
    $.ajax({
        url: '$ratingUrl',
        type: 'post',
        data: {rating: rating.val()},
        success: function (data) {
            if (!data) {
                rating.prop('checked', false);
            }
        },
        error: function (error) {
            console.log('Error: ' + error);
        }
    });
});
JS;
$this->registerJs($js);

?>

<fieldset class="rate-input-group">
    <input type="radio" name="rate" id="rate-3" value="3" <?= ($lead->rating == 3) ? 'checked' : '' ?> <?= $disabled ?>>
    <label for="rate-3"></label>

    <input type="radio" name="rate" id="rate-2" value="2" <?= ($lead->rating == 2) ? 'checked' : '' ?> <?= $disabled ?>>
    <label for="rate-2"></label>

    <input type="radio" name="rate" id="rate-1" value="1" <?= ($lead->rating == 1) ? 'checked' : '' ?> <?= $disabled ?>>
    <label for="rate-1"></label>
</fieldset>
