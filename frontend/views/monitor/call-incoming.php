<?php

/* @var $this yii\web\View */
/* @var $cfConnectionUrl string */
/* @var $cfToken string */
/* @var $cfChannelName string */
/* @var $cfUserOnlineChannel string */

use frontend\assets\MonitorCallIncomingAsset;

$this->title = 'Realtime Call Map';

MonitorCallIncomingAsset::register($this);
?>

    <?= $this->render('vue/_call_item_tpl')?>

    <div id="realtime-map-app" class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Online Users  ({{ onlineUserCounter }}), TimeZone: {{ userTimeZone }}</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <transition-group name="fade2" tag="div" class="card-body">
                            <div v-for="(item, index) in onlineUserList" class="list-item col-md-2 truncate" :key="item">
                                <i v-if="item.us_is_on_call ? 'true' : 'false'" :class="'fa fa-phone text-success'"></i>
                                <i v-else-if="item.us_call_phone_status ? 'true' : 'false'" :class="'fa fa-tty text-danger'"></i>
                                <i v-else-if="item.us_has_call_access ? 'true' : 'false'" :class="'fa fa-random'"></i> {{ getUserName(item.uo_user_id) }}
                            </div>
                        </transition-group>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Metrics</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="tile_count">
                            <div class="col-md-4 col-sm-4  tile_stats_count">
                                <span class="count_top"><i class="fa fa-list"></i> Call Items</span>
                                <div class="count">{{ callList.length }}</div>
                            </div>
                            <div class="col-md-4 col-sm-4  tile_stats_count">
                                <span class="count_top"><i class="fa fa-recycle"></i> IVR</span>
                                <div class="count">{{ ivrCounter }}</div>
                            </div>
                            <div class="col-md-4 col-sm-4  tile_stats_count">
                                <span class="count_top"><i class="fa fa-pause"></i> Queue</span>
                                <div class="count">{{ queueCounter }}</div>
                            </div>
                            <div class="col-md-4 col-sm-4  tile_stats_count">
                                <span class="count_top"><i class="fa fa-phone"></i> InProgress / Ringing</span>
                                <div class="count">{{ inProgressCounter }} / {{ ringingCounter }}</div>
                            </div>
                            <div class="col-md-4 col-sm-4  tile_stats_count">
                                <span class="count_top"><i class="fa fa-stop"></i> Delay / Hold</span>
                                <div class="count">{{ delayCounter }} / {{ holdCounter }}</div>
                            </div>
                            <div class="col-md-4 col-sm-4  tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Idle / OnLine</span>
                                <div class="count">{{ idleUserList.length }} / {{ onlineUserCounter }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Calls in Queue: IVR, Queued, Hold, Delay ({{ callListQueued.length }})</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <transition-group name="list" tag="div" class="card-body row">
                            <div v-for="(item, index) in callListQueued" class="list-item col-md-12" :key="item">
                                <call-item-component :item="item" :key="item.с_id" :index="index"></call-item-component>
                            </div>
                        </transition-group>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Calls in Progress: Ringing, In Progress ({{ callListInProgress.length }})</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <transition-group name="list" tag="div" class="card-body row">
                            <div v-for="(item, index) in callListInProgress" class="list-item col-md-12" :key="item">
                                <call-item-component :item="item" :key="item.с_id" :index="index"></call-item-component>
                            </div>
                        </transition-group>
                    </div>
                </div>
            </div>

        </div>
    </div>



<?php
$js = <<<JS
let cfChannelName = '$cfChannelName';
let cfUserOnlineChannel = '$cfUserOnlineChannel';
let cfToken = '$cfToken';
let cfConnectionUrl = '$cfConnectionUrl';

var centrifuge = new Centrifuge(cfConnectionUrl, {debug: false});
centrifuge.setToken(cfToken);

centrifuge.on('connect', function(ctx){
    console.log('Connected over ' + ctx.transport);
    
    var subscription = centrifuge.subscribe(cfChannelName, function(message) {
        let jsonData = message.data;
        
        if (jsonData.object === 'callUserAccess') {
            if (jsonData.action === 'delete') {
                callMapApp.deleteCallUserAccess(jsonData.data.callUserAccess);
            } else {
                callMapApp.addCallUserAccess(jsonData.data.callUserAccess);
            }
        } else if (jsonData.object === 'call') {
            callMapApp.addCall(jsonData.data.call);
        } 
    });
    
    var subUserOnline = centrifuge.subscribe(cfUserOnlineChannel, function(message) {
        let jsonData = message.data;
        if (jsonData.object === 'userOnline') {
            if (jsonData.action === 'delete') {
                callMapApp.deleteUserOnline(jsonData.data.userOnline);
            } else {
                //console.info(jsonData.data);
                callMapApp.addUserOnline(jsonData.data.userOnline);
            }
        }
    });
});

centrifuge.on('disconnect', function(ctx){
    console.log('Disconnected: ' + ctx.reason);
});
centrifuge.connect();
JS;

$this->registerJs($js);
