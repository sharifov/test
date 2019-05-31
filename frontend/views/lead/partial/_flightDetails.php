<?php

use yii\widgets\ActiveForm;
use frontend\models\LeadForm;
use yii\helpers\Html;
use common\models\LeadFlightSegment;
use \sales\helpers\lead\LeadHelper;

/**
 * @var $this \yii\web\View
 * @var $form ActiveForm
 * @var $leadForm LeadForm
 * @var $itineraryForm \sales\forms\lead\ItineraryForm
 */


$this->registerJsFile('/js/moment.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);

$itineraryFormId = $itineraryForm->formName() . '-form';

?>

<div class="panel panel-primary sl-request-wrap">

    <div class="panel-heading collapsing-heading">
        <a data-toggle="collapse" href="#request-form-wrap" class="collapsing-heading__collapse-link"
           aria-expanded="true">

            <!--Flight Details-->
            <div class="sl-request-summary">
                <?php if ($itineraryForm->isEmpty()) : ?>
                    <div class="sl-request-summary__block">
                        <div class="sl-request-summary__locations">
                            <strong>Flight Details</strong>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="sl-request-summary__block">
                        <?php
                        $location = $departing = [];
                        foreach ($itineraryForm->segmentform as $segment) {
                            $location[] = $segment->origin . ' -> ' . $segment->destination;
                            $departing[] = Yii::$app->formatter->asDate(strtotime($segment->departure));
                        }
                        ?>
                        <div class="sl-request-summary__locations">
                            <strong><?= implode(', ', $location) ?></strong>
                        </div>
                        <div class="sl-request-summary__dates"><?= implode(', ', $departing) ?></div>
                    </div>
                    <div class="sl-request-summary__block">
                        <?php if ($itineraryForm->adults) : ?>
                            <div>
                                <i class="fa fa-user"></i> <strong><?= $itineraryForm->adults ?></strong>
                                adult
                            </div>
                        <?php endif; ?>
                        <?php if ($itineraryForm->children) : ?>
                            <div>
                                <i class="fa fa-user"></i> <strong><?= $itineraryForm->children ?></strong>
                                children
                            </div>
                        <?php endif; ?>
                        <?php if ($itineraryForm->infants) : ?>
                            <div>
                                <i class="fa fa-user"></i> <strong><?= $itineraryForm->infants ?></strong>
                                infants
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <i class="collapsing-heading__arrow"></i>
        </a>
    </div>


    <div class="panel-body collapse in" id="request-form-wrap" aria-expanded="true" style="">
        <div class="sl-itinerary-form">
            <?php $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'enableAjaxValidation' => true,
                'validationUrl' => ['/lead-validator/edit-itinerary'],
                'id' => $itineraryFormId
            ]); ?>

            <?= Html::hiddenInput('id', $itineraryForm->leadId)?>



            <div class="btn-wrapper">
                <?= Html::button('<span class="btn-icon"><i class="fa fa-plus"></i></span><span>Add Flight</span>', [
                    'id' => 'lead-new-segment-button',
                    'class' => 'btn btn-success btn-with-icon js-add-mc-row ',
                ]) ?>
            </div>

            <!--Passengers-->
            <div class="row sl-itinerary-form__pax">
                <div class="col-sm-3">
                    <?= $form->field($itineraryForm, 'cabin', [
                    ])->dropDownList(LeadHelper::cabinList(), [
                        'prompt' => '---'
                    ]) ?>
                </div>
                <div class="col-sm-2">
                </div>
                <div class="col-sm-2">
                    <?= $form->field($itineraryForm, 'adults')->textInput([
                        'class' => 'form-control lead-form-input-element',
//                        'type' => 'number',
                        'min' => 0,
                        'max' => 9,
                    ]) ?>
                </div>
                <div class="col-sm-2">
                    <?= $form->field($itineraryForm, 'children')->textInput([
                        'class' => 'form-control lead-form-input-element',
//                        'type' => 'number',
                        'min' => 0,
                        'max' => 9,
                    ]) ?>
                </div>
                <div class="col-sm-2">
                    <?= $form->field($itineraryForm, 'infants')->textInput([
                        'class' => 'form-control lead-form-input-element',
//                        'type' => 'number',
                        'min' => 0,
                        'max' => 9,
                    ]) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
