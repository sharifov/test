<?php

use common\components\i18n\Formatter;
use common\models\Client;
use frontend\widgets\clientChat\ClientChatClientInfoWidget;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use modules\offer\src\entities\offer\OfferQuery;
use modules\taskList\abac\TaskListAbacObject;
use src\auth\Auth;
use src\entities\cases\CasesStatus;
use src\helpers\clientChat\ClientChatHelper;
use src\model\client\query\ClientChatCounter;
use src\model\client\query\ClientLeadCaseCounter;
use src\model\clientChat\entity\abac\ClientChatAbacObject;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatHold\service\ClientChatHoldService;
use src\model\clientChat\permissions\ClientChatActionPermission;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatVisitorData\entity\ClientChatVisitorData;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\Pjax;

/***
 * @var ClientChat $clientChat
 * @var Client $client
 * @var View $this
 * @var bool $existAvailableLeadQuotes
 * @var ClientChatActionPermission $actionPermissions
 */

$_self = $this;

$counter = new ClientLeadCaseCounter($client->id, Auth::id());
$chatCounter = new ClientChatCounter($client->id);

$chatSendQuoteListUrl = Url::toRoute('/client-chat/send-quote-list');
$chatSendOfferListUrl = Url::toRoute('/client-chat/send-offer-list');
$formatter = new Formatter();
$formatter->timeZone = Auth::user()->timezone;

$cases = $clientChat->cases;
$leads = $clientChat->leads;
$formResponses = $clientChat->formResponses;
?>

<div class="_rc-client-chat-info-wrapper">
    <div class="_rc-block-wrapper">
        <div class="col-md-12">

            <div class="col-md-8">
                <?php echo Html::a(
                    Html::tag('i', '', ['class' => 'fa fa-external-link']),
                    ['client-chat/view', 'chid' => $clientChat->cch_id],
                    [
                        'target' => '_blank',
                        'title' => 'Open chat in new tab',
                        'data-toggle' => 'tooltip',
                    ]
                )?>

                <b><?= Html::encode($clientChat->cchProject ? $clientChat->cchProject->name : '-'); ?></b>:
                <?= Html::encode($clientChat->cchChannel ? $clientChat->cchChannel->ccc_name : '-'); ?>
                <br>
                <small title="Created date & time">
                    <i class="fa fa-calendar"></i> <?= $formatter->asDatetime($clientChat->cch_created_dt, 'php:F d Y'); ?> &nbsp;&nbsp;
                    <i class="fa fa-clock-o"></i> <?= $formatter->asDatetime($clientChat->cch_created_dt, 'php:H:i') ?> &nbsp;&nbsp;
                    <span title="Owner"><i class="fa fa-user"></i> <?= $clientChat->cchOwnerUser ? Html::encode($clientChat->cchOwnerUser->username) : '-'; ?></span>
                </small>
            </div>

            <div class="col-md-4 text-right" title="Current Status">
                <?= $clientChat->getStatusLabel(); ?> <br />

                <div class="dropdown " style="margin-top: 10px; float: right;">
                    <button class="btn text-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false"
                            style="box-shadow: 0 0 0 0.2rem rgba(240, 184, 81, 0.25); height: 25px; margin-top: 3px;" >
                        <i class="fa fa-bars warning"></i> <span class="text-warning">Actions</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                        <?php echo Html::a('<i class="fa fa-info-circle"> </i> Information', null, [
                            'class' => 'dropdown-item text-success cc_full_info',
                            'title' => 'Additional Information',
                            'data-cch-id' => $clientChat->cch_id
                        ]) ?>

                        <?php if ($actionPermissions->canCancelTransfer($clientChat)) : ?>
                            <?php echo Html::a('<i class="fa fa-exchange"> </i> Cancel Transfer', null, [
                                'class' => 'dropdown-item cc_cancel_transfer',
                                'title' => 'Cancel Transfer',
                                'data-cch-id' => $clientChat->cch_id
                            ]) ?>
                        <?php endif; ?>

                        <?php if ($actionPermissions->canClose($clientChat)) : ?>
                            <?php echo Html::a('<i class="fa fa-times-circle"> </i> Close Chat', null, [
                                'class' => 'dropdown-item text-danger cc_close',
                                'title' => 'Close',
                                'data-cch-id' => $clientChat->cch_id
                            ]) ?>
                        <?php endif;?>

                        <?php if ($actionPermissions->canTransfer($clientChat)) : ?>
                            <?php echo Html::a('<i class="fa fa-exchange"> </i> Transfer', null, [
                                'class' => 'dropdown-item text-warning cc_transfer',
                                'title' => 'Transfer',
                                'data-cch-id' => $clientChat->cch_id
                            ]) ?>
                        <?php endif;?>

                        <?php if ($actionPermissions->canReopenChat($clientChat)) : ?>
                            <?php echo Html::a('<i class="fa fa-undo"> </i> Reopen', null, [
                                'class' => 'dropdown-item text-warning cc_reopen',
                                'title' => 'Reopen',
                                'data-cch-id' => $clientChat->cch_id
                            ]) ?>
                        <?php endif; ?>

                        <?php if ($actionPermissions->canHold($clientChat)) : ?>
                            <?php echo Html::a('<i class="fa fa-pause"> </i> Hold', null, [
                                'class' => 'dropdown-item text-secondary cc_hold',
                                'title' => 'Hold',
                                'data-cch-id' => $clientChat->cch_id
                            ]) ?>
                        <?php elseif ($actionPermissions->canUnHold($clientChat)) : ?>
                            <?php echo Html::a('<i class="fa fa-play"> </i> UnHold', null, [
                                'class' => 'dropdown-item text-nowrap text-info cc_un_hold',
                                'title' => 'UnHold',
                                'data-cch-id' => $clientChat->cch_id
                            ]) ?>
                        <?php endif; ?>

                        <?php if ($actionPermissions->canReturn($clientChat)) : ?>
                            <?php echo Html::a('<i class="fa fa-arrows-h"> </i> Return', null, [
                                'class' => 'dropdown-item text-info cc_return',
                                'title' => 'Return the chat to In Progress',
                                'data-cch-id' => $clientChat->cch_id
                            ]) ?>
                        <?php endif; ?>

                        <?php if ($actionPermissions->canTake($clientChat)) : ?>
                            <?php echo Html::a('<i class="fa fa-arrows-h"> </i> Take', null, [
                                'class' => 'dropdown-item text-info cc_take ',
                                'title' => 'Take',
                                'data-cch-id' => $clientChat->cch_id
                            ]) ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="_rc-block-wrapper">
        <div class="row">
            <div class="client-chat-client-info-wrapper col-md-12">
                <?= ClientChatClientInfoWidget::widget(['chat' => $clientChat]) ?>
            </div>
            <?php if (Auth::can('client-chat/manage', ['chat' => $clientChat])) : ?>
                <div class="col-md-6">
                    <div class="dropdown " style="margin-top: 10px; float: left;">
                        <button class="btn text-warning dropdown-toggle" type="button" id="menuClientInfoActions" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"
                                style="box-shadow: 0 0 0 0.2rem rgba(240, 184, 81, 0.25); height: 25px; margin-top: 3px;" >
                            <i class="fa fa-bars warning"> </i> <span class="text-warning">Actions</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="menuClientInfoActions">

                            <?php echo Html::a('<i class="fas fa-edit warning"> </i> Update Client', null, [
                                'class' => 'dropdown-item showModalButton',
                                'title' => 'Update Client',
                                'data-modal_id' => "client-manage-info",
                                'data-content-url' => Url::to(['/client-chat-client-actions/ajax-edit-client-name-modal-content', 'id' => $clientChat->cch_id])
                            ]) ?>

                            <?php echo Html::a('<i class="fas fa-plus-circle success"> </i> Add Email', null, [
                                'class' => 'dropdown-item showModalButton',
                                'title' => 'Add Email',
                                'data-modal_id' => "client-manage-info",
                                'data-content-url' => Url::to(['/client-chat-client-actions/ajax-add-client-email-modal-content', 'id' => $clientChat->cch_id])
                            ]) ?>

                            <?php echo Html::a('<i class="fas fa-plus-circle success"> </i> Add Phone', null, [
                                'class' => 'dropdown-item showModalButton',
                                'title' => 'Add Phone',
                                'data-modal_id' => "client-manage-info",
                                'data-content-url' => Url::to(['/client-chat-client-actions/ajax-add-client-phone-modal-content', 'id' => $clientChat->cch_id])
                            ]) ?>

                            <?php echo Html::a('<i class="fas fa-info-circle"> </i> Details', null, [
                                'id' => 'btn-client-info-details',
                                'data-client-id' => $client->id,
                                'class' => 'dropdown-item',
                                'title' => 'Details',
                            ]) ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="margin-top: 10px; float: right;">
                        <?php ?>
                        <?= Html::button(
                            '<i class="fa fa-comment"> </i> Chats (' . $chatCounter->countActiveChats() . '/' . $chatCounter->countAllChats() . ')',
                            [
                                'class' => 'btn btn-default',
                                'id' => 'btn-client-chats',
                                'data-chat-id' => $clientChat->cch_id,
                                'data-client-id' => $clientChat->cch_client_id
                                ]
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <?php if ($clientChat->isShowDeadlineProgress() && $clientChatHold = $clientChat->clientChatHold) : ?>
        <div class="_rc-block-wrapper" id="progress_bar_box">
            <div class="x_panel">
                <div class="x_title">
                    <h2>
                        Status Hold (<?php echo ClientChatHoldService::formatTimeFromSeconds($clientChatHold->deadlineStartDiffInSeconds()) ?>)
                    </h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="progress" id="progressBar" style="height: 13px;">
                                <div
                                    class="progress-bar progress-bar-striped bg-info progress-bar-animated"
                                    role="progressbar"
                                    style="width: 100%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div id="countdown-clock text-center badge badge-warning" style="font-size: 12px">
                                <i class="fa fa-clock-o"></i> <span id="clock">00:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>

    <?php if ($clientChat->feedback) : ?>
        <?php $feedback = $clientChat->feedback; ?>
        <div class="_rc-block-wrapper">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Feedback </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div>
                        <strong>Rating</strong>:
                        <?php if ($feedback->ccf_rating) : ?>
                            <?php for ($i = 1; $i <= $feedback->ccf_rating; $i++) : ?>
                                <i class="fa fa-star text-warning"></i>
                            <?php endfor; ?>
                        <?php else : ?>
                            -
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong>Message</strong>: <?php echo Html::encode($feedback->ccf_message) ?>
                    </div>
                    <div class="_cc_chat_note_date_item">
                        <?php echo $feedback->ccf_created_dt ? Yii::$app->formatter->asDatetime(strtotime($feedback->ccf_created_dt)) : ''; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="_rc-block-wrapper">
        <div class="x_panel">
            <div class="x_title">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 style="margin: 0;">Cases (<?= $counter->countActiveCases()?> / <?= $counter->countAllCases()?>) </h2>
                    <div class="dropdown">
                        <button class="btn text-warning dropdown-toggle" type="button" id="dropdownMenuButtonCase" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"
                                style="box-shadow: 0 0 0 0.2rem rgba(240, 184, 81, 0.25); height: 25px; margin: 0" >
                          <i class="fa fa-bars warning"></i> <span class="text-warning">Actions</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonCase">
                            <?php if ($actionPermissions->canLinkCase($clientChat)) : ?>
                                <?php echo Html::a('<i class="fas fa-link"> </i> Link Case', null, [
                                    'class' => 'dropdown-item link_case',
                                    'title' => 'Link Case',
                                    'data-link' => Url::to(['/cases/link-chat', 'chat_id' => $clientChat->cch_id]),
                                ]) ?>
                            <?php endif; ?>

                            <?php if ($actionPermissions->canCreateCase($clientChat)) : ?>
                                <?php echo Html::a('<i class="fas fa-plus"> </i> Create Case', null, [
                                    'class' => 'dropdown-item create_case',
                                    'title' => 'Create Case',
                                    'data-link' => Url::to(['/cases/create-by-chat', 'chat_id' => $clientChat->cch_id]),
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="x_content">
                <div id="chat-info-case-info">
                <?php if ($cases) : ?>
                        <?php foreach ($cases as $case) : ?>
                            <div class="_cc-case-item">
                                <span>
                                    <?= Yii::$app->formatter->format($case, 'case'); ?>
                                </span>
                                <?= CasesStatus::getLabel($case->cs_status); ?>
                            </div>
                        <?php endforeach; ?>
                <?php else : ?>
                    <p>Cases are not found</p>
                <?php endif; ?>
                </div>
            </div>
        </div>

        <hr>

        <div class="x_panel">
            <div class="x_title">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 style="margin: 0;">Leads (<?= $counter->countActiveLeads()?> / <?= $counter->countAllLeads()?>) </h2>
                    <div class="dropdown">
                        <button class="btn text-warning dropdown-toggle" type="button" id="dropdownMenuButtonLead" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false"
                                style="box-shadow: 0 0 0 0.2rem rgba(240, 184, 81, 0.25); height: 25px; margin: 0" >
                          <i class="fa fa-bars warning"></i> <span class="text-warning">Actions</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonLead">
                            <?php /** @abac ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE, Access To search|add|send Quotes*/ ?>
                            <?php if (!$leads && Yii::$app->abac->can(null, ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE)) : ?>
                                <?php echo Html::a('<i class="fas fa-search"> </i> Search Quotes', null, [
                                    'class' => 'dropdown-item search_quotes',
                                    'title' => 'Search Quotes',
                                    'data-link' => Url::to(['/client-chat-flight-quote/ajax-search-quotes-by-chat', 'chat_id' => $clientChat->cch_id]),
                                ]) ?>
                            <?php endif; ?>

                            <?php if ($actionPermissions->canLinkLead($clientChat)) : ?>
                                <?php echo Html::a('<i class="fas fa-link"> </i> Link Lead', null, [
                                    'class' => 'dropdown-item link_lead',
                                    'title' => 'Link Lead',
                                    'data-link' => Url::to(['/lead/link-chat', 'chat_id' => $clientChat->cch_id]),
                                ]) ?>
                            <?php endif; ?>

                            <?php if ($actionPermissions->canCreateLead($clientChat)) : ?>
                                <?php echo Html::a('<i class="fas fa-plus"> </i> Create Lead', null, [
                                    'class' => 'dropdown-item create_lead',
                                    'title' => 'Create Lead',
                                    'data-link' => Url::to(['/lead/create-by-chat', 'chat_id' => $clientChat->cch_id]),
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="x_content">
                <div id="chat-info-lead-info">
                <?php if ($leads) : ?>
                        <?php foreach ($leads as $lead) : ?>
                        <div class="_cc-lead-item align-items-center">
                            <span class="d-flex align-items-center">
                                <span>
                                  <?= Yii::$app->formatter->format($lead, 'lead'); ?>
                                </span>

                                <div class="dropdown" style="margin-left: 10px;">
                                    <button class="btn text-warning dropdown-toggle btn-sm" type="button" id="dropdownMenuButtonLead" data-toggle="dropdown"
                                            style="box-shadow: 0 0 0 0.2rem rgba(240, 184, 81, 0.25); margin: 0" >
                                      <i class="fa fa-bars warning"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButtonLead">
                                      <?php /** @abac ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE, Access To search|add|send Quotes*/ ?>
                                      <?php if (Yii::$app->abac->can(null, ClientChatAbacObject::ACT_CREATE_SEND_QUOTE, ClientChatAbacObject::ACTION_CREATE)) : ?>
                                            <?php echo Html::a('<i class="fas fa-search"> </i> Search Quotes', null, [
                                              'class' => 'dropdown-item search_quotes',
                                              'title' => 'Search Quotes',
                                              'data-link' => Url::to(['/client-chat-flight-quote/ajax-search-quotes-by-chat', 'chat_id' => $clientChat->cch_id, 'lead_id' => $lead->id]),
                                            ]) ?>
                                      <?php endif; ?>
                                        <?php
                                         /** @abac LeadAbacObject::ACT_TAKE_LEAD_FROM_CHAT, LeadAbacObject::ACTION_ACCESS, Access To Take Lead From Chat*/
                                        $leadAbacDto = new LeadAbacDto($lead, Auth::id());
                                        if (Auth::can('leadSection', ['lead' => $lead]) && Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_TAKE_LEAD_FROM_CHAT, LeadAbacObject::ACTION_ACCESS)) :
                                            echo Html::a('<i class="fa fa-download"></i> Take', null, [
                                                'class' => 'dropdown-item',
                                                'id' => 'take_button',
                                                'data-gid' => $lead->gid,
                                                'data-pjax' => 0
                                                ]);
                                        endif;
                                        ?>
                                      <span data-cc-lead-info-quote="<?= $lead->id?>">
                                      <?php if (!$clientChat->isClosed() && $lead->isExistQuotesForSend()) : ?>
                                            <?= Html::a(
                                                '<i class="fa fa-plane"> </i> Send Quotes',
                                                '#',
                                                ['class' => 'chat-offer dropdown-item', 'data-chat-id' => $clientChat->cch_id, 'data-lead-id' => $lead->id, 'data-url' => $chatSendQuoteListUrl]
                                            ); ?>
                                      <?php endif; ?>
                                      </span>
                                      <span data-cc-lead-info-offer="<?= $lead->id?>">
                                      <?php if (!$clientChat->isClosed() && OfferQuery::existsOffersByLeadId($lead->id)) : ?>
                                            <?= Html::a('<i class="fa fa-plane"> </i> Offer', '#', ['class' => 'chat-offer dropdown-item', 'data-chat-id' => $clientChat->cch_id, 'data-lead-id' => $lead->id, 'data-url' => $chatSendOfferListUrl]); ?>
                                      <?php endif; ?>
                                      </span>
                                    </div>
                                </div>
                            </span>
                            <span>
                                <?= $lead->getStatusLabel($lead->status); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                <?php else : ?>
                    <p>Leads are not found</p>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php /* Pjax::begin([
        'id' => 'pjax-chat-additional-data-' . $clientChat->cch_id,
        'timeout' => 5000,
        'enablePushState' => false,
    ]); */ ?>
    <?php /** @abac ClientChatAbacObject::UI_CLIENT_CHAT_FORM, ClientChatAbacObject::ACTION_ACCESS, Access To show Client Chat Form Response */ ?>
    <?php  if (Yii::$app->abac->can(null, ClientChatAbacObject::UI_CLIENT_CHAT_FORM, ClientChatAbacObject::ACTION_ACCESS)) : ?>
        <div class="_rc-block-wrapper">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Client Chat Form </h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <?php /** @abac ClientChatAbacObject::UI_CLIENT_CHAT_FORM, ClientChatAbacObject::ACTION_CREATE, Access To create Client Chat Form Response */ ?>
                        <?php if (Yii::$app->abac->can(null, ClientChatAbacObject::UI_CLIENT_CHAT_FORM, ClientChatAbacObject::ACTION_CREATE)) : ?>
                            <li class="">
                                <?php echo Html::a('<i class="fa fa-plus"> </i> New booking id', null, [
                                    'class' => 'dropdown-item showModalButton',
                                    'title' => 'Add booking id',
                                    'style' => 'color: #0073ce;',
                                    'data-modal_id' => "client-manage-info",
                                    'data-content-url' => Url::to(['/client-chat-client-actions/ajax-add-booking-id-modal-content', 'id' => $clientChat->cch_id])
                                ]) ?>
                            </li>
                        <?php endif ?>
                        <li>
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content client-chat-client-form">
                    <?php foreach ($formResponses as $formResponse) : ?>
                        <div class="_cc-addition-data-item">
                            <span><?= $formResponse->clientChatForm->ccf_name ?></span>
                            <span ><?= $formResponse->ccfr_value ?></span>
                        </div>
                    <?php endforeach ?>

                </div>
            </div>
        </div>
    <?php endif ?>

        <?php if ($clientChat->ccv && $clientChat->ccv->ccvCvd) : ?>
            <div class="_rc-block-wrapper">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Additional Data </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div class="_cc-addition-data-item">
                            <span>Status</span>
                            <span class="badge badge-<?= $clientChat->getStatusClass(); ?>"><?= $clientChat->getStatusName(); ?></span>
                        </div>
                        <?=
                        \yii\widgets\DetailView::widget([
                            'model' => $clientChat->ccv->ccvCvd,
                            'attributes' => [
                                'cvd_country',
                                'cvd_region',
                                'cvd_city',
                                'cvd_timezone',
                                [
                                    'label' => 'Last Url',
                                    'value' => static function (ClientChatVisitorData $model) {
                                        if ($model->cvd_url) {
                                            return Yii::$app->formatter->asUrl($model->cvd_url, ['target' => '_blank']);
                                        }
                                        return Yii::$app->formatter->nullDisplay;
                                    },
                                    'format' => 'raw',
                                ],
                            ],
                            'template' => '<div class="_cc-addition-data-item"><span>{label}</span><span>{value}</span></div>',
                        ]);
                        ?>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    <?php // Pjax::end();?>
</div>

<?php if (isset($clientChatHold)) :
    $formatTimer = ClientChatHoldService::isMoreThanHourLeft($clientChatHold) ? "%H:%M:%S" : "%M:%S";
    $maxProgressBar = $clientChatHold->deadlineStartDiffInSeconds();
    $leftProgressBar = $clientChatHold->deadlineNowDiffInSeconds();
    $warningZone = $clientChatHold->halfWarningSeconds();

    $js = <<<JS
    setTimeout(() => window.clientChatHoldTimeProgressbar('$formatTimer', {$maxProgressBar}, {$leftProgressBar}, {$warningZone}), 500);
    JS;
    $this->registerJs($js);
endif; ?>

<?php
$clientInfoUrl = \yii\helpers\Url::to(['/client/ajax-get-info']);
$clientChatsUrl = \yii\helpers\Url::to(['/client-chat-client/get-chats']);
$js = <<<JS
$(document).on('click', '#btn-client-info-details', function(e) {
    e.preventDefault();
    var client_id = $(this).data('client-id');
    $('#modalChat .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
    $('#modalChat-label').html('Client Details (' + client_id + ')');
    $('#modalChat').modal();
    $.post('$clientInfoUrl', {client_id: client_id},
            function (data) {
                $('#modalChat .modal-body').html(data);
            }
        );
    });

   $(document).on('click', '.showModalButton', function(e){
       e.preventDefault();
        let id = $(this).data('modal_id');
        let url = $(this).data('content-url');

        $('#modal-' + id + '-label').html($(this).attr('title'));
        $('#modal-' + id).modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

        $.post(url, function(data) {
            $('#modal-' + id).find('.modal-body').html(data);
        });
    });
   
   $(document).on('click', '#btn-client-chats', function(e) {
    e.preventDefault();
    var chatId = $(this).data('chat-id');
    var clientId = $(this).data('client-id');
    $('#modalChat .modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
    $('#modalChat-label').html('Client Details (' + clientId + ')');
    $('#modalChat').modal();
    $.post('$clientChatsUrl' + '?chatId=' + chatId, {},
            function (data) {
                $('#modalChat .modal-body').html(data);
            }
        );
    });
   
  
JS;
$this->registerJs($js);

Modal::begin([
    'title' => '',
    'id' => 'modal-client-manage-info',
    'size' => Modal::SIZE_SMALL,
]);
Modal::end();

Modal::begin([
    'id' => 'modalChat',
    'title' => '',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
Modal::end();
