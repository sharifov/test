<?php

use common\components\i18n\Formatter;
use frontend\widgets\clientChat\ClientChatWidgetAsset;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $access array */
/** @var $open bool */
/** @var $formatter Formatter */
/** @var $page int */

ClientChatWidgetAsset::register($this);

$totalRequest = count($access);
?>

<?php // yii\widgets\Pjax::begin(['id' => 'client-chat-box-pjax', 'timeout' => 10000, 'enablePushState' => false, 'options' => []])?>
<div class="_cc-fabs">
    <div class="_cc-box <?= $open && $access ? 'is-visible' : '' ?>">
        <div class="_cc-box-header <?= $access ? 'active' : '' ?>">
            <div class="_cc-box-option">
                <div class="header_img">
                    <?= Html::img('/img/user.png')?>
                </div>
                <span id="_cc-box-title">Client Chat Request</span> <br>
            </div>
        </div>
        <div class="_cc-box-body" id="_cc-box-body">
        </div>

        <span id="_wrap_cc_load_requests">
            <button id="_cc_load_requests" class="btn btn-default" data-page="<?= $page ?>" >Load more</button>
        </span>
    </div>
    <a id="_cc-access-wg" data-loading="1" total-items="<?= $totalRequest ?>" class="_cc-fab <?= $open && $access ? 'is-visible' : '' ?> <?= $access ? '' : 'inactive' ?>">
        <i class="fa fa-spinner fa-spin" id="_cc-access-icon"></i>

        <div id="_circle_wrapper" class="">
            <span class="_cc_total_request_wrapper">
                <?= $totalRequest ?: 0 ?>
            </span>
            <span class="circle" style="animation-delay: 0s"></span>
            <span class="circle" style="animation-delay: 1s"></span>
            <span class="circle" style="animation-delay: 2s"></span>
            <span class="circle" style="animation-delay: 3s"></span>
        </div>
    </a>
</div>
<?php // yii\widgets\Pjax::end() ?>

<?php
$url = \yii\helpers\Url::to(['/client-chat/chat-requests']);
$js = <<<JS
    
let _ccWgStatus = localStorage.getItem('_cc_wg_status');
let _access = {$totalRequest} > 0 ? true : false;
if (_ccWgStatus === 'true' && _access) {
    toggleClientChatAccess();
}

window.addEventListener('storage', function (event) {
    if (event.key === '_cc_wg_status') {
        let _ccWgStatus = localStorage.getItem('_cc_wg_status');
        if (_ccWgStatus === 'true') {
            toggleClientChatAccess(true);
        } else {
            toggleClientChatAccess(false);
        }
    }
});
// $(document).on('click', '._cc_chevron', function () {
//     $(this).closest('._cc-box-item').toggleClass('active');
// });

var spinnWrap = $('#_wrap_cc_load_requests');
$(document).on('click', '#_cc_load_requests', function()
{
    let page = $(this).attr('data-page');
    let btn = $(this);
    if(spinnWrap.hasClass('active') && page)
    {
        btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);
        let ajax = window.chat.loadData(page);
        
        ajax
        .then(() => {window.chat.displayAllRequests(parseInt(page)+1)})
        .catch(() => {btn.html('All request loaded').prop('disabled', true).addClass('disabled');})
    }
});

(function (window) {
    var ChatApp = window.ChatApp;
    var DataStore = ChatApp.DataStore;
    var Chat = ChatApp.Chat;

    var ds = new DataStore();
    window.chat = new Chat('$url', ds, $page);
})(window);
JS;

$this->registerJs($js);
