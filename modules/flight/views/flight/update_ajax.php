<?php

use modules\flight\models\Flight;
use modules\flight\models\forms\ItineraryEditForm;
use modules\flight\src\helpers\FlightFormatHelper;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/**
 * @var $itineraryForm ItineraryEditForm
 * @var $pjaxIdWrap string;
 */


$pjaxId = 'pjax-flight-update';

$itineraryFormId = $itineraryForm->formName() . '-form';
?>
<div class="flight-update-ajax">
    <div class="hotel-form">
        <script>
            pjaxOffFormSubmit('#<?= $pjaxId ?>');
        </script>
        <?php Pjax::begin(['id' => $pjaxId, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>

        <div class="clearfix"></div>
        <div class="request-form collapse in show" id="request" aria-expanded="true">
            <div id="modeFlightSegments" data-value="edit" style="display: none"></div>
            <div class="sl-itinerary-form2">
				<?php $form = ActiveForm::begin([
					'action' => ['/flight/flight/ajax-update-itinerary'],
					'enableClientValidation' => false,
					'options' => [
						'data-pjax' => true
					]
				]); ?>

				<?php
				    echo $form->errorSummary($itineraryForm);
				?>

				<?= Html::hiddenInput('flightId', $itineraryForm->flightId) ?>
				<?= Html::hiddenInput('pjaxIdWrap', $pjaxIdWrap) ?>

                <div class="sl-itinerary-form__tabs">
                    <div class="sl-itinerary-form__tab sl-itinerary-form__tab--rt js-tab"
                         id="flight-segments">
						<?= $this->render('partial/_formFlightSegment', [
							'model' => $itineraryForm,
							'form' => $form
                        ]) ?>
                    </div>
                </div>

                <div class="row ">
                    <div class="col-sm-3">
						<?= $form->field($itineraryForm, 'cabin', [
						])->dropDownList(Flight::getCabinClassList(), [
							'prompt' => '---']) ?>
                    </div>
                    <div class="col-sm-2">
						<?= $form->field($itineraryForm, 'adults')->dropDownList(FlightFormatHelper::adultsChildrenInfantsList(), ['prompt' => '-']) ?>
                    </div>
                    <div class="col-sm-2">
						<?= $form->field($itineraryForm, 'children')->dropDownList(FlightFormatHelper::adultsChildrenInfantsList(), ['prompt' => '-']) ?>
                    </div>
                    <div class="col-sm-2">
						<?= $form->field($itineraryForm, 'infants')->dropDownList(FlightFormatHelper::adultsChildrenInfantsList(), ['prompt' => '-']) ?>
                    </div>

                </div>

                <div class="row ">
                    <div class="col-sm-2">
                        <?= $form->field($itineraryForm, 'fl_stops')->input('number', ['min' => 0, 'max' => 9]) ?>
                    </div>
                    <div class="col-sm-2">
                        <?= $form->field($itineraryForm, 'fl_delayed_charge')->dropDownList([1 => 'Yes', 0 => 'No']) ?>
                    </div>
                </div>

                <div class="btn-wrapper text-center" style="margin-top: 10px;">

					<?= Html::submitButton('<i class="fa fa-check"></i> Save flight request', [
						'class' => 'btn btn-success',
					]) ?>

                </div>

				<?php ActiveForm::end(); ?>
            </div>
        </div>

        <?php Pjax::end(); ?>
    </div>
</div>
<?php
$js = <<<JS

JS;
//$this->registerJs($js, View::POS_HEAD);