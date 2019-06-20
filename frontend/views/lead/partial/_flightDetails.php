<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use \sales\helpers\lead\LeadHelper;
use \yii\web\JqueryAsset;
use \yii\web\View;
use \common\widgets\Alert;

/**
 * @var $this yii\web\View
 * @var $form ActiveForm
 * @var $itineraryForm sales\forms\lead\ItineraryEditForm
 * @var $leadForm frontend\models\LeadForm
 */


$this->registerJsFile('/js/moment.min.js', [
    'position' => View::POS_HEAD,
    'depends' => [
        JqueryAsset::class
    ]
]);

$itineraryFormId = $itineraryForm->formName() . '-form';

?>
<div class="row">
    <div class="col-md-12">
        <?= Alert::widget() ?>
    </div>
</div>

<div class="panel panel-primary sl-request-wrap">


    <div class="panel-heading collapsing-heading">
        <a data-toggle="collapse" href="#request-form-wrap" class="collapsing-heading__collapse-link"
           aria-expanded="true">

            <!--Flight Details-->
            <div class="sl-request-summary">

                <div class="sl-request-summary__block">
                    <?php
                    $location = $departing = [];
                    foreach ($itineraryForm->segments as $segment) {
                        $location[] = $segment->origin . ' -> ' . $segment->destination;
                        $departing[] = Yii::$app->formatter->asDate(strtotime($segment->departure));
                    }
                    ?>
                    <div class="sl-request-summary__locations">
                        <strong><?= implode(', ', $location) ?></strong>
                    </div>
                    <div class="sl-request-summary__dates"><?= implode(', ', $departing) ?></div>

                    <?php if ($itineraryForm->tripType) : ?>
                        <div>
                            Trip type <strong><?= LeadHelper::tripTypeName($itineraryForm->tripType) ?></strong>
                        </div>
                    <?php endif; ?>

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

            </div>
            <i class="collapsing-heading__arrow"></i>
        </a>
    </div>

    <div class="panel-body collapse in" id="request-form-wrap" aria-expanded="true"
         style="">

        <?php if ($itineraryForm->isViewMode()) : ?>
            <?php if (Yii::$app->user->can('updateLead', ['id' => $itineraryForm->leadId])) : ?>
                <?php $form = ActiveForm::begin([
                    'action' => ['/lead-itinerary/view-edit-form'],
                    'options' => [
                        'data-pjax' => 1
                    ]
                ]); ?>
                <?= Html::hiddenInput('id', $itineraryForm->leadId) ?>
                <div style="margin:20px">
                    <?= Html::a('<span class="btn-icon"><i class="fa fa-edit"></i></span><span>Edit</span>',
                        ['/lead-itinerary/view-edit-form'],
                        ['class' => 'btn btn-success btn-with-icon', 'data' => ['method' => 'post']]) ?>
                </div>
                <div id="modeFlightSegments" data-value="view" style="display: none"></div>
                <?php ActiveForm::end() ?>
            <?php endif; ?>

        <?php elseif ($itineraryForm->isEditMode()) : ?>

            <div id="modeFlightSegments" data-value="edit" style="display: none"></div>

            <div class="sl-itinerary-form">
                <?php $form = ActiveForm::begin([
                    'action' => ['/lead-itinerary/edit'],
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => true,
                    'validationUrl' => ['/lead-itinerary/validate'],
                    'id' => $itineraryFormId,
                    'options' => [
                        'data-pjax' => 1
                    ]
                ]); ?>

                <?= Html::hiddenInput('id', $itineraryForm->leadId) ?>

                <div class="sl-itinerary-form__tabs">
                    <div class="sl-itinerary-form__tab sl-itinerary-form__tab--rt js-tab" id="lead-segments">
                        <?= $this->render('_formLeadSegment', [
                            'model' => $itineraryForm,
                            'form' => $form]) ?>
                    </div>
                </div>

                <div class="row ">
                    <div class="col-sm-3">
                        <?= $form->field($itineraryForm, 'cabin', [
                        ])->dropDownList(LeadHelper::cabinList(), [
                            'prompt' => '---']) ?>
                    </div>
                    <div class="col-sm-2">
                    </div>
                    <div class="col-sm-2">
                        <?= $form->field($itineraryForm, 'adults')->textInput([
                            'class' => 'form-control lead-form-input-element',
                            'type' => 'number',
                            'min' => 0,
                            'max' => 9]) ?>
                    </div>
                    <div class="col-sm-2">
                        <?= $form->field($itineraryForm, 'children')->textInput([
                            'class' => 'form-control lead-form-input-element',
                            'type' => 'number',
                            'min' => 0,
                            'max' => 9]) ?>
                    </div>
                    <div class="col-sm-2">
                        <?= $form->field($itineraryForm, 'infants')->textInput([
                            'class' => 'form-control lead-form-input-element',
                            'type' => 'number',
                            'min' => 0,
                            'max' => 9]) ?>
                    </div>

                </div>

                <div class="row">
                    <div class="col-sm-2">
                        <div class="btn-wrapper">
                            <?= Html::submitButton('<span class="btn-icon"><i class="fa fa-plus"></i></span><span>Save</span>', [
                                'id' => 'lead-new-segment-button',
                                'class' => 'btn btn-success btn-with-icon js-add-mc-row ',
                            ]) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

        <?php endif; ?>

    </div>

</div>
