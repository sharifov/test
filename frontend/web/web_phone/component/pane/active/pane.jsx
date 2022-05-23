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
            //only for minify version
            window.phoneWidget.emptyFunc('ActivePane.callUpdateHandler');

            self.setState({
                call: event.call
            });
        }
    }

    render() {
        let call = this.state.call;
        let controls = this.props.controls;
        if (call.data.connectionError) {
            controls.reconnect.active = true;
        }
        return (
            <React.Fragment>
                <CallInfo project={call.data.project} source={call.data.source}/>
                <ActiveContactInfo call={call} />
                <div className="actions-container">
                    <CallBtns call={call}/>
                    <SoundIndication/>
                    <RecordIndicator call={call} canRecordingDisabled={this.props.controls.canRecordingDisabled}/>
                    <AddInBlacklist call={call} canAddBlackList={this.props.controls.canAddBlackList}/>
                </div>
                <ActivePaneControls call={call} controls={controls}/>
            </React.Fragment>
        );
    }
}

function ActiveContactInfo(props) {
    let call = props.call;
    return (
        <div className="contact-info-card">
            <div className="contact-info-card__details">
                {/*<AntiSpamElement call={call}/>*/}
                <div className="contact-info-card__line history-details">
                    {call.data.typeId !== 3
                        ? <span className="contact-info-card__label">{call.data.type}</span>
                        : ''
                    }
                    <div className="contact-info-card__name">
                        <button className="call-pane__info" data-call-id={call.data.id}>
                            <i className="user-icon fa fa-user"> </i>
                            <i className="info-icon fa fa-info"> </i>
                        </button>
                        <strong>{call.data.contact.name}</strong>
                    </div>
                </div>
                <div className="contact-info-card__line history-details">
                    {call.data.typeId !== 3
                        ? <span className="contact-info-card__call-type">{call.data.contact.phone}</span>
                        : ''
                    }
                </div>
            </div>
        </div>
    );
}

function CallBtns(props) {
    let call = props.call;
    let paneBtnClass = 'call-pane__call-btns';
    if (call.data.isHold) {
        paneBtnClass = paneBtnClass + ' is-on-hold';
    } else {
        paneBtnClass = paneBtnClass + ' is-on-call';
    }
    return (
        <div className={paneBtnClass}>
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
            {call.data.isHold ? <CallingStateBlockHold call={call} /> : <CallingStateBlock call={call} />}
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

function CallingStateBlock(props) {
    let call = props.call;
    return (
        <button className={call.data.isListen || call.data.isCoach ? 'call-pane__start-call calling-state-block join' : 'call-pane__start-call calling-state-block'}>
            <div className="call-in-action">
                {call.data.isListen || call.data.isCoach ? <i className="fa fa-headphones-alt"> </i> : ''}
                <span className="call-in-action__text">
                        {call.data.isCoach
                            ? 'Coaching'
                            : call.data.isListen
                                ? 'Listening'
                                : call.data.isBarge
                                    ? 'Barge'
                                    : 'on call'
                        }
                    </span>
                <span className="call-in-action__time"><PhoneWidgetTimer duration={call.getDuration()} timeStart={Date.now()} styleClass="more"/></span>
            </div>
        </button>
    );
}

function CallingStateBlockHold(props) {
    let call = props.call;
    return (
        <button className="call-pane__start-call calling-state-block">
            <div className="call-in-action">
                <span className="call-in-action__text">on hold</span>
                <span className="call-in-action__time"><PhoneWidgetTimer duration={call.getHoldDuration()} timeStart={Date.now()} styleClass="more"/></span>
            </div>
        </button>
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

function RecordIndicator(props) {
    let call = props.call;
    let style = {'color':'#dc3545'};
    let text = 'ON';
    let title = 'OFF'
    if (call.data.recordingDisabled) {
        style = {'color':'#ccc'};
        text = 'OFF';
        title = 'ON'
    }
    let faIcon = "fa fa-record-vinyl";
    if (call.isSentRecordingRequestState()) {
        faIcon = "fa fa-spinner fa-spin";
    }
    let canManageRecord =
        props.canRecordingDisabled
        && !call.data.isJoin
        && !call.data.recordingDisabled
        && !(call.data.isInternal && call.data.type === 'Incoming');
    if (canManageRecord) {
        style.cursor = "pointer";
    }
    return (
        <div className="sound-indication">
            <div className="sound-control-wrap">
                {canManageRecord
                    ? <i className={faIcon} style={style} id="wg-call-record" data-call-sid={call.data.callSid} title={title}> </i>
                    : <i className={faIcon} style={style}> </i>
                }
                <div style={{"marginLeft":"10px", "color": "#fff"}}> Record {text}</div>
            </div>
        </div>
    );
}

function AddInBlacklist(props) {
    let call = props.call;

    if (!props.canAddBlackList) {
        return ('');
    }

    if (call.data.contact.isPhoneInBlackList) {
        return ('');
    }

    return (
        <div className="sound-indication">
            <div className="sound-control-wrap">
                <small className="contact-info-card__call-info btn-add-in-blacklist" data-phone={call.data.contact.phone}>
                    <i className="fas fa-ban text-danger"/>
                    <span style={{"marginLeft":"10px", "color": "#fff"}}> Add in block list</span>
                </small>
            </div>
        </div>
    );
}
