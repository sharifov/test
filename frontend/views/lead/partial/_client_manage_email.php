<?php

use common\models\ClientEmail;
use common\models\Lead;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $lead Lead
 * @var $clientEmails ClientEmail[]
 */
?>

<div id="client-manage-email">
    <table class="table table-condensed">
    <? foreach ($clientEmails as $key => $email): ?>
        <tr>
            <td title="<?= ClientEmail::EMAIL_TYPE[$email->type] ?? '' ?>">
                <?= ClientEmail::EMAIL_TYPE_ICONS[$email->type] ?? '' ?>
            </td>
            <td class="<?= ClientEmail::EMAIL_TYPE_TEXT_DECORATION[$email->type] ?? '' ?>"><i class="fa fa-envelope"></i> <?= $email->email ?? 'email is not set'?></td>

<!--            --><?// $count = $email->countUsersSameEmail(); ?>
<!--            --><?// if($count): ?>
<!--                <td class="text-right showModalButton" data-modal_id="client-large" title="The Same users by email" data-content-url="--><?//= Url::to([
//                    'lead-view/ajax-get-users-same-email-info',
//                    'email' => $email->email,
//                    'clientId' => $email->client_id
//                ]) ?><!--" style="cursor:pointer;"><i class="fa fa-user"></i> --><?//= $count ?><!--</td>-->
<!--            --><?// else: ?>
<!--            <td></td>-->
<!--            --><?// endif; ?>

            <td class="text-right showModalButton" title="Edit Email" data-content-url="<?= Url::to([
                'lead-view/ajax-edit-client-email-modal-content',
                'gid' => $lead->gid,
                'pid' => $email->id]) ?>" data-modal_id="client-manage-info"><i class="fa fa-edit" style="cursor:pointer;"></i></td>
        </tr>
    <? endforeach; ?>
    </table>
</div>