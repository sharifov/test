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
                        <?php if ($callType === CallLogType::IN): ?>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0)">
                                <path
                                    d="M17.5 13.6833V16.63C17.5001 16.841 17.4202 17.0441 17.2763 17.1985C17.1325 17.3528 16.9355 17.4469 16.725 17.4617C16.3608 17.4867 16.0633 17.5 15.8333 17.5C8.46917 17.5 2.5 11.5308 2.5 4.16667C2.5 3.93667 2.5125 3.63917 2.53833 3.275C2.5531 3.06454 2.64715 2.86751 2.8015 2.72367C2.95585 2.57984 3.15902 2.4999 3.37 2.5H6.31667C6.42003 2.4999 6.51975 2.53822 6.59644 2.60752C6.67313 2.67682 6.72133 2.77215 6.73167 2.875C6.75083 3.06667 6.76833 3.21917 6.785 3.335C6.95061 4.49077 7.29 5.61486 7.79167 6.66917C7.87083 6.83583 7.81917 7.035 7.66917 7.14167L5.87083 8.42667C6.97038 10.9887 9.01212 13.0305 11.5742 14.13L12.8575 12.335C12.91 12.2617 12.9865 12.2091 13.0737 12.1864C13.161 12.1637 13.2535 12.1723 13.335 12.2108C14.3892 12.7116 15.513 13.0501 16.6683 13.215C16.7842 13.2317 16.9367 13.25 17.1267 13.2683C17.2294 13.2789 17.3245 13.3271 17.3936 13.4038C17.4628 13.4805 17.501 13.5801 17.5008 13.6833H17.5Z"
                                    fill="#33404F" />
                                <path d="M12.5 1.66666V7.49999M12.5 7.49999H18.3333M12.5 7.49999L18.3333 1.66666" stroke="#008344"
                                      stroke-width="2" />
                            </g>
                            <defs>
                                <clipPath id="clip0">
                                    <path d="M0 0L20 0V20H0L0 0Z" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                        <?php elseif ($callType === CallLogType::IN && (int)$call['cl_status_id'] === CallLogStatus::NOT_ANSWERED): ?>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">-->
                                <path
                                    d="M17.5 13.6833V16.63C17.5001 16.841 17.4202 17.0441 17.2763 17.1985C17.1325 17.3528 16.9355 17.4469 16.725 17.4617C16.3608 17.4867 16.0633 17.5 15.8333 17.5C8.46917 17.5 2.5 11.5308 2.5 4.16667C2.5 3.93667 2.5125 3.63917 2.53833 3.275C2.5531 3.06454 2.64715 2.86751 2.8015 2.72367C2.95585 2.57984 3.15902 2.4999 3.37 2.5H6.31667C6.42003 2.4999 6.51975 2.53822 6.59644 2.60752C6.67313 2.67682 6.72133 2.77215 6.73167 2.875C6.75083 3.06667 6.76833 3.21917 6.785 3.335C6.95061 4.49077 7.29 5.61486 7.79167 6.66917C7.87083 6.83583 7.81917 7.035 7.66917 7.14167L5.87083 8.42667C6.97038 10.9887 9.01212 13.0305 11.5742 14.13L12.8575 12.335C12.91 12.2617 12.9865 12.2091 13.0737 12.1864C13.161 12.1637 13.2535 12.1723 13.335 12.2108C14.3892 12.7116 15.513 13.0501 16.6683 13.215C16.7842 13.2317 16.9367 13.25 17.1267 13.2683C17.2294 13.2789 17.3245 13.3271 17.3936 13.4038C17.4628 13.4805 17.501 13.5801 17.5008 13.6833H17.5Z"
                                    fill="#33404F" />
                                <path d="M18.3333 1.66667L12.5 7.5M12.5 1.66667L18.3333 7.5" stroke="#C60000" stroke-width="2" />
                            </svg>
                        <?php else: ?>
                            <div class="contact-info-card__call-icon">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.3617 14.6833V17.63C17.3618 17.841 17.2819 18.0441 17.138 18.1985C16.9942 18.3528 16.7972 18.4469 16.5867 18.4617C16.2225 18.4867 15.925 18.5 15.695 18.5C8.33086 18.5 2.36169 12.5308 2.36169 5.16667C2.36169 4.93667 2.37419 4.63917 2.40003 4.275C2.4148 4.06454 2.50885 3.86751 2.6632 3.72367C2.81755 3.57984 3.02071 3.4999 3.23169 3.5H6.17836C6.28173 3.4999 6.38144 3.53822 6.45814 3.60752C6.53483 3.67682 6.58302 3.77215 6.59336 3.875C6.61253 4.06667 6.63003 4.21917 6.64669 4.335C6.81231 5.49077 7.1517 6.61486 7.65336 7.66917C7.73253 7.83583 7.68086 8.035 7.53086 8.14167L5.73253 9.42667C6.83207 11.9887 8.87382 14.0305 11.4359 15.13L12.7192 13.335C12.7717 13.2617 12.8482 13.2091 12.9354 13.1864C13.0227 13.1637 13.1152 13.1723 13.1967 13.2108C14.2509 13.7116 15.3747 14.0501 16.53 14.215C16.6459 14.2317 16.7984 14.25 16.9884 14.2683C17.0911 14.2789 17.1862 14.3271 17.2553 14.4038C17.3244 14.4805 17.3627 14.5801 17.3625 14.6833H17.3617Z" fill="#33404F"/>
                                    <path d="M18.1951 8.5V2.66667M18.1951 2.66667L12.3617 2.66667M18.1951 2.66667L12.3617 8.5" stroke="#1898C0" stroke-width="2"/>
                                </svg>
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

