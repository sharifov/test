<?php

use common\models\Employee;
use frontend\widgets\multipleUpdate\redial\MultipleUpdateWidget;
use frontend\widgets\multipleUpdate\redialAll\UpdateAllWidget;
use frontend\widgets\UserInfoProgress;
use src\access\ListsAccess;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadQcallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataProviderLastCalls yii\data\ActiveDataProvider */
/* @var array $guard */

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
            <?= UserInfoProgress::widget(['user' => $user])?>
        </div>

        <?php /*= Html::button('<i class="fa fa-phone"></i> Call Next', [
            'class' => 'btn btn-success btn-lg lead-next-btn', 'style' => 'font-size: 18px'
        ]) */ ?>

        <div class="row">
            <div class="col-md-12">
                <div id="loading" style="text-align:center;font-size: 60px;display: none">
                    <i class="fa fa-spin fa-spinner"></i> Loading ...
                </div>

                <div id="redial-call-box-wrapper">
                    <div id="redial-call-box">
                        <?php /* <div class="text-center badge badge-warning call-status" style="font-size: 35px">
                            <span id="text-status-call">Ready</span>
                        </div> */ ?>
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

        <?php $showQueue = (bool)Yii::$app->params['settings']['agent_show_redial_queue']; ?>

        <?php if ($showQueue || (!$showQueue && !$user->isAgent())) : ?>
            <p></p>

            <div style="font-size: 30px">Redial Queue</div>

            <?php if ($user->isAdmin()) : ?>
                <?= MultipleUpdateWidget::widget([
                    'gridId' => 'redialGrid',
                    'script' => "let pjax = $('#lead-redial-pjax'); if (pjax.length) { $.pjax.reload({container: '#lead-redial-pjax', async: false}); }",
                    'actionUrl' => Url::to(['lead-redial/multiple-update']),
                    'validationUrl' => Url::to(['lead-redial/multiple-update-validate']),
                    'reportWrapperId' => 'redial-call-box-wrapper'
                ]) ?>

                <?= UpdateAllWidget::widget([
                    'modalId' => 'modal-df',
                    'showUrl' => Url::to(['/lead-redial/update-all-show']),
                ]) ?>

            <?php endif; ?>

            <?php Pjax::begin(['id' => 'lead-redial-pjax', 'enablePushState' => false, 'enableReplaceState' => true, 'scrollTo' => 0]); ?>

            <?= $this->render('_redial_list', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'list' => $list,
                'userIsFreeForCall' => $userIsFreeForCall,
                'user' => $user,
                'guard' => $guard
            ]) ?>

            <?php Pjax::end(); ?>

        <?php endif; ?>

    </div>

<?php

$nextUrl = Url::to(['lead-redial/next']);

$blockSeconds = (int)(Yii::$app->params['settings']['call_redial_delay_time'] ?? 0);

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
        if (!data.success) {
           let text = 'Error. Try again later';
           if (data.message) {
               text = data.message;
           }
            createNotifyByObject({title: "Lead redial", type: "error", text: text, hide: true});
        } 
        if (data.data) {
            $("#redial-call-box-wrapper").html(data.data);
        }
    })
    .fail(function() {
        $("#loading").hide();
        createNotifyByObject({title: "Lead redial", type: "error", text: 'Error', hide: true});
    })
}

$("body").on("click", ".lead-redial-btn", function(e) {
    loadRedialCallBoxBlock('post', $(this).data('url'), {gid: $(this).data('gid')});
});

$("body").on("click", ".lead-next-btn", function(e) {
    
    if ({$blockSeconds}) {
        if (blockLeadRedialNextButton($(this), {$blockSeconds})) {
            return;
        }    
    }
    
    loadRedialCallBoxBlock('post', '{$nextUrl}');
});

if ({$blockSeconds}) {
    checkForEnabledLeadRedialNextButton();    
} 

function blockLeadRedialNextButton(obj, duration) {
    let now = (new Date).getTime();
    let end = +localStorage.getItem("lead_redial_end")||0;
    duration = duration * 1E3;
    let d = duration;
    let isBlocked = false;
    
    if (end && end > now) {
          d = end - now;
          isBlocked = true;
    } else {
       localStorage.setItem("lead_redial_end", now + duration);
    }
    
    d = d/1000 | 0;
         
    leadRedialNextButtonTimer(obj, d);    
    
    return isBlocked;
}

function leadRedialNextButtonTimer(obj, d) {
    let that = obj.text('Processing... ' + d).prop('disabled', true),
    timer = setInterval(function() {
        that.text('Processing... ' + --d);
        if (d <= 0 ) {
            clearInterval(timer);
            localStorage.setItem("lead_redial_end", 0);
            that.html('<i class="fa fa-phone"></i> Call Next').prop('disabled', false);
        }
    }, 1000);    
} 

function checkForEnabledLeadRedialNextButton() {
    let now = (new Date).getTime();
    let end = +localStorage.getItem("lead_redial_end")||0;
    if (end && end > now) {
          leadRedialNextButtonTimer($('.lead-next-btn'), ((end - now)/1000 | 0));    
    }
}

JS;

$this->registerJs($js);

$lastCallsUrl = Url::to(['lead-redial/show-last-calls']);

$js = <<<JS

function reloadCallFunction() {
    let pjax = $('#lead-redial-pjax');
    if (pjax.length) { $.pjax.reload({container: '#lead-redial-pjax', async: false}); }
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
        createNotifyByObject({title: "Reload last dialed leads", type: "error", text: 'Error', hide: true});
    })
}

JS;

$this->registerJs($js, $this::POS_END);
