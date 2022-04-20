<?php

use kartik\select2\Select2;
use modules\shiftSchedule\src\entities\shift\Shift;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var \common\models\Employee $model */
/* @var ActiveForm $form */

$this->title = 'User Shift Assigns Update. Employee: ' . $model->username . '/' . $model->id . '';
$this->params['breadcrumbs'][] = ['label' => 'User Shift Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="user-shift-assign">
    <div class="col-md-4">
        <h6><?= Html::encode($this->title) ?></h6>

        <?php $form = ActiveForm::begin(); ?>

        <?php echo $form->field($model, 'user_shift_assigns', ['options' => []])->widget(Select2::class, [
            'data' => Shift::getList(),
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select Shift', 'multiple' => true],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>

        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
