<?php

use common\widgets\Alert;
use sales\model\clientChat\entity\ClientChat;
use sales\model\lead\useCases\lead\create\LeadCreateByChatForm;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var ClientChat $chat
 * @var LeadCreateByChatForm $form
 */

?>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <?php Pjax::begin([
            'id' => '_create_lead_by_chat',
            'timeout' => 2000,
            'enablePushState' => false,
            'enableReplaceState' => false
        ]); ?>
        <?php $activeForm = ActiveForm::begin([
            'id' => $form->formName() . '-form',
            'enableClientValidation' => true,
            'options' => [
                'data-pjax' => 1
            ],
        ]) ?>
        <?= $activeForm->field($form, 'source')->hiddenInput()->label(false)?>
        <?= $activeForm->field($form, 'projectId')->hiddenInput()->label(false)?>
        <div class="row">
            <div class="col-md-12">
                <?= Alert::widget() ?>
                <br>
                <?= $activeForm->errorSummary($form, ['showAllErrors' => false]) ?>
            </div>
            <?php if ($client = $chat->cchClient) : ?>
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
                            <?= $this->render('_lead_create_by_chat_client_info', [
                                'client' => $client,
                            ])
                            ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Client Not Found</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <!--                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>-->
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-12 text-center">
                <?= Html::submitButton('<i class="fa fa-save"> </i> Create Lead', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end() ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
