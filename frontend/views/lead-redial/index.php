<?php

use common\models\Employee;
use sales\access\ListsAccess;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadQcallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataProviderLastCalls yii\data\ActiveDataProvider */

$this->title = 'Lead Redial';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;
$userIsFreeForCall = $user->isCallFree();

$list = new ListsAccess($user->id);

?>
    <div class="lead-qcall-list">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-md-12">

                <div id="loading" style="text-align:center; display: none">
                    <img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"/>
                </div>

                <div id="redial-call-box-wrapper">
                    <div id="redial-call-box">
                        <div class="text-center badge badge-warning call-status" style="font-size: 35px">
                            <span id="text-status-call">Ready</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <p></p>

        <div id="lead-redial-last-calls">

            <div style="font-size: 30px">Last dialed leads</div>

            <div id="lead-redial-last-calls-data">

                <?= $this->render('_last_calls', [
                    'dataProvider' => $dataProviderLastCalls,
                    'list' => $list,
                    'userIsFreeForCall' => $userIsFreeForCall,
                    'user' => $user
                ])  ?>

            </div>

        </div>

        <p></p>

        <div style="font-size: 30px">Redial Queue</div>

        <?php Pjax::begin(['id' => 'lead-redial-pjax', 'enablePushState' => false, 'enableReplaceState' => true]); ?>

            <?= $this->render('_redial_list', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'list' => $list,
                'userIsFreeForCall' => $userIsFreeForCall,
                'user' => $user
            ]) ?>

        <?php Pjax::end(); ?>

    </div>

<?php


$js = <<<JS

function loadRedialCallBoxBlock(type, url, data) {
    $("#redial-call-box").html('');
    $("#loading").show();
    $.ajax({
        type: type,
        url: url,
        data: data
    })
    .done(function(data) {
        $("#loading").hide();
        $("#redial-call-box-wrapper").html(data);
    })
    .fail(function() {
        $("#loading").hide();
        new PNotify({title: "Lead redial", type: "error", text: 'Error', hide: true});
    })
}

$("body").on("click", ".lead-redial-btn", function(e) {
    loadRedialCallBoxBlock('post', $(this).data('url'), {gid: $(this).data('gid')});
});

JS;

$this->registerJs($js);

$lastCallsUrl = Url::to(['lead-redial/show-last-calls']);

$js = <<<JS

function reloadCallFunction() {
    $.pjax.reload({container: '#lead-redial-pjax', async: false});
    leadRedialLastCallsReload();
}

function leadRedialLastCallsReload() {
    $.ajax({
        type: 'get',
        url: '{$lastCallsUrl}'
    })
    .done(function(data) {
        $("#lead-redial-last-calls-data").html(data);
    })
    .fail(function() {
        new PNotify({title: "Reload last dialed leads", type: "error", text: 'Error', hide: true});
    })
}

JS;

$this->registerJs($js, $this::POS_END);
