<?php

use common\models\Call;

?>
<script type="text/x-template" id="call-item-tpl">
    <div v-if="show" class="col-md-12" style="margin-bottom: 0px">
        <table class="table table-condensed  table-bordered">
            <tbody>
            <tr>
                <td class="text-center" style="width:35px">
                    {{ index + 1 }}
                    <br>
                    <div class="call-menu" @click="showModalWindow"><i class="fas fa-ellipsis-v"></i></div>
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

                    <?php //<img v-if="getCountryByPhoneNumber(item.c_from)" :src="'https://purecatamphetamine.github.io/country-flag-icons/3x2/' + getCountryByPhoneNumber(item.c_from) + '.svg'" width="20"/> &nbsp;?>
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