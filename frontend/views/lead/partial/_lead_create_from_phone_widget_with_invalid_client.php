<?php

use common\widgets\Alert;
use sales\model\lead\useCases\lead\create\fromPhoneWidgetWithInvalidClient\Form;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var Form $leadForm
 */

?>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <?php Pjax::begin([
            'id' => '_create_lead_from_phone_widget_without_client',
            'timeout' => 2000,
            'enablePushState' => false,
            'enableReplaceState' => false
        ]); ?>
        <?php $form = ActiveForm::begin([
            'id' => $leadForm->formName() . '-form',
            'enableClientValidation' => false,
            'action' => ['/lead/ajax-create-from-phone-widget-with-invalid-client?callSid=' . $leadForm->getCallSid()],
            'options' => [
                'data-pjax' => 1
            ],
        ]) ?>
        <div class="row">
            <div class="col-md-12">
                <?= Alert::widget() ?>
                <br>
                <?= $form->errorSummary($leadForm, ['showAllErrors' => false]) ?>
            </div>
            <div class="col-md-12">
                <div class="x_panel">
                      Client phone number is marked as invalid, please specify the right contact information below.
                </div>
            </div>
            <div class="col-md-12 col-sm-12">
                <div class="x_panel">

                    <div class="x_title">
                        <h2>Client Info</h2>
                        <ul class="nav navbar-right panel_toolbox">
<!--                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>-->
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="col-md-12">
                            <?= $this->render('_lead_create_client_from_phone_widget_with_invalid_client', [
                                'form' => $form,
                                'leadForm' => $leadForm,
                            ])
?>
                        </div>
                        <div class="col-md-12">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <?= Html::submitButton('<i class="fa fa-save"></i> Create Lead', [
                    'class' => 'btn btn-success'
                ]) ?>
            </div>
        </div>
        <?php ActiveForm::end() ?>
        <?php Pjax::end(); ?>
    </div>
</div>
