<?php

use modules\qaTask\src\useCases\qaTask\decide\noAction\QaTaskDecideNoActionForm;
use common\components\bootstrap4\activeForm\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var QaTaskDecideNoActionForm $model */
/** @var View $this */

$formId = 'qa-task-decide-no-action-form';

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => ['/qa-task/qa-task-action/decide-no-action', 'gid' => $model->getTaskGid()],
]);

?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Apply', ['class' => 'btn btn-info']) ?>
    </div>

<?php

ActiveForm::end();
