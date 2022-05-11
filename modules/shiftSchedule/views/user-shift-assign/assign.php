<?php

use kartik\select2\Select2;
use modules\shiftSchedule\src\entities\shift\Shift;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var \common\models\Employee $model */
/* @var ActiveForm $form */

$this->title = 'Employee: ' . $model->username . '/' . $model->id . '';
$this->params['breadcrumbs'][] = ['label' => 'User Shift Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="user-shift-assign">
    <div class="col-md-12">
        <h6><?= Html::encode($this->title) ?></h6>

        <?php Pjax::begin([
            'id' => 'pjax-usha-box-form',
            'enableReplaceState' => false,
            'enablePushState' => false,
            'timeout' => 5000,
            'clientOptions' => ['async' => false]
        ]); ?>

            <?php $form = ActiveForm::begin([
                'id' => 'js-usha-form',
                'options' => ['data-pjax' => 1],
                'enableClientValidation' => true,
                'validateOnChange' => false,
                'validateOnBlur' => false,
            ]) ?>

                <?php echo $form->field($model, 'user_shift_assigns', ['options' => []])->widget(Select2::class, [
                    'data' => Shift::getList(),
                    'size' => Select2::SMALL,
                    'options' => ['placeholder' => 'Select Shift', 'multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>

                <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'id' => 'js-usha-submit']) ?>
                </div>

            <?php ActiveForm::end(); ?>

<?php
$js = <<<JS
    $("#pjax-usha-box-form").on("pjax:beforeSend", function() {
        $('#js-usha-submit').removeClass('btn-success').addClass('fa-spin fa-spinner disabled').prop('disabled', true);
    });

    $("#pjax-usha-box-form").on("pjax:complete", function() {
        $('#js-usha-submit').removeClass('fa-spin fa-spinner disabled').addClass('btn-success').removeAttr('disabled');
    });
JS;
$this->registerJs($js);
?>

        <?php Pjax::end() ?>
    </div>

</div>
