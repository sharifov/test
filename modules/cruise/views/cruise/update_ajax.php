<?php

use dosamigos\datepicker\DatePicker;
use kartik\select2\Select2;
use modules\cruise\src\useCase\updateCruiseRequest\CruiseUpdateRequestForm;
use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model CruiseUpdateRequestForm */

$pjaxId = 'pjax-cruise-update'
?>
<div class="cruise-update-ajax">
    <div class="cruise-form">
        <script>
            pjaxOffFormSubmit('#<?=$pjaxId?>');
        </script>
        <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ['data-pjax' => true],
            'action' => ['/cruise/cruise/update-ajax', 'id' => $model->getCruiseId()],
            'method' => 'post',
            'enableClientValidation' => false
        ]);
        ?>

            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'crs_departure_date_from')->widget(
                DatePicker::class,
                [
                    'inline' => false,
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayBtn' => true
                    ]
                ]
            )?>

            <?= $form->field($model, 'crs_arrival_date_to')->widget(
                DatePicker::class,
                [
                'inline' => false,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayBtn' => true
                ]
                ]
            )?>

            <?= $form->field($model, 'crs_destination_code')->widget(Select2::class, [
                'data' => $model->getDestinations(),
                'size' => Select2::SMALL,
                'pluginOptions' => ['allowClear' => true],
                'options' => ['placeholder' => 'Select destination', 'multiple' => false],
            ]) ?>

            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>

<?php
$js = <<<JS
function formatRepo( repo ) {
				if (repo.loading) return repo.text;

				var markup = "<div class='select2-result-repository clearfix'>" +
					//"<div class='select2-result-repository__avatar'><i class=\"fa fa-plane\"></div>" +
					"<div class='select2-result-repository__meta'>" +
						"<div class='select2-result-repository__title'>" + repo.text + "</div>";
				
				/*markup += "<div class='select2-result-repository__statistics'>" +
							"<div class='select2-result-repository__forks'>" + repo.id + "</div>" +
						"</div>" +*/
				markup +=	"</div></div>";

				return markup;
			}
JS;
$this->registerJs($js, View::POS_HEAD);
?>
