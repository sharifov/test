<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLinePhoneNumber\entity\PhoneLinePhoneNumber */
/* @var $form ActiveForm */
?>

<div class="phone-line-phone-number-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'plpn_line_id')->textInput() ?>

        <?= $form->field($model, 'plpn_pl_id')->widget(\sales\widgets\PhoneSelect2Widget::class) ?>

        <?= $form->field($model, 'plpn_default')->checkbox() ?>

        <?= $form->field($model, 'plpn_enabled')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

    <div class="col-md-4">
		<?php

		try {
			echo $form->field($model, 'plpn_settings_json')->widget(
				\kdn\yii2\JsonEditor::class,
				[
					'clientOptions' => [
						'modes' => ['code', 'form', 'tree', 'view'], //'text',
						'mode' => 'tree'
					],
					//'collapseAll' => ['view'],
					'expandAll' => ['tree', 'form'],
				]
			);
		} catch (Exception $exception) {
			echo $form->field($model, 'plpn_settings_json')->textarea(['rows' => 6]);
		}

		?>
    </div>

</div>
