<?php

use common\models\Call;
use frontend\widgets\newWebPhone\DeviceAsset;
use frontend\widgets\newWebPhone\DeviceStorageKey;
use src\auth\Auth;

/** @var View $this */
/** @var \common\models\UserCallStatus $userCallStatus */
/** @var int $countMissedCalls */
/** @var array $formattedPhoneProject */

$canDialpad = true;
if (!Auth::can('PhoneWidget_Dialpad')) {
    $canDialpad = false;
}

?>
<div class="phone-widget__tab is_active" id="tab-phone">

  <div class="call-pane call-pane-initial is_active pw-start pw-connecting">
    <?php if ($canDialpad) : ?>
        <div class="calling-from-info" style="display: none">
          <div class="current-number">
            <div class="custom-phone-select"></div>
          </div>
        </div>
    <?php endif;?>
    <div class="call-pane__number" style="display: none">

            <ul class="phone-widget__list-item calls-history suggested-contacts"> </ul>

            <div class="phone-input-wrap">
                <label class="call-pane-label" for="">Calling to <span id="call-to-label" style="color: white"> </span></label>
                <?php

                use common\models\UserCallStatus;
                use src\guards\phone\PhoneBlackListGuard;
                use src\helpers\setting\SettingHelper;
                use yii\bootstrap4\Html;
                use yii\helpers\Url;
                use yii\web\View;
                use yii\widgets\ActiveForm;

                $canDialpadSearch = Auth::can('PhoneWidget_DialpadSearch');
                if ($canDialpadSearch) {
                    $form = ActiveForm::begin([
                        'id' => 'contact-list-calls-ajax',
                        'action' => ['/contacts/list-calls-ajax'],
                        'method' => 'get',
                    ]);
                }

                echo Html::textInput('q', null, [
                    'id' => 'call-pane__dial-number',
                    'class' => 'call-pane__dial-number',
                    'placeholder' => 'Name, company, phone...',
                    'autocomplete' => 'off',
                    'maxlength' => 16,
                    'disabled' => !$canDialpad,
                    'readonly' => !$canDialpad,
                ]);

                echo Html::hiddenInput('q_value', null, [
                    'id' => 'call-pane__dial-number-value',
                    'data-user-id' => null,
                    'data-phone' => null,
                ]);

                if ($canDialpadSearch) {
                    ActiveForm::end();
                }

                ?>
            </div>

        <?php if ($canDialpad) : ?>
            <a href="#" class="call-pane__dial-clear-all is-shown call_pane_dialpad_clear_number">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M7 8.20625L12.7937 14L13.9999 12.7938L8.2062 7.00004L14 1.20621L12.7938 0L7 5.79383L1.2062 0L0 1.20621L5.7938 7.00004L7.97135e-05 12.7938L1.20628 14L7 8.20625Z"
                            fill="white" />
                </svg>
            </a>
        <?php else : ?>
            <a href="#" class="call-pane__dial-clear-all is-shown call_pane_dialpad_clear_number_disabled">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M7 8.20625L12.7937 14L13.9999 12.7938L8.2062 7.00004L14 1.20621L12.7938 0L7 5.79383L1.2062 0L0 1.20621L5.7938 7.00004L7.97135e-05 12.7938L1.20628 14L7 8.20625Z"
                            fill="white" />
                </svg>
            </a>
        <?php endif; ?>


    </div>
      <?php
        $dialPadButtonDisabledClass = '';
        if (!$canDialpad) {
            $dialPadButtonDisabledClass = ' disabled="disabled"';
        }
        ?>
    <div class="call-pane__dial-block" style="display: none">
      <ul class="call-pane__dial dial">
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="1"<?= $dialPadButtonDisabledClass?>>1</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="2"<?= $dialPadButtonDisabledClass?>>2</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="3"<?= $dialPadButtonDisabledClass?>>3</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="4"<?= $dialPadButtonDisabledClass?>>4</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="5"<?= $dialPadButtonDisabledClass?>>5</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="6"<?= $dialPadButtonDisabledClass?>>6</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="7"<?= $dialPadButtonDisabledClass?>>7</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="8"<?= $dialPadButtonDisabledClass?>>8</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="9"<?= $dialPadButtonDisabledClass?>>9</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="✱"<?= $dialPadButtonDisabledClass?>>✱</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="0"<?= $dialPadButtonDisabledClass?>>0 +</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_init" value="#"<?= $dialPadButtonDisabledClass?>>#</button></li>
      </ul>
      <div class="call-pane__call-btns">
        <button class="call-pane__start-call calling-state-block" id="btn-new-make-call">
          <i class="fas fa-phone"> </i>
        </button>
        <?php if ($canDialpad) : ?>
            <button class="call-pane__correction">
              <i class="fas fa-backspace"> </i>
            </button>
        <?php endif;?>
      </div>
    </div>

    <!--        <div class="call-pane__note-block">-->
    <!--            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">-->
    <!--                <path fill-rule="evenodd" clip-rule="evenodd"-->
    <!--                      d="M4.5778 11.0407C4.5778 11.2514 4.74859 11.4222 4.95928 11.4222H7.49903C7.70138 11.4222 7.89544 11.3418 8.03853 11.1987L15.7766 3.46065C15.9196 3.31757 16 3.12355 16 2.92123C16 2.71892 15.9196 2.52489 15.7766 2.38182L13.6182 0.223386C13.4751 0.0803521 13.2811 0 13.0788 0C12.8765 0 12.6824 0.0803521 12.5393 0.223386L4.80126 7.96147C4.65818 8.10455 4.5778 8.29862 4.5778 8.50097V11.0407ZM14.1576 2.92123L7.18256 9.89627H6.10373V8.81744L13.0788 1.8424L14.1576 2.92123Z"-->
    <!--                      fill="#446D97"></path>-->
    <!--                <path-->
    <!--                    d="M1.52593 14.474V2.26655H5.34076V0.740614H1.52593C0.683183 0.740614 0 1.4238 0 2.26655V14.474C0 15.3168 0.683184 15.9999 1.52593 15.9999H13.7334C14.5761 15.9999 15.2593 15.3168 15.2593 14.474V10.6592H13.7334V14.474H1.52593Z"-->
    <!--                    fill="#446D97"></path>-->
    <!--            </svg>-->
    <!---->
    <!--            <div class="form-group">-->
    <!--                <input type="text" class="call-pane__note-msg form-control" placeholder="Add Note">-->
    <!--                <div class="error-message"></div>-->
    <!--            </div>-->
    <!--            <button class="call-pane__add-note">-->
    <!--                <svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">-->
    <!--                    <path fill-rule="evenodd" clip-rule="evenodd"-->
    <!--                          d="M16.7072 1.70718L6.50008 11.9143L0.292969 5.70718L1.70718 4.29297L6.50008 9.08586L15.293 0.292969L16.7072 1.70718Z"-->
    <!--                          fill="white" />-->
    <!--                </svg>-->
    <!--            </button>-->
    <!--        </div>-->

    <div class="phone-widget__start">
        <div class="phone-widget__start-content">
            <i class="far fa-handshake phone-widget__start-icn"></i>
            <h4 class="phone-widget__start-title">Welcome!</h4>
            <p class="phone-widget__start-subtitle">Connecting...</p>
            <button class="btn phone-widget__start-btn" type="button" style="display: none">
                START
                <i class="fa fa-play ml-2"></i>
            </button>
        </div>
    </div>
  </div>

  <div class="call-pane-calling call-pane-initial" id="call-pane-calling"> </div>

  <div class="call-pane-incoming call-pane-initial" id="call-pane-incoming"> </div>

  <div class="call-pane-outgoing call-pane-initial" id="call-pane-accepted"> </div>

  <div class="call-pane-outgoing call-pane-initial" id="call-pane-outgoing"> </div>

  <!-- Dial popup -->
  <div class="additional-info dial-popup">
    <div class="additional-info__header">
    <span class="additional-info__header-title">Dialpad</span>
        <a href="#" class="additional-info__close">
        <i class="fas fa-times"></i>
        </a>
    </div>
    <div class="additional-info__body">
      <form id="contact-list-calls-ajax" action="/contacts/list-calls-ajax" method="get">
        <input type="text" id="call-pane__dial-number_active_dialpad" class="call-pane__dial-number" name="q" maxlength="16" placeholder="" autocomplete="off" readonly="readonly">
        <a href="#" class="call-pane__dial-clear-all is-shown call_pane_dialpad_clear_number_active_dialpad">
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M7 8.20625L12.7937 14L13.9999 12.7938L8.2062 7.00004L14 1.20621L12.7938 0L7 5.79383L1.2062 0L0 1.20621L5.7938 7.00004L7.97135e-05 12.7938L1.20628 14L7 8.20625Z" fill="white"></path>
          </svg>
        </a>
      </form>
      <ul class="call-pane__dial dial">
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="1">1</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="2">2</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="3">3</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="4">4</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="5">5</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="6">6</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="7">7</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="8">8</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="9">9</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="✱">✱</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="0">0</button></li>
        <li class="dial__item"><button class="dial__btn dialpad_btn_active" value="#">#</button></li>
      </ul>
    </div>
  </div>

  <div class="additional-info add-note" id="add-note"></div>

  <div class="additional-info contact-info" id="contact-info"></div>

</div>

<?php
$userId = Auth::id();
$ajaxCallRedirectGetAgents = Url::to(['/phone/ajax-call-get-agents']);
$ajaxAcceptIncomingCall = Url::to(['/call/ajax-accept-incoming-call']);
$callStatusUrl = Url::to(['/user-call-status/update-status']);
$ajaxMuteUrl = Url::to(['/phone/ajax-mute-participant']);
$ajaxUnMuteUrl = Url::to(['/phone/ajax-unmute-participant']);
$ajaxCallAddNoteUrl = Url::to(['/call/ajax-add-note']);
$updateStatusUrl = Url::to(['/user-call-status/update-status']);
$clearMissedCallsUrl = Url::to(['/call/clear-missed-calls']);
$holdUrl = Url::to(['/phone/ajax-hold-conference-call']);
$unHoldUrl = Url::to(['/phone/ajax-unhold-conference-call']);
$returnHoldCallUrl = Url::to(['/call/return-hold-call']);
$ajaxHangupUrl = Url::to(['/phone/ajax-hangup']);
$sendDigitUrl = Url::to(['/phone/send-digit']);
$prepareCurrentCallsUrl = Url::to(['/phone/prepare-current-calls']);
$callLogInfoUrl = Url::to(['/call/ajax-call-log-info']);
$callInfoUrl = Url::to(['/call/ajax-call-info']);
$clientInfoUrl = Url::to(['/client/ajax-get-info']);
$ajaxRecordingEnableUrl = Url::to(['/phone/ajax-recording-enable']);
$ajaxRecordingDisableUrl = Url::to(['/phone/ajax-recording-disable']);
$ajaxAcceptPriorityCallUrl = Url::to(['/call/ajax-accept-priority-call']);
$ajaxAcceptWarmTransferCallUrl = Url::to(['/call/ajax-accept-warm-transfer-call']);
$ajaxAddPhoneToBlackList = Url::to(['/call/ajax-add-phone-black-list']);
$ajaxCreateLeadUrl = Url::to('/lead/ajax-create-from-phone-widget');
$ajaxCreateLeadWithInvalidClientUrl = Url::to('/lead/ajax-create-from-phone-widget-with-invalid-client');
$ajaxClientGetInfoJsonUrl = Url::to('/client/ajax-get-info-json');
$reconnectUrl = Url::to('/call/reconnect');
$ajaxCallTransferUrl = Url::to(['/phone/ajax-call-transfer']);
$ajaxWarmTransferToUserUrl = Url::to(['/phone/ajax-warm-transfer-to-user']);
$ajaxCallRedirectUrl = Url::to(['/phone/ajax-call-redirect']);
$ajaxGetPhoneListIdUrl = Url::to(['/phone/ajax-get-phone-list-id']);
$ajaxJoinToConferenceUrl = Url::to(['/phone/ajax-join-to-conference']);
$leadViewPageShortUrl = Url::to(['/lead/view'], true);
$getCallHistoryFromNumberUrl = Url::to(['/phone/get-call-history-from-number']);
$ajaxCheckRecording = Url::to(['/phone/ajax-check-recording']);
$getUserByPhoneUrl = Url::to(['/phone/get-user-by-phone']);
$ajaxBlackList = Url::to(['/phone/check-black-phone']);
$ajaxCheckUserForCallUrl = Url::to(['/phone/ajax-check-user-for-call']);
$createCallUrl = Url::to(['/voip/create-call']);

$ucStatus = $userCallStatus->us_type_id ?? UserCallStatus::STATUS_TYPE_OCCUPIED;

$btnHoldShow = Auth::can('PhoneWidget_OnHold') ? 'true' : 'false';
$btnTransferShow = Auth::can('PhoneWidget_Transfer') ? 'true' : 'false';
$canRecordingDisabled = Auth::can('PhoneWidget_CallRecordingDisabled') ? 'true' : 'false';
$canAddBlockList = PhoneBlackListGuard::canAdd($userId) ? 'true' : 'false';
$btnReconnectShow = 'true';

$redialSourceType = Call::SOURCE_REDIAL_CALL;

$conferenceSources = json_encode([
    'listen' => [
        'name' => Call::SOURCE_LIST[Call::SOURCE_LISTEN],
        'id' => Call::SOURCE_LISTEN,
    ],
    'barge' => [
        'name' => Call::SOURCE_LIST[Call::SOURCE_BARGE],
        'id' => Call::SOURCE_BARGE,
    ],
    'coach' => [
        'name' => Call::SOURCE_LIST[Call::SOURCE_COACH],
        'id' => Call::SOURCE_COACH,
    ],
]);

$csrf_param = Yii::$app->request->csrfParam;
$csrf_token = Yii::$app->request->csrfToken;

DeviceAsset::register($this);
$phoneDeviceRemoteLogsEnabled = SettingHelper::phoneDeviceLogsEnabled() ? 'true' : 'false';

$js = <<<JS

window.phoneDeviceRemoteLogsEnabled = $phoneDeviceRemoteLogsEnabled;
window.phoneWidget.initParams = {
    'ajaxCallRedirectGetAgents': '$ajaxCallRedirectGetAgents',
    'acceptCallUrl': '$ajaxAcceptIncomingCall',
    'callStatusUrl': '$callStatusUrl',
    'muteUrl': '$ajaxMuteUrl',
    'unMuteUrl': '$ajaxUnMuteUrl',
    'callAddNoteUrl': '$ajaxCallAddNoteUrl',
    'updateStatusUrl': '$updateStatusUrl',
    'countMissedCalls': $countMissedCalls,
    'clearMissedCallsUrl': '$clearMissedCallsUrl',
    'status': $ucStatus,
    'holdUrl': '$holdUrl',
    'unHoldUrl': '$unHoldUrl',
    'returnHoldCallUrl': '$returnHoldCallUrl',
    'ajaxHangupUrl': '$ajaxHangupUrl',
    'btnHoldShow': $btnHoldShow,
    'btnTransferShow': $btnTransferShow,
    'sendDigitUrl': '$sendDigitUrl',    
    'prepareCurrentCallsUrl': '$prepareCurrentCallsUrl',
    'callLogInfoUrl': '$callLogInfoUrl',
    'callInfoUrl': '$callInfoUrl',
    'clientInfoUrl': '$clientInfoUrl',
    'recordingEnableUrl': '$ajaxRecordingEnableUrl',
    'recordingDisableUrl': '$ajaxRecordingDisableUrl',
    'canRecordingDisabled': $canRecordingDisabled,
    'acceptPriorityCallUrl': '$ajaxAcceptPriorityCallUrl',
    'acceptWarmTransferCallUrl': '$ajaxAcceptWarmTransferCallUrl',
    'addPhoneBlackListUrl': '$ajaxAddPhoneToBlackList',
    'canAddBlockList': $canAddBlockList,
    'ajaxCreateLeadUrl': '$ajaxCreateLeadUrl',
    'ajaxClientGetInfoJsonUrl': '$ajaxClientGetInfoJsonUrl',
    'ajaxCreateLeadWithInvalidClientUrl': '$ajaxCreateLeadWithInvalidClientUrl',
    'btnReconnectShow': $btnReconnectShow,
    'reconnectUrl': '$reconnectUrl',
    'ajaxCallTransferUrl': '$ajaxCallTransferUrl',
    'ajaxWarmTransferToUserUrl': '$ajaxWarmTransferToUserUrl',
    'ajaxCallRedirectUrl': '$ajaxCallRedirectUrl',
    'ajaxGetPhoneListIdUrl': '$ajaxGetPhoneListIdUrl',
    'redialSourceType': parseInt('$redialSourceType'),
    'conferenceSources': $conferenceSources,
    'ajaxJoinToConferenceUrl': '$ajaxJoinToConferenceUrl',
    'csrf_param': '$csrf_param',
    'csrf_token': '$csrf_token',
    'leadViewPageShortUrl': '$leadViewPageShortUrl',
    'getCallHistoryFromNumberUrl': '$getCallHistoryFromNumberUrl',
    'ajaxCheckRecording': '$ajaxCheckRecording',
    'getUserByPhoneUrl': '$getUserByPhoneUrl',
    'ajaxBlackList': '$ajaxBlackList',
    'ajaxCheckUserForCallUrl': '$ajaxCheckUserForCallUrl',
    'phoneNumbers': toSelect($('.custom-phone-select'),  JSON.parse('{$formattedPhoneProject}')),
    'createCallUrl': '$createCallUrl',
    'userId': $userId
};
PhoneWidget.init(window.phoneWidget.initParams);
JS;
$this->registerJs($js);
