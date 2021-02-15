<?php

use sales\forms\cases\CasesLinkChatForm;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var $model CasesLinkChatForm
 */

?>

<?php Pjax::begin([
    'id' => '_link_case',
    'timeout' => 2000,
    'enablePushState' => false,
    'enableReplaceState' => false
]); ?>
    <?php $activeForm = ActiveForm::begin([
        'id' => $model->formName() . '-form',
        'enableClientValidation' => true,
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

        <?= $activeForm->errorSummary($model) ?>

        <?= $activeForm->field($model, 'chatId')->hiddenInput()->label(false) ?>

        <?= $activeForm->field($model, 'caseId')->input('number') ?>

        <div class="text-center">
            <?= Html::submitButton('<i class="fa fa-link"> </i> Link Case', ['class' => 'btn btn-success']) ?>
        </div>

    <?php ActiveForm::end(); ?>
<?php Pjax::end(); ?>
