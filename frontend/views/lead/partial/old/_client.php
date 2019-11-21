<?php

use frontend\widgets\client\ClientCounterWidget;
use sales\access\ClientInfoAccess;
use sales\access\EmployeeGroupAccess;
use yii\bootstrap\ActiveForm;
use frontend\models\LeadForm;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this \yii\web\View
 * @var $formClient ActiveForm
 * @var $leadForm LeadForm
 */

$formId = sprintf('%s-form', $leadForm->getClient()->formName());
$lead = $leadForm->lead;

$manageClientInfoAccess = ClientInfoAccess::isUserCanManageLeadClientInfo($lead, Yii::$app->user->id);
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
            <i class="fa fa-user"></i> Client Info
        </h3>
        <div class="sidebar__subsection text-center">
			<?php if ($leadForm->mode !== $leadForm::VIEW_MODE || $manageClientInfoAccess): ?>

				 <div class="">
                     <?= Html::button('<i class="fas fa-plus"></i> <i class="fa fa-phone"></i>', [
						'id' => 'client-new-phone-button',
						'data-modal_id' => 'client-manage-info',
						'title' => 'Add Phone',
						'data-content-url' => Url::to(['lead-view/ajax-add-client-phone-modal-content', 'gid' => $lead->gid]),
						'class' => 'btn btn-primary showModalButton'
					]) ?>

                     <?= Html::button('<i class="fas fa-plus"></i> <i class="fa fa-envelope"></i>', [
						'id' => 'client-new-email-button',
						'data-modal_id' => 'client-manage-info',
						'title' => 'Add Email',
						'data-content-url' => Url::to(['lead-view/ajax-add-client-email-modal-content', 'gid' => $lead->gid]),
						'class' => 'btn btn-primary showModalButton'
					]) ?>

                     <?= Html::button('<i class="fas fa-pencil"></i> <i class="fa fa-user"></i>', [
						'id' => 'client-edit-user-name-button',
						'data-modal_id' => 'client-manage-info',
						'title' => 'Update user name',
						'data-content-url' => Url::to(['lead-view/ajax-edit-client-name-modal-content', 'gid' => $lead->gid]),
						'class' => 'btn btn-primary showModalButton'
					]) ?>
                 </div>

			<?php endif; ?>
        </div>
        <div class="sidebar__subsection">
            <?= $this->render('_client_manage_name', [
                    'client' => $lead->client
            ]) ?>
        </div>

        <div id="client-manage-email">
            <?php if ($emails = $lead->client->clientEmails): ?>
                <?php
                    if ($leadForm->viewPermission) {
                        echo $this->render('_client_manage_email', [
                            'clientEmails' => $emails,
                            'lead' => $lead,
                            'manageClientInfoAccess' => $manageClientInfoAccess
                        ]);
                    }
                ?>
            <?php endif; ?>
        </div>

        <div id="client-manage-phone">
            <?php if ($phones = $lead->client->clientPhones): ?>
                <?php
                    if ($leadForm->viewPermission) {
                        echo $this->render('_client_manage_phone', [
                            'clientPhones' => $phones,
                            'lead' => $lead,
							'manageClientInfoAccess' => $manageClientInfoAccess
						]);
                    }
                ?>
            <?php endif; ?>
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
    'size' => Modal::SIZE_SMALL,
]) ?>

<?= Modal::widget([
	'id' => 'modal-client-large',
	'size' => Modal::SIZE_LARGE,
])
?>

