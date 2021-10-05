<?php

/**
 * @var \modules\qaTask\src\useCases\qaTask\cancel\QaTaskMultipleCancelFrom $model
 */

use common\models\Employee;
use frontend\themes\gentelella_v2\widgets\FlashAlert;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

$pjaxId = 'pjaxQaTaskMultiCancel'
?>
<script>pjaxOffFormSubmit('#<?= $pjaxId ?>')</script>
<?php Pjax::begin(['id' => $pjaxId,'enableReplaceState' => false, 'enablePushState' => false, 'timeout' => 5000]) ?>

<?= FlashAlert::widget() ?>

<?php $form = ActiveForm::begin([
    'options' => ['data-pjax' => 1],
]) ?>

<?= $form->errorSummary($model) ?>

<?php /*echo $form->field($model, 'userId')->widget(Select2::class, [
    'model' => $model,
    'data' => Employee::getList(),
    'options' => [
        'prompt' => '---'
    ]
]) */?>

<?php if ($model->actionReasonsExists) : ?>
    <?= $form->field($model, 'actionId')->dropdownList($model->actionReasons, [
        'prompt' => '---',
    ]) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 5]) ?>
<?php endif; ?>

<div class="form-group text-right">
    <?= Html::submitButton('<i class="fa fa-check-square"></i> Apply', ['class' => 'btn btn-info']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php Pjax::end(); ?>
