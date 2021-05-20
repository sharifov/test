<?php

/** @var array $callHistory */
/** @var int $page */
/** @var int $userId */
use sales\helpers\call\CallHelper;

$userCanAddPhoneBlackList = \sales\guards\phone\PhoneBlackListGuard::canAdd($userId);
?>

<?php foreach ($callHistory as $key => $day) : ?>
    <?php if ($day) : ?>
        <span class="section-separator"><?= $key ?></span>
        <ul class="phone-widget__list-item calls-history<?php if ($key === 'Today' && $page === 1) {
            echo ' history-tab-today-first-block';
                                                        }?>">
            <?php
            foreach ($day as $call) {
                $call['cl_call_created_dt'] = \Yii::$app->formatter->asDate(strtotime($call['cl_call_created_dt']), 'php:h:i A');
                echo CallHelper::formCallToHistoryTab($call, $userCanAddPhoneBlackList);
            }
            ?>
        </ul>
    <?php endif; ?>

<?php endforeach; ?>
