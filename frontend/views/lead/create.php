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

            <div class="col-md-2">
            </div>
            <div class="col-md-8">
                <div class="panel panel-primary sl-request-wrap">

                    <div class="panel-heading ">

                        <div class="sl-request-summary">
                            <div class="sl-request-summary__block">
                                <div class="sl-request-summary__locations">
                                    <strong>Flight Details</strong>
                                </div>
                            </div>
                        </div>

                    </div>

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

                            <div class="col-sm-2">
                                <?= $form->field($leadForm, 'adults')->textInput([
                                    'class' => 'form-control lead-form-input-element',
                                    'type' => 'number',
                                    'min' => 0,
                                    'max' => 9,
                                ]) ?>
                            </div>
                            <div class="col-sm-2">
                                <?= $form->field($leadForm, 'children')->textInput([
                                    'class' => 'form-control lead-form-input-element',
                                    'type' => 'number',
                                    'min' => 0,
                                    'max' => 9,
                                ]) ?>

                            </div>
                            <div class="col-sm-2">
                                <?= $form->field($leadForm, 'infants')->textInput([
                                    'class' => 'form-control lead-form-input-element',
                                    'type' => 'number',
                                    'min' => 0,
                                    'max' => 9,
                                ]) ?>
                            </div>

                        </div>

                    </div>

                </div>
                <div class="text-right">
                    <?=Html::submitButton('<i class="fa fa-plus"></i> Create Lead', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
            <div class="col-md-2"></div>
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