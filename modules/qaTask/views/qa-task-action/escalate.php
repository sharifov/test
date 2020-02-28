<?php

use modules\qaTask\src\useCases\qaTask\escalate\QaTaskEscalateForm;
use sales\yii\bootstrap4\activeForm\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var QaTaskEscalateForm $model */
/** @var View $this */

$formId = 'qa-task-escalate-form';

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => ['/qa-task/qa-task-action/escalate', 'gid' => $model->getTaskGid()],
]);

?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'reasonId')->dropDownList($model->getReasonList(), ['prompt' => 'Select reason']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

    <?= $form->field($model, 'rating')->dropDownList($model->getRatingList(), ['prompt' => 'Select rating']) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Apply', ['class' => 'btn btn-info']) ?>
    </div>

<?php

ActiveForm::end();
