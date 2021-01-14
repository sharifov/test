<?php
/* @var $this yii\web\View */
/* @var $cfConnectionUrl string */
/* @var $cfToken string */
/* @var $cfChannelName string */

$this->title = 'Realtime Call Map';
///\frontend\assets\VueAsset::register($this);
?>

<style>
    #realtime-map-app table {margin: 2px 0 1px 0}
    .card-body {padding: 0 0 0 0}

    .list-move {
        transition: transform 0.8s;
    }

    .list-item {
        display: inline-block;
    }
    .list-enter-active, .list-leave-active {
        transition: all 0.4s;
    }
    .list-enter, .list-leave-to {
        opacity: 0;
        /*transform: translateY(30px);*/
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
                <td class="text-center" style="width:50px">
                    <u><a :href="'/call/view?id=' + item.c_id" target="_blank">{{ item.c_id }}</a></u>
<!--                    <span class="badge badge-danger">{{ callTypeName }}</span>-->
                </td>
                <td class="text-center" style="width:95px">
                    <i class="fa fa-clock-o"></i> {{ createdDateTime("HH:mm:ss") }}
                </td>

                <td class="text-center" style="width:120px">
                    <span class="badge badge-info">{{ projectName }}</span>
                </td>
                <td class="text-center" style="width:80px">
                    <span v-if="item.c_dep_id" class="label label-default">{{ departmentName }}</span>
                </td>
                <td class="text-center" style="width:90px">
                    <span v-if="item.c_source_type_id">{{ callSourceName }}</span>
                </td>
                <td class="text-left" style="width:65px">
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

                <td class="text-left" style="width:150px">
                    <i v-if="item.c_client_id" class="fa fa-male text-info fa-1x fa-border"></i>
                    <i v-if="!item.c_client_id" class="fa fa-phone fa-1x fa-border"></i>
                    {{ formatPhoneNumber(item.c_from) }}
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
                        {{ getUserName(item.c_created_user_id) }}
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
            <span class="label" :class="{ 'label-success': access.cua_status_id == 2, 'label-default': access.cua_status_id != 2 }"
                  v-for="(access, index) in item.userAccessList" :key="access.cua_user_id"
                  style="margin-right: 4px" :title="getUserAccessStatusTypeName(access.cua_status_id)">
                <i class="fa fa-user"></i> {{ getUserName(access.cua_user_id) }}
            </span>
        </div>
    </div>
</script>

<div id="realtime-map-app" class="col-md-12">
    <div class="row">
        <div class="col-md-6" >
            <div class="card card-default">
                <div class="card-header"> Incoming Calls in IVR, QUEUE, RINGING ({{ callList1.length }})</div>
                <transition-group name="list" tag="div" class="card-body row">
                    <div v-for="(item, index) in callList1" class="list-item col-md-12" :key="item">
                        <call-item-component :item="item" :key="item.с_id" :index="index"></call-item-component>
                    </div>
                </transition-group>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-default">
                <div class="card-header"> Incoming Calls in PROGRESS, DELAY ({{ callList2.length }})</div>
                <transition-group name="list" tag="div" class="card-body row">
                    <div v-for="(item, index) in callList2" class="list-item col-md-12" :key="item">
                        <call-item-component :item="item" :key="item.с_id" :index="index"></call-item-component>
                    </div>
                </transition-group>
            </div>
        </div>

    </div>
</div>



<?php
$js = <<<JS
let cfChannelName = '$cfChannelName';
let cfToken = '$cfToken';
let cfConnectionUrl = '$cfConnectionUrl';

var centrifuge = new Centrifuge(cfConnectionUrl, {debug: false});
centrifuge.setToken(cfToken);

centrifuge.on('connect', function(ctx){
    console.log('Connected over ' + ctx.transport);
    
    var subscription = centrifuge.subscribe(cfChannelName, function(message) {
        let jsonData = message.data;
        //console.log(jsonData.data);
        
        if (jsonData.action == 'update') {
            //callMapApp.addCall(jsonData.data.call);
            //let data = JSON.parse(jsonData.data);
            callMapApp.addCall(jsonData.data.call);
        }
        //console.log(message);
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
centrifuge.connect();
JS;

$this->registerJs($js);

$this->registerJsFile('/js/vue/call-realtime-map/realtime-map.js', [
    'position' => \yii\web\View::POS_END,
    'depends' => [
        \frontend\assets\VueAsset::class
    ]
]);
