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

<div class="sidebar__subsection">
    <table class="table table-condensed" style="margin-bottom: 0;">
        <?php foreach ($clientPhones as $key => $phone): ?>
            <tr>
                <td title="<?= $phone::getPhoneType($phone->type) ?>">
                    <?= $phone::getPhoneTypeIcon($phone->type) ?>
                </td>
                <td><i class="fa fa-phone"></i> <span style="line-height: 0;" class="<?= $phone::getPhoneTypeTextDecoration($phone->type) ?>"><?= $phone->phone ?></span></td>

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

                <?php if($manageClientInfoAccess): ?>
                    <td class="text-right showModalButton" title="Edit Phone" data-content-url="<?= Url::to([
                        'lead-view/ajax-edit-client-phone-modal-content',
                        'gid' => $lead->gid, 'pid' => $phone->id]) ?>" data-modal_id="client-manage-info" style="cursor:pointer;"><i class="fa fa-edit"></i></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</div>