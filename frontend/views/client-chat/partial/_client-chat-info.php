<?php
use common\models\Client;
use common\models\Quote;
use sales\auth\Auth;
use sales\entities\cases\CasesStatus;
use sales\guards\clientChat\ClientChatManageGuard;
use sales\helpers\clientChat\ClientChatHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatVisitorData\entity\ClientChatVisitorData;
use sales\repositories\clientChatStatusLogRepository\ClientChatStatusLogRepository;
use yii\bootstrap4\Button;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\View;
use yii\widgets\Pjax;

/***
 * @var ClientChat $clientChat
 * @var Client $client
 * @var View $this
 * @var bool $existAvailableLeadQuotes
 */

$_self = $this;

$statusLogRepository = Yii::createObject(ClientChatStatusLogRepository::class);
$guard = new ClientChatManageGuard($statusLogRepository);
?>

<div class="_rc-client-chat-info-wrapper">
    <div class="_rc-block-wrapper">
        <div class="col-md-12">

            <div class="col-md-8">
                <b><?= Html::encode($clientChat->cchProject ? $clientChat->cchProject->name : '-'); ?></b>:
                <?= Html::encode($clientChat->cchChannel ? $clientChat->cchChannel->ccc_name : '-'); ?>
                <br>
                <small title="Created date & time">
                    <i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($clientChat->cch_created_dt, 'php:F d Y'); ?> &nbsp;&nbsp;
                    <i class="fa fa-clock-o"></i> <?= Yii::$app->formatter->asDate($clientChat->cch_created_dt, 'php:H:i'); ?> &nbsp;&nbsp;
                    <span title="Owner"><i class="fa fa-user"></i> <?= $clientChat->cchOwnerUser ? Html::encode($clientChat->cchOwnerUser->username) : '-'; ?></span>
                </small>
            </div>

            <div class="col-md-4 text-right" title="Current Status">
                <?= $clientChat->getStatusLabel(); ?>
            </div>
        </div>
    </div>
    <div class="_rc-block-wrapper">
        <div style="display: flex; margin-bottom: 15px;">
            <span class="_rc-client-icon _cc-item-icon-round">
                <span class="_cc_client_name"><?= ClientChatHelper::getFirstLetterFromName(ClientChatHelper::getClientName($clientChat)); ?></span>
                <span class="_cc-status-wrapper">
                    <span class="_cc-status" data-is-online="<?= (int) $clientChat->cch_client_online; ?>"></span>
                </span>
            </span>
            <div class="_rc-client-info">

                <span class="_rc-client-name">
                    <span><?= Html::encode($client->full_name ?: 'Guest-' . $client->id); ?></span>
                </span>

                <?php if ($emails = $client->clientEmails): ?>
                    <span class="_rc-client-email">
                        <i class="fa fa-envelope"></i>
                        <?php foreach ($emails as $email): ?>
                            <code><?= Html::encode($email->email); ?></code>
                        <?php endforeach; ?>
                    </span>
                <?php endif; ?>

                <?php if ($phones = $client->clientPhones): ?>
                    <span class="_rc-client-phone">
                        <i class="fa fa-phone"></i>
                        <?php foreach ($phones as $phone): ?>
                            <code><?= Html::encode($phone->phone); ?></code>
                        <?php endforeach; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-center" style="width: 100%;">
            <?= Html::button('<i class="fa fa-info-circle"></i> Information', ['class' => 'btn btn-info cc_full_info', 'title' => 'Additional Information', 'data-cch-id' => $clientChat->cch_id]); ?>
            <?php if ($clientChat->isTransfer()): ?>
				<?= $guard->isCanCancelTransfer($clientChat, Auth::user()) ? Html::button('<i class="fa fa-exchange"></i> Cancel Transfer', ['class' => 'btn btn-danger cc_cancel_transfer', 'title' => 'Cancel Transfer', 'data-cch-id' => $clientChat->cch_id]) : ''; ?>
            <?php elseif (!$clientChat->isClosed()): ?>
                <?= Html::button('<i class="fa fa-times-circle"></i> Close Chat', ['class' => 'btn btn-danger cc_close', 'title' => 'Close', 'data-cch-id' => $clientChat->cch_id]); ?>
                <?= Html::button('<i class="fa fa-exchange"></i> Transfer', ['class' => 'btn btn-warning cc_transfer', 'title' => 'Transfer', 'data-cch-id' => $clientChat->cch_id]); ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($clientChat->feedback): ?>
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
                        <?php if ($feedback->ccf_rating): ?>
                            <?php for ($i = 1; $i <= $feedback->ccf_rating; $i++): ?>
                                <i class="fa fa-star text-warning"></i>
                            <?php endfor; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </div>
                    <div >
                        <strong>Message</strong>: <?php echo Html::encode($feedback->ccf_message); ?>
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
                <h2>Cases </h2>
                <ul class="nav navbar-right panel_toolbox">
                    <?php if (!$clientChat->isClosed() && Auth::can('/cases/create-by-chat')): ?>
                    <li>
                        <a class="create_case" data-link="<?= Url::to(['/cases/create-by-chat', 'chat_id' => $clientChat->cch_id]); ?>"><i class="fa fa-plus"></i> Create Case</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="clearfix"></div>
            </div>

            <div class="x_content">
                <div id="chat-info-case-info">
                <?php if ($cases = $clientChat->cases): ?>
                        <?php foreach ($cases as $case): ?>
                            <div class="_cc-case-item">
                                <span>
								    <?= Yii::$app->formatter->format($case, 'case'); ?>
                                </span>
                                <?= CasesStatus::getLabel($case->cs_status); ?>
                            </div>
                        <?php endforeach; ?>
                <?php else: ?>
                    <p>Cases are not found</p>
                <?php endif; ?>
                </div>
            </div>
        </div>

        <hr>

        <div class="x_panel">
            <div class="x_title">
                <h2>Leads </h2>
                <ul class="nav navbar-right panel_toolbox">
					<?php if (!$clientChat->isClosed() && Auth::can('/lead/create-by-chat')): ?>
                        <li>
                            <a class="create_lead" data-link="<?= Url::to(['/lead/create-by-chat', 'chat_id' => $clientChat->cch_id]); ?>"><i class="fa fa-plus"></i> Create Lead</a>
                        </li>
					<?php endif; ?>
                </ul>
                <div class="clearfix"></div>
            </div>

            <div class="x_content">
                <div id="chat-info-lead-info">
				<?php if ($leads = $clientChat->leads): ?>
						<?php foreach ($leads as $lead): ?>
                        <div class="_cc-lead-item">
                            <span>
                                <?= Yii::$app->formatter->format($lead, 'lead'); ?>
                            </span>
                            <span>
                                <?php if (!$clientChat->isClosed() && $lead->isExistQuotesForSend()): ?>
                                    <?= \yii\helpers\Html::tag('span', '<i class="fa fa-plane"></i> Offer', ['class' => 'chat-offer', 'data-chat-id' => $clientChat->cch_id, 'data-lead-id' => $lead->id]); ?>
                                <?php endif; ?>
                                <?= $lead->getStatusLabel($lead->status); ?>
                            </span>
                        </div>
						<?php endforeach; ?>
				<?php else: ?>
                    <p>Leads are not found</p>
				<?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php Pjax::begin([
        'id' => 'pjax-chat-additional-data-' . $clientChat->cch_id,
        'timeout' => 5000,
        'enablePushState' => false,
    ]); ?>
        <?php if ($clientChat->ccv && $clientChat->ccv->ccvCvd): ?>
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
                                    'value' => static function (ClientChatVisitorData $model) use ($clientChat) {
                                        $visitorId = '';
                                        if ($clientChat->ccv && $clientChat->ccv->ccvCvd) {
                                            $visitorId = $clientChat->ccv->ccvCvd->cvd_visitor_rc_id ?? '';
                                        }

                                        if ($chatRequest = ClientChatRequest::getLastRequestByVisitorId($visitorId, ClientChatRequest::EVENT_TRACK)) {
                                            if ($pageUrl = $chatRequest->getPageUrl()) {
                                                return Yii::$app->formatter->asUrl($pageUrl, ['target' => '_blank']);
                                            }
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
    <?php Pjax::end(); ?>
</div>


