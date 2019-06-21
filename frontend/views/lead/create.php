<?php

/**
 * @var $leadForm sales\forms\lead\LeadCreateForm
 */

use yii\bootstrap\Html;
use \yii\widgets\ActiveForm;
use \common\widgets\Alert;
use \sales\helpers\lead\LeadHelper;

$this->title = 'Create Lead';

?>

    <div class="page-header">
        <div class="container-fluid">
            <div class="page-header__wrapper">
                <h2 class="page-header__title">
                    <?= Html::encode($this->title) ?>
                    <span class="label status-label label-info">New</span>
                </h2>
            </div>
        </div>
    </div>

<?php $form = ActiveForm::begin([
    'id' => $leadForm->formName() . '-form',
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validationUrl' => ['/lead/validate-lead-create']

]) ?>

    <div class="main-sidebars">
        <div class="panel panel-main">


            <div class="col-md-12">
                <?= Alert::widget() ?>
                <br>
            </div>

            <div class="col-md-1">
            </div>

            <div class="col-md-10">
                <div class="request">
                    <div class="request-overview">
                        <div style="letter-spacing: 0.8px; padding-bottom: 13px;" class="row-flex row-flex-justify">
                            <span style="font-weight: 600; font-size: 18px;">Flight Request</span>
                        </div>
                        <div class="separator"></div>
                        <div class="request-form collapse in" id="request" aria-expanded="true">
                            <div class="panel panel-primary sl-request-wrap">



                                <div class="sl-itinerary-form">
                                    <div class="sl-request-summary__block">
                                        <?= $this->render('partial/_formLeadSegment', [
                                            'model' => $leadForm,
                                            'form' => $form]) ?>
                                    </div>

                                    <div class="row sl-itinerary-form__pax">

                                        <div class="col-sm-3">
                                            <?= $form->field($leadForm, 'cabin', [
                                            ])->dropDownList(LeadHelper::cabinList(), [
                                                'prompt' => '---'
                                            ]) ?>
                                        </div>

                                        <div class="col-sm-2">
                                        </div>

                                        <div class="col-sm-1">
                                            <?= $form->field($leadForm, 'adults', [
                                            ])->dropDownList(LeadHelper::AdultsChildrenInfantsList()) ?>
                                        </div>
                                        <div class="col-sm-1">
                                            <?= $form->field($leadForm, 'children', [
                                            ])->dropDownList(LeadHelper::AdultsChildrenInfantsList()) ?>
                                        </div>
                                        <div class="col-sm-1">
                                            <?= $form->field($leadForm, 'infants', [
                                            ])->dropDownList(LeadHelper::AdultsChildrenInfantsList()) ?>
                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="text-right">
                                <?=Html::submitButton('<i class="fa fa-check"></i> Create Lead', ['class' => 'btn btn-success']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-1"></div>
        </div>

        <aside class="sidebar right-sidebar sl-right-sidebar">
            <?= $this->render('partial/_client_create', [
                'form' => $form,
                'leadForm' => $leadForm,
            ]);
            ?>

            <?= $this->render('partial/_preferences_create', [
                'form' => $form,
                'leadForm' => $leadForm
            ]);
            ?>
        </aside>

    </div>

<?php ActiveForm::end() ?>