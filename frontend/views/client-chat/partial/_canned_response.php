<?php

use sales\auth\Auth;
use sales\helpers\clientChat\ClientChatHelper;
use sales\model\clientChat\entity\ClientChat;
use yii\helpers\Html;

/* @var ClientChat|null $clientChat */

?>
<div id="canned-response-wrap" class="<?php echo ClientChatHelper::isShowInput($clientChat, Auth::user()) ? '' : 'disabled' ?>">
<?php echo Html::textarea(
    'canned-response',
    '',
    [
        'placeholder' => 'Try to search quickly response by typing "/"',
        'id' => 'canned-response',
        'class' => 'form-control canned-response',
        'data-chat-id' => $clientChat->cch_id ?? null,
        'rows' => 3,
    ]
) ?>
        <span id="send-canned-response" class="canned-response-icon">
            <i class="fa fa-paper-plane"></i>
        </span>
        <span id="loading-canned-response" class="canned-response-icon" style="display: none">
            <i class="fa fa-spin fa-spinner"></i>
        </span>
    </div>
