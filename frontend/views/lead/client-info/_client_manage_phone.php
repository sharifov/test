<?php

use common\models\ClientPhone;
use common\models\Lead;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $lead Lead
 * @var $clientPhones ClientPhone[]
 * @var $manageClientInfoAccess bool
 */
?>

<table class="table table-condensed table-bordered">
    <?php foreach ($clientPhones as $key => $phone): ?>
        <tr>
            <td title="<?= $phone::getPhoneType($phone->type) ?>" class="text-center" style="width:35px; background-color: #eef3f9">
                <?= $phone::getPhoneTypeIcon($phone->type) ?>
            </td>
            <td> <span style="line-height: 0;" class="<?= $phone::getPhoneTypeTextDecoration($phone->type) ?>"><?= \yii\helpers\Html::encode($phone->phone) ?></span></td>

            <td class="text-right" style="width: 70px">
                <?php if($count = $phone->countUsersSamePhone()): ?>
                    <a class="showModalButton" data-modal_id="client-large" title="The Same users by phone" data-content-url="<?= Url::to([
                        'lead-view/ajax-get-users-same-phone-info',
                        'phone' => $phone->phone,
                        'clientId' => $phone->client_id
                    ]) ?>" ><i class="fa fa-user"></i> <sup><?= $count ?></sup></a>
                <?php endif; ?>
                <?php if($manageClientInfoAccess): ?>
                    <a class="showModalButton text-warning" title="Edit Phone" data-content-url="<?= Url::to([
                        'lead-view/ajax-edit-client-phone-modal-content',
                        'gid' => $lead->gid, 'pid' => $phone->id]) ?>" data-modal_id="client-manage-info">
                        <i class="fa fa-edit fa-border"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
