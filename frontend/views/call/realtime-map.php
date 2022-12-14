<?php
/* @var $this yii\web\View */
/* @var $cfConnectionUrl string */
/* @var $cfToken string */
/* @var $cfChannelName string */
/* @var $cfUserOnlineChannel string */
/* @var $cfUserStatusChannel string */

$this->title = 'Realtime Call Map';
//\frontend\assets\VueAsset::register($this);
?>

<style>
    @-webkit-keyframes alertPulse {
        0% {background-color: #f5dad8; color: #000000; opacity: 1;}
        50% {opacity: #ce5252; opacity: 0.75; }
        100% {opacity: #e85858; opacity: 1;}
    }

    #realtime-map-app table {margin: 2px 0 1px 0}
    .card-body {padding: 0 0 0 0}

    .crop-line {
        white-space: nowrap;
        overflow-x: hidden;
        text-overflow: ellipsis;
    }

    .truncate {
        width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .list-move {
        transition: transform 1.1s;
    }

    .list-item {
        display: inline-block;
    }
    .list-enter-active, .list-leave-active {
        transition: all 2.4s;
    }
    .list-enter, .list-leave-to {
        opacity: 0;
        transform: translateX(200px);
    }


    .list2-move {
        transition: transform 1.4s;
        /*opacity: 1;*/
        /*transition: opacity 2s ease-in-out;*/
    }

    .list2-item {
        display: inline-block;
    }
    .list2-enter-active, .list2-leave-active {
        transition: all 2.1s;
    }
    .list2-enter, .list2-leave-to {
        opacity: 0;
        transform: translateX(200px);
    }



    .fade-enter-active, .fade-leave-active {
        transition: opacity .2s;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }

    .fade2 {
        backface-visibility: hidden;
        z-index: 1;
    }

    /* moving */
    .fade2-move {
        /*transition: all 600ms ease-in-out 50ms;*/
        opacity: 1;
        transition: opacity 2s ease-in-out;
    }

    /* appearing */
    .fade2-enter-active {
        /*transition: all 800ms ease-out;*/
        animation: alertPulse 2s ease-out;
        animation-iteration-count: infinite;
        opacity: 1;
    }

    /* disappearing */
    .fade2-leave-active {
        /*transition: all 400ms ease-in;*/
        animation: scale-up-center 2s;
        position: absolute;
        z-index: 0;
    }

    /* appear at / disappear to */
    .fade2-enter,
    .fade2-leave-to {
        opacity: 0;
    }

</style>

<script type="text/x-template" id="call-item-tpl">
    <div v-if="show" class="col-md-12" style="margin-bottom: 0px">
        <table class="table table-condensed  table-bordered">
            <tbody>
            <tr>
                <td class="text-center" style="width:35px">
                    {{ index + 1 }}
                </td>
                <td class="text-center" style="width:80px">
                    <u><a :href="'/call/view?id=' + item.c_id" target="_blank">{{ item.c_id }}</a></u><br>
                    <b>{{ callTypeName }}</b>
                </td>
                <td class="text-center" style="width:90px">
                    <i class="fa fa-clock-o"></i> {{ createdDateTime("HH:mm") }}<br>
                    <span v-if="item.c_source_type_id">{{ callSourceName }}</span>
                </td>

                <td class="text-center" style="width:140px">
                    <span class="badge badge-info">{{ projectName }}</span><br>
                    <span v-if="item.c_dep_id" class="label label-default">{{ departmentName }}</span>
                </td>
                <td class="text-left" style="width:70px">
                    <?php //<img v-if="getCountryByPhoneNumber(item.c_from)" :src="'https://purecatamphetamine.github.io/country-flag-icons/3x2/' + getCountryByPhoneNumber(item.c_from) + '.svg'" width="20"/> &nbsp; ?>
                    <img v-if="getCountryByPhoneNumber(item.c_from)" :src="'https://flagcdn.com/20x15/' + getCountryByPhoneNumber(item.c_from).toLowerCase() + '.png'" width="20" height="15" :alt="getCountryByPhoneNumber(item.c_from)"/>
                    {{ getCountryByPhoneNumber(item.c_from) }}
                </td>
                <?php /*<td class="text-left" style="width:110px">
                    <small v-if="item.c_from_country">
                        <?php //<img :src="'https://flagcdn.com/' + item.c_from_state.toLowerCase() + '.svg'" width="20"/> &nbsp; ?>
                        {{ item.c_from_country }}
                    </small>
                </td>*/ ?>

                <td class="text-left" style="width:180px">
                    <div v-if="item.c_client_id" class="crop-line">
                        <i class="fa fa-male text-info fa-1x fa-border"></i>&nbsp;
                        <span v-if="item.client">
                            <a :href="'/client/view?id=' + item.c_client_id" target="_blank">
                                <small style="text-transform: uppercase">{{ clientFullName }}</small>
                            </a>
                        </span>
                    </div>
                    <i class="fa fa-phone fa-1x fa-border"></i> {{ formatPhoneNumber(item.c_from) }}
                </td>

                <td class="text-center" style="width:120px">
                    <b>{{ callStatusName }}</b>
                </td>
                <td class="text-center" style="width:120px">
                    <timer :fromDt="callStatusTimerDateTime"></timer>
                </td>

                <td class="text-left" style="width:160px">
                    <div v-if="item.c_created_user_id">
                        <i class="fa fa-user fa-1x fa-border text-success"></i>
                        {{ getUserName(item.c_created_user_id) }}<br>
                        <i class="fa fa-phone fa-1x fa-border"></i>
                        <small>{{ formatPhoneNumber(item.c_to) }}</small>
                    </div>
                    <div v-else>
                        <i class="fa fa-phone fa-1x fa-border"></i>
                        {{ formatPhoneNumber(item.c_to) }}
                    </div>
                </td>
                <td></td>
           </tr>
            </tbody>
        </table>
        <div v-if="item.userAccessList && item.userAccessList.length > 0" class="text-right" style="margin-bottom: 5px">
            <transition-group name="fade">
            <span class="label" :class="{ 'label-success': access.cua_status_id == 2, 'label-default': access.cua_status_id != 2 }"
                  v-for="(access, index) in item.userAccessList" :key="access.cua_user_id"
                  style="margin-right: 4px" :title="getUserAccessStatusTypeName(access.cua_status_id)">
                <i class="fa fa-user"></i> {{ getUserName(access.cua_user_id) }}
            </span>
            </transition-group>
        </div>
    </div>
</script>

<div id="realtime-map-app" class="col-md-12">


    <div class="top_tiles row">
            <div class="animated flipInY col-md-2 col-sm-6 ">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count">{{ callList.length }}</div>
                    <h3>Call Items</h3>
                </div>
            </div>
            <div class="animated flipInY col-md-2 col-sm-6 ">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-recycle"></i></div>
                    <div class="count">{{ ivrCounter }}</div>
                    <h3>IVR</h3>
                </div>
            </div>
            <div class="animated flipInY col-md-2 col-sm-6 ">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-pause"></i></div>
                    <div class="count">{{ queueCounter }}</div>
                    <h3>Queue</h3>
                </div>
            </div>
            <div class="animated flipInY col-md-2 col-sm-6 ">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-phone"></i></div>
                    <div class="count">{{ inProgressCounter }} / {{ ringingCounter }}</div>
                    <h3>InProgress / Ringing</h3>
                </div>
            </div>
            <div class="animated flipInY col-md-2 col-sm-6 ">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-stop"></i></div>
                    <div class="count">{{ delayCounter }} / {{ holdCounter }}</div>
                    <h3>Delay / Hold</h3>
                </div>
            </div>

            <div class="animated flipInY col-md-2 col-sm-6 ">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-user"></i></div>
                    <div class="count">{{ idleUserList.length }} / {{ onlineUserCounter }}</div>
                    <h3>Idle / OnLine</h3>
                </div>
            </div>
        </div>


    <div class="row">
        <div class="col-md-5" >
            <div class="card card-default">
                <div class="card-header"> All Calls in IVR, QUEUE ({{ callList1.length }})</div>
                <transition-group name="list" tag="div" class="card-body row">
                    <div v-for="(item, index) in callList1" class="list-item col-md-12" :key="item">
                        <call-item-component :item="item" :key="item.??_id" :index="index"></call-item-component>
                    </div>
                </transition-group>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-default">
                <div class="card-header"> All Calls RINGING, in PROGRESS, DELAY, HOLD ({{ callList2.length }})</div>
                <transition-group name="list2" tag="div" class="card-body row">
                    <div v-for="(item, index) in callList2" class="list-item col-md-12" :key="item">
                        <call-item-component :item="item" :key="item.??_id" :index="index"></call-item-component>
                    </div>
                </transition-group>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card card-default" style="min-height: 98%">
                <div class="card-header"> Online Users  ({{ onlineUserCounter }}), TimeZone: {{ userTimeZone }}</div>
                <transition-group name="fade2" tag="div" class="card-body">
                    <div v-for="(item, index) in userOnlineList()" class="list-item col-md-6 truncate" :key="item">
                        <user-component :item="item" :key="item.uo_user_id" :index="index"></user-component>
                    </div>
                </transition-group>
            </div>
        </div>

    </div>
</div>



<?php
$js = <<<JS
let cfChannelName = '$cfChannelName';
let cfUserOnlineChannel = '$cfUserOnlineChannel';
let cfUserStatusChannel = '$cfUserStatusChannel';
let cfToken = '$cfToken';
let cfConnectionUrl = '$cfConnectionUrl';

var centrifuge = new Centrifuge(cfConnectionUrl, {debug: false});
centrifuge.setToken(cfToken);

centrifuge.on('connect', function(ctx){
    console.log('Connected over ' + ctx.transport);
    
    var subscription = centrifuge.subscribe(cfChannelName, function(message) {
        let jsonData = message.data;
        //console.log(jsonData.data);
        
        if (jsonData.object === 'callUserAccess') {
            if (jsonData.action === 'delete') {
                callMapApp.deleteCallUserAccess(jsonData.data.callUserAccess);
            } else {
                callMapApp.addCallUserAccess(jsonData.data.callUserAccess);
            }
        } else if (jsonData.object === 'call') {
            //callMapApp.addCall(jsonData.data.call);
            //let data = JSON.parse(jsonData.data);
            callMapApp.actionCall(jsonData.data.call);
        } 
        //console.log(message);
    });
    
    var subUserOnline = centrifuge.subscribe(cfUserOnlineChannel, function(message) {
        let jsonData = message.data;
        // console.log(jsonData.data);
        if (jsonData.object === 'userOnline') {
            if (jsonData.action === 'delete') {
                callMapApp.deleteUserOnline(jsonData.data.userOnline);
            } else {
                //console.info(jsonData.data);
                callMapApp.addUserOnline(jsonData.data.userOnline);
            }
        }
        //console.log(jsonData.data.userOnline.uo_idle_state);
    });
    
    var subUserStatus = centrifuge.subscribe(cfUserStatusChannel, function(message) {
        let jsonData = message.data;
        // console.log(jsonData.data);
        if (jsonData.object === 'userStatus') {
            if (jsonData.action === 'delete') {
                callMapApp.deleteUserStatus(jsonData.data.userStatus);
            } else {
                callMapApp.addUserStatus(jsonData.data.userStatus);
            }
        }
        //console.log(jsonData.data.userOnline.uo_idle_state);
    });
   

    // subscription.on('ready', function(){
    //     subscription.presence(function(message) {
    //         console.log('subscription.presence');        
    //         // information about who connected to channel at moment received
    //     });
    //     subscription.history(function(message) {
    //         console.log('subscription.history');
    //         // information about last messages sent into channel received
    //     });
    //     subscription.on('join', function(message) {
    //         console.log('subscription.join');
    //         // someone connected to channel
    //     });
    //     subscription.on('leave', function(message) {
    //         console.log('subscription.leave');
    //         // someone disconnected from channel
    //     });
    // });

    
});

centrifuge.on('disconnect', function(ctx){
    console.log('Disconnected: ' + ctx.reason);
});
centrifuge.connect();
JS;

$this->registerJs($js);

$this->registerJsFile('/js/vue/call-realtime-map/realtime-map.js', [
    'position' => \yii\web\View::POS_END,
    'depends' => [
        \frontend\assets\VueAsset::class
    ]
]);
