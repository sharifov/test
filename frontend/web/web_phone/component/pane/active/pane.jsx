function ActivePane(props) {
    return (
        <React.Fragment>
            <CallInfo {...props} />
            <ContactInfo {...props} />
            <CallBtns {...props} />
            <SoundIndication/>
            <ActivePaneControls {...props} />
            <AddNote {...props}/>
        </React.Fragment>
    );
}

function ContactInfo(props) {
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
                        <strong>{props.name}</strong>
                    </div>
                </div>
                <div className="contact-info-card__line history-details">
                    <span className="contact-info-card__call-type">{props.phone}</span>
                </div>
            </div>
        </div>
    );
}

function CallBtns(props) {
    return (
        <div className="call-pane__call-btns is-on-call">
            <button className="call-pane__mute" id="call-pane__mute" disabled={props.isListen || props.isHold}
                    data-muted={props.isMute} data-active={!(props.isListen || props.isHold)}>
                {props.isMute
                    ? <i className="fas fa-microphone-alt-slash"> </i>
                    : <i className="fas fa-microphone"> </i>
                }
            </button>
            <button className="call-pane__start-call calling-state-block">
                <div className="call-in-action">
                    <span className="call-in-action__text">on call</span>
                    <ActiveCallTimer duration={props.duration} timeStart={Date.now()}/>
                </div>
            </button>
            <button className="call-pane__end-call" id="cancel-active-call" data-call-id={props.callId}>
                <i className="fa fa-phone-slash"> </i>
            </button>
        </div>
    );
}

class ActiveCallTimer extends React.Component {

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
        let minutes = Math.floor(duration / 60) - (hours * 60);
        let seconds = duration % 60;
        if (hours > 0) {
            out = hours.toString().padStart(2, '0') + ':';
        }
        out += minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
        return out;
    }

    render() {
        return (
            <React.Fragment>
                <span className="call-in-action__time">{this.formatDuration(this.state.duration)}</span>
            </React.Fragment>
        );
    }
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
                        <div className="sound-progress"></div>
                        <div className="sound-ovf" style={sound_ovf_100}></div>
                    </div>
                </div>
            </div>
            <div className="sound-control-wrap" id="wg-call-microphone">
                <i className="fa fa-microphone"> </i>
                <div className="sound-controls">
                    <div className="progres-wrap">
                        <div className="sound-progress"></div>
                        <div className="sound-ovf" style={sound_ovf_30}></div>
                    </div>
                </div>
            </div>
        </div>
    );
}

function AddNote(props) {
    const rule = 'evenodd';
    const d = 'M16.7072 1.70718L6.50008 11.9143L0.292969 5.70718L1.70718 4.29297L6.50008 9.08586L15.293 0.292969L16.7072 1.70718Z';
    const fill = 'white';
    return (
        <div className="d-flex justify-content-between align-items-center align-content-center">
            <div className="form-group">
                <input type="text" className="call-pane__note-msg form-control" id="active_call_add_note"
                       placeholder="Add Note" autoComplete="off"/>
                <div className="error-message"></div>
            </div>
            <button className="call-pane__add-note" id="active_call_add_note_submit" data-call-id={props.callId}>
                <svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fillRule={rule} clipRule={rule} d={d} fill={fill}/>
                </svg>
            </button>
        </div>
    );
}
