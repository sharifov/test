<?php

use sales\model\clientChat\entity\ClientChat;

/** @var $clientChats ClientChat[] */
/** @var $clientChatRid string|null */
?>

<?php foreach($clientChats as $clientChat): ?>
    <div class="_cc-list-item <?= $clientChatRid && $clientChatRid === $clientChat->cch_rid ? 'active' : '' ?>" data-goto-param="/live/<?= $clientChat->cch_rid ?>?layout=embedded" data-rid="<?= $clientChat->cch_rid ?>" data-cch-id="<?= $clientChat->cch_id ?>">
        <div class="_cc-item-icon-wrapper">
                            <span class="_cc-item-icon-round">
                                <i class="fa fa-comment"></i>
                                <span class="_cc-status-wrapper">
                                    <span class="_cc-status <?= $clientChat->getStatusClass() ?>"></span>
                                </span>
                            </span>
            <span class="_cc-title">
                                <p><?= $clientChat->cch_title ?: 'Client Chat' ?></p>
                                <p><?= Yii::$app->formatter->format($clientChat->cch_created_dt,'byUserDateTime') ?></p>
                            </span>
        </div>
        <div>
            <?php if ($clientChat->cchDep): ?>
                <span class="label label-info"><?= $clientChat->cchDep->dep_name ?></span>
            <?php endif; ?>

            <?php if ($clientChat->cchProject): ?>
                <span class="label label-success"><?= $clientChat->cchProject->name ?></span>
            <?php endif; ?>

            <span class="label label-default label-"><?= $clientChat->cchChannel->ccc_name ?></span>
        </div>
    </div>
<?php endforeach; ?>
