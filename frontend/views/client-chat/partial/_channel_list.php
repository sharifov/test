<?php

use common\models\Department;
use common\models\Project;
use kartik\select2\Select2;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\ClientChatReadFilter;
use sales\model\clientChat\entity\ClientChatTabGroups;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var $channels ClientChatChannel[] */
/** @var $this \yii\web\View */
/** @var $dataProvider \yii\data\ArrayDataProvider|null */
/** @var $loadChannelsUrl string */
/** @var $page int */
/** @var $channelId int|null */
/** @var $clientChatId int|null */
/** @var $tab int */
/** @var $dep int */
/** @var $project int */
/** @var $totalUnreadMessages int */
/** @var $group int */
/** @var $readFilter int */

?>


<div class="_cc-wrapper">
    <div class="_cc_tabs_wrapper">
        <?php foreach (ClientChat::getTabList() as $key => $item): ?>
            <div class="_cc_tab <?= $key === $tab ? 'active' : ''; ?>" data-tab-id="<?= $key; ?>"> <?= $item; ?>
                <?php if (ClientChat::isTabActive($key)): ?>
                    <sup class="_cc_unread_messages label label-danger" ><?= $totalUnreadMessages ?: ''; ?></sup>
                <?php endif; ?>
                <span class="_cc_tab_active"></span>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="_cc_filter_wrapper">
        <div class="_cc_filter">
			<?= Html::label('Department:', null, ['class' => 'control-label']); ?>
            <?= Select2::widget([
                'data' => ArrayHelper::merge(['All'], Department::getList()),
                'name' => 'dep',
                'size' => Select2::SIZE_SMALL,
                'options' => [
                    'placeholder' => 'Choose the channel...',
                    'id' => 'dep-list',
                ],
                'value' => $dep ?: 0,
                'pluginOptions' => [
                    'width' => '100%',
                ],
                'pluginEvents' => [
                    'change' => new \yii\web\JsExpression('function (e) {
                    let selectedChannel = $("#channel-list").val();
                    let selectedDep = $(this).val();
                    let selectedProject = $("#project-list").val();
                    window._cc_apply_filter(selectedChannel, "' . $loadChannelsUrl . '", ' . $tab . ', selectedDep, selectedProject, ' . $group . ', ' . $readFilter . ');
                }'),
                ],
            ]); ?>
        </div>
        <div class="_cc_filter">
			<?= Html::label('Project:', null, ['class' => 'control-label']); ?>
			<?= Select2::widget([
			    'data' => ArrayHelper::merge(['All'], Project::getList()),
			    'name' => 'project',
			    'size' => Select2::SIZE_SMALL,
			    'options' => [
			        'placeholder' => 'Choose the channel...',
			        'id' => 'project-list',
			    ],
			    'value' => $project ?: 0,
			    'pluginOptions' => [
			        'width' => '100%',
			    ],
			    'pluginEvents' => [
			        'change' => new \yii\web\JsExpression('function (e) {
                    let selectedChannel = $("#channel-list").val();
                    let selectedDep = $("#dep-list").val();
                    let selectedProject = $(this).val();
                    window._cc_apply_filter(selectedChannel, "' . $loadChannelsUrl . '", ' . $tab . ', selectedDep, selectedProject, ' . $group . ', ' . $readFilter . ');
                }'),
			    ],
			]); ?>
        </div>
    </div>
	<div class="_cc-channel-select">
		<?= Html::label('Channel list:', null, ['class' => 'control-label']); ?>
		<?= Select2::widget([
		    'data' => ArrayHelper::merge(['All'], ArrayHelper::map(ArrayHelper::toArray($channels), 'ccc_id', 'ccc_name')),
		    'name' => 'channel-list',
		    'size' => Select2::SIZE_SMALL,
		    'pluginEvents' => [
		        'change' => new \yii\web\JsExpression('function (e) {
                    let selectedChannel = $(this).val();
                    let selectedDep = $("#dep-list").val();
                    let selectedProject = $("#project-list").val();
                    window._cc_apply_filter(selectedChannel, "' . $loadChannelsUrl . '", ' . $tab . ', selectedDep, selectedProject, ' . $group . ', ' . $readFilter . ');
                }'),
		    ],
		    'options' => [
		        'placeholder' => 'Choose the channel...',
		        'id' => 'channel-list',
		    ],
		    'value' => $channelId ?: 0,
		]); ?>
	</div>
    <div class="_cc_groups_wrapper">
        <?php foreach (ClientChatTabGroups::LIST as $key => $item): ?>
            <div class="_cc_group cc_btn_group_filter <?= $key === $group ? 'active' : ''; ?>" data-group-id="<?= $key; ?>"> <?= $item; ?>
                <span class="_cc_group_active"></span>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (ClientChatTabGroups::isMy($group)): ?>
        <div class="row">
            <div class="_cc_groups_wrapper">
                <div class="col-md-6">
                    <?php foreach (ClientChatReadFilter::LIST as $key => $item): ?>
                        <div class="col-md-6">
                            <div class="_cc_group cc_btn_read_filter <?= $key === $readFilter ? 'active' : ''; ?>"
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
		<button class="btn btn-default" id="btn-load-channels" data-page="<?= $page; ?>">Load more</button>
	</div>
</div>
