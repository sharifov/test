<?php

use sales\helpers\clientChat\ClientChatHelper;
use sales\model\client\helpers\ClientFormatter;
use sales\model\clientChat\entity\ClientChat;
use yii\helpers\Html;

/** @var ClientChat $clientChat */

$client = $clientChat->cchClient;

?>
<div class="col-md-12">
    <?php if ($client && $client->isExcluded()) : ?>
        <div class="alert alert-danger" role="alert">
            <b><i class="fa fa-warning"></i> Warning!</b> Excluded client.
        </div>
    <?php endif; ?>
    <div style="display: flex; margin-bottom: 15px;">
        <span class="_rc-client-icon _cc-item-icon-round">
            <span class="_cc_client_name"><?= ClientChatHelper::getFirstLetterFromName(ClientChatHelper::getClientName($clientChat)) ?></span>
            <span class="_cc-status-wrapper"><span class="_cc-status" data-is-online="<?= (int)$clientChat->cch_client_online ?>"> </span></span>
        </span>
        <div class="_rc-client-info">
            <span class="_rc-client-name"><span><?= ClientFormatter::formatFullName($client) ?></span></span>

            <?php if ($emails = $client->clientEmails) : ?>
                <div class="box_client_info_data">
                    <span class="_rc-client-email"> <i class="fa fa-envelope"> </i>
                    <?php foreach ($emails as $key => $email) : ?>
                        <?php $class = (bool) $key ? 'client_info_email' : '' ?>
                        <code class="<?php echo $class ?>"><?= Html::encode($email->email) ?></code><br />
                    <?php endforeach; ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if ($phones = $client->clientPhones) : ?>
                <div class="box_client_info_data box_client_phone">
                    <span class="_rc-client-phone"><i class="fa fa-phone"> </i>
                    <?php foreach ($phones as $key => $phone) : ?>
                        <?php $class = (bool) $key ? 'client_info_phone' : '' ?>
                        <code class="<?php echo $class ?>"><?= Html::encode($phone->phone) ?></code><br />
                    <?php endforeach; ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if (!empty($client->cl_locale)) : ?>
                <div class="box_client_info_data box_locale">
                    <span title="locale" class="_rc-client-locale"><i class="fa fa-language"> </i>
                        <code><?= Html::encode($client->cl_locale) ?></code>
                    </span>
                </div>
            <?php endif; ?>
            <?php if (!empty($client->cl_marketing_country)) : ?>
                <div class="box_client_info_data box_country">
                    <span title="Market country" class="_rc-client-country"><i class="fa fa-map-marker"> </i>
                        <code style="margin-left: 4px;">
                            <?= Html::encode($client->cl_marketing_country) ?>
                        </code>
                    </span>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
$css = <<<CSS
    .box_client_info_data .client_info_email {
       margin-left: 17px;
    }
    .box_client_info_data .client_info_phone {
       margin-left: 14px;
    }
    .box_client_phone {
        margin-top: 4px;
    }
CSS;
$this->registerCss($css);
?>
