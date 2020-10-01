<?php

use dosamigos\datepicker\DatePicker;
use kartik\select2\Select2;
use sales\model\clientChat\dashboard\FilterForm;
use sales\model\clientChat\dashboard\GroupFilter;
use sales\widgets\UserSelect2Widget;
use yii\helpers\Html;

/** @var FilterForm $filter */
/** @var string $loadChannelsUrl */

/*
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
 */

?>

<?= Html::beginForm(\yii\helpers\Url::to(['/client-chat/index']), 'GET', ['id' => $filter->getId()]); ?>

    <div class="_cc_filter_wrapper ">
        <div class="row">

            <?php if ($filter->permissions->canDepartment()): ?>
                <div class="_cc_filter col-md-6" >
                    <?= Html::label('Department:', null, ['class' => 'control-label']); ?>
                    <?= Select2::widget([
                        'data' => $filter->getDepartments(),
                        'name' => Html::getInputName($filter, 'dep'),
                        'size' => Select2::SIZE_SMALL,
                        'options' => [
                            'placeholder' => 'Choose the channel...',
                            'id' => Html::getInputId($filter, 'dep'),
                        ],
                        'value' => $filter->dep,
                        'pluginOptions' => [
                            'width' => '100%',
                        ],
                        'pluginEvents' => [
                            'change' => new \yii\web\JsExpression('function (e) {
                                window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                            }'),
                        ],
                    ]); ?>
                </div>
            <?php endif; ?>

            <?php if ($filter->permissions->canProject()): ?>
                <div class="_cc_filter col-md-6">
                    <?= Html::label('Project:', null, ['class' => 'control-label']); ?>
                    <?= Select2::widget([
                        'data' => $filter->getProjects(),
                        'name' => Html::getInputName($filter, 'project'),
                        'size' => Select2::SIZE_SMALL,
                        'options' => [
                            'placeholder' => 'Choose the channel...',
                            'id' => Html::getInputId($filter, 'project'),
                        ],
                        'value' => $filter->project,
                        'pluginOptions' => [
                            'width' => '100%',
                        ],
                        'pluginEvents' => [
                            'change' => new \yii\web\JsExpression('function (e) {
                                window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                            }'),
                        ],
                    ]); ?>
                </div>
            <?php endif; ?>

            <?php if ($filter->permissions->canChannel()): ?>
                <div class="_cc_filter col-md-6">
                    <?= Html::label('Channel:', null, ['class' => 'control-label']); ?>
                    <?= Select2::widget([
                        'data' => $filter->getChannels(),
                        'name' => Html::getInputName($filter, 'channelId'),
                        'size' => Select2::SIZE_SMALL,
                        'pluginEvents' => [
                            'change' => new \yii\web\JsExpression('function (e) {
                                window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                            }'),
                        ],
                        'pluginOptions' => [
                            'width' => '100%',
                        ],
                        'options' => [
                            'placeholder' => 'Choose the channel...',
                            'id' => Html::getInputId($filter, 'channelId'),
                        ],
                        'value' => $filter->channelId,
                    ]); ?>
                </div>
            <?php endif; ?>

            <?php if ($filter->permissions->canStatus()): ?>
                <div class="_cc_filter col-md-6">
                    <?= Html::label('Status:', null, ['class' => 'control-label']); ?>
                    <?= Select2::widget([
                        'data' => $filter->getStatuses(),
                        'name' => Html::getInputName($filter, 'status'),
                        'size' => Select2::SIZE_SMALL,
                        'pluginEvents' => [
                            'change' => new \yii\web\JsExpression('function (e) {
                                window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                            }'),
                        ],
                        'pluginOptions' => [
                            'width' => '100%',
                        ],
                        'options' => [
                            'placeholder' => 'Choose the status...',
                            'id' => Html::getInputId($filter, 'status'),
                        ],
                        'value' => $filter->status,
                    ]); ?>
                </div>
            <?php endif; ?>

            <?php if ($filter->permissions->canUser()): ?>
                <div class="_cc_filter col-md-6">
                    <?= Html::label('Agent:', null, ['class' => 'control-label']); ?>
                    <?= UserSelect2Widget::widget([
                        'name' => Html::getInputName($filter, 'userId'),
                        'size' => Select2::SIZE_SMALL,
                        'pluginEvents' => [
                            'change' => new \yii\web\JsExpression('function (e) {
                                window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                            }'),
                        ],
                        'pluginOptions' => [
                            'width' => '100%',
                        ],
                        'options' => [
                            'placeholder' => 'Choose the agent...',
                            'id' => Html::getInputId($filter, 'userId'),
                        ],
                        'value' => $filter->userId,
                        'initValueText' => $filter->userName,
                    ]); ?>
                </div>
            <?php endif; ?>

            <?php if ($filter->permissions->canCreatedDate()): ?>
                <div class="_cc_filter col-md-6">
                    <?= Html::label('Created:', null, ['class' => 'control-label']); ?>
                    <?= DatePicker::widget([
                        'name' => Html::getInputName($filter, 'createdDate'),
                        'id' => Html::getInputId($filter, 'createdDate'),
                        'value' => $filter->createdDate,
                        'template' => '{addon}{input}',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-mm-yyyy',
                            'clearBtn' => true,
                        ],
                        'clientEvents' => [
                            'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                            'changeDate' => new \yii\web\JsExpression('function(e){
                                window.updateClientChatFilter("' . $filter->getId() . '", "' . $filter->formName() . '", "' . $loadChannelsUrl . '");
                             }'),
                        ],
                    ]); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($filter->permissions->canOneOfGroup()): ?>
        <div class="_cc_groups_wrapper">
            <?php foreach ($filter->getGroupFilterUI() as $key => $item): ?>
                <div class="_cc_group cc_btn_group_filter <?= ($key === $filter->group ? 'active' : ''); ?>" data-group-id="<?= $key; ?>"><?= $item; ?><span class="_cc_group_active"> </span></div>
            <?php endforeach; ?>
            <?= $filter->getGroupInput(); ?>
        </div>
    <?php else: ?>
        <div class="_cc_groups_wrapper"><h5>Not found chat permissions</h5></div>
    <?php endif; ?>

    <?php if (GroupFilter::isMy($filter->group) && $filter->permissions->canReadUnread()): ?>
        <div class="row">
            <div class="_cc_groups_wrapper">
                <div class="col-md-6" style="padding-top: 10px">
                    <?= $filter->getReadUnreadInput(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?= Html::endForm();
