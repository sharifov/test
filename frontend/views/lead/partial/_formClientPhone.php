<?php
/**
 * @var $form ActiveForm
 * @var $phone ClientPhone
 * @var $key string|integer
 * @var $nr integer
 * @var $leadForm LeadForm
 */

use yii\widgets\ActiveForm;
use common\models\ClientPhone;
use yii\helpers\Html;
use frontend\models\LeadForm;
use borales\extensions\phoneInput\PhoneInput;

?>

<div class="form-group sl-client-field">
    <?php
    if ($key == '__id__') {
        echo $form->field($phone, '[' . $key . ']phone', [
            'options' => [
                'class' => '',
            ],
            'template' => '{input}{error}'
        ])->textInput([
            'class' => 'form-control lead-form-input-element',
            'type' => 'tel'
        ])->label(false);
    } else {
        echo $form->field($phone, '[' . $key . ']phone', [
            'options' => [
                'class' => '',
            ],
            'template' => '{input}{error}'
        ])->widget(PhoneInput::class, [
            'options' => [
                'class' => 'form-control lead-form-input-element'
            ],
            'jsOptions' => [
                'nationalMode' => false,
                'preferredCountries' => ['us'],
            ]
        ])->label(false);
    }

    if (($key == '__id__' || strpos($key, 'new') !== false) && $nr != 0) {
        echo Html::a('<i class="fa fa-trash"></i>', 'javascript:void(0);', [
            'class' => 'btn sl-client-field-del js-cl-email-del client-remove-phone-button',
        ]);
    } else if (!$phone->isNewRecord) {
        $popoverId = 'addPhoneComment-' . $key;
        $commentTemplate = '
<div>
    <form action="/lead/add-comment?type=phone&amp;id=' . $key . '" method="post">
        <textarea id="email-comment-' . $key . '" style="background-color: #fafafc; border: 1px solid #e4e8ef;" class="form-control mb-20" name="comment" rows="3">' . $phone->comments . '</textarea>
        <button type="button" class="btn btn-success popover-close-btn" onclick="addPhoneComment($(this), \'' . $key . '\');">Add</button>    
    </form>
</div>
';
        echo Html::a('<i class="fa fa-comment"></i>', 'javascript:void(0);', [
            'id' => $popoverId,
            'data-toggle' => 'popover',
            'data-placement' => 'right',
            'data-content' => $commentTemplate,
            'class' => 'btn sl-client-field-del js-cl-email-del client-comment-phone-button',
        ]);
    }
    ?>
</div>
