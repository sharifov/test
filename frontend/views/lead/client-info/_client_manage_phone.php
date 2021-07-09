<?php

use common\models\ClientPhone;
use common\models\Lead;
use yii\helpers\Url;
use yii\web\View;
use sales\helpers\phone\MaskPhoneHelper;
use modules\lead\src\abac\LeadAbacObject;

/**
 * @var $this View
 * @var $lead Lead
 * @var $clientPhones ClientPhone[]
 * @var $leadAbacDto \stdClass
 * @var $disableMasking bool
 */
?>

<table class="table table-condensed table-bordered" style="margin: 0">
    <?php foreach ($clientPhones as $key => $phone) : ?>
        <tr>
            <td title="<?= $phone::getPhoneType($phone->type) ?>" class="text-center" style="width:35px; background-color: #eef3f9">
                <?= $phone::getPhoneTypeIcon($phone->type) ?>
            </td>
            <td> <span style="line-height: 0;" class="<?= $phone::getPhoneTypeTextDecoration($phone->type) ?>"><?= \yii\helpers\Html::encode(MaskPhoneHelper::masking($phone->phone, $disableMasking)) ?></span></td>

            <td class="text-right" style="width: 70px">
                <?php /** @abac $leadAbacDto, LeadAbacObject::ACT_USER_SAME_PHONE_INFO, LeadAbacObject::ACTION_ACCESS, Access to btn The same user by phone on lead*/ ?>
                <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_USER_SAME_PHONE_INFO, LeadAbacObject::ACTION_ACCESS)) : ?>
                    <?php if ($count = $phone->countUsersSamePhone()) : ?>
                        <a class="showModalButton" data-modal_id="client-large" title="The Same users by phone" data-content-url="<?= Url::to([
                            'lead-view/ajax-get-users-same-phone-info',
                            'phone' => $phone->phone,
                            'clientId' => $phone->client_id
                        ]) ?>" ><i class="fa fa-user"></i> <sup><?= $count ?></sup></a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php /** @abac $leadAbacDto, LeadAbacObject::ACT_CLIENT_SUBSCRIBE, LeadAbacObject::ACTION_ACCESS, Access to button client edit phone on lead*/ ?>
                <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_CLIENT_EDIT_PHONE, LeadAbacObject::ACTION_ACCESS)) : ?>
                    <a class="showModalButton" title="Edit Phone" data-content-url="<?= Url::to([
                        'lead-view/ajax-edit-client-phone-modal-content',
                        'gid' => $lead->gid, 'pid' => $phone->id]) ?>" data-modal_id="client-manage-info">
                        <i class="fa fa-edit text-warning"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
