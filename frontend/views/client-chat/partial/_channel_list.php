<?php

use kartik\select2\Select2;
use sales\model\clientChat\dashboard\FilterForm;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\dashboard\ReadFilter;
use sales\model\clientChat\dashboard\GroupFilter;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ArrayDataProvider|null */
/** @var $loadChannelsUrl string */
/** @var $clientChatId int|null */
/** @var $totalUnreadMessages int */
/** @var FilterForm $filter */

?>

<div class="_cc-wrapper">
    <?php /*
    <div class="_cc_tabs_wrapper">
        <?php foreach (ClientChat::getTabList() as $key => $item): ?>
            <div class="_cc_tab <?= $key === $filter->status ? 'active' : ''; ?>" data-tab-id="<?= $key; ?>"> <?= $item; ?>
                <?php if (ClientChat::isTabActive($key)): ?>
                    <sup class="_cc_unread_messages label label-danger" ><?= $totalUnreadMessages ?: ''; ?></sup>
                <?php endif; ?>
                <span class="_cc_tab_active"></span>
            </div>
        <?php endforeach; ?>
    </div>
 */ ?>
    <div class="_cc_filter_wrapper">
        <div class="_cc_filter">
			<?= Html::label('Department:', null, ['class' => 'control-label']); ?>
            <?= Select2::widget([
                'data' => $filter->getDepartments(),
                'name' => 'dep',
                'size' => Select2::SIZE_SMALL,
                'options' => [
                    'placeholder' => 'Choose the channel...',
                    'id' => 'dep-list',
                ],
                'value' => $filter->dep,
                'pluginOptions' => [
                    'width' => '100%',
                ],
                'pluginEvents' => [
                    'change' => new \yii\web\JsExpression('function (e) {
                    let selectedStatus = $("#status-list").val();
                    let selectedChannel = $("#channel-list").val();
                    let selectedDep = $(this).val();
                    let selectedProject = $("#project-list").val();
                    window._cc_apply_filter(selectedChannel, "' . $loadChannelsUrl . '", selectedStatus, selectedDep, selectedProject, ' . $filter->group . ', ' . $filter->read . ');
                }'),
                ],
            ]); ?>
        </div>
        <div class="_cc_filter">
			<?= Html::label('Project:', null, ['class' => 'control-label']); ?>
			<?= Select2::widget([
			    'data' => $filter->getProjects(),
			    'name' => 'project',
			    'size' => Select2::SIZE_SMALL,
			    'options' => [
			        'placeholder' => 'Choose the channel...',
			        'id' => 'project-list',
			    ],
			    'value' => $filter->project,
			    'pluginOptions' => [
			        'width' => '100%',
			    ],
			    'pluginEvents' => [
			        'change' => new \yii\web\JsExpression('function (e) {
			        let selectedStatus = $("#status-list").val();
                    let selectedChannel = $("#channel-list").val();
                    let selectedDep = $("#dep-list").val();
                    let selectedProject = $(this).val();
                    window._cc_apply_filter(selectedChannel, "' . $loadChannelsUrl . '", selectedStatus, selectedDep, selectedProject, ' . $filter->group . ', ' . $filter->read . ');
                }'),
			    ],
			]); ?>
        </div>
    </div>
    <div class="_cc_filter_wrapper">
        <div class="_cc_filter">
            <?= Html::label('Channel list:', null, ['class' => 'control-label']); ?>
            <?= Select2::widget([
                'data' => $filter->getChannels(),
                'name' => 'channel-list',
                'size' => Select2::SIZE_SMALL,
                'pluginEvents' => [
                    'change' => new \yii\web\JsExpression('function (e) {
                    let selectedStatus = $("#status-list").val();
                    let selectedChannel = $(this).val();
                    let selectedDep = $("#dep-list").val();
                    let selectedProject = $("#project-list").val();
                    window._cc_apply_filter(selectedChannel, "' . $loadChannelsUrl . '", selectedStatus, selectedDep, selectedProject, ' . $filter->group . ', ' . $filter->read . ');
                }'),
                ],
                'pluginOptions' => [
                    'width' => '100%',
                ],
                'options' => [
                    'placeholder' => 'Choose the channel...',
                    'id' => 'channel-list',
                ],
                'value' => $filter->channelId,
            ]); ?>
        </div>
        <div class="_cc_filter">
            <?= Html::label('Status:', null, ['class' => 'control-label']); ?>
            <?= Select2::widget([
                'data' => $filter->getStatuses(),
                'name' => 'status-list',
                'size' => Select2::SIZE_SMALL,
                'pluginEvents' => [
                    'change' => new \yii\web\JsExpression('function (e) {
                    let selectedStatus = $(this).val();
                    let selectedChannel = $("#channel-list").val();
                    let selectedDep = $("#dep-list").val();
                    let selectedProject = $("#project-list").val();
                    window._cc_apply_filter(selectedChannel, "' . $loadChannelsUrl . '", selectedStatus, selectedDep, selectedProject, ' . $filter->group . ', ' . $filter->read . ');
                }'),
                ],
                'pluginOptions' => [
                    'width' => '100%',
                ],
                'options' => [
                    'placeholder' => 'Choose the status...',
                    'id' => 'status-list',
                ],
                'value' => $filter->status,
            ]); ?>
        </div>
    </div>
    <div class="_cc_groups_wrapper">
        <?php foreach ($filter->getGroupFilter() as $key => $item): ?>
            <div class="_cc_group cc_btn_group_filter <?= $key === $filter->group ? 'active' : ''; ?>" data-group-id="<?= $key; ?>"> <?= $item; ?>
                <span class="_cc_group_active"> </span>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (GroupFilter::isMy($filter->group)): ?>
        <div class="row">
            <div class="_cc_groups_wrapper">
                <div class="col-md-6">
                    <?php foreach ($filter->getReadFilter() as $key => $item): ?>
                        <div class="col-md-6">
                            <div class="_cc_group cc_btn_read_filter <?= $key === $filter->read ? 'active' : ''; ?>"
                                 data-read-id="<?= $key; ?>"> <?= $item; ?>
                                <span class="_cc_group_active"> </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

	<div id="cc-dialogs-wrapper" class="_cc-list-wrapper">
        <?php if ($dataProvider): ?>
		    <?= $this->render('_client-chat-item', ['clientChats' => $dataProvider->getModels(), 'clientChatId' => $clientChatId]); ?>
        <?php endif; ?>
	</div>

	<div class="_cc-channel-pagination" style="display: flex;justify-content: center; padding: 15px 0 10px;">
		<button class="btn btn-default" id="btn-load-channels" data-page="<?= $filter->page; ?>">Load more</button>
	</div>
</div>
