<?php

/**
 * @var StatisticsHelper $statistics
 *
 */

use sales\helpers\communication\StatisticsHelper;

?>

    <div class="row">
        <div class="col-sm-1"><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo $statistics->emailCount ?></div>
        <div class="col-sm-1"><i class="fa fa-comments" aria-hidden="true"></i> <?php echo $statistics->smsCount ?></div>
        <div class="col-sm-1"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo $statistics->callCount ?></div>
        <div class="col-sm-1"><i class="fa fa-weixin" aria-hidden="true"></i> <?php echo $statistics->clientChatCount ?></div>
        <div></div>
    </div>
