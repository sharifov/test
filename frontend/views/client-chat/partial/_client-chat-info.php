<?php
use common\models\Client;
use sales\model\clientChat\entity\ClientChat;
use yii\helpers\Html;
use yii\web\View;

/***
 * @var ClientChat $clientChat
 * @var Client $client
 * @var View $this
 *
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
            </div>
        </div>
	</div>

    <div class="_rc-block-wrapper">
        <h3 style="margin: 0;">Chat info</h3>
        <div class="d-flex align-content-center justify-content-center">
            <?= Html::button('<i class="fa fa-info"></i>', ['class' => 'btn btn-info cc_full_info', 'data-cch-id' => $clientChat->cch_id]) ?>
            <?php if (!$clientChat->isClosed()): ?>
                <?= Html::button('<i class="fa fa-close"></i>', ['class' => 'btn btn-danger cc_close', 'data-cch-id' => $clientChat->cch_id]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="_rc-block-wrapper">
        <?=
            \yii\widgets\DetailView::widget([
                'model' => $clientChat,
                'attributes' => [
                    'cch_title',
                    'cch_description',
					'cchCase:case',
					'cchLead:lead',
					[
						'attribute' => 'cch_status_id',
						'value' => static function (ClientChat $model) {
							return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-'.$model->getStatusClass()]);
						},
						'format' => 'raw',
						'filter' => ClientChat::getStatusList()
					],
                    'cch_note',
                ]
            ])
        ?>
    </div>

    <?php if ($clientChat->cchData): ?>
        <div class="_rc-block-wrapper">
            <h3 style="margin: 0;">Additional Data</h3>
        </div>

        <div class="_rc-block-wrapper">
            <?=
            \yii\widgets\DetailView::widget([
                'model' => $clientChat->cchData,
                'attributes' => [
                    'ccd_country',
                    'ccd_region',
                    'ccd_city',
                    'ccd_timezone',
                ]
            ])
            ?>
        </div>
    <?php endif; ?>
</div>


