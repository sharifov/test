<?php

use common\models\ClientPhone;
use common\models\Lead;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $lead Lead
 * @var $clientPhones ClientPhone[]
 */
?>

<div id="client-manage-phone">
    <table class="table table-condensed">
        <? foreach ($clientPhones as $key => $phone): ?>
            <tr>
                <td title="<?= ClientPhone::PHONE_TYPE[$phone->type] ?? '' ?>">
                    <?= ClientPhone::PHONE_TYPE_ICONS[$phone->type] ?? '' ?>
                </td>
                <td class="<?= ClientPhone::PHONE_TYPE_TEXT_DECORATION[$phone->type] ?? '' ?>"><i class="fa fa-phone"></i> <?= $phone->phone ?></td>

<!--				--><?// $count = $phone->countUsersSamePhone(); ?>
<!--                --><?// if($count): ?>
<!--                    <td class="text-right showModalButton" data-modal_id="client-large" title="The Same users by phone" data-content-url="--><?//= Url::to([
//                        'lead-view/ajax-get-users-same-phone-info',
//                        'phone' => $phone->phone,
//                        'clientId' => $phone->client_id
//                    ]) ?><!--" style="cursor:pointer;"><i class="fa fa-user"></i> --><?//= $count; ?><!--</td>-->
<!--				--><?// else: ?>
<!--                    <td></td>-->
<!--                --><?// endif; ?>

                <td class="text-right showModalButton" title="Edit Phone" data-content-url="<?= Url::to([
                    'lead-view/ajax-edit-client-phone-modal-content',
                    'gid' => $lead->gid, 'pid' => $phone->id]) ?>" data-modal_id="client-manage-info" style="cursor:pointer;"><i class="fa fa-edit"></i></td>
            </tr>
        <? endforeach; ?>
    </table>
</div>