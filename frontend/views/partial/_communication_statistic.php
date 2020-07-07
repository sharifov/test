<?php

/**
 * @var StatisticsHelper $statistics
 *
 */

use sales\helpers\communication\StatisticsHelper;

?>

<div class="box-statistics">
    <i class="fa fa-phone success" aria-hidden="true" title="<?php echo $statistics::HINT_CALLS?>"></i>
        <sup><?php echo $statistics->callCount ?></sup>&nbsp;|&nbsp;
    <i class="fa fa-comments info" aria-hidden="true" title="<?php echo $statistics::HINT_SMS?>"></i>
        <sup><?php echo $statistics->smsCount ?></sup>&nbsp;|&nbsp;
    <i class="fa fa-envelope danger" aria-hidden="true" title="<?php echo $statistics::HINT_EMAILS?>"></i>
        <sup><?php echo $statistics->emailCount ?></sup>&nbsp;|&nbsp;
    <i class="fa fa-weixin warning" aria-hidden="true" title="<?php echo $statistics::HINT_CHATS?>"></i>
        <sup><?php echo $statistics->clientChatCount ?></sup>
</div>

<?php
$css = <<<CSS
    .box-statistics {    
    }
CSS;
$this->registerCss($css);
