<?php
/** @var array $callHistory */
/** @var int $page */

use common\models\Call;
use common\models\Department;
use sales\auth\Auth;
use sales\model\callLog\entity\callLog\CallLogCategory;
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
                $title = '';
                if ($call['user_id']) {
                    $phone = $call['formatted'];
                } elseif ((int)$call['cl_type_id'] === 1) {
                    $phone = $call['cl_phone_to'];
                    $title = $call['formatted'] !== $call['cl_phone_to'] ? $call['formatted'] : '';
                } else {
                    $phone = $call['cl_phone_from'];
                    $title = $call['formatted'] !== $call['cl_phone_from'] ? $call['formatted'] : '';
                }
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
                        <strong class="contact-info-card__name phone-dial-history" style="cursor:pointer;"
                                data-title="<?= $title ?>"
                                data-user-id="<?= $call['user_id'] ?>"
                                data-phone="<?= Html::encode($phone) ?>"
                                data-project-id="<?= Html::encode($call['cl_project_id']) ?>"
                                data-department-id="<?= Html::encode($call['cl_department_id']) ?>"
                                data-client-id="<?= Html::encode($call['cl_client_id']) ?>"
                                <?php
                                    if ((int)$call['cl_type_id'] === Call::CALL_TYPE_OUT) {
                                        echo ' data-source-type-id="' . $call['cl_category_id'] . '"';
                                        echo ' data-lead-id="' . $call['lead_id'] . '"';
                                        echo ' data-case-id="' . $call['case_id'] . '"';
                                    } elseif ((int)$call['cl_type_id'] === Call::CALL_TYPE_IN) {
                                        $department = (int)$call['cl_department_id'];
                                        if ($department === Department::DEPARTMENT_SALES) {
                                            if ($call['lead_id']) {
                                                echo ' data-source-type-id="' . Call::SOURCE_LEAD . '"';
                                                echo ' data-lead-id="' . $call['lead_id'] . '"';
                                            }
                                        } elseif ($department) {
                                            if ($call['case_id']) {
                                                echo ' data-source-type-id="' . Call::SOURCE_CASE . '"';
                                                echo ' data-case-id="' . $call['case_id'] . '"';
                                            }
                                        }
                                    }
                                ?>
                        >
                            <?= Html::encode($call['formatted']) ?>
                        </strong>
                        <small class="contact-info-card__timestamp"><?= Yii::$app->formatter->asDate(strtotime($call['cl_call_created_dt']), 'php:h:i A') ?></small>
                    </div>
                    <div class="contact-info-card__line history-details">
                        <span class="contact-info-card__call-type">
                            <?= CallLogType::getName($callType) ?>
                            <?php if ($call['cl_category_id']): ?>
                                 - <?= \common\models\Call::SOURCE_LIST[$call['cl_category_id']] ?? 'undefined' ?>
                            <?php endif;?>
                        </span>
                        <small><i class="contact-info-card__call-info fa fa-info btn-history-call-info" data-call-sid="<?= $call['cl_call_sid'] ?>"> </i></small>
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

