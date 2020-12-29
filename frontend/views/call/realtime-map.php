<?php
/* @var $this yii\web\View */
/* @var $cfChannels array */
/* @var $cfConnectionUrl string */
/* @var $cfToken string */
/* @var $cfChannelName string */

$this->title = 'Realtime Call Map';

/*$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
//$this->registerJs($js, \yii\web\View::POS_READY);*/
// $bundle = \frontend\assets\TimerAsset::register($this);
$userId = Yii::$app->user->id;
$dtNow = date('Y-m-d H:i:s');


$cfChannelsJs = '["' . implode('", "', $cfChannels) . '"]';

?>



<style>
    #realtime-map-page table {margin-bottom: 5px}
</style>
<div id="realtime-map-page" class="col-md-12">
    <div class="row">
        <div class="col-md-2">
            <div id="sales-department" class="card card-default d-none" style="margin-bottom: 20px;">
                <div class="card-header"><i class="fa fa-users"></i> OnLine - Department SALES (<span id="card-sales-header-count"></span>)</div>
                <div id="card-sales" class="card-body">
                    <!-- User List: from SALES department -->
                </div>
            </div>

            <div id="exchange-department" class="card card-default d-none" style="margin-bottom: 20px;">
                <div class="card-header"><i class="fa fa-users"></i> OnLine - Department EXCHANGE (<span id="card-exchange-header-count"></span>)</div>
                <div id="card-exchange" class="card-body">
                    <!-- User List: from EXCHANGE department -->
                </div>
            </div>

            <div id="support-department" class="card card-default d-none" style="margin-bottom: 20px;">
                <div class="card-header"><i class="fa fa-users"></i> OnLine - Department SUPPORT (<span id="card-support-header-count"></span>)</div>
                <div id="card-support" class="card-body">
                    <!-- User List: from SUPPORT department -->
                </div>
            </div>

            <div id="non-department" class="card card-default d-none" style="margin-bottom: 20px;">
                <!--<div class="card-header"><i class="fa fa-users"></i> OnLine Users - W/O Department (1)</div>-->
                <div id="card-other" class="card-body">
                    <!-- User List: from W/O department -->
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-default">
                <div class="card-header"><i class="fa fa-list"></i> Calls in IVR, DELAY, QUEUE, RINGING, PROGRESS (Updated: <i class="fa fa-clock-o"></i> <span id="page-updated-time"></span>)</div>
                <div id="card-live-calls" class="card-body">
                    <!-- Calls in IVR, DELAY, QUEUE, RINGING, PROGRESS -->
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-default">
                <div class="card-header"><i class="fa fa-list"></i> Last 10 ended Calls</div>
                <div id="card-history-calls" class="card-body">
                    <!--Last 10 ended Calls-->
                </div>
            </div>
        </div>

    </div>
</div>

<?php
$js = <<<JS
let cfChannelName = '$cfChannelName';
let cfChannels = $cfChannelsJs;
let cfToken = '$cfToken';
let cfConnectionUrl = '$cfConnectionUrl';

var centrifuge = new Centrifuge(cfConnectionUrl, {debug: false});
centrifuge.setToken(cfToken);

centrifuge.on('connect', function(ctx){
    console.log('Connected over ' + ctx.transport);
    
    var subscription = centrifuge.subscribe(cfChannelName, function(message) {
        console.log('centrifuge.subscribe');
        //let messageObj = JSON.parse(message.data.message);
        console.log(message);
    });

    subscription.on('ready', function(){
        subscription.presence(function(message) {
            console.log('subscription.presence');        
            // information about who connected to channel at moment received
        });
        subscription.history(function(message) {
            console.log('subscription.history');
            // information about last messages sent into channel received
        });
        subscription.on('join', function(message) {
            console.log('subscription.join');
            // someone connected to channel
        });
        subscription.on('leave', function(message) {
            console.log('subscription.leave');
            // someone disconnected from channel
        });
    });

    
});

centrifuge.on('disconnect', function(ctx){
    console.log('Disconnected: ' + ctx.reason);
});


// function channelConnector(chName) {    
//    
// }

centrifuge.connect();

// centrifuge.on('connect', function(context) {        
//     console.info('Client connected to Centrifugo and authorized');
//     //contentUpdate()
// });
JS;

$this->registerJs($js);