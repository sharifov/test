<?php

use common\models\ClientEmail;
use common\models\Lead;
use yii\helpers\Url;
use yii\web\View;
use src\helpers\email\MaskEmailHelper;
use modules\lead\src\abac\LeadAbacObject;

/**
 * @var $this View
 * @var $lead Lead
 * @var $clientEmails array
 * @var $leadAbacDto \stdClass
 * @var $unsubscribedEmails array
 * @var $disableMasking bool
 */
$unsubscribedEmails = array_column($lead->project->emailUnsubscribes, 'eu_email');
?>

<table class="table table-condensed table-bordered">
    <?php foreach ($clientEmails as $key => $email) : ?>
        <tr>
            <td class="text-center" style="width:33px; background-color: #eef3f9">
                <?= ClientEmail::EMAIL_TYPE_ICONS[$email['type']] ?? '' ?>
                <?= in_array($email['email'], $unsubscribedEmails) ? '<i title="Unsubscribed" class="fa fa-bell-slash"></i>' : '' ?>
            </td>
            <td class="<?= ClientEmail::EMAIL_TYPE_TEXT_DECORATION[$email['type']] ?? '' ?>"> <?= \yii\helpers\Html::encode(MaskEmailHelper::masking($email['email'], $disableMasking))?></td>

            <td class="text-right" style="width: 70px">
                <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_USER_SAME_EMAIL, Access to btn The same user by email on lead*/ ?>
                <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_USER_SAME_EMAIL)) : ?>
                    <?php if ($count = $email['countUsersSameEmail']) : ?>
                        <a
                            class="showModalButton"
                            data-modal_id="client-large"
                            title="The Same users by email (excluding the lead current client)"
                            data-content-url="<?= Url::to([
                            'lead-view/ajax-get-users-same-email-info',
                            'email' => $email['email'],
                            'clientId' => $email['client_id']
                        ]) ?>"><i class="fa fa-user"></i> <sup><?= $count ?></sup></a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_EDIT_EMAIL, Access to btn client edit email on lead*/ ?>
                <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_EDIT_EMAIL)) : ?>
                    <a class="showModalButton" title="Edit Email" data-content-url="<?= Url::to([
                        'lead-view/ajax-edit-client-email-modal-content',
                        'gid' => $lead->gid,
                        'pid' => $email['id']]) ?>" data-modal_id="client-manage-info">
                        <i class="fa fa-edit warning"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>