<?php

/**
 * @var StatisticsHelper $statistics
 *
 */

use src\auth\Auth;
use src\helpers\communication\StatisticsHelper;
use src\model\clientChat\entity\search\ClientChatQaSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use modules\featureFlag\FFlag;

$linkAttributes = ['target' => '_blank', 'data-pjax' => '0'];
?>
<div class="row box-statistics">
    <div class="col-3">
        <i class="fa fa-phone" aria-hidden="true" title="<?= Html::encode($statistics::HINT_CALLS) ?>"></i>
        <strong><?php echo $statistics::HINT_CALLS ?>: </strong>
        <?php
            $text = $statistics->callCount;
        if (Auth::can('/call/index')) {
            $paramName = $statistics->isTypeCase() ? 'case_id' : 'lead_id';
            echo Html::a(
                $text,
                Url::to(['/call-log/index', 'CallLogSearch[' . $paramName . ']' => $statistics->getId()]),
                $linkAttributes
            );
        } else {
            echo $text;
        }
        ?>
    </div>
    <div class="col-3">
        <i class="fas fa-sms" aria-hidden="true" title="<?= Html::encode($statistics::HINT_SMS) ?>"></i>
        <strong><?php echo $statistics::HINT_SMS ?>: </strong>
        <?php
            $text = $statistics->smsCount;
        if (Auth::can('/sms/index')) {
            $paramName = $statistics->isTypeCase() ? 's_case_id' : 's_lead_id';
            echo Html::a(
                $text,
                Url::to(['/sms/index', 'SmsSearch[' . $paramName . ']' => $statistics->getId()]),
                $linkAttributes
            );
        } else {
            echo $text;
        }
        ?>
    </div>
    <div class="col-3">
        <i class="fa fa-envelope" aria-hidden="true" title="<?= Html::encode($statistics::HINT_EMAILS) ?>"></i>
        <strong><?php echo $statistics::HINT_EMAILS ?></strong>
        <?php
            $text = $statistics->emailCount;
        if (Auth::can('/email/index')) {
            $paramName = $statistics->isTypeCase() ? 'e_case_id' : 'e_lead_id';
            $url = (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE)) ? '/email-normalized/index' : '/email/index';
            echo Html::a(
                $text,
                Url::to([$url, 'EmailSearch[' . $paramName . ']'  => $statistics->getId()]),
                $linkAttributes
            );
        } else {
            echo $text;
        }
        ?>
    </div>
    <div class="col-3">
        <i class="fa fa-comments-o" aria-hidden="true" title="<?= Html::encode($statistics::HINT_CHATS) ?>"></i>
        <strong><?php echo $statistics::HINT_CHATS ?></strong>
        <?php
            $text = $statistics->clientChatCount;
        if (Auth::can('/client-chat-crud/index')) {
            $paramName = $statistics->isTypeCase() ? 'caseId' : 'leadId';

            echo Html::a(
                $text,
                Url::to([
                    '/client-chat-crud/index',
                    'ClientChatQaSearch[' . $paramName . ']'  => $statistics->getId(),
                ]),
                $linkAttributes
            );
        } else {
            echo $text;
        }
        ?>
    </div>
</div>

