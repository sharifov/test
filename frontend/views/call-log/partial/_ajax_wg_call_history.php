<?php
/** @var array $callHistory */
/** @var int $page */

use common\models\Call;
use sales\model\callLog\entity\callLog\CallLogStatus;
use sales\model\callLog\entity\callLog\CallLogType;
use yii\helpers\Html;

?>

<?php foreach ($callHistory as $key => $day): ?>
    <?php if ($day): ?>
    <span class="section-separator"><?= $key ?></span>
    <ul class="phone-widget__list-item calls-history">
    <?php foreach ($day as $call): ?>
            <?php
                $callType = (int)$call['cl_type_id'];
                $date = $call['cl_call_created_dt'];
            ?>
            <li class="calls-history__item contact-info-card">
                <div class="contact-info-card__status">
                    <div class="contact-info-card__call-icon">
                        <?php if ($callType === CallLogType::IN && (int)$call['cl_status_id'] === CallLogStatus::NOT_ANSWERED): ?>
                            <img src="/img/pw-missed.svg">
                        <?php elseif ($callType === CallLogType::IN): ?>
                            <img src="/img/pw-incoming.svg">
                        <?php else: ?>
                            <div class="contact-info-card__call-icon">
                                <img src="/img/pw-outgoing.svg">
                            </div>
						<?php endif; ?>
                    </div>
                </div>
                <div class="contact-info-card__details">
                    <div class="contact-info-card__line history-details">
                        <strong class="contact-info-card__name phone-dial" style="cursor:pointer;" data-phone="<?= Html::encode(($callType === CallLogType::IN ? $call['cl_phone_from'] : $call['cl_phone_to'])) ?>"><?= Html::encode($call['client_name'] ?? ($callType === Call::CALL_TYPE_IN ? $call['cl_phone_from'] : $call['cl_phone_to'])) ?></strong>
                        <small class="contact-info-card__timestamp"><?= Yii::$app->formatter->asDate(strtotime($call['cl_call_created_dt']), 'php:h:i A') ?></small>
                    </div>
                    <div class="contact-info-card__line history-details">
                        <span class="contact-info-card__call-type"><?= CallLogType::getName($callType) ?></span>
<!--                        <small class="contact-info-card__call-length">--><?php // Yii::$app->formatter->asDuration($call['c_call_duration'] ?? 0) ?><!--</small>-->
                    </div>
                    <?php if ($call['callNote']): ?>
                    <div class="contact-info-card__line history-details">
                        <div class="contact-info-card__note">
                            <span class="contact-info-card__note-message"><?= Html::encode($call['callNote']) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>

<?php endforeach; ?>

