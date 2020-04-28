<?php

use common\models\Client;
use common\models\ClientPhone;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var ClientPhone[] $clientPhones
 * @var Client $client
 */
?>

    <?=Html::a('<i class="fas fa-plus-circle success"></i> Add Phone', '#', [
        'id' => 'client-new-phone-button',
        'data-modal_id' => 'sm',
        'title' => 'Add Phone',
        'data-content-url' => Url::to(['contacts/ajax-add-contact-phone-modal-content', 'client_id' => $client->id]),
        'class' => 'showModalButton'
    ])?>

<table class="table table-condensed table-bordered" style="margin: 0">
    <?php foreach ($clientPhones as $key => $phone): ?>
        <tr>
            <td title="<?= $phone::getPhoneType($phone->type) ?>" class="text-center" style="width:35px; background-color: #eef3f9">
                <?= $phone::getPhoneTypeIcon($phone->type) ?>
            </td>
            <td>
                <span style="line-height: 0;" class="<?= $phone::getPhoneTypeTextDecoration($phone->type) ?>"><?= \yii\helpers\Html::encode($phone->phone) ?></span>
            </td>

            <td class="text-right" style="width: 70px">
                <?php if($count = $phone->countUsersSamePhone()): ?>
                    <a class="showModalButton" data-modal_id="client-large" title="The Same users by phone" data-content-url="<?= Url::to([
                        'lead-view/ajax-get-users-same-phone-info',
                        'phone' => $phone->phone,
                        'clientId' => $phone->client_id
                    ]) ?>" ><i class="fa fa-user"></i> <sup><?= $count ?></sup></a>
                <?php endif; ?>

                <a class="showModalButton" title="Edit Phone" data-content-url="<?= Url::to([
                    'lead-view/ajax-edit-client-phone-modal-content',
                    'pid' => $phone->id]) ?>" data-modal_id="client-manage-info">
                    <i class="fa fa-edit text-warning"></i>
                </a>

            </td>
        </tr>
    <?php endforeach; ?>
</table>
