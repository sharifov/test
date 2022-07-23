<?php

/* @var $this yii\web\View */
/* @var $cfConnectionUrl string */
/* @var $cfToken string */
/* @var $cfChannelName string */
/* @var $cfUserOnlineChannel string */
/* @var $cfUserStatusChannel string */

use frontend\assets\MonitorCallIncomingAsset;
use yii\web\View;

$this->title = 'Realtime Call Map';

MonitorCallIncomingAsset::register($this);
?>

    <?= $this->render('vue/_call_item_tpl')?>

    <div id="realtime-map-app" class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_title">
                      <h2>Online Users  (<span v-cloak style="color: inherit">{{ onlineUserCounter }}</span>), TimeZone: <span v-cloak style="color: inherit">{{ userTimeZone }}</span></h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <transition-group name="fade" tag="div" class="card-body">
                            <div v-for="(item, index) in userDataList()" class="list-item truncate" :key="item" style="width: 150px;">
                                <user-component :item="item" :key="item.uo_user_id" :index="index"></user-component>
                            </div>
                        </transition-group>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Filters: </h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li>
                                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="col-md-4">
                                    <div class="form-group field-leadsearch-employee_id">
                                        <label class="control-label" for="leadsearch-employee_id">Select department:</label>
                                        <select v-model="filters.selectedDep" class="form-control" v-on:input="selectDepartment($event.target.value)">
                                            <option v-for="option in depListData()" :value="option.id">
                                                {{ option.label }}
                                            </option>
                                        </select>
                                        <div class="help-block"></div>
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
                                        <div class="count" v-cloak>{{ callList.length }}</div>
                                    </div>
                                    <div class="col-md-4 col-sm-4  tile_stats_count">
                                        <span class="count_top"><i class="fa fa-recycle"></i> IVR</span>
                                        <div class="count" v-cloak>{{ ivrCounter }}</div>
                                    </div>
                                    <div class="col-md-4 col-sm-4  tile_stats_count">
                                        <span class="count_top"><i class="fa fa-pause"></i> Queue</span>
                                        <div class="count" v-cloak>{{ queueCounter }}</div>
                                    </div>
                                    <div class="col-md-4 col-sm-4  tile_stats_count">
                                        <span class="count_top"><i class="fa fa-phone"></i> InProgress / Ringing</span>
                                        <div class="count" v-cloak>{{ inProgressCounter }} / {{ ringingCounter }}</div>
                                    </div>
                                    <div class="col-md-4 col-sm-4  tile_stats_count">
                                        <span class="count_top"><i class="fa fa-stop"></i> Delay / Hold</span>
                                        <div class="count"v-cloak>{{ delayCounter }} / {{ holdCounter }}</div>
                                    </div>
                                    <div class="col-md-4 col-sm-4  tile_stats_count">
                                        <span class="count_top"><i class="fa fa-user"></i> Idle / OnLine</span>
                                        <div class="count" v-cloak>{{ idleUserList.length }} / {{ onlineUserCounter }}</div>
                                    </div>
                                </div>
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
                      <h2>Calls in Queue: IVR, Queued, Hold, Delay (<span v-cloak style="color: inherit">{{ callListQueued.length }} </span>)</h2>
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
                      <h2>Calls in Progress: Ringing, In Progress (<span v-cloak style="color: inherit">{{ callListInProgress.length }}</span>)</h2>
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
let cfUserStatusChannel = '$cfUserStatusChannel';

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
            callMapApp.actionCall(jsonData.data.call);
        } 
    });
    
    var subUserOnline = centrifuge.subscribe(cfUserOnlineChannel, function(message) {
        let jsonData = message.data;
        if (jsonData.object === 'userOnline') {
            if (jsonData.action === 'delete') {
                callMapApp.deleteUserData(jsonData.data.userOnline);
            } else {
                //console.info(jsonData.data);
                callMapApp.addUserData(jsonData.data.userOnline, 'online');
            }
        }
    });
    
    var subUserStatus = centrifuge.subscribe(cfUserStatusChannel, function(message) {
        let jsonData = message.data;
        // console.log(jsonData.data);
        if (jsonData.object === 'userStatus') {
            if (jsonData.action === 'delete') {
                callMapApp.deleteUserData(jsonData.data.userStatus);
            } else {
                callMapApp.addUserData(jsonData.data.userStatus, 'status');
            }
        }
        //console.log(jsonData.data.userOnline.uo_idle_state);
    });
});

centrifuge.on('disconnect', function(ctx){
    console.log('Disconnected: ' + ctx.reason);
});
centrifuge.connect();
JS;

$this->registerJs($js);

$css = <<<CSS
[v-cloak] {display: none}
CSS;

$this->registerCss($css, ['position' => View::POS_HEAD]);

