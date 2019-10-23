<?php

use common\models\ClientEmail;
use common\models\Lead;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var $this View
 * @var $lead Lead
 * @var $clientEmails ClientEmail[]
 * @var $manageClientInfoAccess bool
 */
?>

<div class="sidebar__subsection">
    <table class="table table-condensed" style="margin-bottom: 0;">
        <?php foreach ($clientEmails as $key => $email): ?>
            <tr>
                <td title="<?= ClientEmail::EMAIL_TYPE[$email->type] ?? '' ?>"  class="text-center" style="width:35px; background-color: #eef3f9">
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

                <?php if($manageClientInfoAccess): ?>
                    <td class="text-right">
                        <a class="showModalButton text-warning" title="Edit Email" data-content-url="<?= Url::to([
                            'lead-view/ajax-edit-client-email-modal-content',
                            'gid' => $lead->gid,
                            'pid' => $email->id]) ?>" data-modal_id="client-manage-info">
                            <i class="fa fa-edit fa-border"></i>
                        </a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</div>