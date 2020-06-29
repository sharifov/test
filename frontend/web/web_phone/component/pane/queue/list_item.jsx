function ListItem(props) {
    let call = props.call;
    let duration = call.duration || 0;
    if (call.timeQueuePushed) {
        duration = Math.floor((Date.now() - parseInt(call.timeQueuePushed)) / 1000) + parseInt(duration);
    }
    return (
        <li className="call-in-progress__list-item">
            <div className="call-in-progress__call-item call-list-item" data-call-status={call.state}>
                <div className="call-list-item__info">
                    <ul className="call-list-item__info-list call-info-list">
                        <li className="call-info-list__item">
                            <b className="call-info-list__contact-icon"><i className="fa fa-user"> </i></b><span className="call-info-list__name">{call.contact.name}</span>
                        </li>
                        {call.contact.company
                            ? <li className="call-info-list__item"><span className="call-info-list__company">{call.contact.company}</span></li>
                            : ''
                        }
                        <li className="call-info-list__item"><span className="call-info-list__number">{call.contact.phone}</span></li>
                    </ul>
                    <div className="call-list-item__info-action call-info-action">
                        <CallDurationTimer duration={duration}/>
                        <a href="#" className="call-info-action__more"><i className="fa fa-ellipsis-h"> </i></a>
                    </div>
                    <ul className="call-list-item__menu call-item-menu">
                        <li className="call-item-menu__list-item">
                            <a href="#" className="call-item-menu__close"><i className="fa fa-chevron-right"> </i></a>
                        </li>
                        <li className="call-item-menu__list-item wg-transfer-call" data-call-id={call.callId} data-call-sid={call.callSid}>
                            <a href="#" className="call-item-menu__transfer"><i className="fa fa-random"> </i></a>
                        </li>
                        <li className="call-item-menu__list-item" data-call-id={call.callId} data-call-sid={call.callSid}>
                            <a href="#" className="call-item-menu__transfer"><i className="fa fa-pause"> </i></a>
                        </li>
                        {call.state !== 'inProgress'
                            ? <React.Fragment><li className="call-item-menu__list-item"><a href="#" className="call-item-menu__transfer"><i className="fas fa-phone-slash"> </i></a></li></React.Fragment>
                            : ''
                        }
                    </ul>
                </div>
                <div className="call-list-item__main-action">
                    <a href="#" className="call-list-item__main-action-trigger" data-type-action={call.state === 'inProgress' ? 'hangup' : 'accept'} data-call-id={call.callId} data-call-sid={call.callSid} data-from-internal={call.fromInternal}>
                        {call.isBlocked
                            ? <i className="fa fa-spinner fa-spin" />
                            : <React.Fragment><i className="phone-icon phone-icon--start fa fa-phone" /> <i className="phone-icon phone-icon--end fa fa-phone-slash" /> </React.Fragment>
                        }
                    </a>
                </div>
            </div>
        </li>
    );
}

class CallDurationTimer extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            duration: 0
        };
    }

    componentDidMount() {
        this.startTimer();
    }

    componentWillUnmount() {
        clearInterval(this.timer);
    }

    startTimer() {
        clearInterval(this.timer);
        this.setState({
            duration: this.props.duration
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
                <span className="call-info-action__timer">{this.formatDuration(this.state.duration)}</span>
            </React.Fragment>
        );
    }
}