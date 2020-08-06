<?php

use sales\auth\Auth;

/** @var View $this */
/** @var \common\models\UserCallStatus $userCallStatus */
/** @var int $countMissedCalls */

$canDialpad = true;
if (!Auth::can('PhoneWidget_Dialpad')) {
    $canDialpad = false;
}

?>
<div class="phone-widget__tab is_active" id="tab-phone">

  <div class="call-pane call-pane-initial is_active">

    <div class="calling-from-info">
      <div class="current-number">
        <div class="custom-phone-select"></div>
      </div>
    </div>
    <div class="call-pane__number">

            <ul class="phone-widget__list-item calls-history suggested-contacts"> </ul>

            <div class="phone-input-wrap">
                <label class="call-pane-label" for="">Calling to <span id="call-to-label" style="color: white"> </span></label>
                <?php

                use common\models\UserCallStatus;
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

        <?php if ($canDialpad): ?>
            <a href="#" class="call-pane__dial-clear-all is-shown call_pane_dialpad_clear_number">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M7 8.20625L12.7937 14L13.9999 12.7938L8.2062 7.00004L14 1.20621L12.7938 0L7 5.79383L1.2062 0L0 1.20621L5.7938 7.00004L7.97135e-05 12.7938L1.20628 14L7 8.20625Z"
                            fill="white" />
                </svg>
            </a>
        <?php else: ?>
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
    <div class="call-pane__dial-block">
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
        <?php if ($canDialpad): ?>
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
  </div>

  <div class="call-pane-calling call-pane-initial" id="call-pane-calling"> </div>

  <div class="call-pane-incoming call-pane-initial" id="call-pane-incoming"> </div>

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
$ajaxCallRedirectGetAgents = Url::to(['/phone/ajax-call-get-agents']);
$ajaxAcceptIncomingCall = Url::to(['/call/ajax-accept-incoming-call']);
$callStatusUrl = Url::to(['/user-call-status/update-status']);
$ajaxSaveCallUrl = Url::to(['/phone/ajax-save-call']);
$ajaxMuteUrl = Url::to(['/phone/ajax-mute-participant']);
$ajaxUnMuteUrl = Url::to(['/phone/ajax-unmute-participant']);
$ajaxCallAddNoteUrl = Url::to(['/call/ajax-add-note']);
$updateStatusUrl = Url::to(['/user-call-status/update-status']);
$clearMissedCallsUrl = Url::to(['/call/clear-missed-calls']);
$currentQueueCallsUrl = Url::to(['/call/current-queue-calls']);
$holdUrl = Url::to(['/phone/ajax-hold-conference-call']);
$unHoldUrl = Url::to(['/phone/ajax-unhold-conference-call']);
$returnHoldCallUrl = Url::to(['/call/return-hold-call']);
$ajaxHangupUrl = Url::to(['/phone/ajax-hangup']);
$sendDigitUrl = Url::to(['/phone/send-digit']);
$prepareCurrentCallsUrl = Url::to(['/phone/prepare-current-calls']);

$ucStatus = $userCallStatus->us_type_id ?? UserCallStatus::STATUS_TYPE_OCCUPIED;

$canDialpad = $canDialpad ? 'true' : 'false';

$btnHoldShow = Auth::can('PhoneWidget_OnHold') ? 'true' : 'false';
$btnTransferShow = Auth::can('PhoneWidget_Transfer') ? 'true' : 'false';

$js = <<<JS
PhoneWidgetCall.init({
    'ajaxCallRedirectGetAgents': '$ajaxCallRedirectGetAgents',
    'acceptCallUrl': '$ajaxAcceptIncomingCall',
    'callStatusUrl': '$callStatusUrl',
    'ajaxSaveCallUrl': '$ajaxSaveCallUrl',
    'muteUrl': '$ajaxMuteUrl',
    'unMuteUrl': '$ajaxUnMuteUrl',
    'callAddNoteUrl': '$ajaxCallAddNoteUrl',
    'updateStatusUrl': '$updateStatusUrl',
    'countMissedCalls': $countMissedCalls,
    'clearMissedCallsUrl': '$clearMissedCallsUrl',
    'currentQueueCallsUrl': '$currentQueueCallsUrl',
    'status': $ucStatus,
    'holdUrl': '$holdUrl',
    'unHoldUrl': '$unHoldUrl',
    'returnHoldCallUrl': '$returnHoldCallUrl',
    'ajaxHangupUrl': '$ajaxHangupUrl',
    'dialpadEnabled': $canDialpad,
    'btnHoldShow': $btnHoldShow,
    'btnTransferShow': $btnTransferShow,
    'sendDigitUrl': '$sendDigitUrl',    
    'prepareCurrentCallsUrl': '$prepareCurrentCallsUrl'    
});
JS;
$this->registerJs($js);
