<?php

/**
 * @var $leadForm src\forms\lead\LeadCreateForm
 * @var $delayedChargeAccess bool
 */

use common\models\Department;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use common\widgets\Alert;
use src\helpers\lead\LeadHelper;

$this->title = 'Create Lead';

?>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <?php $form = ActiveForm::begin([
            'id' => $leadForm->formName() . '-form',
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'validationUrl' =>
                $leadForm->caseGid
                    ? ['/lead/validate-lead-create', 'depId' => $leadForm->depId, 'case_gid' => $leadForm->caseGid]
                    : ['/lead/validate-lead-create', 'depId' => $leadForm->depId],
            'action' =>
                $leadForm->caseGid
                    ? ['/lead/create-case', 'case_gid' => $leadForm->caseGid]
                    : ['/lead/create'],
        ]) ?>
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Flight Request</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="offset-xl-1 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-xs-12">


                            <div class="row">
                                <div class="col-md-12">
                                    <?= Alert::widget() ?>
                                    <br>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <?= $this->render('partial/_formLeadSegmentCreate', [
                                        'model' => $leadForm,
                                        'form' => $form]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-12">
                                    <?= $form->field($leadForm, 'cabin', [
                                    ])->dropDownList(LeadHelper::cabinList()) ?>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <?= $form->field($leadForm, 'adults', [
                                    ])->dropDownList(LeadHelper::adultsChildrenInfantsList()) ?>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <?= $form->field($leadForm, 'children', [
                                    ])->dropDownList(LeadHelper::adultsChildrenInfantsList()) ?>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <?= $form->field($leadForm, 'infants', [
                                    ])->dropDownList(LeadHelper::adultsChildrenInfantsList()) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">

                    <div class="x_title">
                        <h2>Client Info</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="offset-xl-1 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?= $this->render('partial/_client_create', [
                                'form' => $form,
                                'leadForm' => $leadForm,
                            ])
?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Lead info and preferences</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="offset-xl-1 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <?= $this->render('partial/_preferences_create', [
                                'form' => $form,
                                'leadForm' => $leadForm,
                                'delayedChargeAccess' => $delayedChargeAccess
                            ])
?>
                        </div>
                        <div class="offset-xl-1 col-xl-9 col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <?=Html::submitButton('<i class="fa fa-save"></i> Create Lead', ['class' => 'btn btn-success']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
