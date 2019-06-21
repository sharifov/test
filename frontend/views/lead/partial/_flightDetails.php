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

/*$this->registerCssFile('/css/style-request-form.css', [
    'position' => View::POS_HEAD,
    'depends' => [
        JqueryAsset::class
    ]
]);*/

$itineraryFormId = $itineraryForm->formName() . '-form';

?>
<div class="row">
    <div class="col-md-12">
        <?= Alert::widget() ?>
    </div>
</div>


<div class="row" style="margin-bottom: 10px">
    <div class="col-sm-12">
        <div class="request">
            <div class="request-overview" style="">
                <div style="letter-spacing: 0.8px; border-bottom: 1px dotted rgb(165, 177, 197); padding-bottom: 13px;"
                     class="row-flex row-flex-justify">
                    <span style="font-weight: 600; font-size: 18px;">Flight Request</span>
                    <span style="font-size: 16px; padding: 0 7px"><i class="fa fa-random text-success"
                                                                     aria-hidden="true"></i> <?= LeadHelper::tripTypeName($itineraryForm->tripType) ?> • <?= LeadHelper::cabinName($itineraryForm->cabin) ?> • <?= (int)$itineraryForm->adults + (int)$itineraryForm->children + (int)$itineraryForm->infants ?> pax</span>
                    <span>
                        <?php if ($itineraryForm->adults): ?>
                            <span><strong class="label label-success"
                                          style="margin-left: 7px;"><?= $itineraryForm->adults ?></strong> ADT</span>
                        <?php endif; ?>
                        <?php if ($itineraryForm->children): ?>
                            <span><strong class="label label-success"
                                          style="margin-left: 7px;"><?= $itineraryForm->children ?></strong> CHD</span>
                        <?php endif; ?>
                        <?php if ($itineraryForm->infants): ?>
                            <span><strong class="label label-success"
                                          style="margin-left: 7px;"><?= $itineraryForm->infants ?></strong> INF</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div style="padding-top: 10px; align-items: flex-end" class="row-flex-justify">
                    <div>
                        <table class="table table-bordered table-hover">
                            <tr>
                                <th></th>
                                <th class="text-center">Origin</th>
                                <th class="text-center">Destination</th>
                                <th class="text-center">Departure</th>
                                <th class="text-center">Flex</th>
                            </tr>
                            <?php foreach ($itineraryForm->segments as $keySegment => $segment): ?>
                                <tr>
                                    <td>
                                        <span style="font-size: 18px; color: #91a5ae; margin-right: 7px; vertical-align: -1px;"><?= $keySegment + 1 ?>. </span>
                                    </td>
                                    <td>
                                    <span style="font-size: 16px; white-space: nowrap; margin-right: 10px; color: #4a525f">
                                        (<b><?= Html::encode($segment->origin) ?></b>)
                                        <?= Html::encode($segment->originCity) ?>
                                    </span>
                                    </td>
                                    <td>
                                    <span style="font-size: 16px; white-space: nowrap; margin-right: 10px; color: #4a525f">
                                        (<b><?= Html::encode($segment->destination) ?></b>)
                                        <?= Html::encode($segment->destinationCity) ?>
                                    </span>
                                    </td>
                                    <td>
                                    <span style="font-size: 14px;">
                                        <?= date('d-M-Y', strtotime($segment->departure)) ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-success text-center"><?= $segment->flexibility ? $segment->flexibilityType . ' ' . $segment->flexibility . ' days' : 'exact' ?></strong>
                                    </td>
                                </tr>
                                <? /*<div>
                                <span style="font-size: 18px; color: #91a5ae; margin-right: 7px; vertical-align: -1px;"><?= $keySegment + 1 ?>. </span>
                                <span style="font-size: 16px; white-space: nowrap; margin-right: 10px; color: #4a525f"><?=$segment->originLabel?> <strong>(<?=Html::encode($segment->origin)?>)</strong> → <?=$segment->destinationLabel?> <strong>(<?=Html::encode($segment->destination)?>)</strong></span>
                                <span style="font-size: 14px;">
                            <?=date('d-M-Y', strtotime($segment->departure))?></span>
                                <strong style="font-size: 13px; margin-left: 3px;" class="text-success"><?= $segment->flexibility ? $segment->flexibilityType . ' ' . $segment->flexibility . ' days': 'Exact'?></strong>
                            </div>*/ ?>
                            <?php endforeach; ?>
                        </table>
                    </div>

                    <? /*= Html::a('<i class="fa fa-edit"></i> Edit',
                        ['/lead-itinerary/view-edit-form'],
                        ['class' => 'btn btn-default', 'data' => ['method' => 'post', 'params'=> ['id'=> $itineraryForm->leadId]]])*/ ?>

                    <?php if ($itineraryForm->isViewMode()) : ?>
                        <div id="modeFlightSegments" data-value="view" style="display: none"></div>
                        <?php if (Yii::$app->user->can('updateLead', ['leadId' => $itineraryForm->leadId])) : ?>

                            <?= Html::a('<i class="fa fa-edit"></i> Edit',
                                ['/lead-itinerary/view-edit-form', 'id' => $itineraryForm->leadId],
                                ['class' => 'btn btn-default']) ?>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>

                <?php if ($itineraryForm->isEditMode()) : ?>

                    <div class="clearfix"></div>
                    <div class="request-form collapse in" id="request" aria-expanded="true">
                        <div class="separator"></div>

                        <div id="modeFlightSegments" data-value="edit" style="display: none"></div>

                        <div class="sl-itinerary-form2">
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
                                <div class="sl-itinerary-form__tab sl-itinerary-form__tab--rt js-tab"
                                     id="lead-segments">
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
                                <div class="col-sm-1">
                                </div>
                                <div class="col-sm-1">
                                    <?= $form->field($itineraryForm, 'adults')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-']) ?>
                                </div>
                                <div class="col-sm-1">
                                    <?= $form->field($itineraryForm, 'children')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-']) ?>
                                </div>
                                <div class="col-sm-1">
                                    <?= $form->field($itineraryForm, 'infants')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-']) ?>
                                </div>

                            </div>

                            <div class="separator"></div>
                            <div class="btn-wrapper text-right">
                                <?= Html::submitButton('<i class="fa fa-check"></i> Save flight request', [
                                    'id' => 'lead-new-segment-button',
                                    'class' => 'btn btn-success',
                                ]) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
