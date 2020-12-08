<?php

use sales\model\clientChat\dashboard\FilterForm;

/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ArrayDataProvider|null */
/** @var $loadChannelsUrl string */
/** @var $clientChatId int|null */
/** @var $totalUnreadMessages int */
/** @var FilterForm $filter */
/** @var int $page */
/** @var int $countFreeToTake */
/** @var int|null $resetUnreadMessagesChatId */
/** @var array $listParams */

$formatter = new \common\components\i18n\Formatter();
$formatter->timeZone = \sales\auth\Auth::user()->timezone;

$loadButtonText = '<i class="fa fa-angle-double-down"> </i> Load more (<span>' . $listParams['moreCount'] . '</span>)';
$loadButtonClass = '';
if ($listParams['isFullList']) {
    $loadButtonText = 'All conversations are loaded';
    $loadButtonClass = ' disabled';
}

?>

<div class="_cc-wrapper">

    <div class="cc-filters-wrapper">
        <?= $this->render('filter/_filter', [
            'filter' => $filter,
            'loadChannelsUrl' => $loadChannelsUrl,
            'dataProvider' => $dataProvider,
            'countFreeToTake' => $countFreeToTake,
        ]) ?>
    </div>

    <div id="cc-dialogs-wrapper" class="_cc-list-wrapper">
        <?php if ($dataProvider) : ?>
            <?= $this->render('_client-chat-item', [
                'clientChats' => $dataProvider->getModels(),
                'clientChatId' => $clientChatId,
                'formatter' => $formatter,
                'resetUnreadMessagesChatId' => $resetUnreadMessagesChatId
            ]); ?>
        <?php endif; ?>
    </div>

    <div class="_cc-channel-pagination" style="display: flex;justify-content: center; padding: 15px 0 10px;">
        <button class="btn btn-default btn-sm<?= $loadButtonClass ?>" id="btn-load-channels" data-page="<?= $listParams['page'] ?>"<?= $loadButtonClass ?>>
            <?= $loadButtonText ?>
        </button>
    </div>
</div>
