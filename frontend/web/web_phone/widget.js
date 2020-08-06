window.phoneWidget = {};
window.phoneWidget.storage = {};

(function () {
  class SimpleEventDispatcher {
    constructor() {
      this.events = {};
    }

    addListener(event, callback) {
      if (typeof callback !== 'function') {
        console.error(`The listener callback must be a function, the given type is ${typeof callback}`);
        return false;
      }

      if (typeof event !== 'string') {
        console.error(`The event name must be a string, the given type is ${typeof event}`);
        return false;
      }

      if (this.events[event] === undefined) {
        this.events[event] = {
          listeners: []
        };
      }

      this.events[event].listeners.push(callback);
    }

    removeListener(event, callback) {
      if (this.events[event] === undefined) {
        console.error(`This event: ${event} does not exist`);
        return false;
      }

      this.events[event].listeners = this.events[event].listeners.filter(listener => {
        return listener.toString() !== callback.toString();
      });
    }

    dispatch(event, data) {
      if (this.events[event] === undefined) {
        console.error(`This event: ${event} does not exist`);
        return false;
      }

      this.events[event].listeners.forEach(listener => {
        listener(data);
      });
    }

  }

  window.phoneWidget.eventDispatcher = new SimpleEventDispatcher();
})();

window.phoneWidget.events = {
  callUpdate: 'callUpdate',
  conferenceUpdate: 'conferenceUpdate'
};

(function () {
  function Call(data) {
    this.data = data;

    this.canTransfer = function () {
      if (!conferenceBase) {
        return this.data.status === 'In progress';
      }

      return this.data.typeId !== 3 && this.data.status === 'In progress' && !this.data.isInternal;
    };

    this.block = function () {
      this.data.blocked = true;
    };

    this.unBlock = function () {
      this.data.blocked = false;
    };

    this.isBlocked = function () {
      return this.data.blocked === true;
    };

    this.canHoldUnHold = function () {
      return this.data.typeId !== 3 && this.data.status === 'In progress' && (!this.data.isInternal || this.data.isInternal && this.data.isConferenceCreator);
    };

    this.setHoldRequestState = function () {
      if (this.isBlocked()) {
        // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
        return false;
      }

      if (!this.canHoldUnHold()) {
        // createNotify('Error', 'Hold or UnHold disallow.', 'error');
        return false;
      }

      if (this.data.isHold) {
        createNotify('Error', 'Call is already Hold.', 'error');
        return false;
      }

      this.data.sentHoldUnHoldRequest = true;
      this.block();
      this.save();
      return true;
    };

    this.setUnHoldRequestState = function () {
      if (this.isBlocked()) {
        // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
        return false;
      }

      if (!this.canHoldUnHold()) {
        // createNotify('Error', 'Hold or UnHold disallow.', 'error');
        return false;
      }

      if (!this.data.isHold) {
        createNotify('Error', 'Call is already UnHold.', 'error');
        return false;
      }

      this.data.sentHoldUnHoldRequest = true;
      this.block();
      this.save();
      return true;
    };

    this.unSetHoldUnHoldRequestState = function () {
      this.data.sentHoldUnHoldRequest = false;
      this.unBlock();
      this.save();
    };

    this.isSentHoldUnHoldRequestState = function () {
      return this.data.sentHoldUnHoldRequest === true;
    };

    this.hold = function () {
      this.unBlock();
      this.data.sentHoldUnHoldRequest = false;
      this.data.holdStartTime = Date.now();
      this.data.isHold = true;
      this.save();
    };

    this.unHold = function () {
      this.unBlock();
      this.data.sentHoldUnHoldRequest = false;
      this.data.holdStartTime = 0;
      this.data.isHold = false;
      this.save();
    };

    this.setMuteRequestState = function () {
      if (this.isBlocked()) {
        // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
        return false;
      }

      if (this.data.isMute) {
        createNotify('Error', 'Call is already Mute.', 'error');
        return false;
      }

      this.data.sentMuteUnMuteRequest = true;
      this.block();
      this.save();
      return true;
    };

    this.setUnMuteRequestState = function () {
      if (this.isBlocked()) {
        // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
        return false;
      }

      if (!this.data.isMute) {
        createNotify('Error', 'Call is already UnMute.', 'error');
        return false;
      }

      this.data.sentMuteUnMuteRequest = true;
      this.block();
      this.save();
      return true;
    };

    this.unSetMuteUnMuteRequestState = function () {
      this.data.sentMuteUnMuteRequest = false;
      this.unBlock();
      this.save();
    };

    this.isSentMuteUnMuteRequestState = function () {
      return this.data.sentMuteUnMuteRequest === true;
    };

    this.mute = function () {
      this.unBlock();
      this.data.sentMuteUnMuteRequest = false;
      this.data.isMute = true;
      this.save();
    };

    this.unMute = function () {
      this.unBlock();
      this.data.sentMuteUnMuteRequest = false;
      this.data.isMute = false;
      this.save();
    };

    this.setHangupRequestState = function () {
      if (this.isBlocked()) {
        // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
        return false;
      }

      this.data.sentHangupRequest = true;
      this.block();
      this.save();
      return true;
    };

    this.unSetHangupRequestState = function () {
      this.data.sentHangupRequest = false;
      this.unBlock();
      this.save();
    };

    this.isSentHangupRequestState = function () {
      return this.data.sentHangupRequest === true;
    };

    this.setReturnHoldCallRequestState = function () {
      if (this.isBlocked()) {
        // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
        return false;
      }

      if (this.data.status !== 'Hold') {
        createNotify('Error', 'Call is not in status Hold.', 'error');
        return false;
      }

      this.data.sentReturnHoldCallRequest = true;
      this.block();
      this.save();
      return true;
    };

    this.unSetReturnHoldCallRequestState = function () {
      this.data.sentReturnHoldCallRequest = false;
      this.unBlock();
      this.save();
    };

    this.isSentReturnHoldCallRequestState = function () {
      return this.data.sentReturnHoldCallRequest === true;
    };

    this.setAcceptCallRequestState = function () {
      if (this.isBlocked()) {
        // createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
        return false;
      }

      this.data.sentAcceptCallRequest = true;
      this.block();
      this.save();
      return true;
    };

    this.unSetAcceptCallRequestState = function () {
      this.data.sentAcceptCallRequest = false;
      this.unBlock();
      this.save();
    };

    this.isSentAcceptCallRequestState = function () {
      return this.data.sentAcceptCallRequest === true;
    };

    this.setAddNoteRequestState = function () {
      if (this.isBlocked()) {
        createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
        return false;
      }

      this.data.sentAddNoteRequest = true;
      this.block();
      this.save();
      return true;
    };

    this.unSetAddNoteRequestState = function () {
      this.data.sentAddNoteRequest = false;
      this.unBlock();
      this.save();
    };

    this.isSentAddNoteRequestState = function () {
      return this.data.sentAddNoteRequest === true;
    };

    this.setRejectInternalRequest = function () {
      if (this.isBlocked()) {
        createNotify('Error', 'Call is blocked. Please wait some seconds.', 'error');
        return false;
      }

      this.data.sentRejectInternalRequest = true;
      this.block();
      this.save();
      return true;
    };

    this.unSetRejectInternalRequest = function () {
      this.data.sentRejectInternalRequest = false;
      this.unBlock();
      this.save();
    };

    this.isSentRejectInternalRequest = function () {
      return this.data.sentRejectInternalRequest === true;
    };

    this.getDuration = function () {
      let duration = this.data.duration || 0;

      if (this.data.timeQueuePushed) {
        return Math.floor((Date.now() - parseInt(this.data.timeQueuePushed)) / 1000) + parseInt(duration);
      }

      return duration;
    };

    this.getHoldDuration = function () {
      let duration = 0;

      if (this.data.holdStartTime) {
        duration = Math.floor((Date.now() - parseInt(this.data.holdStartTime)) / 1000);
      }

      return duration;
    };

    this.clone = function () {
      return new Call(this.data);
    };

    this.save = function () {
      window.phoneWidget.eventDispatcher.dispatch(this.getEventUpdateName(), {
        call: this
      });
    };

    this.getEventUpdateName = function () {
      return window.phoneWidget.events.callUpdate + this.data.callSid;
    };
  }

  window.phoneWidget.call = {
    Call: Call
  };
})();

(function () {
  function Conference(data) {
    this.data = data;

    this.getCountParticipants = function () {
      return this.data.participants.length;
    };

    this.getParticipants = function () {
      let participants = [];
      let timeStoragePushed = this.data.timeStoragePushed;
      this.data.participants.forEach(function (participant) {
        participant.timeStoragePushed = timeStoragePushed;
        participants.push(new Participant(participant));
      });
      return participants;
    };

    this.getDuration = function () {
      let duration = this.data.duration || 0;

      if (this.data.timeStoragePushed) {
        return Math.floor((Date.now() - parseInt(this.data.timeStoragePushed)) / 1000) + parseInt(duration);
      }

      return duration;
    };

    this.clone = function () {
      return new Conference(this.data);
    };

    this.getEventUpdateName = function () {
      return window.phoneWidget.events.conferenceUpdate + this.data.sid;
    };

    this.save = function () {
      window.phoneWidget.eventDispatcher.dispatch(this.getEventUpdateName(), {
        conference: this
      });
    };
  }

  function Participant(data) {
    this.data = data;

    this.getDuration = function () {
      let duration = this.data.duration || 0;

      if (this.data.timeStoragePushed) {
        return Math.floor((Date.now() - parseInt(this.data.timeStoragePushed)) / 1000) + parseInt(duration);
      }

      return duration;
    };

    this.clone = function () {
      return new Participant(this.data);
    };
  }

  window.phoneWidget.conference = {
    Conference: Conference
  };
})();

(function () {
  function CallRequester() {
    this.settings = {
      'holdUrl': '',
      'unHoldUrl': '',
      'acceptCallUrl': '',
      'muteUrl': '',
      'unMuteUrl': '',
      'returnHoldCallUrl': '',
      'ajaxHangupUrl': '',
      'callAddNoteUrl': '',
      'sendDigitUrl': '',
      'prepareCurrentCallsUrl': ''
    };

    this.init = function (settings) {
      Object.assign(this.settings, settings);
    };

    this.hold = function (call) {
      //todo remove after removed old widget
      let btn = $('.btn-hold-call');
      btn.html('<i class="fa fa-spinner fa-spin"> </i> <span>On Hold</span>');
      btn.prop('disabled', true);
      $.ajax({
        type: 'post',
        data: {
          'sid': call.data.callSid
        },
        url: this.settings.holdUrl
      }).done(function (data) {
        if (data.error) {
          createNotify('Hold', data.message, 'error');
          btn.html('<i class="fa fa-pause"> </i> <span>Hold</span>');
          btn.prop('disabled', false);
          call.unSetHoldUnHoldRequestState();
        }
      }).fail(function () {
        createNotify('Hold', 'Server error', 'error');
        btn.html('<i class="fa fa-pause"> </i> <span>Hold</span>');
        btn.prop('disabled', false);
        call.unSetHoldUnHoldRequestState();
      });
    };

    this.unHold = function (call) {
      //todo remove after removed old widget
      let btn = $('.btn-hold-call');
      btn.html('<i class="fa fa-spinner fa-spin"> </i> <span>Resume</span>');
      btn.prop('disabled', true);
      $.ajax({
        type: 'post',
        data: {
          'sid': call.data.callSid
        },
        url: this.settings.unHoldUrl
      }).done(function (data) {
        if (data.error) {
          createNotify('Resume', data.message, 'error');
          btn.html('<i class="fa fa-play"> </i> <span>Resume</span>');
          btn.prop('disabled', false);
          call.unSetHoldUnHoldRequestState();
        }
      }).fail(function () {
        createNotify('Resume', 'Server error', 'error');
        btn.html('<i class="fa fa-play"> </i> <span>Resume</span>');
        btn.prop('disabled', false);
        call.unSetHoldUnHoldRequestState();
      });
    };

    this.accept = function (call) {
      $.ajax({
        type: 'post',
        url: this.settings.acceptCallUrl,
        dataType: 'json',
        data: {
          act: 'accept',
          call_sid: call.data.callSid
        }
      }).done(function (data) {
        if (data.error) {
          createNotify('Accept Call', data.message, 'error');
          call.unSetAcceptCallRequestState();
        }
      }).fail(function () {
        createNotify('Accept Call', 'Server error', 'error');
        call.unSetAcceptCallRequestState();
      });
    };

    this.mute = function (call) {
      $.ajax({
        type: 'post',
        data: {
          'sid': call.data.callSid
        },
        url: this.settings.muteUrl
      }).done(function (data) {
        if (data.error) {
          createNotify('Mute', data.message, 'error');
          call.unSetMuteUnMuteRequestState();
        }
      }).fail(function () {
        createNotify('Mute', 'Server error', 'error');
        call.unSetMuteUnMuteRequestState();
      });
    };

    this.unMute = function (call) {
      $.ajax({
        type: 'post',
        data: {
          'sid': call.data.callSid
        },
        url: this.settings.unMuteUrl
      }).done(function (data) {
        if (data.error) {
          createNotify('UnMute', data.message, 'error');
          call.unSetMuteUnMuteRequestState();
        }
      }).fail(function () {
        createNotify('UnMute', 'Server error', 'error');
        call.unSetMuteUnMuteRequestState();
      });
    };

    this.returnHoldCall = function (call) {
      $.ajax({
        type: 'post',
        url: this.settings.returnHoldCallUrl,
        dataType: 'json',
        data: {
          call_sid: call.data.callSid
        }
      }).done(function (data) {
        if (data.error) {
          createNotify('Return Hold Call', data.message, 'error');
          call.unSetReturnHoldCallRequestState();
        }
      }).fail(function () {
        createNotify('Return Hold Call', 'Server error', 'error');
        call.unSetReturnHoldCallRequestState();
      });
    };

    this.hangupOutgoingCall = function (call) {
      $.ajax({
        type: 'post',
        data: {
          'sid': call.data.callSid
        },
        url: this.settings.ajaxHangupUrl
      }).done(function (data) {
        if (data.error) {
          createNotify('Hangup', data.message, 'error');
          call.unSetHangupRequestState();
        }
      }).fail(function () {
        createNotify('Hangup', 'Server error', 'error');
        call.unSetHangupRequestState();
      });
    };

    this.addNote = function (call, note, $container) {
      $.ajax({
        type: 'post',
        data: {
          note: note,
          callSid: call.data.callSid
        },
        url: this.settings.callAddNoteUrl,
        dataType: 'json'
      }).done(function (data) {
        if (data.error) {
          createNotify('Add Note', data.message, 'error');
        } else {
          createNotify('Add Note', data.message, 'success');
          $container.value = '';
        }

        call.unSetAddNoteRequestState();
      }).fail(function () {
        createNotify('Add Note', 'Server error', 'error');
        call.unSetAddNoteRequestState();
      });
    };

    this.sendDigit = function (conferenceSid, digit) {
      $.ajax({
        type: 'post',
        data: {
          conference_sid: conferenceSid,
          digit: digit
        },
        url: this.settings.sendDigitUrl,
        dataType: 'json'
      }).done(function (data) {
        if (data.error) {
          createNotify('Send digit', data.message, 'error');
        }
      }).fail(function () {
        createNotify('Send digit', 'Server error', 'error');
      });
    };

    this.acceptInternalCall = function (call, connection) {
      $.ajax({
        type: 'post',
        data: {},
        url: this.settings.prepareCurrentCallsUrl,
        dataType: 'json'
      }).done(function (data) {
        if (data.error) {
          createNotify('Prepare current call', data.message, 'error');
          call.unSetAcceptCallRequestState();
        } else {
          connection.accept();
        }
      }).fail(function () {
        createNotify('Prepare current call', 'Server error', 'error');
        call.unSetAcceptCallRequestState();
      });
    };
  }

  return window.phoneWidget.requesters = {
    CallRequester: CallRequester
  };
})();

(function () {
  function OldWidget() {
    this.hold = function () {
      let btn = $('.btn-hold-call');
      btn.html('<i class="fa fa-play"> </i> <span>Resume</span>');
      btn.attr('data-mode', 'hold');
      btn.prop('disabled', false);
    };

    this.unHold = function () {
      let btn = $('.btn-hold-call');
      btn.html('<i class="fa fa-pause"> </i> <span>Hold</span>');
      btn.attr('data-mode', 'unhold');
      btn.prop('disabled', false);
    };
  }

  window.phoneWidget.oldWidget = new OldWidget();
})();

class PhoneWidgetTimer extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      duration: 0,
      timeStart: 0
    };
  }

  componentDidMount() {
    this.startTimer();
  }

  componentDidUpdate(prevProps) {
    if (this.state.timeStart !== this.props.timeStart) {
      this.startTimer();
    }
  }

  componentWillUnmount() {
    clearInterval(this.timer);
  }

  startTimer() {
    clearInterval(this.timer);
    this.setState({
      duration: this.props.duration,
      timeStart: this.props.timeStart
    });
    this.timer = setInterval(() => this.setState({
      duration: this.state.duration + 1
    }), 1000);
  }

  formatDuration(duration) {
    let out = '';
    let hours = Math.floor(duration / 60 / 60);
    let minutes = Math.floor(duration / 60) - hours * 60;
    let seconds = duration % 60;

    if (hours > 0) {
      out = hours.toString().padStart(2, '0') + ':';
    }

    out += minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
    return out;
  }

  render() {
    if (this.props.styleClass && this.state.duration > 3599) {
      return React.createElement("span", {
        className: this.props.styleClass
      }, this.formatDuration(this.state.duration));
    }

    return this.formatDuration(this.state.duration);
  }

}

class CallActionTimer extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      duration: 0,
      timeStart: 0
    };
  }

  componentDidMount() {
    this.startTimer();
  }

  componentDidUpdate(prevProps) {
    if (this.state.timeStart !== this.props.timeStart) {
      this.startTimer();
    }
  }

  componentWillUnmount() {
    clearInterval(this.timer);
  }

  startTimer() {
    clearInterval(this.timer);
    this.setState({
      duration: this.props.duration,
      timeStart: this.props.timeStart
    });
    this.timer = setInterval(() => this.setState({
      duration: this.state.duration + 1
    }), 1000);
  }

  formatDuration(duration) {
    let out = '';
    let hours = Math.floor(duration / 60 / 60);
    let minutes = Math.floor(duration / 60) - hours * 60;
    let seconds = duration % 60;

    if (hours > 0) {
      out = hours.toString().padStart(2, '0') + ':';
    }

    out += minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
    return out;
  }

  render() {
    return React.createElement(React.Fragment, null, React.createElement("span", {
      className: "call-in-action__time"
    }, this.formatDuration(this.state.duration)));
  }

}

class ActivePane extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      call: props.call
    };
  }

  componentDidMount() {
    window.phoneWidget.eventDispatcher.addListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
  }

  componentWillUnmount() {
    window.phoneWidget.eventDispatcher.removeListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
  }

  callUpdateHandler() {
    let self = this;
    return function (event) {
      //active
      self.setState({
        call: event.call
      });
    };
  }

  render() {
    let call = this.state.call;
    return React.createElement(React.Fragment, null, React.createElement(CallInfo, {
      project: call.data.project,
      source: call.data.source
    }), React.createElement(ActiveContactInfo, {
      call: call
    }), React.createElement("div", {
      className: "actions-container"
    }, React.createElement(CallBtns, {
      call: call
    }), React.createElement(SoundIndication, null)), React.createElement(ActivePaneControls, {
      call: call,
      controls: this.props.controls
    }));
  }

}

function ActiveContactInfo(props) {
  let call = props.call;
  return React.createElement("div", {
    className: "contact-info-card"
  }, React.createElement("div", {
    className: "contact-info-card__details"
  }, React.createElement("div", {
    className: "contact-info-card__line history-details"
  }, call.data.typeId !== 3 ? React.createElement("span", {
    className: "contact-info-card__label"
  }, call.data.type) : '', React.createElement("div", {
    className: "contact-info-card__name"
  }, React.createElement("button", {
    className: "call-pane__info"
  }, React.createElement("i", {
    className: "user-icon fa fa-user"
  }, " "), React.createElement("i", {
    className: "info-icon fa fa-info"
  }, " ")), React.createElement("strong", null, call.data.contact.name))), React.createElement("div", {
    className: "contact-info-card__line history-details"
  }, React.createElement("span", {
    className: "contact-info-card__call-type"
  }, call.data.contact.phone))));
}

function CallBtns(props) {
  let call = props.call;
  let paneBtnClass = 'call-pane__call-btns';

  if (call.data.isHold) {
    paneBtnClass = paneBtnClass + ' is-on-hold';
  } else {
    paneBtnClass = paneBtnClass + ' is-on-call';
  }

  return React.createElement("div", {
    className: paneBtnClass
  }, React.createElement("button", {
    className: "call-pane__mute",
    id: "call-pane__mute",
    disabled: call.data.isListen || call.data.isHold || call.isSentMuteUnMuteRequestState(),
    "data-call-sid": call.data.callSid,
    "data-is-muted": call.data.isMute,
    "data-active": !(call.data.isListen || call.data.isHold)
  }, call.isSentMuteUnMuteRequestState() ? React.createElement("i", {
    className: "fa fa-spinner fa-spin"
  }, " ") : call.data.isMute ? React.createElement("i", {
    className: "fas fa-microphone-alt-slash"
  }, " ") : React.createElement("i", {
    className: "fas fa-microphone"
  }, " ")), call.data.isHold ? React.createElement(CallingStateBlockHold, {
    call: call
  }) : React.createElement(CallingStateBlock, {
    call: call
  }), React.createElement("button", {
    className: "call-pane__end-call",
    id: "cancel-active-call",
    "data-call-sid": call.data.callSid,
    disabled: call.isSentHangupRequestState()
  }, call.isSentHangupRequestState() ? React.createElement("i", {
    className: "fa fa-spinner fa-spin"
  }, " ") : React.createElement("i", {
    className: "fa fa-phone-slash"
  }, " ")));
}

function CallingStateBlock(props) {
  let call = props.call;
  return React.createElement("button", {
    className: call.data.isListen || call.data.isCoach ? 'call-pane__start-call calling-state-block join' : 'call-pane__start-call calling-state-block'
  }, React.createElement("div", {
    className: "call-in-action"
  }, call.data.isListen || call.data.isCoach ? React.createElement("i", {
    className: "fa fa-headphones-alt"
  }, " ") : '', React.createElement("span", {
    className: "call-in-action__text"
  }, call.data.isCoach ? 'Coaching' : call.data.isListen ? 'Listening' : call.data.isBarge ? 'Barge' : 'on call'), React.createElement("span", {
    className: "call-in-action__time"
  }, React.createElement(PhoneWidgetTimer, {
    duration: call.getDuration(),
    timeStart: Date.now(),
    styleClass: "more"
  }))));
}

function CallingStateBlockHold(props) {
  let call = props.call;
  return React.createElement("button", {
    className: "call-pane__start-call calling-state-block"
  }, React.createElement("div", {
    className: "call-in-action"
  }, React.createElement("span", {
    className: "call-in-action__text"
  }, "on hold"), React.createElement("span", {
    className: "call-in-action__time"
  }, React.createElement(PhoneWidgetTimer, {
    duration: call.getHoldDuration(),
    timeStart: Date.now(),
    styleClass: "more"
  }))));
}

function SoundIndication() {
  const sound_ovf_100 = {
    'right': '-100%'
  };
  const sound_ovf_30 = {
    'right': '-30%'
  };
  return React.createElement("div", {
    className: "sound-indication"
  }, React.createElement("div", {
    className: "sound-control-wrap",
    id: "wg-call-volume"
  }, React.createElement("i", {
    className: "fa fa-volume-down"
  }, " "), React.createElement("div", {
    className: "sound-controls"
  }, React.createElement("div", {
    className: "progres-wrap"
  }, React.createElement("div", {
    className: "sound-progress"
  }, " "), React.createElement("div", {
    className: "sound-ovf",
    style: sound_ovf_100
  }, " ")))), React.createElement("div", {
    className: "sound-control-wrap",
    id: "wg-call-microphone"
  }, React.createElement("i", {
    className: "fa fa-microphone"
  }, " "), React.createElement("div", {
    className: "sound-controls"
  }, React.createElement("div", {
    className: "progres-wrap"
  }, React.createElement("div", {
    className: "sound-progress"
  }, " "), React.createElement("div", {
    className: "sound-ovf",
    style: sound_ovf_30
  }, " ")))));
}

class IncomingPane extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      call: props.call
    };
  }

  componentDidMount() {
    window.phoneWidget.eventDispatcher.addListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
  }

  componentWillUnmount() {
    window.phoneWidget.eventDispatcher.removeListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
  }

  callUpdateHandler() {
    let self = this;
    return function (event) {
      //incoming
      self.setState({
        call: event.call
      });
    };
  }

  render() {
    let call = this.state.call;
    return React.createElement(React.Fragment, null, React.createElement(CallInfo, {
      project: call.data.project,
      source: call.data.source
    }), React.createElement("div", {
      className: "contact-info-card"
    }, React.createElement("div", {
      className: "contact-info-card__details"
    }, React.createElement("div", {
      className: "contact-info-card__line history-details"
    }, React.createElement("span", {
      className: "contact-info-card__label"
    }, call.data.type), React.createElement("div", {
      className: "credential"
    }, React.createElement("div", {
      className: "contact-info-card__name"
    }, React.createElement("button", {
      className: "call-pane__info"
    }, React.createElement("i", {
      className: "user-icon fa fa-user"
    }, " "), React.createElement("i", {
      className: "info-icon fa fa-info"
    }, " ")), React.createElement("strong", null, call.data.contact.name)), React.createElement("div", {
      className: "contact-info-card__phone"
    }, React.createElement("span", null, " ")))), React.createElement("div", {
      className: "contact-info-card__line history-details"
    }, React.createElement("span", {
      className: "contact-info-card__call-type"
    }, call.data.contact.phone)))), React.createElement(IncomingActions, {
      call: call
    }));
  }

}

function IncomingActions(props) {
  let call = props.call;

  if (call.data.isInternal) {
    return React.createElement("div", {
      className: "actions-container"
    }, React.createElement("div", {
      className: "call-pane__call-btns"
    }, React.createElement("button", {
      className: "call-pane__end-call end-internal",
      id: "hide-incoming-call",
      "data-call-sid": call.data.callSid
    }, React.createElement("i", {
      className: "fa fa-angle-double-right"
    }, " ")), React.createElement("button", {
      className: "call-pane__start-call calling-state-block",
      "data-call-sid": call.data.callSid,
      onClick: () => acceptInternalCall(call)
    }, call.isSentAcceptCallRequestState() ? React.createElement("i", {
      className: "fa fa-spinner fa-spin"
    }, " ") : React.createElement("i", {
      className: "fas fa-phone"
    }, " ")), React.createElement("button", {
      className: "call-pane__end-call",
      "data-call-sid": call.data.callSid,
      onClick: () => rejectInternalCall(call)
    }, call.isSentRejectInternalRequest() ? React.createElement("i", {
      className: "fa fa-spinner fa-spin"
    }, " ") : React.createElement("i", {
      className: "fa fa-phone-slash"
    }, " "))));
  }

  return React.createElement("div", {
    className: "actions-container"
  }, React.createElement("div", {
    className: "call-pane__call-btns"
  }, React.createElement("button", {
    className: "call-pane__start-call calling-state-block",
    id: "btn-accept-call",
    "data-from-internal": call.data.fromInternal,
    "data-call-sid": call.data.callSid,
    disabled: call.isSentAcceptCallRequestState()
  }, call.isSentAcceptCallRequestState() ? React.createElement("i", {
    className: "fa fa-spinner fa-spin"
  }, " ") : React.createElement("i", {
    className: "fas fa-phone"
  }, " ")), React.createElement("button", {
    className: "call-pane__end-call",
    id: "hide-incoming-call",
    "data-call-sid": call.data.callSid
  }, React.createElement("i", {
    className: "fa fa-angle-double-right"
  }, " "))));
}

function ActivePaneControls(props) {
  return React.createElement("ul", {
    className: "in-call-controls"
  }, props.controls.hold.show ? React.createElement(ButtonHold, {
    call: props.call,
    controls: props.controls
  }) : '', props.controls.transfer.show ? React.createElement(ButtonTransfer, {
    call: props.call,
    controls: props.controls
  }) : '', React.createElement(ButtonAddPerson, {
    call: props.call,
    controls: props.controls
  }), React.createElement(ButtonDialpad, {
    call: props.call,
    controls: props.controls
  }), React.createElement(ButtonAddNote, {
    call: props.call,
    controls: props.controls
  }));
}

function ButtonHold(props) {
  return React.createElement("li", {
    className: "in-call-controls__item",
    "data-mode": props.call.data.isHold ? 'hold' : 'unhold',
    id: "wg-hold-call",
    "data-call-sid": props.call.data.callSid,
    "data-active": props.controls.hold.active
  }, React.createElement("a", {
    href: "#",
    className: "in-call-controls__action"
  }, props.call.isSentHoldUnHoldRequestState() ? React.createElement("i", {
    className: "fa fa-spinner fa-spin hold-loader"
  }, " ") : props.call.data.isHold ? React.createElement("i", {
    className: "fa fa-play"
  }, " ") : React.createElement("i", {
    className: "fa fa-pause"
  }, " "), React.createElement("span", null, props.call.data.isHold ? 'Resume' : 'On Hold')));
}

function ButtonTransfer(props) {
  return React.createElement("li", {
    className: "in-call-controls__item wg-transfer-call",
    "data-call-sid": props.call.data.callSid,
    "data-active": props.controls.transfer.active
  }, React.createElement("a", {
    href: "#",
    className: "in-call-controls__action"
  }, React.createElement("i", {
    className: "fa fa-random"
  }, " "), React.createElement("span", null, "Transfer")));
}

function ButtonAddPerson(props) {
  return React.createElement("li", {
    className: "in-call-controls__item",
    id: "wg-add-person",
    "data-active": props.controls.addPerson.active
  }, React.createElement("a", {
    href: "#",
    className: "in-call-controls__action js-add-to-conference",
    "data-toggle-tab": "tab-contacts"
  }, React.createElement("i", {
    className: "fa fa-plus"
  }, " "), React.createElement("span", null, "Add Person")));
}

function ButtonDialpad(props) {
  return React.createElement("li", {
    className: "in-call-controls__item",
    id: "wg-dialpad",
    "data-active": props.controls.dialpad.active
  }, React.createElement("a", {
    href: "#",
    className: "in-call-controls__action js-toggle-dial"
  }, React.createElement("i", {
    className: "fa fa-th"
  }, " "), React.createElement("span", null, "Dialpad")));
}

function ButtonAddNote(props) {
  return React.createElement("li", {
    className: "in-call-controls__item",
    id: "wg-add-note"
  }, React.createElement("a", {
    href: "#",
    className: "in-call-controls__action"
  }, React.createElement("i", {
    className: "fa fa-newspaper-o"
  }, " "), React.createElement("span", null, "Add Note")));
}

function CallInfo(props) {
  return React.createElement("div", {
    className: "calling-from-info"
  }, React.createElement("div", {
    className: "static-number-indicator"
  }, props.project ? React.createElement("span", {
    className: "static-number-indicator__label"
  }, props.project) : '', props.project && props.source ? React.createElement("i", {
    className: "static-number-indicator__separator"
  }, " ") : '', props.source ? React.createElement("span", {
    className: "static-number-indicator__name"
  }, props.source) : ''));
}

class ListItem extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      call: props.call
    };
  }

  componentDidMount() {
    window.phoneWidget.eventDispatcher.addListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
  }

  componentWillUnmount() {
    window.phoneWidget.eventDispatcher.removeListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
  }

  callUpdateHandler() {
    let self = this;
    return function (event) {
      //queue
      self.setState({
        call: event.call
      });
    };
  }

  render() {
    let call = this.state.call;
    return React.createElement("li", {
      className: "call-in-progress__list-item"
    }, React.createElement("div", {
      className: "call-in-progress__call-item call-list-item",
      "data-call-status": call.data.queue
    }, React.createElement("div", {
      className: "call-list-item__info"
    }, React.createElement("ul", {
      className: "call-list-item__info-list call-info-list"
    }, React.createElement("li", {
      className: "call-info-list__item"
    }, React.createElement("b", {
      className: "call-info-list__contact-icon"
    }, React.createElement("i", {
      className: "fa fa-user"
    }, " ")), React.createElement("span", {
      className: "call-info-list__name"
    }, call.data.contact.name)), call.data.contact.company ? React.createElement("li", {
      className: "call-info-list__item"
    }, React.createElement("span", {
      className: "call-info-list__company"
    }, call.data.contact.company)) : '', React.createElement("li", {
      className: "call-info-list__item"
    }, React.createElement("span", {
      className: "call-info-list__number"
    }, call.data.contact.phone))), React.createElement("div", {
      className: "call-list-item__info-action call-info-action"
    }, React.createElement("span", {
      className: "call-info-action__timer"
    }, React.createElement(PhoneWidgetTimer, {
      duration: call.getDuration(),
      timeStart: Date.now()
    })), call.data.queue === 'inProgress' && call.data.typeId !== 3 || call.data.typeId === 3 && !call.data.isListen && !call.data.isHold ? React.createElement("a", {
      href: "#",
      className: "call-info-action__more"
    }, React.createElement("i", {
      className: "fa fa-ellipsis-h"
    }, " ")) : ''), React.createElement(ListItemMenu, {
      call: call
    })), React.createElement("div", {
      className: "call-list-item__main-action"
    }, React.createElement("a", {
      href: "#",
      className: "call-list-item__main-action-trigger",
      "data-type-action": call.data.queue === 'inProgress' ? 'hangup' : call.data.queue === 'hold' ? 'return' : call.data.isInternal ? 'acceptInternal' : 'accept',
      "data-call-sid": call.data.callSid,
      "data-from-internal": call.data.fromInternal
    }, call.isSentAcceptCallRequestState() || call.isSentHangupRequestState() || call.isSentReturnHoldCallRequestState() ? React.createElement("i", {
      className: "fa fa-spinner fa-spin"
    }) : React.createElement(React.Fragment, null, React.createElement("i", {
      className: "phone-icon phone-icon--start fa fa-phone"
    }), " ", React.createElement("i", {
      className: "phone-icon phone-icon--end fa fa-phone-slash"
    }), " ")))));
  }

}

function ListItemMenu(props) {
  let call = props.call;

  if (call.data.queue !== 'inProgress') {
    return null;
  }

  if (call.data.typeId === 3) {
    return React.createElement(ListItemMenuJoinCall, {
      call: call
    });
  }

  if (call.data.isInternal) {
    if (call.data.isConferenceCreator) {
      return React.createElement("ul", {
        className: "call-list-item__menu call-item-menu"
      }, React.createElement("li", {
        className: "call-item-menu__list-item"
      }, React.createElement("a", {
        href: "#",
        className: "call-item-menu__close"
      }, React.createElement("i", {
        className: "fa fa-chevron-right"
      }, " "))), React.createElement(React.Fragment, null, React.createElement(ListItemBtnHold, {
        call: call
      }), React.createElement(ListItemBtnMute, {
        call: call
      })));
    }

    return React.createElement("ul", {
      className: "call-list-item__menu call-item-menu"
    }, React.createElement("li", {
      className: "call-item-menu__list-item"
    }, React.createElement("a", {
      href: "#",
      className: "call-item-menu__close"
    }, React.createElement("i", {
      className: "fa fa-chevron-right"
    }, " "))), React.createElement(ListItemBtnMute, {
      call: call
    }));
  }

  return React.createElement("ul", {
    className: "call-list-item__menu call-item-menu"
  }, React.createElement("li", {
    className: "call-item-menu__list-item"
  }, React.createElement("a", {
    href: "#",
    className: "call-item-menu__close"
  }, React.createElement("i", {
    className: "fa fa-chevron-right"
  }, " "))), React.createElement(ListItemBtnTransfer, {
    call: call
  }), conferenceBase ? React.createElement(React.Fragment, null, React.createElement(ListItemBtnHold, {
    call: call
  }), React.createElement(ListItemBtnMute, {
    call: call
  })) : '');
}

function ListItemBtnTransfer(props) {
  return React.createElement("li", {
    className: "call-item-menu__list-item wg-transfer-call",
    "data-call-sid": props.call.data.callSid
  }, React.createElement("a", {
    href: "#",
    className: "call-item-menu__transfer"
  }, React.createElement("i", {
    className: "fa fa-random"
  }, " ")));
}

function ListItemBtnHold(props) {
  let call = props.call;
  return React.createElement("li", {
    className: "call-item-menu__list-item list_item_hold",
    "data-mode": call.data.isHold ? 'hold' : 'unhold',
    "data-call-sid": call.data.callSid
  }, React.createElement("a", {
    href: "#",
    className: "call-item-menu__transfer"
  }, call.isSentHoldUnHoldRequestState() ? React.createElement("i", {
    className: "fa fa-spinner fa-spin"
  }, " ") : call.data.isHold ? React.createElement("i", {
    className: "fa fa-play"
  }, " ") : React.createElement("i", {
    className: "fa fa-pause"
  }, " ")));
}

function ListItemBtnMute(props) {
  let call = props.call;

  if (call.data.isHold) {
    return null;
  }

  return React.createElement("li", {
    className: "call-item-menu__list-item list_item_mute",
    "data-call-sid": call.data.callSid,
    "data-is-muted": call.data.isMute
  }, React.createElement("a", {
    href: "#",
    className: "call-item-menu__transfer"
  }, call.isSentMuteUnMuteRequestState() ? React.createElement("i", {
    className: "fa fa-spinner fa-spin"
  }, " ") : call.data.isMute ? React.createElement("i", {
    className: "fas fa-microphone-alt-slash"
  }, " ") : React.createElement("i", {
    className: "fas fa-microphone"
  }, " ")));
}

function ListItemMenuJoinCall(props) {
  let call = props.call;

  if (call.data.isListen) {
    return null;
  }

  return React.createElement("ul", {
    className: "call-list-item__menu call-item-menu"
  }, React.createElement("li", {
    className: "call-item-menu__list-item"
  }, React.createElement("a", {
    href: "#",
    className: "call-item-menu__close"
  }, React.createElement("i", {
    className: "fa fa-chevron-right"
  }, " "))), React.createElement(ListItemBtnMute, {
    call: call
  }));
}

function Groups(props) {
  const items = [];
  let data = Object.assign({}, props);
  const externalKey = 'external';

  if (typeof data.groups[externalKey] !== 'undefined') {
    items.push(React.createElement(GroupItem, {
      key: externalKey,
      group: {
        'calls': data.groups[externalKey].calls,
        'project': '',
        'department': 'External Contacts'
      }
    }));
    delete data.groups[externalKey];
  }

  const internalKey = 'internal';

  if (typeof data.groups[internalKey] !== 'undefined') {
    items.push(React.createElement(GroupItem, {
      key: internalKey,
      group: {
        'calls': data.groups[internalKey].calls,
        'project': '',
        'department': 'Internal Contacts'
      }
    }));
    delete data.groups[internalKey];
  }

  for (let key in data.groups) {
    if (key === 'inArray') {
      continue;
    }

    items.push(React.createElement(GroupItem, {
      key: key,
      group: data.groups[key]
    }));
  }

  return React.createElement("ul", {
    className: "calls-separator"
  }, items);
}

function GroupItem(props) {
  return React.createElement("li", {
    className: "calls-separator__list-item"
  }, React.createElement("div", {
    className: "static-number-indicator"
  }, props.group.project ? React.createElement("span", {
    className: "static-number-indicator__label"
  }, props.group.project) : '', props.group.project && props.group.department ? React.createElement("i", {
    className: "static-number-indicator__separator"
  }, " ") : '', props.group.department ? React.createElement("span", {
    className: "static-number-indicator__name"
  }, props.group.department) : ''), React.createElement("ul", {
    className: "call-in-progress"
  }, props.group.calls.map(call => React.createElement(ListItem, {
    key: call.data.callSid,
    call: call
  }))));
}

function AllQueues(props) {
  return React.createElement(React.Fragment, null, countProperties(props.active) > 0 ? React.createElement(QueueItem, {
    groups: props.active,
    name: "Active Calls",
    type: "active"
  }) : '', countProperties(props.direct) > 0 ? React.createElement(QueueItem, {
    groups: props.direct,
    name: "Direct Calls",
    type: "direct"
  }) : '', countProperties(props.general) > 0 ? React.createElement(QueueItem, {
    groups: props.general,
    name: "General Lines",
    type: "general"
  }) : '');
}

function QueueItem(props) {
  return React.createElement("li", {
    className: "queue-separator__item",
    "data-queue-type": props.type
  }, props.name ? React.createElement("div", {
    className: "queue-separator__name"
  }, props.name) : '', React.createElement(Groups, {
    groups: props.groups
  }));
}

function countProperties(obj) {
  let count = 0;

  for (let prop in obj) {
    if (obj.hasOwnProperty(prop)) {
      ++count;
    }
  }

  return count;
}

class OutgoingPane extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      call: props.call
    };
  }

  componentDidMount() {
    window.phoneWidget.eventDispatcher.addListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
  }

  componentWillUnmount() {
    window.phoneWidget.eventDispatcher.removeListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
  }

  callUpdateHandler() {
    let self = this;
    return function (event) {
      //outgoing
      self.setState({
        call: event.call
      });
    };
  }

  render() {
    let call = this.state.call;
    return React.createElement(React.Fragment, null, React.createElement(CallInfo, {
      project: call.data.project,
      source: call.data.source
    }), React.createElement("div", {
      className: "contact-info-card"
    }, React.createElement("div", {
      className: "contact-info-card__details"
    }, React.createElement("div", {
      className: "contact-info-card__line history-details"
    }, React.createElement("span", {
      className: "contact-info-card__label"
    }, call.data.type), React.createElement("div", {
      className: "credential"
    }, React.createElement("div", {
      className: "contact-info-card__name"
    }, React.createElement("button", {
      className: "call-pane__info"
    }, React.createElement("i", {
      className: "user-icon fa fa-user"
    }, " "), React.createElement("i", {
      className: "info-icon fa fa-info"
    }, " ")), React.createElement("strong", null, call.data.contact.name)), React.createElement("div", {
      className: "contact-info-card__phone"
    }, React.createElement("span", null, " ")))), React.createElement("div", {
      className: "contact-info-card__line history-details"
    }, React.createElement("span", {
      className: "contact-info-card__call-type"
    }, call.data.contact.phone)))), React.createElement("div", {
      className: "actions-container"
    }, React.createElement("div", {
      className: "call-pane__call-btns is-pending"
    }, React.createElement("button", {
      className: "call-pane__start-call calling-state-block"
    }, React.createElement("div", {
      className: "call-in-action"
    }, React.createElement("span", {
      className: "call-in-action__text"
    }, call.data.status), React.createElement("span", {
      className: "call-in-action__time"
    }, React.createElement(PhoneWidgetTimer, {
      duration: call.getDuration(),
      timeStart: Date.now()
    }))), React.createElement("i", {
      className: "fas fa-phone"
    }, " ")), React.createElement("button", {
      className: "call-pane__end-call",
      id: "cancel-outgoing-call",
      "data-call-sid": call.data.callSid,
      disabled: call.isSentHangupRequestState()
    }, call.isSentHangupRequestState() ? React.createElement("i", {
      className: "fa fa-spinner fa-spin"
    }, " ") : React.createElement("i", {
      className: "fa fa-phone-slash"
    }, " ")))));
  }

}

function ContactInfo(props) {
  return React.createElement(React.Fragment, null, React.createElement("div", {
    className: "additional-info__header"
  }, React.createElement("div", {
    className: "agent-text-avatar"
  }, React.createElement("span", null, props.avatar)), React.createElement("span", {
    className: "additional-info__header-title"
  }, "Contact Info"), React.createElement("a", {
    href: "#",
    className: "additional-info__close"
  }, React.createElement("i", {
    className: "fas fa-times"
  }, " "))), React.createElement("div", {
    className: "additional-info__body scrollable-block"
  }, React.createElement("ul", {
    className: "info-listing incoming-info"
  }, React.createElement("li", null, React.createElement("small", {
    className: "incoming-info__label"
  }, "Name"), React.createElement("span", {
    className: "incoming-info__value"
  }, props.name)))));
}

class ConferencePane extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      call: props.call,
      conference: props.conference
    };
  }

  componentDidMount() {
    window.phoneWidget.eventDispatcher.addListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
    window.phoneWidget.eventDispatcher.addListener(this.state.conference.getEventUpdateName(), this.conferenceUpdateHandler());
  }

  componentWillUnmount() {
    window.phoneWidget.eventDispatcher.removeListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
    window.phoneWidget.eventDispatcher.removeListener(this.state.conference.getEventUpdateName(), this.conferenceUpdateHandler());
  }

  callUpdateHandler() {
    let self = this;
    return function (event) {
      //conference call
      self.setState({
        call: event.call
      });
    };
  }

  conferenceUpdateHandler() {
    let self = this;
    return function (event) {
      //conference conference
      self.setState({
        conference: event.conference
      });
    };
  }

  render() {
    let call = this.state.call;
    let conference = this.state.conference;
    let countParticipants = conference.getCountParticipants();
    return React.createElement(React.Fragment, null, React.createElement("div", {
      className: "call-details"
    }, React.createElement("div", {
      className: "call-details__col"
    }, React.createElement("span", {
      className: "call-details__participants"
    }, React.createElement("i", {
      className: "call-details__participants-len"
    }, countParticipants), " Participants")), React.createElement("div", {
      className: "call-details__col"
    }, React.createElement("a", {
      href: "#",
      className: "call-details__nav-btn call-details__nav-btn--more"
    }, "Details"))), React.createElement(CallInfo, {
      project: call.data.project,
      source: call.data.source
    }), React.createElement(ParticipantShortList, {
      conference: conference
    }), React.createElement("div", {
      className: "actions-container"
    }, React.createElement(CallBtns, {
      call: call
    }), React.createElement(SoundIndication, null)), React.createElement(ActivePaneControls, {
      call: call,
      controls: this.props.controls
    }), React.createElement("div", {
      className: "conference-call-details"
    }, React.createElement("div", {
      className: "call-details"
    }, React.createElement("div", {
      className: "call-details__col"
    }, React.createElement("a", {
      href: "#",
      className: "call-details__nav-btn call-details__nav-btn--back"
    }, "Back")), React.createElement("div", {
      className: "call-details__col"
    }, React.createElement("span", {
      className: "call-details__participants"
    }, React.createElement("i", {
      className: "fa fa-users"
    }, " "), " ", countParticipants)), React.createElement("div", {
      className: "call-details__col"
    }, React.createElement("span", {
      className: "call-details__time"
    }, React.createElement(PhoneWidgetTimer, {
      duration: conference.getDuration(),
      timeStart: Date.now()
    })))), React.createElement(CallInfo, {
      project: call.data.project,
      source: call.data.source
    }), React.createElement(ParticipantList, {
      conference: conference
    })));
  }

}

function ParticipantList(props) {
  let conference = props.conference;
  let participants = [];

  for (let participant of conference.getParticipants()) {
    participants.push(React.createElement(ParticipantItem, {
      key: participant.data.callSid,
      participant: participant
    }));
  }

  return React.createElement("ul", {
    className: "conference-call__detailed participant-list scrollable-block"
  }, participants);
}

function ParticipantItem(props) {
  let participant = props.participant;
  let className = 'participant-list__item participant';

  if (participant.data.type === 'coaching') {
    className += ' participant-list__item--coach';
  }

  return React.createElement("li", {
    className: className
  }, React.createElement("div", {
    className: "participant__avatar"
  }, React.createElement("span", null, participant.data.avatar)), React.createElement("div", {
    className: "participant__info"
  }, React.createElement("span", {
    className: "participant__name"
  }, participant.data.name), React.createElement("span", {
    className: "participant__phone"
  }, participant.data.phone), participant.data.type === 'coaching' ? React.createElement("span", {
    className: "participant__status"
  }, React.createElement("i", {
    className: "fa fa-headphones-alt"
  }, " "), " coaching") : ''), React.createElement("div", {
    className: "participant__action"
  }, React.createElement("b", {
    className: "participant__timer"
  }, React.createElement(PhoneWidgetTimer, {
    duration: participant.getDuration(),
    timeStart: Date.now()
  }))));
}

function ParticipantShortList(props) {
  let conference = props.conference;
  let participants = [];
  let countParticipants = conference.getCountParticipants();

  if (countParticipants > 10) {
    let count = 0;

    for (let participant of conference.getParticipants()) {
      participants.push(React.createElement(ParticipantShortItem, {
        key: participant.data.callSid,
        participant: participant
      }));
      count++;

      if (count === 9) {
        break;
      }
    }
  } else {
    for (let participant of conference.getParticipants()) {
      participants.push(React.createElement(ParticipantShortItem, {
        key: participant.data.callSid,
        participant: participant
      }));
    }
  }

  return React.createElement("div", {
    className: "conference-call"
  }, React.createElement("ul", {
    className: "conference-call__list"
  }, participants, countParticipants > 10 ? React.createElement("li", null, React.createElement("div", {
    className: "conference-call__collapsed"
  }, React.createElement("span", {
    className: "conference-call__name"
  }, "and ", React.createElement("i", null, countParticipants - 9), " more..."))) : ''));
}

function ParticipantShortItem(props) {
  let participant = props.participant;
  let className = 'conference-call__thumbnail';

  if (participant.data.type === 'coaching') {
    className += ' conference-call__thumbnail--coaching';
  }

  return React.createElement("li", null, React.createElement("div", {
    className: className
  }, React.createElement("div", {
    className: "conference-call__avatar"
  }, participant.data.avatar), React.createElement("span", {
    className: "conference-call__name"
  }, participant.data.name), participant.data.type === 'coaching' ? React.createElement("i", {
    className: "conference-call__icon"
  }, React.createElement("i", {
    className: "fa fa-headphones-alt"
  }, " ")) : ''));
}

class AddNote extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      call: props.call
    };
  }

  componentDidMount() {
    window.phoneWidget.eventDispatcher.addListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
  }

  componentWillUnmount() {
    window.phoneWidget.eventDispatcher.removeListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
  }

  callUpdateHandler() {
    let self = this;
    return function (event) {
      //add note
      self.setState({
        call: event.call
      });
    };
  }

  render() {
    let call = this.state.call;
    return React.createElement(React.Fragment, null, React.createElement("div", {
      className: "additional-info__header"
    }, React.createElement("span", {
      className: "additional-info__header-title"
    }, "Add Note"), React.createElement("a", {
      href: "#",
      className: "additional-info__close"
    }, React.createElement("i", {
      className: "fas fa-times"
    }, " "))), React.createElement("div", {
      className: "additional-info__body scrollable-block add-note-block"
    }, React.createElement("textarea", {
      id: "active_call_add_note",
      cols: "30",
      rows: "10",
      placeholder: "Enter Note...",
      disabled: call.isSentAddNoteRequestState(),
      defaultValue: ""
    }), React.createElement("button", {
      id: "active_call_add_note_submit",
      "data-call-sid": call.data.callSid,
      disabled: call.isSentAddNoteRequestState()
    }, call.isSentAddNoteRequestState() ? React.createElement(React.Fragment, null, React.createElement("i", {
      className: "fa fa-spinner fa-spin"
    }, " "), " Save") : 'Save')));
  }

}

var PhoneWidgetContactInfo = function () {
  let containerId = 'contact-info';
  let $container = $('#contact-info');
  let $reactContainer = document.getElementById(containerId);
  /*
      data = {
          name
      }
   */

  function load(data) {
    data.avatar = data.name.charAt(0);
    data.avatar.toUpperCase();
    ReactDOM.render(React.createElement(ContactInfo, data), $reactContainer);
  }

  function hide() {
    $container.hide();
  }

  return {
    load: load,
    hide: hide
  };
}();

var PhoneWidgetDialpad = function () {
  let $pane = $('.dial-popup');

  function hide() {
    $pane.hide();
  }

  return {
    hide: hide
  };
}();

class PhoneWidgetPaneActiveBtn {
  constructor(pane, id) {
    this.pane = pane;
    this.btn = null;
    this.id = id;
  }

  init() {
    this.btn = this.pane.find(this.id);
    return this;
  }

  show() {
    this.btn.show();
    return this;
  }

  hide() {
    this.btn.hide();
    return this;
  }

  enable() {
    this.btn.attr('data-disabled', false);
    return this;
  }

  disable() {
    this.btn.attr('data-disabled', true);
    return this;
  }

  active() {
    this.btn.attr('data-active', true);
    return this;
  }

  isActive() {
    return this.btn.attr('data-active') === 'true';
  }

  inactive() {
    this.btn.attr('data-active', false);
    return this;
  }

}

class PhoneWidgetPaneActiveBtnHold extends PhoneWidgetPaneActiveBtn {
  constructor(pane) {
    super(pane, '#wg-hold-call');
  }

  sendRequest() {
    this.disable();
    let text = 'Resume';

    if (this.btn.attr('data-mode') === 'unhold') {
      text = 'On Hold';
    }

    this.btn.children().html('<i class="fa fa-spinner fa-spin"> </i><span>' + text + '</span>');
    return this;
  }

  unhold() {
    this.btn.attr('data-mode', 'unhold');
    this.btn.children().html('<i class="fa fa-pause"> </i><span>On Hold</span>');
    return this;
  }

  hold() {
    this.btn.attr('data-mode', 'hold');
    this.btn.children().html('<i class="fa fa-play"> </i><span>Resume</span>');
    return this;
  }

}

class PhoneWidgetPaneActiveBtnMute extends PhoneWidgetPaneActiveBtn {
  constructor(pane) {
    super(pane, '#call-pane__mute');
  }

  sendRequest() {
    this.disable();
    this.btn.attr('data-is-muted', null);
    this.btn.html('<i class="fa fa-spinner fa-spin"> </i>');
    return this;
  }

  mute() {
    this.enable();
    this.btn.attr('data-is-muted', true);
    this.btn.html('<i class="fas fa-microphone-alt-slash"> </i>');
    return this;
  }

  isMute() {
    return this.btn.attr('data-is-muted') === 'true';
  }

  unMute() {
    this.enable();
    this.btn.attr('data-is-muted', false);
    this.btn.html('<i class="fas fa-microphone"> </i>');
    return this;
  }

  disable() {
    this.btn.attr('disabled', true);
    return this;
  }

  enable() {
    this.btn.attr('disabled', false);
    return this;
  }

}

var PhoneWidgetPaneActive = function () {
  let callSid = null;
  const containerId = 'call-pane-calling';
  let $container = $('#' + containerId);
  let $reactContainer = document.getElementById(containerId);
  let $addNoteContainer = document.getElementById('add-note');
  let contactInfo = PhoneWidgetContactInfo;
  let dialpad = PhoneWidgetDialpad;
  let buttons = {
    'hold': new PhoneWidgetPaneActiveBtnHold($container),
    'mute': new PhoneWidgetPaneActiveBtnMute($container)
  };
  let btnHoldShow = true;
  let btnTransferShow = true;

  function setup(btnHoldShowInit, btnTransferShowInit) {
    btnHoldShow = btnHoldShowInit;
    btnTransferShow = btnTransferShowInit;
  }

  function initControls() {
    buttons.hold.init();
    buttons.mute.init();
  } // call => window.phoneWidget.call.Call
  // conference => window.phoneWidget.conference.Conference


  function load(call, conference) {
    if (typeof conference !== 'undefined' && conference !== null) {
      $container.addClass('call-pane-calling--conference');
      ReactDOM.unmountComponentAtNode($reactContainer);
      ReactDOM.render(React.createElement(ConferencePane, {
        call: call,
        controls: getControls(call),
        conference: conference
      }), $reactContainer);
    } else {
      $container.removeClass('call-pane-calling--conference');
      ReactDOM.unmountComponentAtNode($reactContainer);
      ReactDOM.render(React.createElement(ActivePane, {
        call: call,
        controls: getControls(call)
      }), $reactContainer);
    }

    ReactDOM.unmountComponentAtNode($addNoteContainer);
    ReactDOM.render(React.createElement(AddNote, {
      call: call
    }), $addNoteContainer);
    $(".dialpad_btn_active").attr('data-conference-sid', call.data.conferenceSid);
    $("#call-pane__dial-number_active_dialpad").val('');
    contactInfo.load(call.data.contact);
    setCallSid(call.data.callSid);
    initControls();
  }

  function getControls(call) {
    let controls = {
      hold: {
        active: true,
        show: btnHoldShow
      },
      transfer: {
        active: true,
        show: btnTransferShow
      },
      addPerson: {
        active: false
      },
      dialpad: {
        active: true
      }
    };

    if (call.data.typeId === 3) {
      controls.hold.active = false;
      controls.transfer.active = false;
      controls.addPerson.active = false;
      controls.dialpad.active = false;
    }

    if (call.data.isInternal) {
      controls.hold.active = !!call.data.isConferenceCreator;
      controls.transfer.active = false;
      controls.addPerson.active = false;
      controls.dialpad.active = false;
    }

    if (!conferenceBase) {
      controls.hold.active = false;
      controls.transfer.active = true;
      controls.addPerson.active = false;
      controls.dialpad.active = false;
    }

    return controls;
  }

  function setCallSid(sid) {
    callSid = sid;
  }

  function getCallSid() {
    return callSid;
  }

  function removeCallSid() {
    callSid = null;
  }

  function show() {
    contactInfo.hide();
    dialpad.hide();
    $('#tab-phone .call-pane-initial').removeClass('is_active');
    $container.addClass('is_active');
    addCallInProgressIndicator();
  }

  function hide() {
    $container.removeClass('is_active');
    removeCallInProgressIndicator();
  }

  function addCallInProgressIndicator() {
    $('[data-toggle-tab="tab-phone"]').attr('data-call-in-progress', true);
  }

  function removeCallInProgressIndicator() {
    $('[data-toggle-tab="tab-phone"]').attr('data-call-in-progress', false);
  }

  function isActive() {
    return $container.hasClass('is_active');
  }

  function init(call, conference) {
    load(call, conference);
    show();

    if (call.getHoldDuration()) {
      widgetIcon.update({
        type: 'hold',
        timer: true,
        'timerStamp': call.getHoldDuration(),
        text: 'on hold',
        currentCalls: null,
        status: 'online'
      });
      return;
    }

    widgetIcon.update({
      type: 'inProgress',
      timer: true,
      'timerStamp': call.getDuration(),
      text: 'on call',
      currentCalls: '',
      status: 'online'
    });
  }

  return {
    setup: setup,
    buttons: buttons,
    init: init,
    load: load,
    show: show,
    hide: hide,
    getCallSid: getCallSid,
    removeCallSid: removeCallSid,
    isActive: isActive,
    removeCallInProgressIndicator: removeCallInProgressIndicator
  };
}();

var PhoneWidgetPaneIncoming = function () {
  let callSid = null;
  const containerId = 'call-pane-incoming';
  let $container = $('#' + containerId);
  let $reactContainer = document.getElementById(containerId);
  let contactInfo = PhoneWidgetContactInfo;
  let dialpad = PhoneWidgetDialpad; // call => window.phoneWidget.call.Call

  function load(call) {
    contactInfo.load(call.data.contact);
    ReactDOM.unmountComponentAtNode($reactContainer);
    ReactDOM.render(React.createElement(IncomingPane, {
      call: call
    }), $reactContainer);
    setCallSid(call.data.callSid);
  }

  function show() {
    contactInfo.hide();
    dialpad.hide();
    $('#tab-phone .call-pane-initial').removeClass('is_active');
    $container.addClass('is_active');
  }

  function hide() {
    ReactDOM.unmountComponentAtNode($reactContainer);
    $container.removeClass('is_active');
  }

  function setCallSid(sid) {
    callSid = sid;
  }

  function getCallSid() {
    return callSid;
  }

  function removeCallSid() {
    callSid = null;
  }

  function isActive() {
    return $container.hasClass('is_active');
  }

  function init(data, countIncoming, countActive) {
    load(data);
    show();
    initWidgetIcon(countIncoming, countActive);
  }

  function initWidgetIcon(countIncoming, countActive) {
    let currentCalls = '';

    if (countActive) {
      currentCalls = countIncoming + '+' + countActive;
    } else {
      if (countIncoming > 1) {
        currentCalls = countIncoming;
      }
    }

    widgetIcon.update({
      type: 'incoming',
      timer: false,
      text: null,
      currentCalls: currentCalls,
      status: 'online'
    });
  }

  return {
    init: init,
    load: load,
    show: show,
    hide: hide,
    getCallSid: getCallSid,
    removeCallSid: removeCallSid,
    isActive: isActive,
    initWidgetIcon: initWidgetIcon
  };
}();

var PhoneWidgetPaneOutgoing = function () {
  let callSid = null;
  const containerId = 'call-pane-outgoing';
  let $container = $('#' + containerId);
  let $reactContainer = document.getElementById(containerId);
  let contactInfo = PhoneWidgetContactInfo;
  let dialpad = PhoneWidgetDialpad; // call => window.phoneWidget.call.Call

  function load(call) {
    contactInfo.load(call.data.contact);
    ReactDOM.unmountComponentAtNode($reactContainer);
    ReactDOM.render(React.createElement(OutgoingPane, {
      call: call
    }), $reactContainer);
    setCallSid(call.data.callSid);
  }

  function show() {
    contactInfo.hide();
    dialpad.hide();
    $('#tab-phone .call-pane-initial').removeClass('is_active');
    $container.addClass('is_active');
  }

  function hide() {
    ReactDOM.unmountComponentAtNode($reactContainer);
    $container.removeClass('is_active');
  }

  function setCallSid(sid) {
    callSid = sid;
  }

  function getCallSid() {
    return callSid;
  }

  function removeCallSid() {
    callSid = null;
  }

  function isActive() {
    return $container.hasClass('is_active');
  }

  function init(call) {
    load(call);
    show();
    widgetIcon.update({
      type: 'outgoing',
      timer: true,
      'timerStamp': call.getDuration(),
      text: call.data.status,
      currentCalls: null,
      status: 'online'
    });
  }

  return {
    init: init,
    load: load,
    show: show,
    hide: hide,
    getCallSid: getCallSid,
    removeCallSid: removeCallSid,
    isActive: isActive
  };
}();

function PhoneWidgetPaneQueue(initQueues) {
  let queues = initQueues;
  let self = this;
  let filterToggle = '.call-filter__toggle';
  let activeQueue = null;
  $(document).on('click', filterToggle, function (e) {
    e.preventDefault();
    let type = $(this).attr('data-call-filter');

    switch (type) {
      case 'active':
        activeShow();
        break;

      case 'direct':
        directShow();
        break;

      case 'general':
        generalShow();
        break;
    }

    $(filterToggle).removeClass('is-checked');
    $(this).addClass('is-checked');
    clearIndicators($(this));
    self.show();
    $('[data-toggle-tab]').removeClass('is_active');
  });
  $(document).on('click', '.widget-line-overlay__show-all-queues', function (e) {
    e.preventDefault();
    $(filterToggle).addClass('is-checked');
    clearIndicators($(this));
    allShow();
  });

  this.openAllCalls = function () {
    let target = $('.widget-line-overlay__show-all-queues');
    $(filterToggle).addClass('is-checked');
    clearIndicators(target);
    $('[data-toggle-tab]').removeClass('is_active');
    allShow();
    self.show();
  };

  function mergeActiveCalls() {
    let activeCollection = Object.assign({}, queues.active.all());
    let holdCollection = Object.assign({}, queues.hold.all());

    for (let key in holdCollection) {
      if (typeof activeCollection[key] === 'undefined') {
        activeCollection[key] = holdCollection[key];
      } else {
        holdCollection[key].calls.forEach(function (call) {
          activeCollection[key].calls.push(call);
        });
      }
    }

    return activeCollection;
  }

  function activeShow() {
    ReactDOM.render(React.createElement(QueueItem, {
      groups: mergeActiveCalls(),
      type: 'active',
      name: ''
    }), document.getElementById('queue-separator'));
    activeQueue = 'active';
  }

  function directShow() {
    ReactDOM.render(React.createElement(QueueItem, {
      groups: queues.direct.all(),
      type: 'direct',
      name: ''
    }), document.getElementById('queue-separator'));
    activeQueue = 'direct';
  }

  function generalShow() {
    ReactDOM.render(React.createElement(QueueItem, {
      groups: queues.general.all(),
      type: 'general',
      name: ''
    }), document.getElementById('queue-separator'));
    activeQueue = 'general';
  }

  function allShow() {
    ReactDOM.render(React.createElement(AllQueues, {
      active: mergeActiveCalls(),
      direct: queues.direct.all(),
      general: queues.general.all()
    }), document.getElementById('queue-separator'));
    activeQueue = 'all';
  }

  function isActiveActive() {
    return activeQueue === 'active';
  }

  function isDirectActive() {
    return activeQueue === 'direct';
  }

  function isGeneralActive() {
    return activeQueue === 'general';
  }

  function isAllActive() {
    return activeQueue === 'all';
  }

  this.refresh = function () {
    updateCounters();

    if (isActiveActive()) {
      activeShow();
    } else if (isDirectActive()) {
      directShow();
    } else if (isGeneralActive()) {
      generalShow();
    } else if (isAllActive()) {
      allShow();
    }
  };

  this.show = function () {
    $('.widget-line-overlay').show();
  };

  function updateCounters() {
    $('.call-filter__toggle--line-active').html(queues.hold.count() + queues.active.count());
    $('.call-filter__toggle--line-direct').html(queues.direct.count());
    $('.call-filter__toggle--line-general').html(queues.general.count());
  }

  function clearIndicators(target) {
    var markElement = $('.widget-line-overlay__queue-marker');
    markElement.removeClass('tab-active');
    markElement.removeClass('tab-direct');
    markElement.removeClass('tab-general');
    markElement.removeClass('tab-all');

    switch ($(target).attr('data-call-filter')) {
      case 'active':
        $('[data-queue-marker]').html('Active Calls');
        markElement.addClass('tab-active');
        break;

      case 'direct':
        $('[data-queue-marker]').html('Direct Calls');
        markElement.addClass('tab-direct');
        break;

      case 'general':
        $('[data-queue-marker]').html('General Lines');
        markElement.addClass('tab-general');
        break;

      case 'all':
        $('[data-queue-marker]').html('Calls Queue');
        break;
    }
  }

  this.hide = function () {
    $('.widget-line-overlay').hide();
    $(filterToggle).removeClass('is-checked');
  };
}

(function () {
  function Queue() {
    this.calls = [];

    function init(data) {
      data.timeQueuePushed = Date.now();
      data.blocked = false;
      data.sentHoldUnHoldRequest = false;
      data.sentMuteUnMuteRequest = false;
      data.sentHangupRequest = false;
      data.sentReturnHoldCallRequest = false;
      data.sentAcceptCallRequest = false;
      data.sentAddNoteRequest = false;
      data.sentRejectInternalRequest = false;
    }

    this.add = function (data) {
      if (this.getIndex(data.callSid) !== null) {
        return null;
      }

      init(data);

      if (data.isHold) {
        data.holdStartTime = Date.now() - parseInt(data.holdDuration) * 1000;
      }

      this.calls.unshift(data);
      return new window.phoneWidget.call.Call(data);
    };

    this.remove = function (callSid) {
      let index = this.getIndex(callSid);

      if (index !== null) {
        this.calls.splice(index, 1);
      }
    };

    this.getIndex = function (callSid) {
      let index = null;
      this.calls.forEach(function (call, i) {
        if (call.callSid === callSid) {
          index = i;
          return false;
        }
      });
      return index;
    };

    this.getLast = function () {
      let call = null;

      for (let i in this.calls) {
        if (i === 'inArray') {
          continue;
        }

        call = this.calls[i];
      }

      if (typeof call == 'undefined' || call === null) {
        return null;
      }

      return new window.phoneWidget.call.Call(call);
    };

    this.getFirst = function () {
      let call = null;

      for (let i in this.calls) {
        if (i === 'inArray') {
          continue;
        }

        call = this.calls[i];
        break;
      }

      if (typeof call == 'undefined' || call === null) {
        return null;
      }

      return new window.phoneWidget.call.Call(call);
    };

    this.one = function (callSid) {
      let index = this.getIndex(callSid);

      if (index !== null) {
        return new window.phoneWidget.call.Call(this.calls[index]);
      }

      return null;
    };

    this.count = function () {
      return this.calls.length;
    };

    this.all = function () {
      let calls = [];
      this.calls.forEach(function (call) {
        calls.push(new window.phoneWidget.call.Call(call));
      });
      return calls;
    };

    this.showAll = function () {
      this.calls.forEach(function (call) {
        console.log(call);
      });
    };
  }

  class QueueItem {
    constructor(queue, queueName) {
      this.queue = queue;
      this.queueName = queueName;
    }

    getList() {
      let calls = [];
      let self = this;
      this.queue.all().forEach(function (call) {
        if (call.data.queue === self.queueName) {
          calls.push(call);
        }
      });
      return calls;
    }

    one(callSid) {
      return this.queue.one(callSid);
    }

    all() {
      let calls = this.getList();

      if (calls.length < 1) {
        return [];
      }

      let groups = [];
      let key = '';
      calls.forEach(function (call) {
        if (call.data.isInternal) {
          if (!groups['internal']) {
            groups['internal'] = {
              'calls': []
            };
          }

          groups['internal'].calls.push(call);
          return;
        } else if (!call.data.project) {
          if (!groups['external']) {
            groups['external'] = {
              'calls': []
            };
          }

          groups['external'].calls.push(call);
          return;
        }

        key = call.data.project + call.data.department;

        if (!groups[key]) {
          groups[key] = {
            project: call.data.project,
            department: call.data.department,
            calls: []
          };
        }

        groups[key].calls.push(call);
      });
      return groups;
    }

    count() {
      return this.getList().length;
    }

    add(data) {
      return this.queue.add(data);
    }

    getLast() {
      return this.queue.getLast();
    }

    getFirst() {
      return this.queue.getFirst();
    }

    remove(callSid) {
      this.queue.remove(callSid);
    }

  }

  class Hold extends QueueItem {
    constructor(queue) {
      super(queue, 'hold');
    }

  }

  class Direct extends QueueItem {
    constructor(queue) {
      super(queue, 'direct');
    }

  }

  class General extends QueueItem {
    constructor(queue) {
      super(queue, 'general');
    }

  }

  function Active() {
    return new QueueItem(new Queue(), 'inProgress');
  }

  window.phoneWidget.queue = {
    Queue: Queue,
    Direct: Direct,
    Hold: Hold,
    General: General,
    Active: Active
  };
})();

(function () {
  function ConferenceStorage() {
    this.conferences = [];

    function init(data) {
      data.timeStoragePushed = Date.now();
    }

    this.add = function (data) {
      if (this.getIndex(data.sid) !== null) {
        return null;
      }

      init(data);
      this.conferences.unshift(data);
      return new window.phoneWidget.conference.Conference(data);
    };

    this.remove = function (sid) {
      let index = this.getIndex(sid);

      if (index !== null) {
        this.conferences.splice(index, 1);
      }
    };

    this.getIndex = function (sid) {
      let index = null;
      this.conferences.forEach(function (conference, i) {
        if (conference.sid === sid) {
          index = i;
          return false;
        }
      });
      return index;
    };

    this.removeByParticipantCallSid = function (sid) {
      let index = this.getIndexByParticipantCallSid(sid);

      if (index !== null) {
        this.conferences.splice(index, 1);
      }
    };

    this.getIndexByParticipantCallSid = function (callSid) {
      let index = null;
      this.conferences.forEach(function (conference, i) {
        conference.participants.forEach(function (participant) {
          if (participant.callSid === callSid) {
            index = i;
          }
        });
      });
      return index;
    };

    this.one = function (sid) {
      let index = this.getIndex(sid);

      if (index !== null) {
        return new window.phoneWidget.conference.Conference(this.conferences[index]);
      }

      return null;
    };

    this.update = function (conference) {
      this.remove(conference.sid);
      let conf = this.add(conference);

      if (conf === null) {
        console.log('conference not added to storage');
        return;
      }

      conf.save();
    };

    this.showAll = function () {
      this.conferences.forEach(function (conference) {
        console.log(conference);
      });
    };
  }

  window.phoneWidget.storage.conference = new ConferenceStorage();
})();

$(document).ready(function () {
  window.widgetIcon = new handleWidgetIcon();
  widgetIcon.init();
  $phoneTabAnchor = $('[data-toggle-tab]');
  var historySimpleBar;

  function delay(callback, ms) {
    var timer = 0;
    return function () {
      var context = this,
          args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

  var tabHistoryLoaded = false;
  $phoneTabAnchor.on("click", function () {
    $current = "#" + $(this).data("toggle-tab");
    $phoneTabAnchor.removeClass("is_active");
    $(this).addClass("is_active");
    $(".phone-widget__tab").removeClass("is_active");
    $($current).addClass("is_active");
    $('.widget-modal').hide();
    $('.collapsible-container').collapse('hide');
    filterCalls.reset();

    if ($(this).data("toggle-tab") === 'tab-history') {
      if (!tabHistoryLoaded) {
        tabHistoryLoaded = true;
        $.ajax({
          url: '/call-log/ajax-get-call-history',
          type: 'post',
          data: {},
          dataType: 'json',
          beforeSend: function () {
            $($current).append('<div class="wg-history-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
          },
          success: function (data) {
            $('#tab-history .simplebar-content').append(data.html);
            historySimpleBar.recalculate();
            $('#tab-history').attr('data-page', data.page);
          },
          complete: function (data) {
            $($current).find('.wg-history-load').remove();
          },
          error: function (xhr, error) {}
        });
      }

      let countMissedCalls = parseInt($(this).attr('data-missed-calls'));

      if (countMissedCalls > 0) {
        PhoneWidgetCall.requestClearMissedCalls();
      }
    }

    if ($current === '#tab-contacts') {
      if (PhoneWidgetContacts.fullListIsEmpty()) {}

      if ($(this).hasClass('js-add-to-conference')) {
        window.localStorage.setItem('contactSelectableState', 1);
        $('.contacts-list').addClass('contacts-list--selection');
        $('.selection-amount').show();
      } else {
        window.localStorage.setItem('contactSelectableState', 0);
        $('.contacts-list').removeClass('contacts-list--selection');
        $('.submit-selected-contacts').slideUp(10);
        $('.selection-amount').hide();
      }

      PhoneWidgetContacts.requestFullList();
    }
  });

  function initLazyLoadHistory(simpleBar) {
    var ajax = false;
    simpleBar.getScrollElement().addEventListener('scroll', function (e) {
      if (e.target.scrollTop + e.target.clientHeight === e.target.scrollHeight && !ajax) {
        // ajax call get data from server and append to the div
        var page = $('#tab-history').attr('data-page');
        $.ajax({
          url: '/call-log/ajax-get-call-history',
          type: 'post',
          data: {
            page: page,
            uid: userId
          },
          dataType: 'json',
          beforeSend: function () {
            $($current).append('<div class="wg-history-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
            ajax = true;
          },
          success: function (data) {
            $('#tab-history .simplebar-content').append(data.html);
            historySimpleBar.recalculate();
            $('#tab-history').attr('data-page', data.page);

            if (!data.rows) {
              ajax = false;
            }
          },
          complete: function () {
            $($current).find('.wg-history-load').remove();
          },
          error: function (xhr, error) {}
        });
      }
    });
  }

  $('.phone-widget__tab').each(function (i, el) {
    var simpleBar = new SimpleBar(el);
    simpleBar.getContentElement();

    if ($(el).attr('id') === 'tab-history') {
      initLazyLoadHistory(simpleBar);
      historySimpleBar = simpleBar;
    }

    if ($(el).attr('id') === 'tab-contacts' && typeof PhoneWidgetContacts !== 'undefined') {
      PhoneWidgetContacts.initLazyLoadFullList(simpleBar);
    }
  });
  $('.js-toggle-contact-info').on('click', function () {
    $('.contact-modal-info').show();
  });

  function addCC() {
    return '<input type="text" class="email-modal__contact-input additional-subj" placeholder="CC">';
  }

  function addBCC() {
    return '<input type="text" class="email-modal__contact-input additional-subj" placeholder="BCC">';
  }

  function clearEmailTab() {
    $('.subject-option__add').removeClass('added');
    $('.additional-subj').remove();
  }

  $('.subject-option__add').on('click', function () {
    if ($(this).hasClass('added')) {
      return;
    }

    switch ($(this).data('add-type')) {
      case 'cc':
        $('.email-modal__modal-input-list').append(addCC());
        break;

      case 'bcc':
        $('.email-modal__modal-input-list').append(addBCC());
        break;
    }

    $(this).addClass('added');
  }); // var messagesModal = $(".messages-modal__messages-scroll");
  // var emailModal = $(".email-modal__messages-scroll");

  var elemScrollable = $('.scrollable-block');
  var additionalBar = $('.additional-bar__body');
  var contactModal = $(".contact-modal-info");
  var blockSuggestion = $(".suggested-contacts"); // var msgModalScroll = new SimpleBar(messagesModal[0]);
  // var emailModalScroll = new SimpleBar(emailModal[0]);

  var suggestions = new SimpleBar(blockSuggestion[0]);
  var modalScroll = new SimpleBar(contactModal[0]);
  modalScroll.getContentElement();
  suggestions.getContentElement(); // msgModalScroll.getContentElement();
  // emailModalScroll.getContentElement();
  // msgModalScroll.recalculate();

  $(additionalBar).each(function (i, el) {
    var elem = new SimpleBar(el);
    elem.getContentElement();
  });
  $('.toggle-bar-settings').on('click', function () {
    $('#bar-settings').slideToggle(150);
    $('#bar-logs').slideUp(150);
  });
  $('.additional-bar__close').on('click', function () {
    console.log($(this).parents('.additional-bar'));
    $(this).parents('.additional-bar').slideUp(150);
  });
  $('.toggle-bar-logs').on('click', function () {
    $('#bar-logs').slideToggle(150);
    $('#bar-settings').slideUp(150);
  });
  $('.additional-bar__close').on('click', function () {
    console.log($(this).parents('.additional-bar'));
    $(this).parents('.additional-bar').slideUp(150);
  });
  var activeSettingTab = $('.tab-trigger.active-tab').attr('href');
  $(activeSettingTab).show();
  $('.tab-trigger').on('click', function (e) {
    e.preventDefault();
    $('.tab-trigger').removeClass('active-tab');
    $(this).addClass('active-tab');
    $('.tabs__item').hide();
    $($(this).attr('href')).show();
  });
  $(elemScrollable).each(function (i, elem) {
    var el = new SimpleBar(elem);
    el.getContentElement();
  }); //--------------------------------------------------------------------------
  // polyfill

  var AudioContext = window.AudioContext || window.webkitAudioContext || window.mozAudioContext;

  function Tone(context, freq1, freq2) {
    this.context = context;
    this.status = 0;
    this.freq1 = freq1;
    this.freq2 = freq2;
  }

  Tone.prototype.setup = function () {
    this.osc1 = context.createOscillator();
    this.osc2 = context.createOscillator();
    this.osc1.frequency.value = this.freq1;
    this.osc2.frequency.value = this.freq2;
    this.gainNode = this.context.createGain();
    this.gainNode.gain.value = 0.25;
    this.filter = this.context.createBiquadFilter();
    this.filter.type = "lowpass";
    this.filter.frequency = 8000;
    this.osc1.connect(this.gainNode);
    this.osc2.connect(this.gainNode);
    this.gainNode.connect(this.filter);
    this.filter.connect(context.destination);
  };

  Tone.prototype.start = function () {
    this.setup();
    this.osc1.start(0);
    this.osc2.start(0);
    this.status = 1;
  };

  Tone.prototype.stop = function () {
    this.osc1.stop(0);
    this.osc2.stop(0);
    this.status = 0;
  };

  var dtmfFrequencies = {
    "1": {
      f1: 697,
      f2: 1209
    },
    "2": {
      f1: 697,
      f2: 1336
    },
    "3": {
      f1: 697,
      f2: 1477
    },
    "4": {
      f1: 770,
      f2: 1209
    },
    "5": {
      f1: 770,
      f2: 1336
    },
    "6": {
      f1: 770,
      f2: 1477
    },
    "7": {
      f1: 852,
      f2: 1209
    },
    "8": {
      f1: 852,
      f2: 1336
    },
    "9": {
      f1: 852,
      f2: 1477
    },
    "✱": {
      f1: 941,
      f2: 1209
    },
    "0": {
      f1: 941,
      f2: 1336
    },
    "#": {
      f1: 941,
      f2: 1477
    },
    "+": {
      f1: 941,
      f2: 1497
    }
  };
  var context = new AudioContext();
  var dtmf = new Tone(context, 350, 440);
  var dialpadCurrentValue = null;
  var dialpadButtonTimer = null;
  $('.dialpad_btn_init').on("mousedown touchstart", function (e) {
    e.preventDefault();
    var keyPressed = $(this).val();
    dialpadCurrentValue = keyPressed;
    dialpadButtonTimer = setInterval(function () {
      if (dialpadCurrentValue === '0') {
        let currentVal = $('.call-pane__dial-number').val();

        if (currentVal) {
          currentVal = currentVal.substring(0, currentVal.length - 1);
          currentVal = currentVal + '+';
          $('.call-pane__dial-number').val(currentVal);
        }
      }

      clearInterval(dialpadButtonTimer);
    }, 700);
    var frequencyPair = dtmfFrequencies[keyPressed]; // this sets the freq1 and freq2 properties

    dtmf.freq1 = frequencyPair.f1;
    dtmf.freq2 = frequencyPair.f2;

    if (dtmf.status == 0) {
      dtmf.start();
    } //let btnVal = $(this).val();


    let currentVal = $('.call-pane__dial-number').val();
    $('.call-pane__dial-number').val(currentVal + keyPressed);
    $('.call-pane__dial-clear-all').addClass('is-shown'); //$('.suggested-contacts').addClass('is_active');

    $('.call-pane__dial-number').focus();
  });
  $('.dialpad_btn_active').on("mousedown touchstart", function (e) {
    e.preventDefault();
    var keyPressedFormatted = $(this).val();
    var keyPressed = keyPressedFormatted === '✱' ? '*' : keyPressedFormatted;
    let conferenceSid = $(this).attr('data-conference-sid'); // var frequencyPair = dtmfFrequencies[keyPressed];
    // this sets the freq1 and freq2 properties
    // dtmf.freq1 = frequencyPair.f1;
    // dtmf.freq2 = frequencyPair.f2;
    // if (dtmf.status == 0){
    //     dtmf.start();
    // }

    let currentVal = $('#call-pane__dial-number_active_dialpad').val();
    $('#call-pane__dial-number_active_dialpad').val(currentVal + keyPressedFormatted);
    $('.call-pane__dial-clear-all').addClass('is-shown');
    $('#call-pane__dial-number_active_dialpad').focus();
    PhoneWidgetCall.callRequester.sendDigit(conferenceSid, keyPressed);
  });
  $(window).on("mouseup touchend", function () {
    if (typeof dtmf !== "undefined" && dtmf.status) {
      dtmf.stop();
    }

    clearInterval(dialpadButtonTimer);
  }); //---------------------------------------------------

  $('.call_pane_dialpad_clear_number_active_dialpad').on('click', function (e) {
    e.preventDefault();
    $('#call-pane__dial-number_active_dialpad').val('');
  });
  $('.call_pane_dialpad_clear_number').on('click', function (e) {
    e.preventDefault();
    $('#call-pane__dial-number').val('').attr('readonly', false).prop('readonly', false);
    $('#call-to-label').text('');
    $('#call-pane__dial-number-value').attr('data-user-id', '').attr('data-phone', '');
    $('.suggested-contacts').removeClass('is_active');
    $('.dialpad_btn_init').attr('disabled', false).removeClass('disabled');
    $('.call-pane__correction').attr('disabled', false); // $(this).removeClass('is-shown')
  });
  $('.call_pane_dialpad_clear_number_disabled').on('click', function (e) {
    e.preventDefault();
    $('.call-pane__dial-number').val('').attr('readonly', true).prop('readonly', true);
    $('#call-to-label').text('');
    $('#call-pane__dial-number-value').attr('data-user-id', '').attr('data-phone', '');
    $('.suggested-contacts').removeClass('is_active');
  });
  $('.call-pane__correction').on('click', function (e) {
    e.preventDefault();
    var currentVal = $('.call-pane__dial-number').val();
    $('.call-pane__dial-number').val(currentVal.slice(0, -1));

    if (currentVal.length === 1) {
      $('.suggested-contacts').removeClass('is_active'); // $('.call-pane__dial-clear-all').removeClass('is-shown');
    }
  });
  $(".js-edit-mode").on("click", function (e) {
    e.preventDefault();

    if ($(this).hasClass("is-editing")) {
      $(this).removeClass("is-editing");
      $('.contact-modal-info').find(".contact-full-info").removeClass("edit-mode");
      $(this).find("span").text("Edit");
      $('.contact-modal-info').find(".contact-full-info .form-control").each(function (i, el) {
        $(el).attr("readonly", true);
        $(el).attr("disabled", true);
      });
      return;
    }

    $('.contact-modal-info').find(".contact-full-info").addClass("edit-mode");
    $(this).addClass("is-editing");
    $('.contact-modal-info').find(".contact-full-info .form-control").each(function (i, el) {
      $(el).attr("readonly", false);
      $(el).attr("disabled", false);
    });
    $(".is-editing").find("span").text("Save");
  });
  $(".select-contact-type").on("change", function () {
    $(this).closest(".form-control-wrap").attr("data-type", $(this).val().toLowerCase());
  });
  $(".js-toggle-phone-widget").on("click", function (e) {
    e.preventDefault();
    $(".phone-widget").toggleClass("is_active");
    $(this).toggleClass("is-mirror");
  });
  $(".phone-widget__close").on("click", function (e) {
    e.preventDefault();
    $(".phone-widget").toggleClass("is_active");
    $(".js-toggle-phone-widget").toggleClass("is-mirror");
  });
  $(".js-call-tab-trigger").on("click", function (e) {
    e.preventDefault();
    $(".widget-modal").hide();
    $(".phone-widget__tab").removeClass("is_active");
    $("#tab-phone").addClass("is_active");
    $("[data-toggle-tab]").removeClass("is_active");
    $('[data-toggle-tab="tab-phone"]').addClass("is_active");
  }); // presentational scripts

  var timeout;

  function callTimeout() {
    timeout = setTimeout(function () {
      $('.phone-widget-icon').removeClass('is-pending');
      $('.phone-widget-icon').addClass('is-on-call');
      $('.call-pane__call-btns').removeClass('is-pending');
      $('.call-pane__call-btns').addClass('is-on-call');
      $('.call-in-action__text').text('on call');
    }, 4000);
  } // $('.call-pane__start-call').on('click', function(e) {
  //     e.preventDefault();
  //
  // });
  // $('.messages-modal__send-btn').on('click', function() {
  //     // var scroll = $(msgModalScroll.getContentElement());
  //     var scroll = $('.messages-modal__messages-scroll').find($('.simplebar-content-wrapper'))[0];
  //
  //     $('.messages-modal__msg-list').append(appendMsg($('.messages-modal__msg-input').val()))
  //     $(scroll).scrollTop($(scroll)[0].scrollHeight)
  //
  //     $('.messages-modal__msg-input').val('')
  // });
  // function appendMsg(msg) {
  //     var time = new Date();
  //
  //     var node = '<li class="messages-modal__msg-item pw-msg-item pw-msg-item--user">'+
  //         '<div class="pw-msg-item__avatar">'+
  //         '<div class="agent-text-avatar">'+
  //         '<span>B</span>'+
  //         '</div>'+
  //         '</div>'+
  //         '<div class="pw-msg-item__msg-main">'+
  //         '<div class="pw-msg-item__data">'+
  //         '<span class="pw-msg-item__name">Me</span>'+
  //         '<span class="pw-msg-item__timestamp">' + time.getHours() + ':'+ time.getMinutes() +' PM</span>'+
  //         '</div>'+
  //         '<div class="pw-msg-item__msg-wrap">'+
  //         '<p class="pw-msg-item__msg">' + msg + '</p>'+
  //         '</div>'+
  //         '</div>'+
  //         '</li>';
  //     return node;
  // }
  // var data = {
  //     'selected': {
  //         'value': '+1-222-555-2222',
  //         'project': 'gtt',
  //         'id': 'dd-select'
  //     },
  //     'options': [
  //         {
  //             'value': '+1-222-555-4444',
  //             'project': 'flygtravel'
  //         },
  //         {
  //             'value': '+1-222-555-3333',
  //             'project': 'wowgateway'
  //         },
  //         {
  //             'value': '+1-222-555-2222',
  //             'project': 'gtt'
  //         },
  //         {
  //             'value': '+1-222-555-1111',
  //             'project': 'gtt2'
  //         }
  //     ]
  // }
  // var currentNumber = toSelect($('.custom-phone-select'), data, function() {
  //     console.log('here goes a callback')
  //     console.log(currentNumber.getData);
  // });


  $(document).on("click", ".widget-modal__close", function () {
    $(".widget-modal").hide();
    $(".phone-widget__tab").removeClass('ovf-hidden');
    $('.collapsible-container').collapse('hide');
    clearEmailTab();
  });
  var callsData = [{
    id: 1,
    state: 'inProgress',
    project: 'ovago',
    department: 'sales',
    length: 100,
    contact: {
      name: 'Geff Robertson1',
      company: 'LLC "DREAM TRAVEL"',
      number: '+123 321 234 432'
    }
  }, {
    id: 2,
    state: 'hold',
    project: 'wowfare',
    department: 'sales',
    length: 30,
    contact: {
      name: 'New name',
      company: '"Rrtsa TRAVEL"',
      number: '+373 45 45'
    }
  }, {
    id: 3,
    state: 'direct',
    project: 'arangrant',
    department: 'sales',
    length: 30,
    contact: {
      name: 'New name',
      company: '"Rrtsa TRAVEL"',
      number: '+373 45 45'
    }
  }, {
    id: 4,
    state: 'general',
    project: 'hop2',
    department: 'sales',
    length: 30,
    contact: {
      name: 'New name',
      company: '"Rrtsa TRAVEL"',
      number: '+373 45 45'
    }
  }];
  var callsObj = [{
    project: 'ovago',
    department: 'sales',
    id: 1,
    calls: [{
      state: 'inProgress',
      length: 6001,
      id: 1,
      contact: {
        name: 'Geff Robertson1',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }, {
      state: 'hold',
      length: 2201,
      id: 2,
      contact: {
        name: 'John Doe2',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }, {
      state: 'direct',
      length: 3201,
      id: 3,
      contact: {
        name: 'John Doe6',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }, {
      state: 'hold',
      length: 6201,
      id: 4,
      contact: {
        name: 'John Doe7',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }]
  }, {
    project: null,
    department: 'SALES',
    id: 2,
    calls: [{
      state: 'general',
      length: 5301,
      id: 2,
      contact: {
        name: 'John Doe4',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }, {
      state: 'general',
      length: 5301,
      id: 3,
      contact: {
        name: 'John Doe5',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }, {
      state: 'direct',
      length: 5401,
      id: 4,
      contact: {
        name: 'John Doe8',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }, {
      state: 'direct',
      length: 6241,
      id: 1,
      contact: {
        name: 'Gerrombo Saltison3',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }]
  }, {
    project: 'wowfare',
    department: null,
    id: 2,
    calls: [{
      state: 'general',
      length: 5301,
      id: 2,
      contact: {
        name: 'John Doe4',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }, {
      state: 'general',
      length: 5301,
      id: 3,
      contact: {
        name: 'John Doe5',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }, {
      state: 'direct',
      length: 5401,
      id: 4,
      contact: {
        name: 'John Doe8',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }, {
      state: 'direct',
      length: 6241,
      id: 1,
      contact: {
        name: 'Gerrombo Saltison3',
        company: 'LLC "DREAM TRAVEL"',
        number: '+123 321 234 432'
      }
    }]
  }];
  var filterCalls = new callsFilter(callsObj); //filterCalls.init();
  // function phoneWidgetBehavior(elem) {
  //     var $main = $(elem),
  //         backElement = '.widget-modal__close',
  //         widgetModal = '.widget-modal',
  //         widgetTab = '.phone-widget__tab',
  //         collapsibleContainer = '.collapsible-container';
  //         var events = {
  //             pwBackAction: 'pw-back-action'
  //         }
  //         this.actionMapping = function(object) {
  //             return {
  //                 back: object.back
  //             }
  //         };
  //         function getElement(selector) {
  //             return $($main).find(selector)
  //         }
  //         function backAction() {
  //             getElement(widgetModal).hide();
  //             getElement(widgetTab).removeClass('ovf-hidden');
  //             getElement(collapsibleContainer).collapse('hide');
  //         }
  //         $($main).on('click', backElement, function() {
  //             backAction();
  //             $(backElement).trigger(events.pwBackAction);
  //         });
  //     return {
  //         control: $main
  //     };
  // }
  // var widget = phoneWidgetBehavior('.phone-widget');
  // $(widget.control).on('pw-back-action', function () {
  //     console.log('here is a event for back button');
  // })
});

function stateTimer() {
  var interval = null;
  return {
    start: function (el, timerStamp) {
      var sec = Math.floor(timerStamp % 60);
      var min = Math.floor((timerStamp - sec) / 60);
      var hr = Math.floor((timerStamp - min) / 60);
      interval = setInterval(function () {
        sec = Math.floor(timerStamp % 60);
        min = Math.floor((timerStamp - sec) / 60 % 60);
        hr = Math.floor(timerStamp / 3600);

        if (timerStamp === 86399) {
          timerStamp = 0;
        }

        if (parseInt(sec) < 10) {
          sec = '0' + sec;
        }

        if (parseInt(min) < 10) {
          min = '0' + min;
        }

        if (parseInt(hr) < 10) {
          hr = '0' + hr;
        }

        timerStamp++;
        $(el).html(hr + ':' + min + ':' + sec);
      }, 1000);
    },
    clear: function () {
      clearInterval(interval);
    }
  };
}

function formatPhoneNumber(phoneNumberString) {
  let cleaned = ('' + phoneNumberString).replace(/\D/g, '');
  let match = cleaned.match(/^(1|)?(\d{3})(\d{3})(\d{4})$/);

  if (match) {
    var intlCode = match[1] ? '+1 ' : '';
    return [intlCode, '(', match[2], ') ', match[3], '-', match[4]].join('');
  }

  return null;
}

function toSelect(elem, obj, cb) {
  var $element = $(elem),
      $toggle = '.dropdown-toggle',
      $option = '.dropdown-item',
      selectedNumber = '.current-number__selected-nr',
      selectedText = '.current-number__selected-project';
  optionClass = 'dropdown-item';
  var selected = 'optionselected';
  this.data = {
    value: obj.selected.value,
    project: obj.selected.project,
    projectId: obj.selected.projectId
  };
  this.primaryData = {
    value: obj.primary ? obj.primary.value || null : null,
    project: obj.primary ? obj.primary.project || null : null,
    projectId: obj.primary ? obj.primary.projectId || null : null
  }; // nodes

  function selectedNode(value, project, id, projectId, length) {
    let chevronDown = '';

    if (length > 1) {
      chevronDown = '<i class="fa fa-chevron-down"></i>';
    }

    return '<button value="' + value + '" data-info-project="' + project + '" data-info-project-id="' + projectId + '" class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' + '<small class="current-number__phone current-number__selected-nr">' + formatPhoneNumber(value) + '</small>' + '<span class="current-number__identifier current-number__selected-project">' + project + '</span>' + chevronDown + '</button>';
  }

  function optionNode(optionList) {
    let arr = [];

    if (optionList.length > 1) {
      optionList.forEach(function (el) {
        arr.push('<button class="dropdown-item" type="button" value="' + el.value + '" data-info-project="' + el.project + '" data-info-project-id="' + el.projectId + '">' + '<small class="current-number__phone">' + formatPhoneNumber(el.value) + '</small>' + '<span class="current-number__identifier">' + el.project + '</span>' + '</button>');
      });
    }

    return arr;
  }

  function containerNode(selected, optionList) {
    let arr = optionNode(optionList).join('');
    let str = '<div class="dropdown">' + selected + '<div class="dropdown-menu" >' + arr + '</div>'; // if (optionList.length > 1) {
    //     str = str + '<i class="fa fa-chevron-down"></i>';
    // }

    str = str + '</div>';
    return str;
  }

  function generateSelect(obj) {
    let length = obj.options.length;
    $element.append(containerNode(selectedNode(obj.selected.value, obj.selected.project, obj.selected.id, obj.selected.projectId, length), obj.options));
  }

  function setValue(option) {
    this.data.value = option.val();
    this.data.project = option.attr('data-info-project');
    this.data.projectId = option.attr('data-info-project-id');
    $($element).trigger(selected);
  }

  this.getData = function () {
    return this.data;
  };

  this.setData = function () {
    return function (obj) {
      this.data.value = obj.value;
      this.data.project = obj.project;
      this.data.projectId = obj.projectId;
    }.bind(this);
  };

  this.setPrimaryData = function () {
    return function (obj) {
      console.log(obj);
      this.primaryData.value = obj.value;
      this.primaryData.project = obj.project;
      this.primaryData.projectId = obj.projectId;
    }.bind(this);
  };

  this.getPrimaryData = function () {
    return this.primaryData;
  };

  this.clearPrimaryData = function () {
    return function () {
      this.primaryData.value = null;
      this.primaryData.project = null;
      this.primaryData.projectId = null;
      return this;
    }.bind(this);
  };

  generateSelect(obj);
  $($element).on(selected, $($toggle), function (e) {
    var elem = e.target,
        $selectedNumber = $element.find(selectedNumber),
        $selectedText = $element.find(selectedText);
    $(elem).find($toggle).val(this.data.value);
    $selectedNumber.text(this.data.value);
    $selectedText.text(this.data.project);

    if (typeof cb === 'function') {
      cb.call(this);
    }
  }.bind(this));
  $($element).on('click', $option, function () {
    setValue($(this));
  });
  return {
    getData: this.getData(),
    setData: this.setData(),
    setPrimaryData: this.setPrimaryData(),
    getPrimaryData: this.getPrimaryData(),
    clearPrimaryData: this.clearPrimaryData()
  };
}

function handleWidgetIcon() {
  var $parent = $('.phone-widget-icon'),
      $inner = '.widget-icon-inner',
      animationClass = 'animate',
      initialNode;
  var interval = null;

  function createInitialIcon(type, status) {
    initialNode = '<div class="widget-icon-inner" data-wi-type="' + type + '" data-wi-status="' + status + '">' + '<div class="standby-phone">' + '<i class="fa fa-phone-volume icon-phone-answer"></i>' + '<div class="phone-widget-icon__state">' + '<span class="phone-widget-icon__ongoing"></span>' + '<span class="phone-widget-icon__text"></span>' + '<span class="phone-widget-icon__time"></span>' + '</div>' + '<i class="fa fa-phone icon-phone"></i>' + '</div>' + '</div>';
    $($parent).append(initialNode);
  }

  function stateTimer(el, timerStamp) {
    var sec = Math.floor(timerStamp % 60);
    var min = Math.floor((timerStamp - sec) / 60);
    var hr = Math.floor((timerStamp - min) / 60);
    interval = setInterval(function () {
      sec = Math.floor(timerStamp % 60);
      min = Math.floor((timerStamp - sec) / 60 % 60);
      hr = Math.floor(timerStamp / 3600);

      if (timerStamp === 86399) {
        timerStamp = 0;
      }

      if (parseInt(sec) < 10) {
        sec = '0' + sec;
      }

      if (parseInt(min) < 10) {
        min = '0' + min;
      }

      if (parseInt(hr) < 10) {
        hr = '0' + hr;
      }

      timerStamp++;
      $(el).html(hr + ':' + min + ':' + sec);
    }, 1000);
  }

  function updateIcon(props) {
    $($inner).removeClass(animationClass);
    var inner = '.widget-icon-inner',
        ongoing = '.phone-widget-icon__ongoing',
        text = '.phone-widget-icon__text',
        time = '.phone-widget-icon__time';

    if (props.timer) {
      $(time).html(null);
    }

    clearInterval(interval);
    $(inner).attr('data-wi-status', props.status);
    $(inner).attr('data-wi-type', props.type);
    $(ongoing).html(props.currentCalls);
    $(text).html(props.text);

    if (props.timer) {
      stateTimer(time, props.timerStamp);
    } else {
      $(time).html(null);
    }

    props = null;
    $($inner).addClass(animationClass);
  }

  return {
    init: function () {
      createInitialIcon('default', false);
    },
    update: function (props) {
      updateIcon(props);
    }
  };
}

function callsFilter(object) {
  var queuesParent = '.queue-separator',
      queuesItem = '.queue-separator__item',
      listingParent = '.calls-separator',
      callsParent = '.call-in-progress',
      filterToggle = '.call-filter__toggle',
      listingItem = '.calls-separator__list-item',
      callItem = '.call-in-progress__list-item'; // function getQueueItem (string, data) {
  //     var queueItem = '<li class="queue-separator__item" data-queue-type="' + data + '">' +
  //         '<div class="queue-separator__name">' + string + '</div>' +
  //         '<ul class="calls-separator"> </ul>' +
  //         '</li>';
  //     return queueItem;
  // }
  //
  // function getListingItem (props) {
  //
  //     function getProjectBinding (data) {
  //         if (data.project && data.department) {
  //             return '<div class="static-number-indicator">' +
  //                 '<span class="static-number-indicator__label">' + props.project + '</span>' +
  //                 '<i class="static-number-indicator__separator"></i>' +
  //                 '<span class="static-number-indicator__name">' + props.department + '</span>' +
  //                 '</div>'
  //         } else if (data.project && !data.department) {
  //             return '<div class="static-number-indicator">' +
  //                 '<span class="static-number-indicator__label">' + props.project + '</span>' +
  //                 '</div>'
  //         } else {
  //             return '<div class="static-number-indicator">' +
  //                 '<span class="static-number-indicator__name">Exteral contact</span>' +
  //                 '</div>'
  //         }
  //     }
  //
  //
  //
  //     var listing = '<li class="calls-separator__list-item" id="' + props.id + '">' +
  //         getProjectBinding(props) +
  //         '<ul class="call-in-progress">' +
  //         '</ul>' +
  //         '</li>';
  //
  //     return listing;
  //
  // }
  //
  // function getCallNode (props) {
  //
  //     var item = '<li class="call-in-progress__list-item" id="' + props.id + '">' +
  //         '<div class="call-in-progress__call-item call-list-item" data-call-status="' + props.state + '">' +
  //         '<div class="call-list-item__info">' +
  //         '<ul class="call-list-item__info-list call-info-list">' +
  //         '<li class="call-info-list__item">' +
  //         '<b class="call-info-list__contact-icon">' +
  //         '<i class="fa fa-user"></i>' +
  //         '</b>' +
  //         '<span class="call-info-list__name">' + props.contact.name + '</span>' +
  //         '</li>' +
  //         '<li class="call-info-list__item">' +
  //         '<span class="call-info-list__company">' + props.contact.company + '</span>' +
  //         '</li>' +
  //         '<li class="call-info-list__item">' +
  //         '<span class="call-info-list__number">' + props.contact.number + '</span>' +
  //         '</li>' +
  //         '</ul>' +
  //         '<div class="call-list-item__info-action call-info-action">' +
  //         '<span class="call-info-action__timer"></span>' +
  //         '<a href="#" class="call-info-action__more"><i class="fa fa-ellipsis-h"></i></a>' +
  //         '' +
  //         '</div>' +
  //         '<ul class="call-list-item__menu call-item-menu">' +
  //         '<li class="call-item-menu__list-item">' +
  //         '<a href="#" class="call-item-menu__close">' +
  //         '<i class="fa fa-chevron-right"></i>' +
  //         '</a>' +
  //         '</li>' +
  //         '<li class="call-item-menu__list-item">' +
  //         '<a href="#" class="call-item-menu__transfer">' +
  //         '<i class="fa fa-random"></i>' +
  //         '</a>' +
  //         '</li>' +
  //         '<li class="call-item-menu__list-item">' +
  //         '<a href="#" class="call-item-menu__transfer">' +
  //         '<i class="fa fa-pause"></i>' +
  //         '</a>' +
  //         '</li>' +
  //         '<li class="call-item-menu__list-item">' +
  //         '<a href="#" class="call-item-menu__transfer">' +
  //         '<i class="fas fa-phone-slash"></i>' +
  //         '</a>' +
  //         '</li>' +
  //         '</ul>' +
  //         '</div>' +
  //         '<div class="call-list-item__main-action">' +
  //         '<a href="#" class="call-list-item__main-action-trigger">' +
  //         '<i class="phone-icon phone-icon--start fa fa-phone"></i>' +
  //         '<i class="phone-icon phone-icon--end fa fa-phone-slash"></i>' +
  //
  //         '</a>' +
  //         '</div>' +
  //         '</div>' +
  //         '</li>';
  //
  //
  //     return item;
  // }
  //
  // function filterData (handler, dataObj) {
  //     var obj = JSON.parse(JSON.stringify(dataObj))
  //     var filtered = [];
  //
  //     for (const item in obj) {
  //         if (obj.hasOwnProperty(item)) {
  //             filtered.push(obj[item])
  //         }
  //     }
  //
  //     filtered.forEach(function (el, i) {
  //
  //         if ($(handler).attr('data-call-filter') === 'all') {
  //             return filtered;
  //         }
  //
  //         el.calls = el.calls.filter(function (call, i) {
  //             if (call.state === $(handler).attr('data-call-filter')) {
  //                 return call
  //             }
  //         })
  //     })
  //
  //     return filtered;
  // }
  //
  // function renderData (incomingData) {
  //     var refData = JSON.parse(JSON.stringify(incomingData));
  //     var callsList = [];
  //     var queues = [];
  //
  //
  //     $(queuesItem).detach();
  //     $(listingItem).detach();
  //
  //     refData.forEach(function (element, i) {
  //         element.calls.forEach(function (call) {
  //             if (queues.indexOf(call.state) === -1) {
  //                 queues.push(call.state)
  //             }
  //
  //             if (callsList.indexOf(call) === -1) {
  //                 callsList.push(call)
  //             }
  //         })
  //     })
  //
  //     callsList.forEach(function (call, i) {
  //         var timer = new stateTimer();
  //         timer.start($('.call-info-action__timer')[i], call.length);
  //
  //     })
  //
  //     function objRemaster(list, obj) {
  //         var data = {};
  //         var foo = [];
  //         var tmpArr;
  //
  //         list.forEach(function(listItem, i) {
  //
  //             data[listItem] = [];
  //             obj.forEach(function (objEl, i) {
  //                 var tmpArr = [];
  //                 objEl.calls.filter(function(call){
  //                     if (call.state === listItem) {
  //                         tmpArr.push(call)
  //                     }
  //                     return call
  //                 })
  //                 for (var key in data) {
  //                     data[key] = obj
  //                 }
  //                 foo.push(tmpArr)
  //
  //             })
  //
  //         })
  //
  //
  //         for (var key in data) {
  //
  //             data[key].forEach(function(obj) {
  //                 var arr = [];
  //                 obj.calls.filter(function(item, i) {
  //                     arr = obj.calls;
  //                     if (item.state !== key) {
  //                         // console.log(item)
  //                         // console.log(data[key],obj)
  //                         // obj.calls.splice(obj.calls.indexOf(item), 1)
  //                     }
  //                 })
  //
  //
  //             })
  //         }
  //         return data
  //
  //     }
  //
  //
  //     var queueName = {
  //         'hold': 'On Hold',
  //         'direct': 'Direct Calls',
  //         'general': 'General Line',
  //         'inProgress': 'Active'
  //     }
  //
  //
  //     var rData = objRemaster(queues, refData);
  //
  //     for (var key in rData) {
  //         $(queuesParent).append(getQueueItem(queueName[key], key))
  //
  //         var section = $('[data-queue-type="'+ key +'"]');
  //
  //         rData[key].forEach(function (listing) {
  //
  //             $(section).find(listingParent).append(getListingItem(listing))
  //
  //             listing.calls.forEach(function(call) {
  //
  //                 if ($(section).attr('data-queue-type') === call.state) {
  //                     $(section).find(listingItem).append(getCallNode(call))
  //                 }
  //             })
  //         })
  //     }
  //
  //
  //
  // }

  return {
    init: function () {// renderData(object);
      // function clearIndicators (target) {
      //     var markElement = $('.widget-line-overlay__queue-marker');
      //
      //     markElement.removeClass('tab-hold');
      //     markElement.removeClass('tab-direct');
      //     markElement.removeClass('tab-general');
      //     markElement.removeClass('tab-all');
      //
      //     switch ($(target).attr('data-call-filter')) {
      //         case 'hold':
      //             $('[data-queue-marker]').html('Calls On Hold');
      //             markElement.addClass('tab-hold')
      //             break;
      //         case 'direct':
      //             $('[data-queue-marker]').html('Direct Calls')
      //             markElement.addClass('tab-direct')
      //             break;
      //         case 'general':
      //             $('[data-queue-marker]').html('General Lines')
      //             markElement.addClass('tab-general')
      //             break;
      //         case 'all':
      //             $('[data-queue-marker]').html('Calls Queue')
      //             break;
      //     }
      // }
      // $(document).on('click', filterToggle, function (e) {
      //     e.preventDefault();
      //
      //     $('.widget-line-overlay').show();
      //     var activeClass = 'is-checked';
      //     var localObj = filterData($(this), object);
      //     renderData(localObj);
      //     $(filterToggle).removeClass(activeClass);
      //     $(this).addClass(activeClass);
      //     clearIndicators($(this));
      // });
      // $(document).on('click', filterToggle, function (e) {
      //     $('.widget-line-overlay').show();
      //
      //     e.preventDefault();
      //     var markElement = $('.widget-line-overlay__queue-marker');
      //
      //     var activeClass = 'is-checked';
      //     var localObj = filterData($(this), object)
      //     renderData(localObj);
      //
      //     $(filterToggle).removeClass(activeClass)
      //     $(this).addClass(activeClass)
      //
      //     clearIndicators($(this));
      // });
      // $(document).on('click', '.widget-line-overlay__show-all-queues', function (e) {
      //     e.preventDefault();
      //     $(filterToggle).addClass('is-checked');
      //     var localObj = filterData($(this), object)
      //     renderData(localObj);
      //
      //    // clearIndicators($(this));
      //
      // })
    },
    reset: function () {
      $('.widget-line-overlay').hide();
      var activeClass = 'is-checked';
      $(filterToggle).removeClass(activeClass);
    }
  };
}

$(document).on('click', '.call-item-menu__close', function (e) {
  e.preventDefault();
  $(this).closest('.call-list-item').removeClass('call-list-item--menu');
});
$(document).on('click', '.call-info-action__more', function (e) {
  e.preventDefault();
  $(this).closest('.call-list-item').addClass('call-list-item--menu');
});
$(document).on('click', '.call-details__nav-btn--more', function (e) {
  e.preventDefault();
  $('.conference-call-details').addClass('is_active');
});
$(document).on('click', '.call-details__nav-btn--back', function (e) {
  e.preventDefault();
  $('.conference-call-details').removeClass('is_active');
});

function widgetStatus(selector, updateStatusUrl) {
  let url = updateStatusUrl;
  var parent = '.status-confirmation';
  var state = {
    status: $(selector).attr('checked') ? true : false,
    shown: false
  };

  function node(status) {
    return '<div class="status-confirmation-tooltip">' + '<span>Switch to <i class="' + (status ? 'occupied' : 'online') + '">' + (status ? 'occupied' : 'online') + '</i> ?</span>' + '<div class="status-action-group">' + '<a href="#" data-status-action="false">NO</a>' + '<a href="#" data-status-action="true"><i class="fa fa-check"></i></a>' + '</div>' + '</div>';
  }

  function handleChange(btn) {
    let action = btn.attr('data-status-action');

    if (action === 'true') {
      btn.html('<i class="fa fa-spinner fa-spin"></i>');
      let type_id = 1;

      if (state.status) {
        type_id = 2;
      }

      $.ajax({
        type: 'post',
        data: {
          'type_id': type_id
        },
        url: url
      }).done(function (data) {
        let status = true;

        if (type_id === 2) {
          status = false;
        }

        $(selector).prop('checked', status);
        state.status = status;
      }).fail(function () {
        new PNotify({
          title: "Change status",
          type: "error",
          text: "Server error",
          hide: true
        });
      }).always(function () {
        btn.html('<i class="fa fa-check"></i>');

        if (state.shown) {
          $('.status-confirmation-tooltip').detach();
        }

        state.shown = false;
      });
    } else {
      if (state.shown) {
        $('.status-confirmation-tooltip').detach();
      }

      state.shown = false;
    }
  }

  $(document).on('click', '[data-status-action]', function (e) {
    e.preventDefault();
    handleChange($(this));
  });
  $(document).on('click', selector, function (e) {
    e.preventDefault();

    if (!state.shown) {
      state.shown = true;
      $(parent).append(node(state.status));
    }
  });
  $(document).on('click', '.phone-widget', function (e) {
    if (state.shown && !$(e.target).closest('.number-toggle').length) {
      $('.status-confirmation-tooltip').detach();
      state.shown = false;
    }
  });
  return {
    getStatus: function () {
      switch (state.status) {
        case true:
          return 1;

        case false:
          return 2;
      }
    },
    setStatus: function (status) {
      if (status === 1) {
        state.status = true;
      } else {
        state.status = false;
      }

      $(parent).html('');
      $('.status-confirmation-tooltip').detach();
      state.shown = false;
      $(selector).prop('checked', state.status);
    }
  };
}

var PhoneWidgetCall = function () {
  this.connection = '';
  let statusCheckbox = null;
  let settings = {
    'ajaxCallRedirectGetAgents': '',
    'callStatusUrl': '',
    'ajaxSaveCallUrl': '',
    'clearMissedCallsUrl': '',
    'currentQueueCallsUrl': '',
    'dialpadEnabled': true
  };
  let callRequester = new window.phoneWidget.requesters.CallRequester();
  let waitQueue = new window.phoneWidget.queue.Queue();
  let queues = {
    'wait': waitQueue,
    'direct': new window.phoneWidget.queue.Direct(waitQueue),
    'hold': new window.phoneWidget.queue.Hold(waitQueue),
    'general': new window.phoneWidget.queue.General(waitQueue),
    'outgoing': new window.phoneWidget.queue.Queue(),
    'active': window.phoneWidget.queue.Active()
  };
  let storage = {
    'conference': window.phoneWidget.storage.conference
  };
  let panes = {
    'active': PhoneWidgetPaneActive,
    'outgoing': PhoneWidgetPaneOutgoing,
    'incoming': PhoneWidgetPaneIncoming,
    'queue': new PhoneWidgetPaneQueue(queues)
  };

  function init(options) {
    callRequester.init(options);
    Object.assign(settings, options);
    statusCheckbox = new widgetStatus('.call-status-switcher', options.updateStatusUrl);
    statusCheckbox.setStatus(options.status);
    widgetIcon.update({
      type: 'default',
      timer: false,
      text: null,
      currentCalls: null,
      status: statusCheckbox.getStatus() === 1
    });
    setCountMissedCalls(options.countMissedCalls);
    panes.active.setup(options.btnHoldShow, options.btnTransferShow);
    muteBtnClickEvent();
    transferCallBtnClickEvent();
    acceptCallBtnClickEvent();
    rejectIncomingCallClickEvent();
    hideIncomingCallClickEvent();
    callAddNoteCLickEvent();
    dialpadCLickEvent();
    contactInfoClickEvent();
    holdClickEvent();
    hangupClickEvent();
    insertPhoneNumberEvent();
    loadCurrentQueueCalls();
  }

  function removeIncomingRequest(callSid) {
    waitQueue.remove(callSid);
    panes.queue.refresh();

    if (panes.incoming.getCallSid() === callSid) {
      panes.incoming.removeCallSid();

      if (panes.incoming.isActive()) {
        panes.incoming.hide();
        refreshPanes();
      }
    }
  }

  function refreshPanes() {
    PhoneWidgetContactInfo.hide();
    PhoneWidgetDialpad.hide();

    if (refreshOutgoingPane()) {
      return;
    }

    if (refreshActivePane()) {
      return;
    }

    widgetIcon.update({
      type: 'default',
      timer: false,
      text: null,
      currentCalls: null,
      status: statusCheckbox.getStatus() === 1
    });
    $('#tab-phone .call-pane-initial').removeClass('is_active');
    $('#tab-phone .call-pane').addClass('is_active');
  }

  function refreshOutgoingPane() {
    let call = queues.outgoing.getLast();

    if (call !== null) {
      panes.outgoing.init(call);
      return true;
    }

    return false;
  }

  function refreshActivePane() {
    let call = queues.active.getLast();

    if (call !== null) {
      let conference = storage.conference.one(call.data.conferenceSid);

      if (conference !== null) {
        if (conference.getCountParticipants() > 2) {
          panes.active.init(call, conference);
          return true;
        }
      }

      panes.active.init(call);
      return true;
    }

    return false;
  }

  function requestIncomingCall(data) {
    console.log('incoming call');
    let call = waitQueue.add(data);

    if (call === null) {
      console.log('Call is already exist in Wait Queue');
      return false;
    }

    if (call.data.queue === 'hold') {
      console.log('hold call');
      panes.queue.refresh();
      return false;
    }

    panes.queue.refresh();
    panes.queue.hide();
    panes.incoming.init(call, queues.direct.count() + queues.general.count(), queues.active.count() + queues.hold.count());
    openWidget();
    openCallTab();
  }

  function requestOutgoingCall(data) {
    console.log('outgoing call');
    let call = null;

    if (data.callSid) {
      call = queues.outgoing.add(data);

      if (call === null) {
        console.log('Call is already exist in Outgoing Queue');
        return false;
      }
    } else {
      call = new window.phoneWidget.call.Call(data);
    }

    panes.outgoing.init(call);
    openWidget();
    openCallTab();
  }

  function requestActiveCall(data) {
    console.log('active call');
    waitQueue.remove(data.callSid);
    queues.outgoing.remove(data.callSid);
    let call = queues.active.add(data);

    if (call === null) {
      console.log('Call is already exist in Active Queue');
      return false;
    }

    if (panes.outgoing.getCallSid() === call.data.callSid) {
      panes.outgoing.removeCallSid();
      panes.outgoing.hide();
    }

    if (typeof data.conference !== 'undefined' && data.conference !== null) {
      storage.conference.remove(data.conference.sid);
      let conference = storage.conference.add(data.conference);

      if (conference === null) {
        console.log('conference not added');
      } else {
        if (conference.getCountParticipants() > 2) {
          panes.active.init(call, conference);
          openWidget();
          openCallTab();
          panes.queue.refresh();
          panes.queue.hide();
          return;
        }
      }
    }

    panes.active.init(call);
    openWidget();
    openCallTab();
    panes.queue.refresh();
    panes.queue.hide();
  }

  function conferenceUpdate(data) {
    console.log('conference update');
    let call = null;
    data.conference.participants.forEach(function (participant) {
      if (call === null) {
        call = queues.active.one(participant.callSid);
      }
    });

    if (call === null) {
      console.log('not found call in active queue');
      return;
    }

    let conference = storage.conference.one(data.conference.sid);

    if (conference !== null) {
      let newConferenceCountParticipant = data.conference.participants.length;
      let oldConferenceCountParticipant = conference.getCountParticipants();

      if (oldConferenceCountParticipant < 3 && newConferenceCountParticipant < 3) {
        //todo
        storage.conference.remove(data.conference.sid);
        conference = storage.conference.add(data.conference);
        return;
      }

      if (oldConferenceCountParticipant < 3 && newConferenceCountParticipant > 2) {
        storage.conference.remove(data.conference.sid);
        conference = storage.conference.add(data.conference);
        panes.active.init(call, conference);
        return;
      }

      if (oldConferenceCountParticipant > 2 && newConferenceCountParticipant > 2) {
        storage.conference.update(data.conference);
        return;
      }

      if (oldConferenceCountParticipant > 2 && newConferenceCountParticipant < 3) {
        storage.conference.remove(data.conference.sid);
        storage.conference.add(data.conference);
        panes.active.init(call);
        return;
      }

      console.log('not found rule for conference update');
      return;
    }

    conference = storage.conference.add(data.conference);

    if (conference.getCountParticipants() > 2) {
      panes.active.init(call, conference);
      return;
    }

    panes.active.init(call);
  }

  function completeCall(callSid) {
    queues.active.remove(callSid);
    queues.outgoing.remove(callSid);
    waitQueue.remove(callSid);
    storage.conference.removeByParticipantCallSid(callSid);
    panes.queue.refresh();
    let needRefresh = false;

    if (panes.active.getCallSid() === callSid) {
      panes.active.removeCallSid();
      panes.active.removeCallInProgressIndicator();
      window.connection = '';

      if (panes.active.isActive()) {
        needRefresh = true;
      }
    }

    if (panes.outgoing.getCallSid() === callSid) {
      panes.outgoing.removeCallSid();

      if (panes.outgoing.isActive()) {
        needRefresh = true;
      }
    }

    if (panes.incoming.getCallSid() === callSid) {
      panes.incoming.removeCallSid();

      if (panes.incoming.isActive()) {
        needRefresh = true;
      }
    }

    if (needRefresh) {
      refreshPanes();
    } else {
      if (panes.incoming.isActive()) {
        panes.incoming.initWidgetIcon(queues.direct.count() + queues.general.count(), queues.active.count() + queues.hold.count());
      }
    }
  }

  function rejectIncomingCallClickEvent() {
    $(document).on('click', '#reject-incoming-call', function (e) {
      e.preventDefault();

      if (window.connection) {
        window.connection.reject();
        $.get(settings.ajaxSaveCallUrl + '?sid=' + window.connection.parameters.CallSid);
        $('#call-controls2').hide();
      }
    });
  }

  function hideIncomingCallClickEvent() {
    $(document).on('click', '#hide-incoming-call', function (e) {
      e.preventDefault();
      let callSid = $(this).attr('data-call-sid');

      if (!callSid) {
        createNotify('Error', 'Not found Call SID', 'error');
        return false;
      }

      if (panes.incoming.getCallSid() === callSid) {
        panes.incoming.removeCallSid();

        if (panes.incoming.isActive()) {
          panes.incoming.hide();
          refreshPanes();
        }
      }
    });
  }

  function callAddNoteCLickEvent() {
    $(document).on('click', '#active_call_add_note_submit', function (e) {
      e.preventDefault();
      let callSid = $(this).data('call-sid');

      if (!callSid) {
        createNotify('Warning', 'Call SID is undefined', 'warning');
        return false;
      }

      let call = queues.active.one(callSid);

      if (call === null) {
        createNotify('Error', 'Not found Call on Active Queue', 'error');
        return false;
      }

      let $container = document.getElementById('active_call_add_note');
      let value = $container.value.trim();

      if (!value) {
        createNotify('Warning', 'Note value is empty', 'warning');
        return false;
      }

      if (!call.setAddNoteRequestState()) {
        return false;
      }

      callRequester.addNote(call, value, $container);
    });
    $(document).on('click', '#wg-add-note', function (e) {
      e.preventDefault();
      $('.additional-info.add-note').slideDown(200);
    });
    $(document).on('click', '.additional-info.add-note .additional-info__close', function () {
      $('.add-note').slideUp(150);
    });
  } // function bindVolumeIndicators(connection)
  // {
  //     connection.on('volume', function (inputVolume, outputVolume) {
  //         volumeIndicatorsChange(inputVolume, outputVolume);
  //     });
  // }


  function volumeIndicatorsChange(inputVolume, outputVolume) {
    $('#wg-call-microphone .sound-ovf').css('right', -Math.floor(inputVolume * 100) + '%');
    $('#wg-call-volume .sound-ovf').css('right', -Math.floor(outputVolume * 100) + '%');
  }

  function muteBtnClickEvent() {
    let _self = this;

    $(document).on('click', '.queue-separator .list_item_mute', function (e) {
      let callSid = $(this).attr('data-call-sid');

      if (!callSid) {
        createNotify('Error', 'Not found Call SID', 'error');
        return false;
      }

      let call = queues.active.one(callSid);

      if (call === null) {
        createNotify('Error', 'Not found Call on Active Queue', 'error');
        return false;
      }

      if (call.data.isMute) {
        if (!call.setUnMuteRequestState()) {
          return false;
        }

        callRequester.unMute(call);
      } else {
        if (!call.setMuteRequestState()) {
          return false;
        }

        callRequester.mute(call);
      }
    });
    $(document).on('click', '.call-pane-calling #call-pane__mute', function (e) {
      let muteBtn = $(this);

      if (conferenceBase) {
        let callSid = $(this).attr('data-call-sid');

        if (!callSid) {
          createNotify('Error', 'Not found Call SID', 'error');
          return false;
        }

        let call = queues.active.one(callSid);

        if (call === null) {
          createNotify('Error', 'Not found Call on Active Queue', 'error');
          return false;
        }

        if (call.data.isMute) {
          if (!call.setUnMuteRequestState()) {
            return false;
          }

          callRequester.unMute(call);
        } else {
          if (!call.setMuteRequestState()) {
            return false;
          }

          callRequester.mute(call);
        }
      } else {
        let connection = _self.connection;
        let oldBtn = $('#btn-mute-microphone');

        if (muteBtn.attr('data-is-muted') === 'false') {
          if (connection) {
            connection.mute(true);

            if (connection.isMuted()) {
              panes.active.buttons.mute.mute();
              oldBtn.html('<i class="fa fa-microphone"></i> Unmute').removeClass('btn-success').addClass('btn-warning');
            } else {
              new PNotify({
                title: "Mute",
                type: "error",
                text: "Error",
                hide: true
              });
            }
          }
        } else {
          if (connection) {
            connection.mute(false);

            if (!connection.isMuted()) {
              panes.active.buttons.mute.unMute();
              oldBtn.html('<i class="fa fa-microphone"></i> Mute').removeClass('btn-warning').addClass('btn-success');
            } else {
              new PNotify({
                title: "Unmute",
                type: "error",
                text: "Error",
                hide: true
              });
            }
          }
        }
      }
    });
  }

  function updateConnection(conn) {
    this.connection = conn;
  }

  function transferCallBtnClickEvent() {
    $(document).on('click', '.wg-transfer-call', function (e) {
      e.preventDefault();
      let callSid = $(this).attr('data-call-sid');

      if (!callSid) {
        createNotify('Error', 'Not found Call SID', 'error');
        return false;
      }

      let call = queues.active.one(callSid);

      if (call === null) {
        createNotify('Error', 'Not found Call on Active Queue', 'error');
        return false;
      }

      if (!call.canTransfer()) {
        // createNotify('Error', 'Disallow transfer', 'error');
        return false;
      }

      initRedirectToAgent(call.data.callSid);
    });
  }

  function initRedirectToAgent(callSid) {
    if (settings.ajaxCallRedirectGetAgents === undefined) {
      alert('Ajax call redirect url is not set');
      return false;
    }

    let modal = $('#web-phone-redirect-agents-modal');
    modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
    $('#web-phone-redirect-agents-modal-label').html('Transfer Call');
    $.post(settings.ajaxCallRedirectGetAgents, {
      sid: callSid
    }) // , user_id: userId
    .done(function (data) {
      modal.find('.modal-body').html(data);
    }); // let connection = this.connection;
    // if (connection && connection.parameters.CallSid) {
    //     let callSid = connection.parameters.CallSid;
    //     let modal = $('#web-phone-redirect-agents-modal');
    //     modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
    //     $('#web-phone-redirect-agents-modal-label').html('Transfer Call');
    //
    //     $.post(options.ajaxCallRedirectGetAgents, { sid: callSid }) // , user_id: userId
    //         .done(function(data) {
    //             modal.find('.modal-body').html(data);
    //         });
    // } else {
    //     alert('Error: Not found Call connection or Call SID!');
    // }
  }

  function refreshCallStatus(obj) {
    if (obj.status === 'In progress') {
      requestActiveCall(obj);
    } else if (obj.status === 'Ringing' || obj.status === 'Queued') {
      if (parseInt(obj.typeId) === 2) {
        requestIncomingCall(obj);
      } else if (parseInt(obj.typeId) === 1) {
        requestOutgoingCall(obj);
      }
    } else if (obj.status === 'Completed' || obj.isEnded || parseInt(obj.cua_status_id) === 5) {
      completeCall(obj.callSid);
    }
  }

  function openWidget() {
    $('.phone-widget').addClass('is_active');
    $('.js-toggle-phone-widget').removeClass('is-mirror');
  }

  function openCallTab() {
    $('.phone-widget__tab').removeClass('is_active');
    $('[data-toggle-tab]').removeClass('is_active');
    $('#tab-phone').addClass('is_active');
    $('[data-toggle-tab]').each(function (index) {
      if ($(this).data('toggle-tab') === 'tab-phone') {
        $(this).addClass('is_active');
      }
    });
  }

  function showCallingPanel() {
    $('#tab-phone .call-pane-initial').removeClass('is_active');
    $('#tab-phone .call-pane-calling').addClass('is_active');
  }

  function acceptCallBtnClickEvent() {
    $(document).on('click', '#btn-accept-call', function () {
      let btn = $(this);
      acceptCall(btn.attr('data-call-sid'), btn.attr('data-from-internal'));
    });
    $(document).on('click', '.call-list-item__main-action-trigger', function () {
      let btn = $(this);
      let action = $(this).attr('data-type-action');

      if (action === 'accept') {
        acceptCall(btn.attr('data-call-sid'), btn.attr('data-from-internal'));
        return false;
      }

      if (action === 'acceptInternal') {
        let call = queues.direct.one(btn.attr('data-call-sid'));

        if (call === null) {
          createNotify('Accept Internal Call', 'Not found Call on Direct Incoming Queue', 'error');
          return false;
        }

        acceptInternalCall(call);
        return false;
      }

      if (action === 'hangup') {
        hangup(btn.attr('data-call-sid'));
        return false;
      }

      if (action === 'return') {
        returnHoldCall(btn.attr('data-call-sid'));
        return false;
      }

      console.log('Undefined type action');
    });
  }

  function returnHoldCall(callSid) {
    if (!checkDevice('Return Hold Call')) {
      return false;
    }

    let call = queues.hold.one(callSid);

    if (call === null) {
      createNotify('Error', 'Not found call on Hold Queue', 'error');
      return false;
    }

    if (!call.setReturnHoldCallRequestState()) {
      return false;
    }

    callRequester.returnHoldCall(call);
  }

  function checkDevice(title) {
    if (typeof device == "undefined" || device == null || device && device._status !== 'ready') {
      createNotify(title, 'Please try again after some seconds. Device is not ready.', 'warning');
      return false;
    }

    return true;
  }

  function acceptCall(callSid, fromInternal) {
    if (!checkDevice('Accept Call')) {
      return false;
    }

    if (fromInternal !== 'false' && window.connection) {
      window.connection.accept();
      showCallingPanel();
      $('#call-controls2').hide();
    } else {
      let call = waitQueue.one(callSid);

      if (call === null) {
        createNotify('Error', 'Not found call on Wait Queue', 'error');
        return false;
      }

      if (!call.setAcceptCallRequestState()) {
        return false;
      }

      callRequester.accept(call);
    }
  }

  function changeStatus(status) {
    statusCheckbox.setStatus(status);

    if (!panes.active.isActive() && !panes.incoming.isActive() && !panes.outgoing.isActive()) {
      widgetIcon.update({
        type: 'default',
        timer: false,
        text: null,
        currentCalls: null,
        status: statusCheckbox.getStatus() === 1
      });
    }
  }

  function setCountMissedCalls(count) {
    $('[data-toggle-tab="tab-history"]').attr('data-missed-calls', count);
  }

  function addMissedCall() {
    let count = $('[data-toggle-tab="tab-history"]').attr('data-missed-calls');
    count++;
    $('[data-toggle-tab="tab-history"]').attr('data-missed-calls', count);
  }

  function requestClearMissedCalls() {
    $.ajax({
      type: 'post',
      data: {},
      url: settings.clearMissedCallsUrl
    }).done(function (data) {
      setCountMissedCalls(data.count);
    }).fail(function () {
      createNotify('Clear missed calls', 'Server error', 'error');
    });
  }

  function hold(callSid) {
    let call = queues.active.one(callSid);

    if (call === null) {
      return;
    }

    call.hold(); //todo remove after removed old widget

    if (!(panes.active.getCallSid() === call.data.callSid && panes.active.isActive())) {
      return;
    }

    window.phoneWidget.oldWidget.hold();
    widgetIcon.update({
      type: 'hold',
      timer: true,
      'timerStamp': 0,
      text: 'on hold',
      currentCalls: null,
      status: 'online'
    });
  }

  function unhold(callSid) {
    let call = queues.active.one(callSid);

    if (call === null) {
      return;
    }

    call.unHold(); //todo remove after removed old widget

    if (!(panes.active.getCallSid() === call.data.callSid && panes.active.isActive())) {
      return;
    }

    window.phoneWidget.oldWidget.unHold();
    widgetIcon.update({
      type: 'inProgress',
      timer: true,
      'timerStamp': call.getDuration(),
      text: 'on call',
      currentCalls: '',
      status: 'online'
    });
  }

  function dialpadCLickEvent() {
    $(document).on('click', '.call-pane-calling #wg-dialpad', function () {
      if ($(this).attr('data-active') === 'true') {
        $('.dial-popup').slideDown(150);
      }
    });
    $(document).on('click', '.dial-popup .additional-info__close', function () {
      $('.dial-popup').slideUp(150);
    });
  }

  function contactInfoClickEvent() {
    $(document).on('click', '.call-pane__info', function () {
      $('.contact-info').slideDown(150);
    });
    $(document).on('click', '.additional-info.contact-info .additional-info__close', function () {
      $('.contact-info').slideUp(150);
    });
  }

  function hangupClickEvent() {
    $(document).on('click', '#cancel-active-call', function (e) {
      hangup($(this).attr('data-call-sid'));
    });
    $(document).on('click', '#cancel-outgoing-call', function (e) {
      e.preventDefault();
      let btn = $(this);
      let callSid = btn.attr('data-call-sid');

      if (!callSid) {
        createNotify('Hangup', 'Please try again after some seconds.', 'warning');
        return false;
      }

      let call = queues.outgoing.one(callSid);

      if (call === null) {
        createNotify('Error', 'Not found Call on Outgoing Queue', 'error');
        return false;
      }

      if (!call.setHangupRequestState()) {
        return false;
      }

      callRequester.hangupOutgoingCall(call);
    });
  }

  function holdClickEvent() {
    $(document).on('click', '#wg-hold-call', function (e) {
      if (!conferenceBase) {
        return false;
      }

      let callSid = $(this).attr('data-call-sid');

      if (!callSid) {
        createNotify('Error', 'Not found Call SID', 'error');
        return false;
      }

      let call = queues.active.one(callSid);

      if (call === null) {
        createNotify('Error', 'Not found Call on Active Queue', 'error');
        return false;
      }

      if (!call.canHoldUnHold()) {
        return false;
      }

      if (call.data.isHold) {
        sendUnHoldRequest(call.data.callSid);
      } else {
        sendHoldRequest(call.data.callSid);
      }
    });
    $(document).on('click', '.list_item_hold', function (e) {
      if (!conferenceBase) {
        return false;
      }

      let callSid = $(this).attr('data-call-sid');

      if (!callSid) {
        createNotify('Error', 'Not found Call SID', 'error');
        return false;
      }

      let call = queues.active.one(callSid);

      if (call === null) {
        createNotify('Error', 'Not found Call on Active Queue', 'error');
        return false;
      }

      if (!call.canHoldUnHold()) {
        return false;
      }

      if (call.data.isHold) {
        sendUnHoldRequest(call.data.callSid);
      } else {
        sendHoldRequest(call.data.callSid);
      }
    });
  }

  function insertPhoneNumberEvent() {
    $(document).on('click', '.phone-dial-history', function (e) {
      e.preventDefault();

      if (settings.dialpadEnabled) {
        phoneDialInsertNumber(this);
      }
    });
    $(document).on('click', '.phone-dial-contacts', function (e) {
      e.preventDefault();
      phoneDialInsertNumber(this);
    });

    function phoneDialInsertNumber(self) {
      let phone = $(self).data('phone');
      let title = $(self).data('title');
      let userId = $(self).data('user-id');
      $(".widget-phone__contact-info-modal").hide();
      $('.phone-widget__header-actions a[data-toggle-tab]').removeClass('is_active');
      $('.phone-widget__tab').removeClass('is_active');
      $('.phone-widget__header-actions a[data-toggle-tab="tab-phone"]').addClass('is_active');
      $('#tab-phone').addClass('is_active');
      insertPhoneNumber(phone, title, userId, phone);
    }
  }

  function sendHoldRequest(callSid) {
    let call = queues.active.one(callSid);

    if (call === null) {
      createNotify('Error', 'Not found Call on Active Queue', 'error');
      return false;
    }

    if (!call.setHoldRequestState()) {
      return false;
    }

    callRequester.hold(call);
  }

  function sendUnHoldRequest(callSid) {
    let call = queues.active.one(callSid);

    if (call === null) {
      createNotify('Error', 'Not found Call on Active Queue', 'error');
      return false;
    }

    if (!call.setUnHoldRequestState()) {
      return false;
    }

    callRequester.unHold(call);
  }

  function dialpadHide() {
    $('.dial-popup').slideUp(150);
  }

  function socket(data) {
    if (data.command === 'add_missed_call') {
      addMissedCall();
      return;
    }

    if (data.command === 'update_count_missed_calls') {
      setCountMissedCalls(data.count);
      return;
    }

    if (data.command === 'hold') {
      hold(data.call.sid);
      return;
    }

    if (data.command === 'unhold') {
      unhold(data.call.sid);
      return;
    }

    if (data.command === 'conferenceUpdate') {
      conferenceUpdate(data);
      return;
    }
  }

  function loadCurrentQueueCalls() {
    $(document).ready(function () {
      $.ajax({
        type: 'post',
        data: {},
        url: settings.currentQueueCallsUrl
      }).done(function (data) {
        if (data.isEmpty) {
          return;
        }

        let holdExist = false;
        data.hold.forEach(function (call) {
          waitQueue.add(call);
          holdExist = true;
        });
        let lastIncomingCall = null;
        let incomingExist = false;
        data.incoming.forEach(function (call) {
          lastIncomingCall = waitQueue.add(call);
          incomingExist = true;
        });
        let outgoingExist = false;
        data.outgoing.forEach(function (call) {
          queues.outgoing.add(call);
          outgoingExist = true;
        });
        let activeExist = false;
        data.active.forEach(function (call) {
          queues.active.add(call);
          activeExist = true;
        });
        data.conferences.forEach(function (conference) {
          storage.conference.add(conference);
        });
        openWidget();
        panes.queue.refresh();

        if (data.lastActive === 'incoming') {
          if (lastIncomingCall !== null) {
            panes.incoming.init(lastIncomingCall, queues.direct.count() + queues.general.count(), queues.active.count() + queues.hold.count());
            openCallTab();
            return;
          }
        }

        if (holdExist && !activeExist && !outgoingExist && !incomingExist) {
          openWidget();
          panes.queue.openAllCalls();
          return;
        }

        refreshPanes();
      }).fail(function () {
        createNotify('Load current calls', 'Server error', 'error');
      });
    });
  }

  return {
    init: init,
    volumeIndicatorsChange: volumeIndicatorsChange,
    updateConnection: updateConnection,
    refreshCallStatus: refreshCallStatus,
    panes: panes,
    requestIncomingCall: requestIncomingCall,
    requestOutgoingCall: requestOutgoingCall,
    changeStatus: changeStatus,
    requestClearMissedCalls: requestClearMissedCalls,
    socket: socket,
    queues: queues,
    removeIncomingRequest: removeIncomingRequest,
    sendHoldRequest: sendHoldRequest,
    sendUnHoldRequest: sendUnHoldRequest,
    storage: storage,
    callRequester: callRequester
  };
}();

(function () {
  function delay(callback, ms) {
    var timer = 0;
    return function () {
      var context = this,
          args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

  $("#call-pane__dial-number").on('keyup', delay(function () {
    $('.suggested-contacts').removeClass('is_active');
    let contactList = $("#contact-list-calls-ajax");
    let q = contactList.find("input[name=q]").val();

    if (q.length < 3) {
      return false;
    }

    contactList.submit();
  }, 300));
  let timeout = '';
  $('#contact-list-calls-ajax').on('beforeSubmit', function (e) {
    e.preventDefault();
    let yiiform = $(this);
    let q = yiiform.find("input[name=q]").val();

    if (q.length < 3) {
      //  new PNotify({
      //     title: "Search contacts",
      //     type: "warning",
      //     text: 'Minimum 2 symbols',
      //     hide: true
      // });
      return false;
    }

    $.ajax({
      type: yiiform.attr('method'),
      url: yiiform.attr('action'),
      data: yiiform.serializeArray(),
      dataType: 'json'
    }).done(function (data) {
      let content = '';

      if (timeout) {
        clearTimeout(timeout);
      }

      if (data.results.length < 1) {// content += loadNotFound();
        // timeout = setTimeout(function () {
        //     $('.suggested-contacts').removeClass('is_active');
        // }, 2000);
      } else {
        $.each(data.results, function (i, item) {
          content += loadContact(item);
        });
        $('.suggested-contacts').html(content).addClass('is_active');
        $('.call-pane__dial-clear-all').addClass('is-shown');
      } //$('.suggested-contacts').html(content).addClass('is_active');
      //$('.call-pane__dial-clear-all').addClass('is-shown')

    }).fail(function () {
      new PNotify({
        title: "Search contacts",
        type: "error",
        text: 'Server Error. Try again later',
        hide: true
      });
    });
    return false;
  });

  function loadContact(contact) {
    //  type = 3 = Internal contact
    console.log(contact);
    let contactIcon = '';

    if (contact['type'] === 3) {
      contactIcon = '<div class="contact-info-card__status">' + '<i class="far fa-user ' + contact['user_status_class'] + ' "></i>' + '</div>';
    }

    let dataUserId = contact.type === 3 ? contact.id : '';
    let content = '<li class="calls-history__item contact-info-card call-contact-card" data-user-id="' + dataUserId + '" data-phone="' + contact['phone'] + '" data-title="' + contact['title'] + '">' + '<div class="collapsible-toggler">' + contactIcon + '<div class="contact-info-card__details">' + '<div class="contact-info-card__line history-details">' + '<strong class="contact-info-card__name">' + contact['name'] + '</strong>' + '</div>' + '</div>' + '</div>' + '</li>';
    return content;
  } // function loadNotFound() {
  //     let content = '<li class="calls-history__item contact-info-card">' +
  //         '<div class="collapsible-toggler">' +
  //         '<div class="contact-info-card__details">' +
  //         '<div class="contact-info-card__line history-details">' +
  //         '<strong class="contact-info-card__name">No results found</strong>' +
  //         '</div>' +
  //         '</div>' +
  //         '</div>' +
  //         '</li>';
  //     return content;
  // }


  $(document).on('click', "li.call-contact-card", function () {
    let phone = $(this).data('phone');
    let title = $(this).data('title');
    let userId = $(this).data('user-id');
    insertPhoneNumber(phone, title, userId, phone);
    $('.suggested-contacts').removeClass('is_active');
  });
})();

let PhoneWidgetSms = function () {
  let listUrl = '';
  let sendUrl = '';
  let userPhones = {};
  let statuses = {
    1: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>',
    //new
    2: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>',
    //pending
    3: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>',
    //process
    4: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>',
    //cancel
    5: '<span class="pw-msg-item__status pw-msg-item__status--delivered"> <i class="fa fa-check-double"></i> </span>',
    //done
    6: '<span class="pw-msg-item__status pw-msg-item__status--error"> <i class="fa fa-exclamation-circle"></i> </span>',
    //error
    7: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>',
    //sent
    8: '<span class="pw-msg-item__status"> <i class="fa fa-check"></i> </span>' //queued

  };

  function init(listUrlInit, sendUrlInit, userPhonesInit) {
    listUrl = listUrlInit;
    sendUrl = sendUrlInit;
    userPhones = userPhonesInit;
  }

  function getUserPhones() {
    return userPhones;
  }

  function getSmsIconStatus(sms) {
    //console.log(sms.status);
    if (typeof statuses[sms.status] === 'undefined') {
      return '';
    }

    return statuses[sms.status];
  }

  function getSmsStatusId(sms) {
    return 'web-phone-widget-sms-' + sms.id;
  }

  function updateStatus(sms) {
    let container = $(document).find('.' + getSmsStatusId(sms));

    if (container) {
      container.html(getSmsIconStatus(sms));
    }
  }

  function encode(str) {
    return btoa(JSON.stringify(str));
  }

  function decode(str) {
    return JSON.parse(atob(str));
  }

  function showModalSelectNumber(contact) {
    let content = '';
    $.each(getUserPhones(), function (i, phone) {
      content += '<span class="phone-widget-userPhones btn btn-success" data-contact="' + encode(contact) + '" data-user-phone="' + phone + '">' + phone + '</span>';
    });
    let modal = $('#modal-df');
    modal.find('.modal-body').html(content);
    modal.find('.modal-title').html('Select your phone number');
    modal.modal('show');
  }

  function loadSmses(contact, user) {
    let container = $(".widget-phone__messages-modal");
    let data = {
      "contactId": contact.id,
      "contactPhone": contact.phone,
      "contactType": contact.type,
      "userPhone": user.phone
    };
    $(".phone-widget__tab").addClass('ovf-hidden');
    container.html("").show();
    container.append(getPreloader());
    $.ajax({
      type: 'POST',
      url: listUrl,
      data: data,
      dataType: 'json'
    }).done(function (data) {
      container.html("");

      if (!data.success) {
        container.append(parseErrors(data.errors));
        return false;
      }

      let content = getContactData(data.contact, data.user) + '<div class="messages-modal__messages-scroll">' + '<div class="messages-modal__body ' + getSmsesContainerName(data.contact, data.user) + '"></div>' + '</div>' + getSendForm(data.contact, data.user);
      container.append(content);
      addSmses(data.smses, getSmsesContainer(data.contact, data.user));
      simpleBarInit();
    }).fail(function (data) {
      container.html("");
      let text = 'Server Error. Try again later';

      if (data.status && data.status === 403) {
        text = 'Access denied';
      }

      new PNotify({
        title: "Get sms",
        type: "error",
        text: text,
        hide: true
      });
    });
  }

  function simpleBarInit() {
    let scroll = $(document).find(".messages-modal__messages-scroll");
    new SimpleBar(scroll[0]);
    scrollDown();
  }

  function scrollDown() {
    let scroll = $(document).find('.messages-modal__messages-scroll').find($('.simplebar-content-wrapper'))[0];

    if (scroll) {
      $(scroll).scrollTop($(scroll)[0].scrollHeight);
    }
  }

  function getSmsesContainerName(contact, user) {
    return 'phone-widget-sms-messages-container-' + contact.id + '-' + processPhone(contact.phone) + '-' + contact.type + '-' + processPhone(user.phone);
  }

  function processPhone(phone) {
    return phone.substr(1);
  }

  function addSmses(smses, container) {
    $.each(smses, function (index, sms) {
      addSms(sms, container);
    });
  }

  function addSms(sms, container) {
    if (!container) {
      return false;
    }

    let added = false;
    container.find(".messages-modal__msg-list").map(function () {
      if (sms.group === $(this).data("group")) {
        $(this).append(getSms(sms));
        added = true;
        return false;
      }
    });

    if (!added) {
      let content = '<span class="section-separator">' + sms.group + '</span>';
      content += '<ul class="messages-modal__msg-list" data-group="' + sms.group + '">';
      content += getSms(sms);
      content += '</ul>';
      container.append(content);
    }
  }

  function getSms(sms) {
    let typeClass = ''; // type = 1 (Out)

    if (sms.type === 1) {
      typeClass = ' pw-msg-item--user';
    }

    return '<li class="messages-modal__msg-item pw-msg-item' + typeClass + '">' + '<div class="pw-msg-item__avatar">' + '<div class="agent-text-avatar">' + '<span>' + sms.avatar + '</span>' + '</div>' + '</div>' + '<div class="pw-msg-item__msg-main">' + '<div class="pw-msg-item__data">' + '<span class="pw-msg-item__name">' + sms.name + '</span>' + '<span class="pw-msg-item__timestamp">' + sms.time + '</span>' + '<span class="' + getSmsStatusId(sms) + '">' + getSmsIconStatus(sms) + '</span>' + '</div>' + '<div class="pw-msg-item__msg-wrap">' + '<p class="pw-msg-item__msg">' + sms.text + '</p>' + '</div>' + '</div>' + '</li>';
  }

  function getContactData(contact, user) {
    return getBackToContacts() + '<div class="modal-messaging__contact-info">' + '<div class="modal-messaging__info-list">' + '<div class="modal-messaging__info-item" style="margin-bottom:0">SMS to <span class="modal-messaging__contact-name">' + contact.name + '</span></div>' + '<span class="modal-messaging__info-number">' + contact.phone + '</span>' + '<div class="modal-messaging__info-item" style="margin-bottom:0">From: <span class="modal-messaging__contact-name">' + user.phone + '</span></div>' + '</div>' + '</div>';
  }

  function getSendForm(contact, user) {
    return '<div class="messages-modal__footer">' + '<form id="phone-widget-send-sms-form" action="' + sendUrl + '" method="post">' + '<div class="messages-modal__input-group">' + '<input name="text" type="text" class="messages-modal__msg-input" placeholder="Your Message">' + '<input name="contactType" type="hidden" value="' + contact.type + '">' + '<input name="contactId" type="hidden" value="' + contact.id + '">' + '<input name="contactPhone" type="hidden" value="' + contact.phone + '">' + '<input name="userPhone" type="hidden" value="' + user.phone + '">' + '<button class="messages-modal__send-btn"><i class="fa fa-paper-plane"></i></button>' + '</div>' + '</form>' + '</div>';
  }

  function getPreloader() {
    return '<div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div>';
  }

  function parseErrors(errors) {
    let content = getBackToContacts() + '<div style="padding: 20px;color:red"><p><strong>Errors:</strong></p>';
    $.each(errors, function (i, error) {
      $.each(error, function (j, err) {
        content += '<div style="padding: 5px 0 5px 0;">' + err + '</div>';
      });
    });
    content += '</div>';
    return content;
  }

  function getBackToContacts() {
    return '<a href="#" class="widget-modal__close"><i class="fa fa-arrow-left"></i>Back to contacts</i></a>';
  }

  function sendStart() {
    $(document).find('.messages-modal__msg-input').prop("disabled", "disabled");
    $(document).find('.messages-modal__send-btn').prop("disabled", "disabled").html('<i class="fa fa-spinner fa-spin"></i>');
  }

  function sendFinish() {
    $(document).find('.messages-modal__msg-input').prop("disabled", false).val("");
    $(document).find('.messages-modal__send-btn').prop("disabled", false).html('<i class="fa fa-paper-plane"></i>');
  }

  function getSmsesContainer(contact, user) {
    let containerClass = '.' + getSmsesContainerName(contact, user);
    let container = $(document).find(containerClass);

    if (!container) {
      return false;
    }

    return container;
  }

  function socket(data) {
    if (data.command === 'update_status') {
      updateStatus(data.sms);
      return true;
    }

    if (data.command === 'add') {
      addSms(data.sms, getSmsesContainer(data.contact, data.user));
      scrollDown();
      return true;
    }
  }

  return {
    init: init,
    getUserPhones: getUserPhones,
    showModalSelectNumber: showModalSelectNumber,
    loadSmses: loadSmses,
    addSms: addSms,
    decode: decode,
    sendStart: sendStart,
    sendFinish: sendFinish,
    getSmsesContainer: getSmsesContainer,
    scrollDown: scrollDown,
    socket: socket
  };
}();

$(document).on('click', '.js-trigger-messages-modal', function () {
  let countPhones = PhoneWidgetSms.getUserPhones().length;

  if (countPhones < 1) {
    new PNotify({
      title: "Get sms messages",
      type: "error",
      text: 'Not found user phones.',
      hide: true
    });
    return false;
  }

  let contact = {
    "id": $(this).data('contact-id'),
    "phone": $(this).data('contact-phone'),
    "type": $(this).data('contact-type')
  };

  if (countPhones > 1) {
    PhoneWidgetSms.showModalSelectNumber(contact);
    return false;
  }

  let user = {
    "phone": PhoneWidgetSms.getUserPhones()[0]
  };
  PhoneWidgetSms.loadSmses(contact, user);
});
$(document).on('click', '.phone-widget-userPhones', function () {
  $('#modal-df').modal('hide');
  let contact = PhoneWidgetSms.decode($(this).data('contact'));
  let user = {
    "phone": $(this).data('user-phone')
  };
  PhoneWidgetSms.loadSmses(contact, user);
});
$(document).on('click', '.messages-modal__send-btn', function (e) {
  e.preventDefault();
  let form = $("#phone-widget-send-sms-form");
  let data = form.serializeArray();
  PhoneWidgetSms.sendStart();
  $.ajax({
    type: form.attr('method'),
    url: form.attr('action'),
    data: data,
    dataType: 'json'
  }).done(function (data) {
    PhoneWidgetSms.sendFinish();

    if (!data.success) {
      let content = '';
      $.each(data.errors, function (i, error) {
        $.each(error, function (j, err) {
          content += err + '<br>';
        });
      });
      new PNotify({
        title: "Send sms",
        type: "error",
        text: content,
        hide: true
      });
      return false;
    }

    PhoneWidgetSms.addSms(data.sms, PhoneWidgetSms.getSmsesContainer(data.contact, data.user));
    PhoneWidgetSms.scrollDown();
  }).fail(function () {
    PhoneWidgetSms.sendFinish();
    new PNotify({
      title: "Send sms",
      type: "error",
      text: 'Server Error. Try again later',
      hide: true
    });
  });
  return false;
});

let PhoneWidgetContacts = function () {
  let titleAccessGetMessages = '';
  let disabledClass = '';
  let urlFullList = '';
  let fullList = null;
  let simpleBar = null;
  let currentFullListContainer = true;
  let selectedContacts = [];

  function init(titleAccessGetMessagesInit, disabledClassInit, urlFullListInit) {
    titleAccessGetMessages = titleAccessGetMessagesInit;
    disabledClass = disabledClassInit;
    urlFullList = urlFullListInit;
    window.localStorage.setItem('contactSelectableState', 0);
  }

  function getUrlFullList() {
    return urlFullList;
  }

  function setFullList(list) {
    fullList = list;
  }

  function getFullList() {
    return fullList;
  }

  function addToFullList(list) {
    if (!list) {
      return;
    }

    $.each(list, function (i, item) {
      if (!i || !item) {
        return;
      }

      if (fullList[i]) {
        $.each(list[i], function (j, contact) {
          fullList[i][fullList[i].length] = contact;
        });
      } else {
        fullList[i] = item;
      }
    });
  }

  function delay(callback, ms) {
    var timer = 0;
    return function () {
      var context = this,
          args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

  function showPreloader() {
    $($current).append('<div class="wg-history-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
  }

  function hidePreloader() {
    $($current).find('.wg-history-load').remove();
  }

  function showCheckbox(contact, index) {
    // handleContactSelection($('[data-selected-contact]'),contact);
    if (window.localStorage.getItem('contactSelectableState') == '1' && contact.phones.length <= 1) {
      return '<div class="select-contact">' + '<div class="checkbox">' + '<input type="checkbox" name="checkedContact' + contact.id + '" id="checkedContact' + contact.id + '" value="' + contact.phones[0] + '" data-selected-contact="' + contact.id + '">' + '<label for="checkedContact' + contact.id + '"></label>' + '<label for="checkedContact' + contact.id + '" data-area-label></label>' + '</div>' + '</div>';
    }

    return '<a href="#" class="collapsible-arrow"><i class="fas fa-chevron-right"></i></a>';
  }

  function showCheckboxMultiple(contact, index) {
    handleContactSelection('[data-selected-contact]', contact);

    if (window.localStorage.getItem('contactSelectableState') == '1' && contact.phones.length >= 2) {
      return '<li class="actions-list__option actions-list__option--if-selectable">' + '<div class="checkbox">' + '<input type="checkbox" name="checkedContact' + index + 2 + '" id="checkedContact' + index + 2 + '" value="' + contact.phones[index] + '" data-selected-contact="' + contact.id + '">' + '<label for="checkedContact' + index + 2 + '"></label>' + '<label for="checkedContact' + index + 2 + '" data-area-label></label>' + '</div>' + '</li>';
    }

    return '';
  }

  function cleanSelectedContacts(elem, elemData) {
    for (var i = 0; i < selectedContacts.length; i++) {
      if (selectedContacts[i].id === parseInt($(elem).attr(elemData))) {
        selectedContacts.splice(selectedContacts.indexOf(selectedContacts[i]), 1);
      }
    }
  }

  function handleContactSelection(current, contact) {
    var elemData = 'data-selected-contact';
    $(document).on('change', current, function () {
      if ($(this).is(':checked')) {
        $('.submit-selected-contacts').slideDown(250);
        var selected = $('[' + elemData + '="' + $(this).attr(elemData) + '"]');
        $(selected).prop('checked', false);
        $(this).prop('checked', true);

        if (contact.hasOwnProperty('id') && contact['id'] === parseInt($(this).attr(elemData))) {
          cleanSelectedContacts($(this), elemData);
          selectedContacts.push(contact);
        }
      } else {
        cleanSelectedContacts($(this), elemData);
      }

      if (selectedContacts.length === 0) {
        $('.submit-selected-contacts').slideUp(150);
      }

      $('.selection-amount__selected').html(selectedContacts.length);
      console.log(selectedContacts);
    });
  }

  function getSelectableState(contact) {
    if (window.localStorage.getItem('contactSelectableState') == '1' && contact.phones.length < 2) {
      return '';
    }

    return 'collapse';
  }

  function getContactItem(contact) {
    let content = '<li class="calls-history__item contact-info-card is-collapsible">' + '<div class="collapsible-toggler collapsed" data-toggle="' + getSelectableState(contact) + '" data-target="#collapse' + contact['id'] + '" aria-expanded="false" aria-controls="collapse' + contact['id'] + '">' + '<div class="contact-info-card__status">' + '<div class="agent-text-avatar">' + '<span>' + contact['avatar'] + '</span>' + '</div>' + '</div>' + '<div class="contact-info-card__details">' + '<div class="contact-info-card__line history-details">' + '<strong class="contact-info-card__name">' + contact['name'] + '</strong>' + '</div>' + '<div class="contact-info-card__line history-details">' + '<span class="contact-info-card__call-type">' + contact['description'] + '</span>' + '</div>' + showCheckbox(contact) + '</div>' + '</div>' + '<div id="collapse' + contact['id'] + '" class="collapse collapsible-container" aria-labelledby="headingOne" data-parent="#contacts-tab">' + '<ul class="contact-options-list">' + '<li class="contact-options-list__option js-toggle-contact-info" data-contact="' + encode(contact) + '">' + '<i class="fa fa-user"></i>';

    if (contact.isInternal) {
      content += '<li class="contact-options-list__option dial-to-user contact-dial-to-user" data-contact="' + encode(contact) + '"> <i class="fa fa-phone"> </i></li>';
    }

    content += '</ul>' + '<ul class="contact-full-info">';

    if (contact['phones']) {
      contact['phones'].forEach(function (phone, index) {
        content += getPhoneItem(phone, index, contact);
      });
    }

    if (contact['emails']) {
      contact['emails'].forEach(function (email, index) {
        content += getEmailItem(email, index, contact);
      });
    }

    content += '</ul>' + '</div>' + '</li>';
    return content;
  }

  function encode(content) {
    return btoa(JSON.stringify(content));
  }

  function decode(content) {
    return JSON.parse(atob(content));
  }

  function viewContact(contact) {
    let content = '<div class="widget-phone__contact-info-modal widget-modal contact-modal-info">' + '<a href="#" class="widget-modal__close">' + '<i class="fa fa-arrow-left"></i>' + 'Back to contacts</i>' + '</a>' + '<div class="contact-modal-info__user">' + '<div class="agent-text-avatar">' + '<span>' + contact['avatar'] + '</span>' + '</div>' + '<h3 class="contact-modal-info__name">' + contact['name'] + '</h3>' + //                '<div class="contact-modal-info__actions">' +
    //                    '<ul class="contact-options-list">' +
    //                        '<li class="contact-options-list__option js-edit-mode">' +
    //                            '<i class="fa fa-user"></i>' +
    //                            '<span>EDIT</span>' +
    //                        '</li>' +
    //                        '<li class="contact-options-list__option js-trigger-messages-modal">' +
    //                            '<i class="fa fa-comment-alt"></i>' +
    //                            '<span>SMS</span>' +
    //                        '</li>' +
    //                        '<li class="contact-options-list__option contact-options-list__option--call js-call-tab-trigger">' +
    //                            '<i class="fa fa-phone"></i>' +
    //                            '<span>Call</span>' +
    //                        '</li>' +
    //                    '</ul>' +
    //                '</div>' +
    '</div>' + '<div class="contact-modal-info__body">' + // added markup 
    '<span class="section-separator">General info</span>' + // '<ul class="contact-modal-info__contacts contact-full-info">' +
    //
    //
    // '<li>'+
    // '<div class="form-group"><label for="">Type</label>'+
    // '<div class="form-control-wrap" data-type="person"><select readonly="" type="text"'+
    // ' class="form-control select-contact-type" autocomplete="off" disabled="">'+
    // '<option value="company">Company</option>'+
    // '<option value="person" selected="selected">Person</option>'+
    // '</select></div>'+
    // '</div>'+
    // '</li>' +
    //
    //
    // '<li>'+
    // '<div class="form-group"><label for="">Date of Birth</label><input readonly="" type="text" class="form-control"'+
    // ' value="24/07/1970" autocomplete="off"></div>'+
    // '</li>' +
    // '</ul>' +
    // '<span class="section-separator">Project - Wowfare</span>' +
    //
    // '<ul class="contact-modal-info__contacts contact-full-info">' +
    //
    // //
    // // '<li>'+
    // // '<div class="form-group"><label for="">Role</label><input readonly="" type="text" class="form-control"'+
    // // ' value="Supervisor" autocomplete="off"></div>'+
    // // '</li>'+
    //
    //
    // '<li>'+
    // '<div class="form-group"><label for="">Phone </label><input readonly="" type="text" class="form-control"'+
    // 'value="+37369271516" autocomplete="off"></div>'+
    // '<ul class="actions-list">'+
    // '<li class="actions-list__option actions-list__option--phone js-call-tab-trigger"><i class="fa fa-phone phone-dial-contacts"'+
    // 'data-phone="+37369271516"></i></li>'+
    // '<li title="" class="actions-list__option js-trigger-messages-modal" data-contact-id="44"'+
    // 'data-contact-phone="+37369271516" data-contact-type="2"><i class="fa fa-comment-alt"></i></li>'+
    // '</ul>'+
    // '</li>' +
    //
    // '<li>'+
    // '<div class="form-group"><label for="">Email </label><input readonly="" type="email" class="form-control"'+
    // 'value="tandroid@gmail.com" autocomplete="off"></div>'+
    // '<ul class="actions-list">'+
    // '<li class="actions-list__option js-trigger-email-modal"'+
    // 'data-contact="eyJncm91cCI6IlQiLCJpZCI6NDQsIm5hbWUiOiJUZXN0IDIiLCJkZXNjcmlwdGlvbiI6IkFuZHJldyB0ZXN0IiwiYXZhdGFyIjoiVCIsImlzX2NvbXBhbnkiOmZhbHNlLCJ0eXBlIjoyLCJwaG9uZXMiOlsiKzM3MzY5MjcxNTE2Il0sImVtYWlscyI6WyJ0YW5kcm9pZEBnbWFpbC5jb20iXX0="'+
    // 'data-contact-email="tandroid@gmail.com"><i class="fa fa-envelope"></i></li>'+
    // '</ul>'+
    // '</li>' +
    // '</ul>'+
    // '<span class="section-separator">Project - Arangrant</span>' +
    // end added markup
    '<ul class="contact-modal-info__contacts contact-full-info">' + '<li>' + '<div class="form-group">' + '<label for="">Type</label>';
    let type = 'person';

    if (contact['is_company']) {
      type = 'company';
    }

    content += '<div class="form-control-wrap" data-type="' + type + '">'; // if (type === 'person') {
    //     content += '<i class="fa fa-user contact-type-person"></i>';
    // } else {
    //     content += '<i class="fa fa-building contact-type-company"></i>';
    // }

    content += '<select readonly type="text" class="form-control select-contact-type" autocomplete="off" readonly disabled>';

    if (contact['is_company']) {
      content += '<option value="company" selected="selected">Company</option> <option value="person">Person</option>';
    } else {
      content += '<option value="company">Company</option> <option value="person" selected="selected">Person</option>';
    }

    content += '</select>' + '</div>' + '</div>' + '</li>';

    if (contact['phones']) {
      contact['phones'].forEach(function (phone, index) {
        content += getPhoneItem(phone, index, contact);
      });
    }

    if (contact['emails']) {
      contact['emails'].forEach(function (email, index) {
        content += getEmailItem(email, index, contact);
      });
    }

    content += '</ul>' + // '<a href="#" class="contact-modal-info__remove-contact">DELETE CONTACT</a>' +
    '</div>' + '</div>';
    return content;
  }

  function getEmailItem(email, index, contact) {
    return '<li class="contact-full-info__email">' + '<div class="form-group">' + '<label for="">Email ' + (index + 1) + '</label>' + '<input readonly type="email" class="form-control" value="' + email + '" autocomplete="off">' + '</div>' + '<ul class="actions-list">' + '<li class="actions-list__option js-trigger-email-modal" data-contact="' + encode(contact) + '" data-contact-email="' + email + '">' + '<i class="fa fa-envelope"></i>' + '</li>' + '</ul>' + '</li>';
  }

  function getPhoneItem(phone, index, contact) {
    let content = '<li class="contact-full-info__phone">' + '<div class="form-group">' + '<label for="">Phone ' + (index + 1) + '</label>' + '<input readonly type="text" class="form-control" value="' + phone + '" autocomplete="off">' + '</div>' + '<ul class="actions-list">' + '<li class="actions-list__option actions-list__option--phone js-call-tab-trigger">';
    let dataUserId = contact.isInternal ? contact.id : '';
    content += '<i class="fa fa-phone phone-dial-contacts" data-user-id="' + dataUserId + '" data-phone="' + phone + '" data-title="' + contact['name'] + '"></i>';
    content += '</li>' + '<li title="' + titleAccessGetMessages + '" class="actions-list__option js-trigger-messages-modal' + disabledClass + '" ' + 'data-contact-id="' + contact['id'] + '" data-contact-phone="' + phone + '" data-contact-type="' + contact['type'] + '">' + '<i class="fa fa-comment-alt"></i>' + '</li>' + showCheckboxMultiple(contact, index) + '</ul>' + '</li>';
    return content;
  }

  function noResultsFound() {
    $("#list-of-contacts").html(noResultsTemplate());
  }

  function noResultsTemplate() {
    return '<div style="width:100%;text-align:center;margin-top:20px">No results found</div>';
  }

  function requestFullList() {
    showPreloader();
    $.ajax({
      type: 'post',
      url: getUrlFullList(),
      data: {},
      dataType: 'json'
    }).done(function (data) {
      if (data.results.length < 1) {
        hidePreloader();
        noResultsFound();
        return;
      }

      setPageNumber(data.page);
      setFullList(data.results);
      let content = addContactsToListOfContacts(getFullList());
      hidePreloader();
      $("#list-of-contacts").html("").append(content);
      simpleBar.recalculate();
    }).fail(function (e) {
      console.log(e);
      hidePreloader();
      new PNotify({
        title: "Search contacts",
        type: "error",
        text: 'Server Error. Try again later',
        hide: true
      });
    });
  }

  function requestSearchList(form) {
    showPreloader();
    $.ajax({
      type: form.attr('method'),
      url: form.attr('action'),
      data: form.serializeArray(),
      dataType: 'json'
    }).done(function (data) {
      if (data.results.length < 1) {
        hidePreloader();
        noResultsFound();
        return;
      }

      let content = addContactsToListOfContacts(data.results);
      hidePreloader();
      $("#list-of-contacts").html("").append(content);
    }).fail(function () {
      hidePreloader();
      new PNotify({
        title: "Search contacts",
        type: "error",
        text: 'Server Error. Try again later',
        hide: true
      });
    });
  }

  function addContactsToListOfContacts(list) {
    let data = '';
    $.each(list, function (i, item) {
      if (!i || !item) {
        return;
      }

      let content = '<span class="section-separator">' + i + '</span>';
      content += '<ul class="phone-widget__list-item calls-history" id="contacts-tab">';
      $.each(list[i], function (j, contact) {
        content += getContactItem(contact);
      });
      content += '</ul>';
      data += content;
    });
    return data;
  }

  function getPageNumber() {
    return $('#tab-contacts').attr('data-page');
  }

  function setPageNumber(page) {
    $('#tab-contacts').attr('data-page', page);
  }

  function initLazyLoadFullList(simpleBarInit) {
    var ajax = false;
    simpleBar = simpleBarInit;
    simpleBar.getScrollElement().addEventListener('scroll', function (e) {
      if (!currentFullListContainer) {
        return;
      }

      if (e.target.scrollTop + e.target.clientHeight === e.target.scrollHeight && !ajax) {
        // ajax call get data from server and append to the div
        var page = getPageNumber();
        $.ajax({
          url: getUrlFullList(),
          type: 'post',
          data: {
            page: page,
            uid: userId
          },
          dataType: 'json',
          beforeSend: function () {
            showPreloader();
            ajax = true;
          },
          success: function (data) {
            setPageNumber(data.page);
            addToFullList(data.results);
            let content = addContactsToListOfContacts(getFullList());
            hidePreloader();
            $("#list-of-contacts").html("").append(content);
            simpleBar.recalculate();

            if (!data.rows) {
              ajax = false;
            }
          },
          complete: function () {
            hidePreloader();
          },
          error: function (xhr, error) {}
        });
      }
    });
  }

  function fullListIsEmpty() {
    return !fullList;
  }

  function loadFullList() {
    showPreloader();

    if (fullList) {
      let content = addContactsToListOfContacts(fullList);
      hidePreloader();
      $("#list-of-contacts").html("").append(content);
    } else {
      hidePreloader();
      $("#list-of-contacts").html("");
    }
  }

  function setCurrentFullListContainer() {
    return currentFullListContainer = true;
  }

  function setCurrentSearchListContainer() {
    return currentFullListContainer = false;
  }

  return {
    init: init,
    viewContact: viewContact,
    decodeContact: decode,
    delay: delay,
    requestFullList: requestFullList,
    initLazyLoadFullList: initLazyLoadFullList,
    fullListIsEmpty: fullListIsEmpty,
    loadFullList: loadFullList,
    requestSearchList: requestSearchList,
    setCurrentFullListContainer: setCurrentFullListContainer,
    setCurrentSearchListContainer: setCurrentSearchListContainer
  };
}();

$('#contact-list-ajax').on('beforeSubmit', function (e) {
  e.preventDefault();
  let yiiform = $(this);
  let q = yiiform.find("input[name=q]").val();

  if (q.length < 2) {
    // new PNotify({title: "Search contacts", type: "warning", text: 'Minimum 2 symbols', hide: true});
    PhoneWidgetContacts.setCurrentFullListContainer();
    return false;
  }

  PhoneWidgetContacts.setCurrentSearchListContainer();
  PhoneWidgetContacts.requestSearchList(yiiform);
  return false;
});
$("#contact-list-ajax-q").on('keyup', PhoneWidgetContacts.delay(function () {
  let contactList = $("#contact-list-ajax");
  let q = contactList.find("input[name=q]").val();

  if (q.length < 2) {
    PhoneWidgetContacts.setCurrentFullListContainer();
    PhoneWidgetContacts.loadFullList();
    return false;
  }

  PhoneWidgetContacts.setCurrentSearchListContainer();
  contactList.submit();
}, 300));
$(document).on('click', ".js-toggle-contact-info", function () {
  let contact = PhoneWidgetContacts.decodeContact($(this).data('contact'));
  let data = PhoneWidgetContacts.viewContact(contact);
  $(".widget-phone__contact-info-modal").html(data);
  $(".widget-phone__contact-info-modal").show();
});
$(document).on('click', ".contact-dial-to-user", function () {
  let contact = PhoneWidgetContacts.decodeContact($(this).data('contact'));
  insertPhoneNumber(contact.name, '', contact.id, '');
  $('.phone-widget__header-actions a[data-toggle-tab]').removeClass('is_active');
  $('.phone-widget__tab').removeClass('is_active');
  $('.phone-widget__header-actions a[data-toggle-tab="tab-phone"]').addClass('is_active');
  $('#tab-phone').addClass('is_active');
}); // $('.js-add-to-conference').on('click', function() {
//     console.log(window.localStorage)
//     $(this).trigger('selection-contacts');
// });

let PhoneWidgetEmail = function () {
  let sendUrl = '';
  let userEmails = {};

  function init(sendUrlInit, userEmailsInit) {
    sendUrl = sendUrlInit;
    userEmails = userEmailsInit;
  }

  function getUserEmails() {
    return userEmails;
  }

  function encode(str) {
    return btoa(JSON.stringify(str));
  }

  function decode(str) {
    return JSON.parse(atob(str));
  }

  function showModalSelectNumber(contact, contactEmail) {
    let content = '';
    $.each(getUserEmails(), function (i, email) {
      content += '<span class="phone-widget-userEmails btn btn-success" data-contact-email="' + contactEmail + '" data-contact="' + contact + '" data-user-email="' + email + '">' + email + '</span>';
    });
    let modal = $('#modal-df');
    modal.find('.modal-body').html(content);
    modal.find('.modal-title').html('Select your email address');
    modal.modal('show');
  }

  function showPreloader() {
    $(document).find(".email-modal").append('<div class="wg-history-load"><div style="width:100%;text-align:center;margin-top:20px"><i class="fa fa-spinner fa-spin fa-5x"></i></div></div>');
  }

  function hidePreloader() {
    $(document).find(".email-modal").find('.wg-history-load').remove();
  }

  function parseErrors(errors) {
    let content = '';
    $.each(errors, function (i, error) {
      $.each(error, function (j, err) {
        content += err + '<br>';
      });
    });
    return content;
  }

  function getBackToContacts() {
    return '<a href="#" class="widget-modal__close"><i class="fa fa-arrow-left"></i>Back to contacts</i></a>';
  }

  function show(contact, contactEmail, user) {
    let container = $(document).find(".email-modal");
    container.html("");
    let content = getBackToContacts() + '    <div class="modal-messaging__contact-info">' + '        <div class="modal-messaging__info-list">' + '            <div class="modal-messaging__info-item">Email to <span class="modal-messaging__contact-name">' + contact.name + '</span></div>' + '            <span class="modal-messaging__info-number">' + contactEmail + '</span>' + '            <div class="modal-messaging__info-item" style="margin-bottom:0">Email From: <span class="modal-messaging__contact-name">' + user.email + '</span></div>' + '        </div>' + '    </div>' + '' + '    <div class="email-modal__messages-scroll">' + '        <div class="email-modal__body">' + '<form id="phone-widget-send-email-form" action="' + sendUrl + '" method="post">' + '               <input name="contactType" type="hidden" value="' + contact.type + '">' + '               <input name="contactId" type="hidden" value="' + contact.id + '">' + '               <input name="contactEmail" type="hidden" value="' + contactEmail + '">' + '               <input name="userEmail" type="hidden" value="' + user.email + '">' + '            <div class="email-modal__input-group">' + '                <div class="email-modal__subject-block">' + '                    <div class="email-modal__modal-input-list"> <input type="text" name="subject" class="email-modal__contact-input" placeholder="Subject"> </div>' + // '                    <ul class="subject-option">' +
    // '                        <li class="subject-option__add" data-add-type="cc">Add CC</li>' +
    // '                        <li class="subject-option__add" data-add-type="bcc">Add BCC</li>' +
    // '                    </ul>' +
    '                </div>' + '                <textarea class="email-modal__msg-input" placeholder="Your Message" name="text" cols="30" rows="10"></textarea>' + '            </div>' + '            <button class="email-modal__send-btn"><span>SEND</span><i class="fa fa-paper-plane"></i></button>' + '            </form>' + '        </div>' + '    </div>';
    container.append(content);
    container.show();
    $(".phone-widget__tab").addClass('ovf-hidden');
  }

  function send(form) {
    let data = form.serializeArray();
    showPreloader();
    $.ajax({
      type: form.attr('method'),
      url: form.attr('action'),
      data: data,
      dataType: 'json'
    }).done(function (data) {
      hidePreloader();

      if (!data.success) {
        let content = parseErrors(data.errors);
        new PNotify({
          title: "Send email",
          type: "error",
          text: content,
          hide: true
        });
        return false;
      }

      form.find(".email-modal__contact-input").val("");
      form.find(".email-modal__msg-input").val("");
      let message = 'Success';

      if (data.message) {
        message = data.message;
      }

      new PNotify({
        title: "Send email",
        type: "success",
        text: message,
        hide: true
      });
    }).fail(function () {
      hidePreloader();
      new PNotify({
        title: "Send email",
        type: "error",
        text: 'Server Error. Try again later',
        hide: true
      });
    });
  }

  return {
    init: init,
    getUserEmails: getUserEmails,
    showModalSelectNumber: showModalSelectNumber,
    decode: decode,
    show: show,
    send: send
  };
}();

$(document).on("click", ".js-trigger-email-modal", function () {
  let countEmails = PhoneWidgetEmail.getUserEmails().length;

  if (countEmails < 1) {
    new PNotify({
      title: "Send email",
      type: "error",
      text: 'Not found user emails.',
      hide: true
    });
    return false;
  }

  if (countEmails > 1) {
    PhoneWidgetEmail.showModalSelectNumber($(this).data('contact'), $(this).data('contact-email'));
    return false;
  }

  let contact = PhoneWidgetEmail.decode($(this).data('contact'));
  let contactEmail = $(this).data('contact-email');
  let user = {
    "email": PhoneWidgetEmail.getUserEmails()[0]
  };
  PhoneWidgetEmail.show(contact, contactEmail, user);
});
$(document).on('click', '.phone-widget-userEmails', function () {
  $('#modal-df').modal('hide');
  let contact = PhoneWidgetEmail.decode($(this).data('contact'));
  let contactEmail = $(this).data('contact-email');
  let user = {
    "email": $(this).data('user-email')
  };
  PhoneWidgetEmail.show(contact, contactEmail, user);
});
$(document).on('click', '.email-modal__send-btn', function (e) {
  e.preventDefault();
  let form = $(document).find("#phone-widget-send-email-form");
  PhoneWidgetEmail.send(form);
});