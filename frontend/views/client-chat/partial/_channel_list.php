<?php

use src\auth\Auth;
use src\model\clientChat\dashboard\FilterForm;

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
$formatter->timeZone = \src\auth\Auth::user()->timezone;

$loadButtonText = '<a id="load-channels-btn"><i class="fa fa-angle-double-down"> </i> Click or scroll to load more (<span>' . $listParams['moreCount'] . '</span>)</a>';
if ($listParams['isFullList']) {
    $loadButtonText = 'All conversations are loaded';
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

    <div id="cc-dialogs-wrapper" class="_cc-list-wrapper" data-page="<?= $listParams['page'] ?>">
        <?php if ($dataProvider) : ?>
            <?= $this->render('_client-chat-item', [
                'clientChats' => $dataProvider->allModels,
                'clientChatId' => $clientChatId,
                'formatter' => $formatter,
                'resetUnreadMessagesChatId' => $resetUnreadMessagesChatId,
                'user' => Auth::user(),
            ]); ?>
        <?php endif; ?>
    </div>

    <div class="_cc-channel-pagination" style="display: flex;justify-content: center; padding: 15px 0 10px;">
        <?= $loadButtonText ?>
        <p id="load-channels-txt"></p>
    </div>
</div>
