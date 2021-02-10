<?php

use common\models\Client;
use yii\helpers\Html;
use sales\helpers\email\MaskEmailHelper;
use sales\helpers\phone\MaskPhoneHelper;

/**
 * @var $this yii\web\View
 * @var $client Client
 */

?>

<table class="table table-bordered table-condensed">
    <tr>
        <td style="width: 100px; background-color: #eef3f9"><?= $client->getAttributeLabel('firstName') ?></td>
        <td><?= \yii\helpers\Html::encode($client->first_name) ?></td>
    </tr>
    <tr>
        <td style="background-color: #eef3f9"><?= $client->getAttributeLabel('last_name') ?></td>
        <td><?= \yii\helpers\Html::encode($client->last_name) ?></td>
    </tr>
    <tr>
        <td style="background-color: #eef3f9"><?= $client->getAttributeLabel('middle_name') ?></td>
        <td><?= \yii\helpers\Html::encode($client->middle_name) ?></td>
    </tr>

    <?php if ($phones = $client->clientPhones) : ?>
        <tr>
            <td style="background-color: #eef3f9">Phones</td>
            <td>
                <?php foreach ($phones as $phone) : ?>
                    <span class="_rc-client-phone">
                        <i class="fa fa-phone"> </i>
                        <code><?= Html::encode(MaskPhoneHelper::masking($phone->phone)) ?></code>
                    </span>
                <?php endforeach; ?>
            </td>
        </tr>
    <?php endif; ?>

    <?php if ($emails = $client->clientEmails) : ?>
        <tr>
            <td style="background-color: #eef3f9">Emails</td>
            <td>
                <?php foreach ($emails as $email) : ?>
                    <span class="_rc-client-email">
                        <i class="fa fa-envelope"> </i>
                        <code><?= Html::encode(MaskEmailHelper::masking($email->email)) ?></code>
                    </span>
                <?php endforeach; ?>

            </td>
        </tr>
    <?php endif; ?>

</table>
