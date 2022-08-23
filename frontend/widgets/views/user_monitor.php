<?php

use common\models\UserConnection;
use src\model\user\entity\monitor\UserMonitor;
use yii\bootstrap4\Modal;

/* @var $userId integer */
/* @var $isAutoLogoutEnabled bool */
/* @var $isIdleMonitorEnabled bool */
/* @var $this \yii\web\View */


if ($isIdleMonitorEnabled) {
    \frontend\assets\IdleAsset::register($this);
}

$bundle = \frontend\assets\TimerAsset::register($this);
if ($isAutoLogoutEnabled) {
    \frontend\assets\BroadcastChannelAsset::register($this);
}


?>

<?php if ($isIdleMonitorEnabled) : ?>
    <li>
        <a href="javascript:;" class="info-number" title="User Monitor" id="user-monitor-indicator">
            <div class="text-success"><i class="fa fa-clock-o"></i> <span id="user-monitor-timer"></span></div>
        </a>
    </li>
<?php endif; ?>


<?php if ($isAutoLogoutEnabled) : ?>
    <?php Modal::begin([
        'id' => 'modal-autologout',
        'closeButton' => false,
        'title' => '<i class="fa fa-power-off"></i> Auto LogOut',
        'size' => Modal::SIZE_SMALL,
        'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
    ])?>
        <div class="text-center">
            <p>You are not active for a long time (<?=UserMonitor::autologoutIdlePeriodMin()?> min.). After a few seconds, the system will automatically log out.</p>
            <?php if (UserMonitor::autoLogoutTimerSec()) : ?>
                <h1 id="autologout-timer" class="text-danger">00:00</h1>
            <?php endif; ?>
            <p><b>Do you want to continue working?</b></p>
            <button class="btn btn-danger" id="btn-logout"><i class="fa fa-power-off"></i> LogOut</button>
            <button class="btn btn-success" id="btn-cancel-autologout"><i class="fa fa-check"></i> Continue working</button>
        </div>
    <?php Modal::end()?>
<?php endif; ?>


<?php

if ($isAutoLogoutEnabled) {
    $isAutoLogoutShowMessage = UserMonitor::isAutologoutShowMessage() ? 'true' : 'false';
    $isAutoLogoutTimerSec = UserMonitor::autoLogoutTimerSec();

    $js = <<<JS
const isAutoLogoutShowMessage = $isAutoLogoutShowMessage;
const isAutoLogoutTimerSec = $isAutoLogoutTimerSec;

$('#btn-cancel-autologout').on('click', function () {
     cancelAutoLogout();
});

$('#btn-logout').on('click', function () {
     if (isAutoLogoutShowMessage) {
        $('#modal-autologout').modal('hide');
     }
     logout();
});

const channel = new BroadcastChannel('tabCommands');

channel.onmessage = function(e) {
    if (e.data.event === 'stopAutoLogout') {
        stopAutoLogout();      
    } else if (e.data.event === 'logout') {
        location.assign('/site/logout?type=autologout');
    }
};

function logout() {
    // window.location.href = '/site/logout?type=autologout';
    channel.postMessage({event: 'logout'});
    location.assign('/site/logout?type=autologout');
}

function cancelAutoLogout() {
    stopAutoLogout();
    channel.postMessage({event: 'stopAutoLogout'});
    return false;
}

function stopAutoLogout() {
    $('#autologout-timer').timer('remove');
    
    if (isAutoLogoutShowMessage) {
        $('#modal-autologout').modal('hide');
    }
    return false;
}

window.autoLogout = function (timerSec = isAutoLogoutTimerSec, isShowMessage = isAutoLogoutShowMessage) {
    if (isShowMessage === 'true') {
        $('#modal-autologout').modal({show: true});
    }
    
    if (timerSec > 0) {
        $('#autologout-timer').timer('remove').timer({countdown: true, format: '%M:%S', seconds: 0, duration: timerSec + 's', callback: function() {
            $('#autologout-timer').timer('remove');
            logout();
        }}).timer('start');
    }
}
JS;

    $this->registerJs($js, \yii\web\View::POS_READY, 'autologout-js');
}


if ($isIdleMonitorEnabled) {
    $idleMs = UserConnection::idleSeconds() * 1000;
    $js = <<<JS
function setIdle() {
    let objDiv = $('#user-monitor-indicator div');
    objDiv.attr('class', 'text-warning');
    objDiv.find('i').attr('class', 'fa fa-coffee');
    $('#user-monitor-timer').timer('remove').timer({format: '%M:%S', seconds: 0}).timer('start');
    //console.log('I\'m idle');
}

function setActive() {
    let objDiv = $('#user-monitor-indicator div');
    objDiv.attr('class', 'text-success');
    objDiv.find('i').attr('class', 'fa fa-clock-o');
    $('#user-monitor-timer').timer('remove').text('');//.timer({format: '%M:%S', seconds: 0}).timer('start');
    //console.log('Hey, I\'m active!');
}

var timerIdleId = '';

$(document).idle({
    onIdle: function(){
        socketSend('idle', 'set', { val: true });
        timerIdleId = setInterval(() => socketSend('idle', 'set', { val: true }), 60000);
        setIdle();
    },
    onActive: function(){
        socketSend('idle', 'set', { val: false });
        clearInterval(timerIdleId);
        setActive();
    },
    onHide: function(){
        socketSend('window', 'set', { val: false });
        //console.log('I\'m hidden');
    },
    onShow: function(){
        socketSend('window', 'set', { val: true });
        //console.log('Hey, I\'m visible!');
    },
    idle: $idleMs
});
JS;

    $this->registerJs($js, \yii\web\View::POS_READY, 'idle-js');
}
