<?php

use yii\grid\ActionColumn;
use src\auth\Auth;
use src\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApiLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var DbCleanerParamsForm $modelCleaner */

$this->title = 'Api Logs';
$this->params['breadcrumbs'][] = $this->title;
$pjaxListId = 'pjax-api-log';
?>
<div class="api-log-index">

    <h1><i class="fa fa-list"></i> <?= Html::encode($this->title) ?></h1>
<?php
$json = '{"sms": ["s_phone_from", "s_phone_to", "s_sms_text", "s_sms_data"], "call": ["c_from", "c_to", "c_forwarded_from", "c_caller_name", "c_recording_url"], "email": ["e_email_from", {"mask": "regexp", "column": "e_email_from", "pattern": "(?<!^).(?=[^@]+@)", "replace": " "}, {"mask": "mail", "column": "e_email_to"}, "e_email_to", "e_email_cc", "e_email_bc", "e_email_body_text", "e_attach", "e_email_from_name", "e_email_to_name", "e_message_id", "e_ref_message_id"], "leads": ["l_client_first_name", "l_client_last_name", "l_client_phone", "l_client_email", "additional_information"], "coupon": ["c_code"], "clients": ["first_name", "middle_name", "last_name"], "invoice": ["inv_description"], "api_user": ["au_api_username", "au_api_password", "au_email"], "call_log": ["cl_phone_from", "cl_phone_to"], "projects": ["api_key"], "case_sale": ["css_sale_data", "css_sale_data_updated"], "conference": ["cf_recording_url", "cf_recording_sid"], "email_list": ["el_email"], "lead_qcall": ["lqc_call_from"], "phone_list": ["pl_phone_number", {"mask": "phone", "start": 2, "column": "pl_phone_number", "length": 3}, {"mask": "year", "column": "pl_created_dt"}], "credit_card": ["cc_number", "cc_display_number", "cc_holder_name", "cc_expiration_month", "cc_expiration_year", "cc_cvv", "cc_security_hash"], "hotel_quote": ["hq_json_booking"], "sale_ticket": ["st_client_name"], "billing_info": ["bi_first_name", "bi_last_name", "bi_middle_name", "bi_address_line1", "bi_address_line2", "bi_contact_phone", "bi_contact_email", "bi_contact_name"], "client_email": ["email"], "client_phone": ["phone"], "cruise_quote": ["crq_data_json"], "flight_quote": [{"path": ["$.title", "$.id", "$.type"], "column": "fq_json_booking"}, "fq_ticket_json"], "order_contact": ["oc_first_name", "oc_last_name", "oc_middle_name", "oc_email", "oc_phone_number"], "attraction_pax": ["atnp_first_name", "atnp_last_name"], "client_account": ["ca_username", "ca_first_name", "ca_middle_name", "ca_last_name", "ca_phone", "ca_email"], "hotel_room_pax": ["hrp_first_name", "hrp_last_name"], "product_holder": ["ph_first_name", "ph_last_name", "ph_middle_name", "ph_email", "ph_phone_number"], "call_log_record": ["clr_record_sid"], "phone_blacklist": ["pbl_phone"], "email_unsubscribe": ["eu_email"], "contact_phone_list": ["cpl_phone_number"], "phone_blacklist_log": ["pbll_phone"], "hotel_quote_room_pax": ["hqrp_first_name", "hqrp_last_name"], "employee_contact_info": ["email_pass"], "sms_distribution_list": ["sdl_phone_from", "sdl_phone_to"]}';
$json = \yii\helpers\Json::decode($json);

$t = \src\helpers\text\MaskStringHelper::maskArray($json);
echo "<pre>";
//print_r($json);
print_r($t);
echo "</pre>";
?>
    <p>
        <?= Html::a('<i class="fa fa-remove"></i> Truncate ApiLog table', ['delete-all'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete all items?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php if (Auth::can('global/clean/table')) : ?>
        <?php echo $this->render('../clean/_clean_table_form', [
            'modelCleaner' => $modelCleaner,
            'pjaxIdForReload' => $pjaxListId,
        ]); ?>
    <?php endif ?>

    <?php Pjax::begin(['id' => $pjaxListId, 'scrollTo' => 0]); ?>

    <?php  echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{pager}\n{summary}\n{items}\n{pager}",
        'summary' => 'Showing <b>{begin}-{end}</b> of <b>{totalCount}</b> items.</br>From <b>' . $searchModel->createTimeStart . ' </b> to <b>' . $searchModel->createTimeEnd . ' </b>',
        'columns' => [
            [
                'attribute' => 'al_id',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_id;
                },
                'options' => ['style' => 'width:100px']
            ],
            [
                'attribute' => 'al_action',
                'value' => function (\common\models\ApiLog $model) {
                    return '<b>' . Html::encode($model->al_action) . '</b>';
                },
                'format' => 'raw',
                'filter' => \common\models\ApiLog::getActionFilter(Yii::$app->request->isPjax, $searchModel->createTimeRange),
            ],
            [
                'label' => 'Relative Time',
                'value' => static function (\common\models\ApiLog $model) {
                    return $model->al_request_dt ? '' . Yii::$app->formatter->asRelativeTime(strtotime($model->al_request_dt)) : '-';
                },
            ],
            [
                'attribute' => 'al_request_data',
                'format' => 'raw',
                'value' => static function (\common\models\ApiLog $model) {
                    $resultStr = '-';
                    if ($decodedData = @json_decode($model->al_request_data, true, 512, JSON_THROW_ON_ERROR)) {
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($decodedData)),
                            1200,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($decodedData, 10, true);
                        $detailBox = '<div id="detail_' . $model->al_id . '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->al_id . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
            ],
            [
                'attribute' => 'al_request_dt',
                'value' => static function (\common\models\ApiLog $model) {
                    if (!$model->al_request_dt) {
                        return Yii::$app->formatter->nullDisplay;
                    }
                    return Html::tag('i', '', ['class' => 'fa fa-calendar']) . ' ' .
                        Yii::$app->formatter->asDatetime(strtotime($model->al_request_dt), 'php:d-M-Y [H:i]');
                },
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:180px;'],
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'createTimeRange',
                    'useWithAddon' => true,
                    'presetDropdown' => true,
                    'hideInput' => true,
                    'convertFormat' => true,
                    'startAttribute' => 'createTimeStart',
                    'endAttribute' => 'createTimeEnd',
                    'pluginOptions' => [
                        'maxDate' => date("Y-m-d 23:59"),
                        'applyButtonClasses' => 'applyBtn btn btn-sm btn-success',
                        'timePicker' => true,
                        'timePickerIncrement' => 1,
                        'timePicker24Hour' => true,
                        'locale' => [
                            'format' => 'Y-m-d H:i',
                            'separator' => ' - '
                        ],
                        'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                    ],
                ]),
            ],
            [
                'attribute' => 'al_response_data',
                'format' => 'raw',
                'value' => function (\common\models\ApiLog $model) {
                    return Yii::$app->formatter->asShortSize(mb_strlen($model->al_response_data), 1);
                },
            ],
            [
                'attribute' => 'al_execution_time',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_execution_time;
                },
            ],
            [
                'attribute' => 'al_memory_usage',
                'format' => 'raw',
                'value' => function (\common\models\ApiLog $model) {
                    return Yii::$app->formatter->asShortSize($model->al_memory_usage, 2);
                },
            ],
            [
                'attribute' => 'al_db_execution_time',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_db_execution_time;
                },
            ],
            [
                'attribute' => 'al_db_query_count',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_db_query_count;
                },
            ],
            [
                'attribute' => 'al_user_id',
                'value' => function (\common\models\ApiLog $model) {
                    $apiUser = \common\models\ApiUser::findOne($model->al_user_id);
                    return $apiUser ? $apiUser->au_name . ' (' . $model->al_user_id . ')' : $model->al_user_id;
                },
                'filter' => \common\models\ApiUser::getList()
            ],
            'al_ip_address',
            ['class' => ActionColumn::class],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
yii\bootstrap4\Modal::begin([
    'title' => 'Log detail',
    'id' => 'modal',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
]);
yii\bootstrap4\Modal::end();

$ajaxUrl = \yii\helpers\Url::to(['/api-log/ajax-action-list', 'timeRange' => $searchModel->createTimeRange]);
$actionValue = $searchModel->al_action ? md5($searchModel->al_action) : '';

$jsCode = <<<JS
    let ajaxUrlCategoryList = '$ajaxUrl';
    let actionValue = '$actionValue';

    function updateActionList() {
        $.getJSON(ajaxUrlCategoryList, function(response) {
            let obj = $( "select[name='ApiLogSearch[al_action]']" );
            obj.html('').append('<option value=""></option>');

            $.each(response.data, function(){
                let selected = '';
                if (actionValue === this.hash) {
                    selected = 'selected';
                }
                obj.append('<option value="'+ this.name +'" ' + selected + '>'+ this.name +' - ['+ this.cnt +']</option>')
            });
        });
    }
    setTimeout(updateActionList, 2000);

    $(document).on('click', '.showDetail', function(){
        
        let logId = $(this).data('idt');
        let detailEl = $('#detail_' + logId);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html(detailEl.html()); 
        $('#modal-label').html('Detail Api Log (' + logId + ')');       
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
