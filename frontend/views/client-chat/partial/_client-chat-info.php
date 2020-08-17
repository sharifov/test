<?php
use common\models\Client;
use common\models\Quote;
use sales\model\clientChat\entity\ClientChat;
use yii\bootstrap4\Button;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\View;

/***
 * @var ClientChat $clientChat
 * @var Client $client
 * @var View $this
 * @var bool $existAvailableLeadQuotes
 */
?>

<div class="_rc-client-chat-info-wrapper">
    <div class="_rc-block-wrapper">
        <div style="display: flex">
            <span class="_rc-client-icon">
                <i class="fa fa-user"></i>
            </span>
            <div class="_rc-client-info">

                <span class="_rc-client-name">
                    <i class="fa fa-user"></i>
                    <span><?= Html::encode($client->full_name ?: 'Guest-' . $client->id) ?></span>
                </span>

                <?php if($emails = $client->clientEmails): ?>
                    <span class="_rc-client-email">
                        <i class="fa fa-envelope"></i>
                        <?php foreach ($emails as $email): ?>
                            <code><?= Html::encode($email->email) ?></code>
                        <?php endforeach; ?>
                    </span>
                <?php endif; ?>

                <?php if ($phones = $client->clientPhones): ?>
                    <span class="_rc-client-phone">
                        <i class="fa fa-phone"></i>
                        <?php foreach ($phones as $phone): ?>
                            <code><?= Html::encode($phone->phone) ?></code>
                        <?php endforeach; ?>
                    </span>
                <?php endif; ?>

                <?php if ($leads = $client->leads): ?>
                    <span class="_rc-client-leads">
                        <br />
                        <h6>Leads from client:</h6>
                        <?php foreach ($leads as $lead): ?>
                            <?php echo Yii::$app->formatter->asLead($lead, 'fa-cubes') ?><br />
                        <?php endforeach; ?>
                    </span>
                <?php endif; ?>

                <?php if ($cases = $client->cases): ?>
                    <span class="_rc-client-cases">
                        <br />
                        <h6>Cases from client:</h6>
                        <?php foreach ($cases as $case): ?>
                            <?php echo Yii::$app->formatter->asCase($case, 'fa-cubes') ?><br />
                        <?php endforeach; ?>
                    </span>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="_rc-block-wrapper">
        <h3 style="margin: 0;">Chat info</h3>
        <div class="d-flex align-items-center justify-content-center">
            <?= Html::button('<i class="fa fa-info"></i>', ['class' => 'btn btn-info cc_full_info', 'title' => 'Additional Information', 'data-cch-id' => $clientChat->cch_id]) ?>
            <?php if (!$clientChat->isClosed()): ?>
                <?= Html::button('<i class="fa fa-close"></i>', ['class' => 'btn btn-danger cc_close', 'title' => 'Close', 'data-cch-id' => $clientChat->cch_id]) ?>
                <?= Html::button('<i class="fa fa-exchange"></i>', ['class' => 'btn btn-warning cc_transfer', 'title' => 'Transfer', 'data-cch-id' => $clientChat->cch_id]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="_rc-block-wrapper">
        <?=
        \yii\widgets\DetailView::widget([
            'model' => $clientChat,
            'attributes' => [
                [
                    'label' => 'Case',
                    'value' => static function(ClientChat $model) {
                        $out = '<span id="chat-info-case-info">';
                        foreach ($model->cases as $case) {
                            $out .= Yii::$app->formatter->format($case, 'case') . ' ';
                        }
                        $out .= '</span>';
                        $out .= Html::button(' [ Create ] ', ['class' => 'btn btn-link default create_case', 'data-link' => Url::to(['/cases/create-by-chat', 'chat_id' => $model->cch_id])]);
                        return $out;
                    },
                    'format' => 'raw',
                ],
                [
                    'label' => 'Lead',
                    'value' => static function(ClientChat $model) {
                        $out = '<span id="chat-info-lead-info">';
                        foreach ($model->leads as $lead) {
                            $out .= Yii::$app->formatter->format($lead, 'lead') . ' ';
                            if ($lead->isExistQuotesForSend()) {
                                $out .= ' ' . Html::button('Offer', ['class' => 'btn btn-info chat-offer default', 'data-chat-id' => $model->cch_id, 'data-lead-id' => $lead->id]) . ' ';
                            }
                        }
                        $out .= '</span>';
                        $out .= Html::button(' [ Create ] ', ['class' => 'btn btn-link default create_lead', 'data-link' => Url::to(['/lead/create-by-chat', 'chat_id' => $model->cch_id])]);
                        return $out;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'cch_status_id',
                    'value' => static function (ClientChat $model) {
                        return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-'.$model->getStatusClass()]);
                    },
                    'format' => 'raw',
                    'filter' => ClientChat::getStatusList()
                ],
            ]
        ])
        ?>
    </div>

    <?php if ($clientChat->ccv && $clientChat->ccv->ccvCvd): ?>
        <div class="_rc-block-wrapper">
            <h3 style="margin: 0;">Additional Data</h3>
        </div>

        <div class="_rc-block-wrapper">
            <?=
            \yii\widgets\DetailView::widget([
                'model' => $clientChat->ccv->ccvCvd,
                'attributes' => [
                    'cvd_country',
                    'cvd_region',
                    'cvd_city',
                    'cvd_timezone',
                ]
            ])
            ?>
        </div>
    <?php endif; ?>
</div>


