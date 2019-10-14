<?php

use frontend\widgets\client\ClientCounterWidget;
use frontend\widgets\client\ClientManagePhoneEmailWidget;
use sales\forms\lead\PhoneCreateForm;
use yii\bootstrap\ActiveForm;
use frontend\models\LeadForm;
use common\models\ClientEmail;
use common\models\ClientPhone;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/**
 * @var $this \yii\web\View
 * @var $formClient ActiveForm
 * @var $leadForm LeadForm
 * @var $nr integer
 * @var $newPhone ClientPhone
 */

$formId = sprintf('%s-form', $leadForm->getClient()->formName());
?>

    <script>
        // add email comment button
        function addEmailComment(element, key) {
            $('#preloader').removeClass('hidden');
            var form = element.parent();
            $.post(form.attr('action'), form.serialize(), function (data) {
                //location.reload();
                $('#addEmailComment-' + key).trigger('click');
                $('#preloader').addClass('hidden');
            });
        }
        // add email comment button
        function addPhoneComment(element, key) {
            $('#preloader').removeClass('hidden');
            var form = element.parent();
            $.post(form.attr('action'), form.serialize(), function (data) {
                //location.reload();
                $('#addPhoneComment-' + key).trigger('click');
                $('#preloader').addClass('hidden');
            });
        }
    </script>

    <div class="sidebar__section">
        <h3 class="sidebar__subtitle">
            <i class="fa fa-user"></i>
        </h3>
        <div class="sidebar__subsection">
			<? if ($leadForm->mode != $leadForm::VIEW_MODE): ?>

				 <div class="">
                     <?= Html::button('<i class="fa fa-plus"></i> <i class="fa fa-phone"></i>', [
						'id' => 'client-new-phone-button',
						'data-modal_id' => 'client-manage-info',
						'title' => 'Add Phone',
						'data-content-url' => Url::to(['lead-view/ajax-add-client-phone-modal-content', 'gid' => $leadForm->getLead()->gid]),
						'class' => 'btn btn-primary showModalButton'
					]) ?>


                     <?= Html::button('<i class="fa fa-plus"></i> <i class="fa fa-envelope"></i>', [
						'id' => 'client-new-email-button',
						'data-modal_id' => 'client-manage-info',
						'title' => 'Add Email',
						'data-content-url' => Url::to(['lead-view/ajax-add-client-email-modal-content', 'gid' => $leadForm->getLead()->gid]),
						'class' => 'btn btn-primary showModalButton'
					]) ?>

                     <?= Html::button('<i class="fa fa-pencil"></i> <i class="fa fa-user"></i>', [
						'id' => 'client-edit-user-name-button',
						'data-modal_id' => 'client-manage-info',
						'title' => 'Update user name',
						'data-content-url' => Url::to(['lead-view/ajax-edit-client-name-modal-content', 'gid' => $leadForm->getLead()->gid]),
						'class' => 'btn btn-primary showModalButton'
					]) ?>

                 </div>

			<? endif; ?>
        </div>
        <div class="sidebar__subsection">
            <?= $this->render('_client_manage_name', [
                    'client' => $leadForm->getClient()
            ]) ?>
        </div>
        <div class="sidebar__subsection">
            <div id="client-emails">
                <?
				if ($leadForm->viewPermission) {
					echo $this->render('_client_manage_email', [
						'clientEmails' => $leadForm->getClientEmail(),
						'lead' => $leadForm->getLead()
					]);
				}
                ?>
                    <!-- new email fields -->
<!--                    <div id="client-new-email-block" style="display: none;">-->
<!--                        --><?php //$newEmail = new ClientEmail(); ?>
<!--                        --><?//= $this->render('_formClientEmail', [
//                            'key' => '__id__',
//                            'form' => $formClient,
//                            'email' => $newEmail,
//                            'leadForm' => $leadForm,
//                            'nr' => $nr
//                        ]) ?>
<!--                    </div>-->
<!--                --><?php //endif; ?>
            </div>
        </div>
        <div class="sidebar__subsection">
            <div id="client-phones">
                <?php
                    if ($leadForm->viewPermission) {
                        echo $this->render('_client_manage_phone', [
                            'clientPhones' => $leadForm->getClientPhone(),
                            'lead' => $leadForm->getLead()
                        ]);
                    }
                ?>
            </div>
        </div>
        <?php if(!$leadForm->getLead()->isNewRecord) :?>
        <div class="sidebar__subsection">
            <div class="btn-group" id="user-actions-block">

                <?php /*  Html::button('<i class="fa fa-history"></i> Actions', [
                    'id' => 'view-client-actions-btn',
                    'class' => 'btn btn-default'
                ]) */ ?>

                <?= Html::button('<i class="fa fa-user"></i> Client Info', [
                    'class' => 'btn btn-default',
                    'id' => 'btn-client-details',
                    'data-client-id' => $leadForm->getClient()->id
                ]) ?>

                <?php if (!empty($leadForm->getLead()->request_ip)): ?>
                    <?= $this->render('_client_ip_info', ['lead' => $leadForm->getLead()]) ?>
                <?php endif; ?>

            </div>
        </div>
        <?php endif;?>

		<?php
            $formClient = ActiveForm::begin([
                'enableClientValidation' => false,
                'id' => $formId
            ]);
		?>

            <?php if (empty($leadForm->getLead()->request_ip) && $leadForm->getLead()->isNewRecord) : ?>
                <div class="sidebar__subsection">
                    <?= $formClient->field($leadForm->getLead(), 'request_ip')
                        ->textInput([
                            'class' => 'form-control lead-form-input-element'
                        ])->label('Client IP') ?>
                </div>
            <?php endif; ?>

            <?= ClientCounterWidget::widget([
                'clientId' => $leadForm->getClient()->id,
                'userId' => Yii::$app->user->id
            ]) ?>

		<?php ActiveForm::end(); ?>
    </div>

<?= Modal::widget([
    'id' => 'modal-client-manage-info',
    'headerOptions' => [
        'id' => 'modal-header'
    ],
    'bodyOptions' => [
        'id' => 'modal-content',
        'class' => 'modal-body'
    ],
    'size' => 'modal-sm',
]) ?>

    <style type="text/css">
        @media screen and (min-width: 768px) {
            .modal-dialog {
                width: 700px; /* New width for default modal */
            }
            .modal-sm {
                width: 350px; /* New width for small modal */
            }
        }
        @media screen and (min-width: 992px) {
            .modal-lg {
                width: 70%; /* New width for large modal */
            }
        }
    </style>


<?php
$jsCode = <<<JS

    $(document).on('click', '.showModalButton', function(){
        var id = $(this).data('modal_id');
        var url = $(this).data('content-url');

        $('#modal-header').html('<h4>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
        
        $('#modal-' + id).modal('show').find('#modal-content').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');

        $.post(url, function(data) {
            $('#modal-' + id).find('#modal-content').html(data);
        });
       return false;
    });
    
JS;

$this->registerJs($jsCode);
