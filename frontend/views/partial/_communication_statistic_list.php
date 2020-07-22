<?php

/**
 * @var StatisticsHelper $statistics
 * @var array $lastCommunication
 */

use frontend\helpers\OutHelper;
use sales\auth\Auth;
use sales\helpers\communication\StatisticsHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$linkAttributes = ['target' => '_blank', 'data-pjax'=> '0'];

$classMap = [
    'call' => 'fa-phone',
    'email' => 'fa-envelope',
    'sms' => 'fa-comments',
];

?>

<div class="box-statistics">
    <?php
        $text = '<i class="fa fa-phone success" aria-hidden="true" title="' . $statistics::HINT_CALLS . '"></i>
            <sup>' . $statistics->callCount . '</sup>';
        if (Auth::can('/call-log/index')) {
            $paramName = $statistics->isTypeCase() ? 'case_id' : 'lead_id';
            echo Html::a($text,
                Url::to(['/call-log/index', 'CallLogSearch[' . $paramName . ']' => $statistics->getId()]),
                $linkAttributes);
        } else {
             echo $text;
        }
    ?>&nbsp;|&nbsp;
    <?php
        $text = '<i class="fa fa-comments info" aria-hidden="true" title="' . $statistics::HINT_SMS . '"></i>
            <sup>' . $statistics->smsCount . '</sup>';
        if (Auth::can('/sms/index')) {
            $paramName = $statistics->isTypeCase() ? 's_case_id' : 's_lead_id';
            echo Html::a($text,
                Url::to(['/sms/index', 'SmsSearch[' . $paramName . ']' => $statistics->getId()]),
                $linkAttributes);
        } else {
             echo $text;
        }
    ?>&nbsp;|&nbsp;
    <?php
        $text = '<i class="fa fa-envelope danger" aria-hidden="true" title="' . $statistics::HINT_EMAILS . '"></i>
            <sup>' . $statistics->emailCount . '</sup>';
        if (Auth::can('/email/index')) {
            $paramName = $statistics->isTypeCase() ? 'e_case_id' : 'e_lead_id';
            echo Html::a($text,
                Url::to(['/email/index', 'EmailSearch[' . $paramName . ']'  => $statistics->getId()]),
                $linkAttributes);
        } else {
             echo $text;
        }
    ?>&nbsp;|&nbsp;
    <?php
        $text = '<i class="fa fa-weixin warning" aria-hidden="true" title="' . $statistics::HINT_CHATS . '"></i>
            <sup>' . $statistics->clientChatCount . '</sup>';
        if (Auth::can('/client-chat-crud/index')) {
            $paramName = $statistics->isTypeCase() ? 'cch_case_id' : 'cch_lead_id';
            echo Html::a($text,
                Url::to(['/client-chat-crud/index', 'ClientChatSearch[' . $paramName . ']'  => $statistics->getId()]),
                $linkAttributes);
        } else {
             echo $text;
        }
    ?>
</div>

<?php if (isset($lastCommunication)) :?>
    <div class="last-communication" style="text-align: left;">
        <?php foreach ($lastCommunication as $item) :?>
            <?php if (!empty($item['created_dt'])) :?>
                <?php
                    $out = '<i class="fa ' . $classMap[$item['type']] . '"></i>';
                    $out .= ' (' . $item['direction'] . ')';
                    $out .= ' ' . OutHelper::diffHoursMinutes(strtotime($item['created_dt'])) . ' ago<br /> ';
                    echo $out;
                ?>
            <?php endif ?>
        <?php endforeach ?>
    </div>
<?php endif ?>

<?php
$css = <<<CSS
    .box-statistics { 
        width: 140px; 
        white-space: nowrap;  
    }
CSS;
$this->registerCss($css);
