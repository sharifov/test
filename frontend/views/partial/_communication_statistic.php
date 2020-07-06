<?php

/**
 * @var StatisticsHelper $statistics
 *
 */

use sales\helpers\communication\StatisticsHelper;

?>

<div class="box-statistics">
    <i class="fa fa-envelope" aria-hidden="true" title="<?php echo $statistics::HINT_EMAILS?>"></i>
        <sup><?php echo $statistics->emailCount ?></sup>&nbsp;&nbsp;
    <i class="fa fa-comments" aria-hidden="true" title="<?php echo $statistics::HINT_SMS?>"></i>
        <sup><?php echo $statistics->smsCount ?></sup>&nbsp;&nbsp;
    <i class="fa fa-phone" aria-hidden="true" title="<?php echo $statistics::HINT_CALLS?>"></i>
        <sup><?php echo $statistics->callCount ?></sup>&nbsp;&nbsp;
    <i class="fa fa-weixin" aria-hidden="true" title="<?php echo $statistics::HINT_CHATS?>"></i>
        <sup><?php echo $statistics->clientChatCount ?></sup>&nbsp;&nbsp;
</div>

<?php
$css = <<<CSS
    .box-statistics {    
    }
CSS;
$this->registerCss($css);
