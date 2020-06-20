let outgoingTpl = '' +
    ' <div class="calling-from-info">' +
    '            <div class="static-number-indicator">' +
    '                <span class="static-number-indicator__label cw-project_name">{{projectName}}</span>' +
    '                <i class="static-number-indicator__separator"></i>' +
    '                <span class="static-number-indicator__name cw-source_name">{{sourceName}}</span>' +
    '            </div>' +
    '        </div>' +
    '        <div class="incall-group">' +
    '            <div class="contact-info-card">' +
    '                <div class="contact-info-card__details">' +
    '                    <div class="contact-info-card__line history-details">' +
    '                        <span class="contact-info-card__label">{{type}}</span>' +
    '                        <div class="contact-info-card__name">' +
    '                                <button class="call-pane__info">' +
    '                                    <i class="user-icon fa fa-user"></i>' +
    '                                    <i class="info-icon fa fa-info"></i>' +
    '                                </button>' +
    '                               <strong id="cw-outgoing-name">' +
    '                                   {{name}}' +
    '                                </strong>' +
    '                        </div>' +
    '                    </div>' +
    '                    <div class="contact-info-card__line history-details">' +
    '                        <span class="contact-info-card__call-type">{{phone}}</span>' +
    '                    </div>' +
    '                </div>' +
    '            </div>' +
    '            <div class="call-pane__call-btns is-pending">' +
    '                <button class="call-pane__start-call calling-state-block">' +
    '                    <div class="call-in-action">' +
    '                      <span class="call-in-action__text">{{status}}</span>' +
    '                      <span class="call-in-action__time">00:00</span>' +
    '                    </div>' +
    '                    <i class="fas fa-phone"></i>' +
    '                </button>' +
    '                <button class="call-pane__end-call" id="cancel-outgoing-call">' +
    '                    <i class="fa fa-phone-slash"></i>' +
    '                </button>' +
    '            </div>' +
    '        </div>'
;
