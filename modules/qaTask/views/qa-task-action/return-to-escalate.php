<?php

use modules\qaTask\src\useCases\qaTask\returnTask\toEscalate\QaTaskReturnToEscalateForm;
use sales\yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var QaTaskReturnToEscalateForm $model */
/** @var View $this */

$formId = 'qa-task-return-to-escalate-form';

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => ['/qa-task/qa-task-action/return-to-escalate', 'gid' => $model->getTaskGid()],
]);

?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'reasonId')->dropDownList($model->getReasonList(), ['prompt' => 'Select reason']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Apply', ['class' => 'btn btn-info']) ?>
    </div>

<?php

ActiveForm::end();
