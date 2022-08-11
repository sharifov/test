<?php

/* @var $this yii\web\View */
/* @var $cfConnectionUrl string */
/* @var $cfToken string */
/* @var $cfChannelName string */
/* @var $cfUserOnlineChannel string */
/* @var $cfUserStatusChannel string */

use frontend\assets\MonitorCallIncomingAsset;

$this->title = 'Realtime Call Map';

MonitorCallIncomingAsset::register($this);
?>

<div id="app" class="container"
     data-cfchannelname="<?= $cfChannelName ?>"
     data-cfuseronlinechannel="<?= $cfUserOnlineChannel ?>"
     data-cftoken="<?= $cfToken ?>"
     data-cfconnectionurl="<?= $cfConnectionUrl ?>"
     data-cfuserstatuschannel="<?= $cfUserStatusChannel ?>"></div>
