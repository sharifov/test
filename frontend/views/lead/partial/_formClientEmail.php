<?php
/**
 * @var $form ActiveForm
 * @var $email ClientEmail
 * @var $key string|integer
 * @var $nr integer
 * @var $leadForm LeadForm
 */

use yii\widgets\ActiveForm;
use common\models\ClientEmail;
use yii\helpers\Html;
use frontend\models\LeadForm;

?>

<div class="form-group sl-client-field">
    <?php
    echo $form->field($email, '[' . $key . ']email', [
        'template' => '{input}{error}'
    ])->textInput([
        'class' => 'form-control email lead-form-input-element'
    ])->label(false);

    if (($key == '__id__' || strpos($key, 'new') !== false) && $nr != 0) {
        echo Html::a('<i class="fa fa-trash"></i>', 'javascript:void(0);', [
            'class' => 'btn sl-client-field-del js-cl-email-del client-remove-email-button',
        ]);
    } else if (!$email->isNewRecord) {
        $popoverId = 'addEmailComment-' . $key;
        $commentTemplate = '
<div>
    <form action="/lead/add-comment?type=email&amp;id=' . $key . '" method="post">
        <textarea id="email-comment-' . $key . '" style="background-color: #fafafc; border: 1px solid #e4e8ef;" class="form-control mb-20" name="comment" rows="3">' . $email->comments . '</textarea>
        <button type="button" class="btn btn-success popover-close-btn" onclick="addEmailComment($(this), \'' . $key . '\');">Add</button>    
    </form>
</div>
';
        echo Html::a('<i class="fa fa-comment"></i>', 'javascript:void(0);', [
            'id' => $popoverId,
            'data-toggle' => 'popover',
            'data-placement' => 'right',
            'data-content' => $commentTemplate,
            'class' => 'btn sl-client-field-del js-cl-email-del client-comment-email-button',
        ]);
    }
    ?>
</div>
