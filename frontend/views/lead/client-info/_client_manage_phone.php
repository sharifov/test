<?php

use common\models\ClientPhone;
use common\models\Lead;
use yii\helpers\Url;
use yii\web\View;
use src\helpers\phone\MaskPhoneHelper;
use modules\lead\src\abac\LeadAbacObject;

/**
 * @var $this View
 * @var $lead Lead
 * @var $clientPhones []
 * @var $leadAbacDto \stdClass
 * @var $disableMasking bool
 */
?>

<table class="table table-condensed table-bordered" style="margin: 0">
    <?php foreach ($clientPhones as $key => $phone) : ?>
        <tr>
            <td title="<?= ClientPhone::getPhoneType($phone['type']) ?>" class="text-center" style="width:35px; background-color: #eef3f9">
                <?= ClientPhone::getPhoneTypeIcon($phone['type']) ?>
            </td>
            <td> <span style="line-height: 0;" class="<?= ClientPhone::getPhoneTypeTextDecoration($phone['type']) ?>"><?= \yii\helpers\Html::encode(MaskPhoneHelper::masking($phone['phone'], $disableMasking)) ?></span></td>

            <td class="text-right" style="width: 70px">
                <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_USER_SAME_PHONE, Access to btn The same user by phone on lead*/ ?>
                <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_USER_SAME_PHONE)) : ?>
                    <?php if ($count = $phone['countUsersSamePhone']) : ?>
                        <a class="showModalButton" data-modal_id="client-large" title="The Same users by phone" data-content-url="<?= Url::to([
                            'lead-view/ajax-get-users-same-phone-info',
                            'phone' => $phone['phone'],
                            'clientId' => $phone['client_id']
                        ]) ?>" ><i class="fa fa-user"></i> <sup><?= $count ?></sup></a>
                    <?php endif; ?>
                <?php endif; ?>

                <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_EDIT_PHONE, Access to button client edit phone on lead*/ ?>
                <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS_EDIT_PHONE)) : ?>
                    <a class="showModalButton" title="Edit Phone" data-content-url="<?= Url::to([
                        'lead-view/ajax-edit-client-phone-modal-content',
                        'gid' => $lead->gid, 'pid' => $phone['id']]) ?>" data-modal_id="client-manage-info">
                        <i class="fa fa-edit text-warning"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
