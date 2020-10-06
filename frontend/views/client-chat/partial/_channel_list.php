<?php

use sales\model\clientChat\dashboard\FilterForm;

/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ArrayDataProvider|null */
/** @var $loadChannelsUrl string */
/** @var $clientChatId int|null */
/** @var $totalUnreadMessages int */
/** @var FilterForm $filter */
/** @var int $page */
?>

<div class="_cc-wrapper">

    <div class="cc-filters-wrapper">
        <?= $this->render('filter/_filter', ['filter' => $filter, 'loadChannelsUrl' => $loadChannelsUrl]); ?>
    </div>

	<div id="cc-dialogs-wrapper" class="_cc-list-wrapper">
        <?php if ($dataProvider): ?>
		    <?= $this->render('_client-chat-item', ['clientChats' => $dataProvider->getModels(), 'clientChatId' => $clientChatId]); ?>
        <?php endif; ?>
	</div>

	<div class="_cc-channel-pagination" style="display: flex;justify-content: center; padding: 15px 0 10px;">
        <button class="btn btn-default btn-sm" id="btn-load-channels" data-page="<?= $page; ?>"><i class="fa fa-angle-double-down"></i> Load more ...</button>
	</div>
</div>
