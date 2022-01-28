<?php

use modules\featureFlag\src\entities\FeatureFlag;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\featureFlag\src\entities\FeatureFlag */
/* @var $form ActiveForm */
\frontend\assets\QueryBuilderAsset::register($this);

$subject_json = [];
$rulesData = [];
$rulesData = @json_decode($subject_json);
$rulesDataStr = json_encode($rulesData);
$filtersData = ['a' => 1, 'b' => 33]; //$model->getObjectAttributeList();
$filtersDataStr = json_encode($filtersData);
$operators = json_encode(\modules\featureFlag\components\FeatureFlagBaseModel::getOperators());
$model->ff_condition = json_encode([]);

?>
<style>
    .rules-group-container {width: 100%}
    .rule-value-container {display:inline-flex!important;}
</style>

<div class="feature-flag-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-6">

        <?php //= $form->field($model, 'ff_key')->textInput(['maxlength' => true]) ?>

        <?php //= $form->field($model, 'ff_name')->textInput(['maxlength' => true]) ?>

        <?php //= $form->field($model, 'ff_type')->textInput(['maxlength' => true]) ?>

        <?php

        if ($model->ff_type === FeatureFlag::TYPE_STRING) {
            echo $form->field($model, 'ff_value')->textInput();
        } elseif ($model->ff_type === FeatureFlag::TYPE_BOOL) {
            echo $form->field($model, 'ff_value')->checkbox();//->label($model->ff_name);
        } elseif ($model->ff_type === FeatureFlag::TYPE_INT) {
            echo $form->field($model, 'ff_value')->input('number');//->label($model->ff_name);
        } elseif ($model->ff_type === FeatureFlag::TYPE_DOUBLE) {
            echo $form->field($model, 'ff_value')->input('number', ['step' => 0.01]);//->label($model->ff_name);
        } elseif ($model->ff_type === FeatureFlag::TYPE_ARRAY) {
            try {
                echo $form->field($model, 'ff_value')->widget(
                    \kdn\yii2\JsonEditor::class,
                    [
                        'clientOptions' => [
                            'modes' => ['code', 'form', 'tree', 'view'], //'text',
                            'mode' => 'tree'
                        ],
                        //'collapseAll' => ['view'],
                        'expandAll' => ['tree', 'form'],
                    ]
                );
            } catch (Exception $exception) {
                echo $form->field($model, 'ff_value')->textarea(['rows' => 5]);//->label($model->ff_name);
            }
        } else {
//                echo $form->field($model, 'ff_value')->textInput(['maxlength' => true])->label($model->ff_name);
            echo $form->field($model, 'ff_value')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'], //'text',
                        'mode' => 'tree'
                    ],
                    //'collapseAll' => ['view'],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        }
        ?>

        <?php //= $form->field($model, 'ff_value')->textInput(['maxlength' => true]) ?>



        <?= $form->field($model, 'ff_category')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ff_enable_type')->dropDownList(FeatureFlag::getEnableTypeList(), ['prompt' => '-']) ?>



        <?php /*= $form->field($model, 'ff_condition')->textInput()*/ ?>
        <?= $form->field($model, 'ff_condition')->textarea(['rows' => 3]) ?>

        <?= $form->field($model, 'ff_description')->textarea(['rows' => 3]) ?>

        <?= $form->field($model, 'ff_attributes')->textInput() ?>




    </div>
    <div class="col-md-6">
        <?php if ($filtersData) : ?>
            <div id="builder" style="width: 100%"></div>
            <br>
            <?php echo Html::a('Show / hide JSON rules', null, ['class' => 'btn btn-sm btn-default', 'id' => 'btn-div-json-rules']) ?>
            <?=Html::button('<i class="fa fa-check-square-o"></i> Validate rules', ['class' => 'btn btn-sm btn-warning', 'id' => 'btn-getcode'])?>

            <div id="div-json-rules" style="display: none">
                <?= $form->field($model, 'ff_condition')->textarea(['rows' => 8, 'id' => 'ff_condition', 'readonly' => true]) ?>
            </div>
        <?php else : ?>
            <div class="alert alert-warning" role="alert">
                <strong>Warning</strong>: ATTRIBUTE list (Filter data) for this object is empty!
            </div>
        <?php endif; ?>

        <?php
        $jsCode = <<<JS
    
    var rulesData = $rulesDataStr;
    var filtersData = $filtersDataStr;
    var operators = $operators;
    
    //var rulesData = {"condition":"AND","rules":[{"id":"env_user_roles","field":"env.user.roles","type":"string","input":"select","operator":"in_array","value":"admin"}],"valid":true};
    //var filtersData = [{"optgroup":"Form","id":"user\/user\/formAttribute","field":"formAttribute","label":"Field","type":"string","input":"select","values":{"username":"Username","email":"Email","full_name":"Full Name","password":"Password","nickname":"Nickname","form_roles":"Roles","status":"Status","user_groups":"User Groups","user_projects":"Projects access","user_departments":"Departments","client_chat_user_channel":"Client chat user channel","up_work_start_tm":"Work Start Time","up_work_minutes":"Work Minutes","up_timezone":"Timezone","up_base_amount":"Base Amount","up_commission_percent":"Commission Percent","up_bonus_active":"Bonus Is Active","up_leaderboard_enabled":"Leader Board Enabled","up_join_date":"Join Date","up_skill":"Skill","up_call_type_id":"Call Type","up_2fa_secret":"2fa secret","up_sip":"Sip","up_2fa_enable":"2fa enable","up_telegram":"Telegram ID","up_telegram_enable":"Telegram Enable","up_auto_redial":"Auto redial","up_kpi_enable":"KPI enable","up_show_in_contact_list":"Show in contact list","up_call_recording_disabled":"Call recording disabled"},"multiple":false,"operators":["==","!="]},{"optgroup":"Form","id":"user\/user\/formMultiAttribute","field":"formMultiAttribute","label":"Multiple Field","type":"string","input":"select","values":{"username":"Username","email":"Email","full_name":"Full Name","password":"Password","nickname":"Nickname","form_roles":"Roles","status":"Status","user_groups":"User Groups","user_projects":"Projects access","user_departments":"Departments","client_chat_user_channel":"Client chat user channel","up_work_start_tm":"Work Start Time","up_work_minutes":"Work Minutes","up_timezone":"Timezone","up_base_amount":"Base Amount","up_commission_percent":"Commission Percent","up_bonus_active":"Bonus Is Active","up_leaderboard_enabled":"Leader Board Enabled","up_join_date":"Join Date","up_skill":"Skill","up_call_type_id":"Call Type","up_2fa_secret":"2fa secret","up_sip":"Sip","up_2fa_enable":"2fa enable","up_telegram":"Telegram ID","up_telegram_enable":"Telegram Enable","up_auto_redial":"Auto redial","up_kpi_enable":"KPI enable","up_show_in_contact_list":"Show in contact list","up_call_recording_disabled":"Call recording disabled"},"multiple":true,"operators":["contains"]},{"optgroup":"ENV - DATA","id":"env_available","field":"env.available","label":"Available for all","type":"boolean","input":"radio","values":{"true":"True","false":"False"},"multiple":false,"default_value":true,"vertical":true,"operators":["=="]},{"optgroup":"ENV - USER","id":"env_username","field":"env.user.username","label":"Username","type":"string","input":"text","operators":["==","!=","in","not_in","match"]},{"optgroup":"ENV - REQUEST","id":"env_controller","field":"env.req.controller","label":"Controller","type":"string","input":"text","operators":["==","!=","in","not_in","match"]},{"optgroup":"ENV - REQUEST","id":"env_action","field":"env.req.action","label":"Controller\/Action","type":"string","input":"text","operators":["==","!=","in","not_in","match"]},{"optgroup":"ENV - REQUEST","id":"env_url","field":"env.req.url","label":"URL","type":"string","input":"text","operators":["==","!=","match"]},{"optgroup":"ENV - REQUEST","id":"env_ip","field":"env.req.ip","label":"IP Address","type":"string","input":"text","placeholder":"___.___.___.___","operators":["==","!=","match"]},{"optgroup":"ENV - DATE & TIME","id":"env_dt_date","field":"env.dt.date","label":"Date","type":"date","input":"text","placeholder":"____-__-__","operators":["==","!=","in","not_in","match"]},{"optgroup":"ENV - DATE & TIME","id":"env_dt_time","field":"env.dt.time","label":"Time","type":"time","input":"text","placeholder":"__:__","operators":["==","!=","in","not_in","match"]},{"optgroup":"ENV - DATE & TIME","id":"env_dt_year","field":"env.dt.year","label":"Year","placeholder":"____","type":"integer","input":"number","operators":["==","!=",">=","<=",">","<","in","not_in"],"validation":{"min":2020,"max":2030,"step":1}},{"optgroup":"ENV - DATE & TIME","id":"env_dt_month","field":"env.dt.month","label":"Month","type":"integer","input":"select","values":{"1":"1 - January","2":"2 - February","3":"3 - March","4":"4 - April","5":"5 - May","6":"6 - June","7":"7 - July","8":"8 - August","9":"9 - September","10":"10 - October","11":"11 - November","12":"12 - December"},"multiple":false,"operators":["==","!=",">=","<=",">","<"]},{"optgroup":"ENV - DATE & TIME","id":"env_dt_month_name","field":"env.dt.month_name","label":"Month name","type":"string","input":"select","values":{"Jan":"January","Feb":"February","Mar":"March","Apr":"April","May":"May","Jun":"June","Jul":"July","Aug":"August","Sep":"September","Oct":"October","Nov":"November","Dec":"December"},"multiple":true,"operators":["in","not_in"]},{"optgroup":"ENV - DATE & TIME","id":"env_dt_dow","field":"env.dt.dow","label":"Day of Week","type":"integer","input":"select","values":{"7":"7 - Sunday","1":"1 - Monday","2":"2 - Tuesday","3":"3 - Wednesday","4":"4 - Thursday","5":"5 - Friday","6":"6 - Saturday"},"multiple":false,"operators":["==","!=",">=","<=",">","<"]},{"optgroup":"ENV - DATE & TIME","id":"env_dt_dow_name","field":"env.dt.dow_name","label":"Day of Week Name","type":"string","input":"select","values":{"Sun":"Sunday","Mon":"Monday","Tue":"Tuesday","Wed":"Wednesday","Thu":"Thursday","Fri":"Friday","Sat":"Saturday"},"multiple":true,"operators":["in","not_in"]},{"optgroup":"ENV - DATE & TIME","id":"env_dt_day","field":"env.dt.day","label":"Day","type":"integer","input":"number","operators":["==","!=",">=","<=",">","<","in","not_in"],"validation":{"min":1,"max":31,"step":1},"description":"This filter is \"day\""},{"optgroup":"ENV - DATE & TIME","id":"env_dt_hour","field":"env.dt.hour","label":"Hour","type":"integer","input":"number","operators":["==","!=",">=","<=",">","<","in","not_in"],"validation":{"min":0,"max":23,"step":1}},{"optgroup":"ENV - DATE & TIME","id":"env_dt_min","field":"env.dt.min","label":"Minutes","type":"integer","input":"number","operators":["==","!=",">=","<=",">","<","in","not_in"],"validation":{"min":0,"max":59,"step":1}},{"optgroup":"ENV - USER","id":"env_user_roles","field":"env.user.roles","label":"User Roles","type":"string","input":"select","values":{"admin":"admin","agent":"agent","ex_agent":"ex_agent","ex_super":"ex_super","exchange_senior":"exchange_senior","qa":"qa","sales_senior":"sales_senior","sup_agent":"sup_agent","sup_super":"sup_super","superadmin":"superadmin","supervision":"supervision","support_senior":"support_senior","userManager":"userManager"},"multiple":false,"operators":["in_array","not_in_array"]},{"optgroup":"ENV - USER","id":"env_user_multi_roles","field":"env.user.roles","label":"User Multi Roles","type":"string","input":"select","values":{"admin":"admin","agent":"agent","ex_agent":"ex_agent","ex_super":"ex_super","exchange_senior":"exchange_senior","qa":"qa","sales_senior":"sales_senior","sup_agent":"sup_agent","sup_super":"sup_super","superadmin":"superadmin","supervision":"supervision","support_senior":"support_senior","userManager":"userManager"},"multiple":true,"operators":["contains"]},{"optgroup":"ENV - USER","id":"env_user_groups","field":"env.user.groups","label":"User Groups","type":"string","input":"select","values":{" Zeta Team":" Zeta Team","100J Team":"100J Team","ABC Team":"ABC Team","Alpha Team":"Alpha Team","ALT Crew":"ALT Crew","Avengers":"Avengers","Bucuresti Team":"Bucuresti Team","Dream Team":"Dream Team","Golden Team":"Golden Team","Gunners":"Gunners","High Altitude":"High Altitude","IND Support":"IND Support","Invincibles":"Invincibles","KIV Support ":"KIV Support ","Marvel Team":"Marvel Team","MNL Support":"MNL Support","office test":"office test","ON-Air Team":"ON-Air Team","Pro Team":"Pro Team","Revelation":"Revelation","Rocket Team":"Rocket Team","Sales Gurus":"Sales Gurus","Sparkle":"Sparkle","The Money Team":"The Money Team","Training Team":"Training Team"},"multiple":false,"operators":["in_array","not_in_array"]},{"optgroup":"ENV - USER","id":"env_user_projects","field":"env.user.projects","label":"User Projects","type":"string","input":"select","values":{"acapulcovuelos":"ACAPULCOVUELOS","airandtour":"AIR AND TOUR","arangrant":"ARANGRANT","kayak":"BOOK WITH KAYAK","businessclass":"BUSINESSCLASS","chatdeal":"CHATDEAL","flygtravel":"FLYGTRAVEL","goway":"GOWAY","gttglobal":"GTTGLOBAL","gurufare":"GURUFARE","hop2":"HOP2","kiwib2b":"KIWI B2B","luxscanner":"LUXSCANNER","marketingfltr":"MARKETINGFLTR","openskyway":"OPENSKYWAY","ovago":"OVAGO","priceline":"PRICELINE","scholarflights":"SCHOLARFLIGHTS","techork":"TECHORK","wefare":"WEFARE","wowfare":"WOWFARE","wowgateway":"WOWGATEWAY"},"multiple":false,"operators":["in_array","not_in_array"]},{"optgroup":"ENV - USER","id":"env_user_departments","field":"env.user.departments","label":"User Departments","type":"string","input":"select","values":{"sales":"Sales","exchange":"Exchange","support":"Support","schedule_change":"Schedule Change","fraud_prevention":"Fraud prevention","chat":"Chat"},"multiple":false,"operators":["in_array","not_in_array"]}];

    
    $('#builder').queryBuilder({
        operators: operators,
        select_placeholder: '-- Select Attribute --',
        allow_empty: true,
        plugins: [
            //'bt-tooltips-errors',
            //'bt-selectpicker',
            // 'chosen-selectpicker'
                'sortable',
            //'filter-description',
            'unique-filter',
            //'bt-tooltip-errors',
            //'bt-selectpicker',    
            'bt-checkbox',
            'invert',
            //'not-group'
        ],
        filters: filtersData,
        rules: rulesData
    });
    JS;

        if ($filtersData) {
            $this->registerJs($jsCode, \yii\web\View::POS_READY);
        }
        ?>

    </div>
    <div class="col-md-12 text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Save Data', ['class' => 'btn btn-success', 'id' => 'btn-submit']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php


$jsCode2 = <<<JS
    $('#btn-submit').on('click', function() {
      if(!getBuilder()) return false;
    });

    $('body').on('click', '#btn-div-attr-list', function() {
        $('#div-attr-list').toggle();
        return false;
    });
    
    // $('body').on('click', '#btn-div-action-list', function() {
    //     $('#div-action-list').toggle();
    //     return false;
    // });
    
     $('body').on('click', '#btn-div-json-rules', function() {
        $('#div-json-rules').toggle();
        return false;
    });

    // function getBuilder()
    // {
    //     var result = $('#builder').queryBuilder('getRules');
    //     if (!$.isEmptyObject(result)) {
    //         var json = JSON.stringify(result, null);
    //         $('#ff_condition').val(json);
    //         if(result.valid) return true;
    //     }
    //     return false;
    // }
    
    // $('body').on('change', '#abacpolicyform-ap_object', function(e) {
    //     var value = $(this).val();
    //     $.pjax.reload({container: '#pjax-abac-policy-form', push: false, replace: false, timeout: 5000, data: {object: value}});
    // });
    
    $('body').on('click', '#btn-getcode', function() {
        var result = $('#builder').queryBuilder('getRules');
        if (!$.isEmptyObject(result)) {
            var json = JSON.stringify(result, null, 2);
            alert(json);
            console.log(json);
        }
    });
    
JS;

$this->registerJs($jsCode2, \yii\web\View::POS_READY);