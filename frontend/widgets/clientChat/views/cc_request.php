<?php

use common\components\i18n\Formatter;
use frontend\widgets\clientChat\ClientChatAsset;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $access ClientChatUserAccess[] */
/** @var $open bool */
/** @var $formatter Formatter */

ClientChatAsset::register($this);

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
        <div class="_cc-box-body">
            <?php if($access): ?>
                <?php foreach($access as $item): ?>
                    <?= $this->render('cc_request_item', [
                        'access' => $item,
                        'formatter' => $formatter
                    ]) ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no active client conversations requests.</p>
            <?php endif; ?>
        </div>

<!--        <div class="fab_field">-->
<!--        </div>-->
    </div>
    <a id="_cc-access-wg" total-items="<?= $totalRequest ?>" class="_cc-fab <?= $open && $access ? 'is-visible' : '' ?> <?= $access ? '' : 'inactive' ?>">
        <i class="fa fa-comments-o"></i>

        <div id="_circle_wrapper" class="<?= $totalRequest ? 'active' : '' ?>">
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
JS;

$this->registerJs($js);
