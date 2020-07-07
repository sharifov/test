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
        }
    }

    render() {
        let call = this.state.call;
        return (
            <React.Fragment>
                <CallInfo project={call.data.project} source={call.data.source}/>
                <ActiveContactInfo {...call.data} />
                <CallBtns call={call}/>
                <SoundIndication/>
                <ActivePaneControls call={call} controls={this.props.controls}/>
                <AddNote call={call}/>
            </React.Fragment>
        );
    }
}

function ActiveContactInfo(props) {
    return (
        <div className="contact-info-card">
            <div className="contact-info-card__details">
                <div className="contact-info-card__line history-details">
                    <span className="contact-info-card__label">{props.type}</span>
                    <div className="contact-info-card__name">
                        <button className="call-pane__info">
                            <i className="user-icon fa fa-user"> </i>
                            <i className="info-icon fa fa-info"> </i>
                        </button>
                        <strong>{props.contact.name}</strong>
                    </div>
                </div>
                <div className="contact-info-card__line history-details">
                    <span className="contact-info-card__call-type">{props.contact.phone}</span>
                </div>
            </div>
        </div>
    );
}

function CallBtns(props) {
    let call = props.call;
    return (
        <div className="call-pane__call-btns is-on-call">
            <button className="call-pane__mute" id="call-pane__mute"
                    disabled={call.data.isListen || call.data.isHold || call.isSentMuteUnMuteRequestState()}
                    data-call-sid={call.data.callSid} data-is-muted={call.data.isMute}
                    data-active={!(call.data.isListen || call.data.isHold)}>
                {call.isSentMuteUnMuteRequestState()
                    ? <i className="fa fa-spinner fa-spin"> </i>
                    : call.data.isMute
                        ? <i className="fas fa-microphone-alt-slash"> </i>
                        : <i className="fas fa-microphone"> </i>
                }
            </button>
            <button className={call.data.isListen || call.data.isCoach ? 'call-pane__start-call calling-state-block join' : 'call-pane__start-call calling-state-block'}>
                <div className="call-in-action">
                    {call.data.isListen || call.data.isCoach ? <i className="fa fa-headphones-alt"> </i> : ''}
                    <span className="call-in-action__text">
                        {call.data.isCoach
                            ? 'Coaching'
                            : call.data.isListen
                                ? 'Listening'
                                : 'on call'
                        }
                    </span>
                    <span className="call-in-action__time"><PhoneWidgetTimer duration={call.getDuration()} timeStart={Date.now()}/></span>
                </div>
            </button>
            <button className="call-pane__end-call" id="cancel-active-call" data-call-sid={call.data.callSid}
                    disabled={call.isSentHangupRequestState()}>
                {call.isSentHangupRequestState()
                    ? <i className="fa fa-spinner fa-spin"> </i>
                    : <i className="fa fa-phone-slash"> </i>
                }
            </button>
        </div>
    );
}

function SoundIndication() {
    const sound_ovf_100 = {
        'right': '-100%'
    };
    const sound_ovf_30 = {
        'right': '-30%'
    };
    return (
        <div className="sound-indication">
            <div className="sound-control-wrap" id="wg-call-volume">
                <i className="fa fa-volume-down"> </i>
                <div className="sound-controls">
                    <div className="progres-wrap">
                        <div className="sound-progress"> </div>
                        <div className="sound-ovf" style={sound_ovf_100}> </div>
                    </div>
                </div>
            </div>
            <div className="sound-control-wrap" id="wg-call-microphone">
                <i className="fa fa-microphone"> </i>
                <div className="sound-controls">
                    <div className="progres-wrap">
                        <div className="sound-progress"> </div>
                        <div className="sound-ovf" style={sound_ovf_30}> </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

function AddNote(props) {
    let call = props.call;
    const rule = 'evenodd';
    const d = 'M16.7072 1.70718L6.50008 11.9143L0.292969 5.70718L1.70718 4.29297L6.50008 9.08586L15.293 0.292969L16.7072 1.70718Z';
    const fill = 'white';
    return (
        <div className="d-flex justify-content-between align-items-center align-content-center">
            <div className="form-group">
                <input type="text" className="call-pane__note-msg form-control" id="active_call_add_note"
                       placeholder="Add Note" autoComplete="off"/>
                <div className="error-message"> </div>
            </div>
            <button className="call-pane__add-note" id="active_call_add_note_submit"
                    data-call-sid={call.data.callSid} disabled={call.isSentAddNoteRequestState()}>
                {call.isSentAddNoteRequestState()
                    ? <i className="fa fa-spinner fa-spin" style={{color: '#fff'}}> </i>
                    : <svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fillRule={rule} clipRule={rule} d={d} fill={fill}/>
                    </svg>
                }
            </button>
        </div>
    );
}
