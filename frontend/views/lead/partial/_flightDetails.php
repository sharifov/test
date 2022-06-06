<?php

use modules\featureFlag\FFlag;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use src\helpers\lead\LeadHelper;
use yii\web\JqueryAsset;
use yii\web\View;
use common\widgets\Alert;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var $form ActiveForm
 * @var $itineraryForm src\forms\lead\ItineraryEditForm
 * @var bool $isCreatedFlightRequest
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
                    <span style="font-weight: 600; font-size: 16px;">Flight Request</span>
                    <span style="font-size: 13px; padding: 0 7px">
                        <?php
                        switch ($itineraryForm->tripType) {
                            case \common\models\Lead::TRIP_TYPE_ONE_WAY:
                                    $iconClass = 'fa fa-long-arrow-right';
                                break;
                            case \common\models\Lead::TRIP_TYPE_ROUND_TRIP:
                                    $iconClass = 'fa fa-exchange';
                                break;
                            case \common\models\Lead::TRIP_TYPE_MULTI_DESTINATION:
                                    $iconClass = 'fa fa-random';
                                break;
                            default:
                                    $iconClass = '';
                        }
                        ?>
                        <i class="<?=$iconClass?> text-success" aria-hidden="true"></i>
                        <?= LeadHelper::tripTypeName($itineraryForm->tripType) ?> •
                        <b><?= LeadHelper::cabinName($itineraryForm->cabin) ?></b> •
                        <?= (int)$itineraryForm->adults + (int)$itineraryForm->children + (int)$itineraryForm->infants ?> pax</span>
                    <span>
                        <?php if ($itineraryForm->adults) : ?>
                            <span><strong class="label label-success"
                                          style="margin-left: 7px;"><?= $itineraryForm->adults ?></strong> ADT</span>
                        <?php endif; ?>
                        <?php if ($itineraryForm->children) : ?>
                            <span><strong class="label label-success"
                                          style="margin-left: 7px;"><?= $itineraryForm->children ?></strong> CHD</span>
                        <?php endif; ?>
                        <?php if ($itineraryForm->infants) : ?>
                            <span><strong class="label label-success"
                                          style="margin-left: 7px;"><?= $itineraryForm->infants ?></strong> INF</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="request-overview__table-wrap">
                    <div>
                        <table class="table request-overview__table">
                            <tr>
                                <th>Nr</th>
                                <th>Origin</th>
                                <th></th>
                                <th>Destination</th>
                                <th>Departure</th>
                                <th>Flex</th>
                            </tr>
                            <?php foreach ($itineraryForm->segments as $keySegment => $segment) : ?>
                                <tr>
                                    <td>
                                        <?= $keySegment + 1 ?>.
                                    </td>
                                    <td>

                                        (<b><?= Html::encode($segment->origin) ?></b>)
                                        <?= Html::encode($segment->originCity) ?>

                                    </td>
                                    <td>
                                        <i class="fa fa-long-arrow-right"></i>
                                    </td>
                                    <td>

                                        (<b><?= Html::encode($segment->destination) ?></b>)
                                        <?= Html::encode($segment->destinationCity) ?>

                                    </td>
                                    <td style="<?=time() > strtotime($segment->departure) ? 'color: red;' : ''?>">
                                        <i class="fa fa-calendar"></i> <?= date('d-M-Y', strtotime($segment->departure)) ?>
                                    </td>
                                    <td>
                                        <?= $segment->flexibility ? '<strong class="text-success">' . $segment->flexibilityType . ' ' . $segment->flexibility . ' days</strong>' : 'exact' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>

                    <?php /*= Html::a('<i class="fa fa-edit"></i> Edit',
                        ['/lead-itinerary/view-edit-form'],
                        ['class' => 'btn btn-default', 'data' => ['method' => 'post', 'params'=> ['id'=> $itineraryForm->leadId]]])*/ ?>

                    <?php if ($itineraryForm->isViewMode()) : ?>
                        <?php if (Yii::$app->user->can('updateLead', ['leadId' => $itineraryForm->leadId])) : ?>
                        <div class="btn-wrapper text-right" style="margin-top: 10px">
                            <?= Html::a(
                                '<i class="fa fa-edit"></i> Edit Request',
                                ['/lead-itinerary/view-edit-form', 'id' => $itineraryForm->leadId],
                                ['class' => 'btn btn-default js-edit-request']
                            ) ?>
                        </div>
                        <?php endif; ?>

                        <div id="modeFlightSegments" data-value="view" style="display: none"></div>

                    <?php endif; ?>

                </div>




                <?php if ($itineraryForm->isEditMode()) : ?>
                    <div class="clearfix"></div>
                    <div class="request-form collapse in show" id="request" aria-expanded="true">
                        <div class="separator"></div>

                        <div id="modeFlightSegments" data-value="edit" style="display: none"></div>
                        <?php
                        $js = <<<JS
    pjaxOffFormSubmit('#product-accordion');
    pjaxOffFormSubmit('#pjax-lead-products-wrap');
JS;
                        $this->registerJs($js, View::POS_HEAD);
                        ?>
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
                                <div class="col-sm-2">
                                    <?= $form->field($itineraryForm, 'adults')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-']) ?>
                                </div>
                                <div class="col-sm-2">
                                    <?= $form->field($itineraryForm, 'children')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-']) ?>
                                </div>
                                <div class="col-sm-2">
                                    <?= $form->field($itineraryForm, 'infants')->dropDownList(LeadHelper::adultsChildrenInfantsList(), ['prompt' => '-']) ?>
                                </div>

                            </div>

                            <div class="separator"></div>
                            <div class="btn-wrapper text-right">


                                <?= Html::a('<i class="fa fa-remove"></i> Close', ['/lead-itinerary/view-edit-form', 'id' => $itineraryForm->leadId, 'mode' => 'view'], [
                                    'class' => 'btn btn-default',
                                ]) ?>

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

<?php
/** @fflag FFlag::FF_KEY_ADD_AUTO_QUOTES, Auto add quote */
/** @abac new LeadAbacDto($lead, Auth::id()), LeadAbacObject::ACT_ADD_AUTO_QUOTES, LeadAbacObject::ACTION_ACCESS, Access to auto add quotes */
$itineraryForm->getLead()->refresh();
$leadAbacDto = new LeadAbacDto($itineraryForm->getLead(), Auth::id());
if (
    $isCreatedFlightRequest &&
    Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_ADD_AUTO_QUOTES) &&
    Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_ADD_AUTO_QUOTES, LeadAbacObject::ACTION_ACCESS)
) {
    $autoAddQuoteUrl = Url::toRoute('/quote/auto-adding-quotes');
    $leadId = $itineraryForm->getLeadId();

    $jsAfterItinerarySave = <<<JS

        let editRequestBtn = $('#js_flight_default');
        let btnContent = editRequestBtn.html();
        editRequestBtn.html('<div><i class="fa fa-spin fa-spinner"></i> Loading quotes. Please, wait.</div>');

        $.ajax({
            url: '$autoAddQuoteUrl',
            type: 'POST',
            data: {leadId: '$leadId'},
            dataType: 'json'
        })
        .done(function(dataResponse) {
            if (dataResponse.status === 1) {
                $.pjax.reload({container: '#quotes_list', async: false});
                $('.popover-class[data-toggle="popover"]').popover({ sanitize: false });
                createNotify('Success', dataResponse.message, 'success');
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
            editRequestBtn.html(btnContent);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.log({
                jqXHR : jqXHR,
                textStatus : textStatus,
                errorThrown : errorThrown
            });
            editRequestBtn.html(btnContent);
        })
        .always(function(jqXHR, textStatus, errorThrown) {
        });
JS;
    $this->registerJs($jsAfterItinerarySave);
}
