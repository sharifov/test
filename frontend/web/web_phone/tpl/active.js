let activeTpl =
    '   <div class="calling-from-info">' +
    '        <div class="static-number-indicator">' +
    '           <span class="static-number-indicator__label cw-project_name">{{projectName}}</span>' +
    '           <i class="static-number-indicator__separator"></i>' +
    '           <span class="static-number-indicator__name cw-source_name">{{sourceName}}</span>' +
    '       </div>' +
    '   </div>' +
    '    <div class="contact-info-card">' +
    '      <div class="contact-info-card__details">' +
    '        <div class="contact-info-card__line history-details">' +
    '           <span class="contact-info-card__label">{{type}}</span>' +
    '           <div class="contact-info-card__name">' +
    '               <button class="call-pane__info">' +
    '                   <i class="user-icon fa fa-user"></i>' +
    '                   <i class="info-icon fa fa-info"></i>' +
    '               </button>' +
    '               <strong id="wg-active-call-name">' +
    '                  {{name}}' +
    '               </strong>' +
    '          </div>' +
    '        </div>' +
    '        <div class="contact-info-card__line history-details">' +
    '           <span class="contact-info-card__call-type">{{phone}}</span>' +
    '        </div>' +
    '      </div>' +
    '    </div>' +
    '        <div class="call-pane__call-btns is-on-call">' +
    '            <button class="call-pane__mute" id="call-pane__mute" data-is-muted="false">' +
    '                <i class="fas fa-microphone"></i>' +
    '            </button>' +
    '            <button class="call-pane__start-call calling-state-block">' +
    '                <div class="call-in-action">' +
    '                    <span class="call-in-action__text">on call</span>' +
    '                    <span class="call-in-action__time"></span>' +
    '                </div>' +
    '                <!-- <i class="fas fa-phone"></i> -->' +
    '            </button>' +
    '            <button class="call-pane__end-call" id="cancel-active-call" data-call-id="{{callId}}">' +
    '                <i class="fa fa-phone-slash"></i>' +
    '            </button>' +
    '        </div>' +
    '        <div class="sound-indication">' +
    '            <div class="sound-control-wrap" id="wg-call-volume">' +
    '                <i class="fa fa-volume-down"></i>' +
    '                <div class="sound-controls">' +
    '                    <div class="progres-wrap">' +
    '                        <div class="sound-progress" ></div>' +
    '                        <div class="sound-ovf" style="right: -100%;"></div>' +
    '                    </div>' +
    '                </div>' +
    '            </div>' +
    '            <div class="sound-control-wrap" id="wg-call-microphone">' +
    '                <i class="fa fa-microphone"></i>' +
    '                <div class="sound-controls">' +
    '                <div class="progres-wrap">' +
    '                    <div class="sound-progress" ></div>' +
    '                    <div class="sound-ovf" style="right: -30%;"></div>' +
    '                </div>' +
    '            </div>' +
    '        </div>' +
    '    </div>' +
    '    <ul class="in-call-controls">' +
    '      <li class="in-call-controls__item" data-mode="unhold" id="wg-hold-call" data-call-id="{{callId}}">' +
    '        <a href="#" class="in-call-controls__action">' +
    '          <i class="fa fa-pause"></i>' +
    '          <span>Hold</span>' +
    '        </a>' +
    '      </li>' +
    '      <li class="in-call-controls__item" id="wg-transfer-call">' +
    '        <a href="#" class="in-call-controls__action">' +
    '        <i class="fa fa-random"></i>' +
    '          <span>Transfer Call</span>' +
    '        </a>' +
    '      </li>' +
    '      <li class="in-call-controls__item" id="wg-add-person">' +
    '<!--        <a href="#" class="in-call-controls__action js-add-to-conference" data-toggle-tab="tab-contacts">-->' +
    '        <a href="#" class="in-call-controls__action js-add-to-conference" >' +
    '          <i class="fa fa-plus"></i>' +
    '          <span>Add Person</span>' +
    '        </a>' +
    '      </li>' +
    '      <li class="in-call-controls__item" id="wg-dialpad">' +
    '        <a href="#" class="in-call-controls__action js-toggle-dial">' +
    '        <i class="fa fa-th"></i>' +
    '          <span>Dialpad</span>' +
    '        </a>' +
    '      </li>' +
    '    </ul>' +
    '      <div class="d-flex justify-content-between align-items-center align-content-center">' +
    '          <div class="form-group">' +
    '              <input type="text" class="call-pane__note-msg form-control" id="active_call_add_note" placeholder="Add Note" autocomplete="off">' +
    '              <div class="error-message"></div>' +
    '          </div>' +
    '          <button class="call-pane__add-note" id="active_call_add_note_submit" data-call-id="{{callId}}">' +
    '            <svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">' +
    '                <path fill-rule="evenodd" clip-rule="evenodd"' +
    '                d="M16.7072 1.70718L6.50008 11.9143L0.292969 5.70718L1.70718 4.29297L6.50008 9.08586L15.293 0.292969L16.7072 1.70718Z"' +
    '                fill="white" />' +
    '            </svg>' +
    '          </button>' +
    '      </div>';
