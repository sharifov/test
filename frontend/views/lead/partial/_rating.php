<?php

use src\auth\Auth;
use src\model\leadUserRating\entity\LeadUserRatingQuery;

/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $canUpdateRating bool;
 */

$disabled = $canUpdateRating ? '' : 'disabled';
$ratingUrl = \yii\helpers\Url::to([
    '/lead/set-user-rating',
]);
$leadUserRating = \src\model\leadUserRating\entity\LeadUserRatingQuery::getByLeadAndUserId($lead->id, Auth::id());
$rating = isset($leadUserRating) ? $leadUserRating->lur_rating : 0;

$js = <<<JS
var leadRating = '$rating';
$('input[name="rate"]').change(function() {
    let rating = $(this);
    let leadId = '$lead->id';
    $.ajax({
        url: '$ratingUrl',
        type: 'post',
        data: 
        {
            leadId: leadId,
            rating: rating.val()
        },
        success: function (data) {
            if (!data.success) {
                createNotify('Error', data.error, 'error');
                $('#rate-'+leadRating).prop('checked',true);
            }
            else 
            {
                createNotify('Success', 'Lead Rating updated to ' + rating.val(), 'success');
                leadRating = $('input[name="rate"]:checked').val();
            }
        },
        error: function (error) {
            createNotify('Error', 'Server error', 'error');
            $('#rate-'+leadRating).prop('checked',true);
        }
    });
});
JS;
$this->registerJs($js);

?>
<div class="col-md-7">
<strong >Lead Rating: </strong>
</div>
  <div class="col-md-7">
<fieldset class="rate-input-group">

    <input type="radio" name="rate" id="rate-5" value="5" <?= ($rating == 5) ? 'checked' : '' ?> <?= $disabled ?>>
    <label for="rate-5"></label>

    <input type="radio" name="rate" id="rate-4" value="4" <?= ($rating == 4) ? 'checked' : '' ?> <?= $disabled ?>>
    <label for="rate-4"></label>

    <input type="radio" name="rate" id="rate-3" value="3" <?= ($rating == 3) ? 'checked' : '' ?> <?= $disabled ?>>
    <label for="rate-3"></label>

    <input type="radio" name="rate" id="rate-2" value="2" <?= ($rating == 2) ? 'checked' : '' ?> <?= $disabled ?>>
    <label for="rate-2"></label>

    <input type="radio" name="rate" id="rate-1" value="1" <?= ($rating == 1) ? 'checked' : '' ?> <?= $disabled ?>>
    <label for="rate-1"></label>
</fieldset></div>
