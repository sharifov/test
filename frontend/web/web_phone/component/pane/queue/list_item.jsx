class ListItem extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            call: props.call
        }
    }

    componentDidMount() {
        window.phoneWidget.eventDispatcher.addListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
    }

    componentWillUnmount() {
        window.phoneWidget.eventDispatcher.removeListener(this.state.call.getEventUpdateName(), this.callUpdateHandler());
    }

    callUpdateHandler() {
        let self = this;
        return function(event) {
            //queue
            self.setState({
                call: event.call
            });
        }
    }

    render () {
        let call = this.state.call;
        return (
            <li className="call-in-progress__list-item">
                <div className="call-in-progress__call-item call-list-item" data-call-status={call.data.queue}>
                    <div className="call-list-item__info">
                        <ul className="call-list-item__info-list call-info-list">
                            <li className="call-info-list__item">
                                <b className="call-info-list__contact-icon"><i className="fa fa-user"> </i></b><span
                                className="call-info-list__name">{call.data.contact.name}</span>
                            </li>
                            {call.data.contact.company
                                ? <li className="call-info-list__item"><span
                                    className="call-info-list__company">{call.data.contact.company}</span></li>
                                : ''
                            }
                            <li className="call-info-list__item"><span
                                className="call-info-list__number">{call.data.contact.phone}</span></li>
                        </ul>
                        <div className="call-list-item__info-action call-info-action">
                            <CallDurationTimer duration={call.getDuration()}/>
                            {(call.data.queue === 'inProgress' && call.data.typeId !== 3)
                                ?
                                <a href="#" className="call-info-action__more"><i className="fa fa-ellipsis-h"> </i></a>
                                : ''
                            }
                        </div>
                        <ListItemMenu call={call}/>
                    </div>
                    <div className="call-list-item__main-action">
                        <a href="#" className="call-list-item__main-action-trigger"
                           data-type-action={call.data.queue === 'inProgress' ? 'hangup' : (call.data.queue === 'hold' ? 'return' : 'accept')}
                           data-call-sid={call.data.callSid} data-from-internal={call.data.fromInternal}>
                            {call.isSentAcceptCallRequestState() || call.isSentHangupRequestState() || call.isSentReturnHoldCallRequestState()
                                ? <i className="fa fa-spinner fa-spin"/>
                                : <React.Fragment><i className="phone-icon phone-icon--start fa fa-phone"/> <i
                                    className="phone-icon phone-icon--end fa fa-phone-slash"/> </React.Fragment>
                            }
                        </a>
                    </div>
                </div>
            </li>
        );
    }
}

function ListItemMenu(props) {
    let call = props.call;
    if (call.data.queue !== 'inProgress' || call.data.typeId === 3) {
        return null;
    }
    return (
        <ul className="call-list-item__menu call-item-menu">
            <li className="call-item-menu__list-item">
                <a href="#" className="call-item-menu__close"><i className="fa fa-chevron-right"> </i></a>
            </li>
            <li className="call-item-menu__list-item wg-transfer-call" data-call-sid={call.data.callSid}>
                <a href="#" className="call-item-menu__transfer"><i className="fa fa-random"> </i></a>
            </li>
            {conferenceBase
                ?
                    <React.Fragment>
                        <li className="call-item-menu__list-item list_item_hold"
                            data-mode={call.data.isHold ? 'hold' : 'unhold'}
                            data-call-sid={call.data.callSid}>
                            <a href="#" className="call-item-menu__transfer">
                                {call.isSentHoldUnHoldRequestState()
                                    ? <i className="fa fa-spinner fa-spin"> </i>
                                    : call.data.isHold
                                        ? <i className="fa fa-play"> </i>
                                        : <i className="fa fa-pause"> </i>
                                }
                            </a>
                        </li>
                    </React.Fragment>
                : ''
            }
            {call.data.queue !== 'inProgress'
                ? <React.Fragment>
                    <li className="call-item-menu__list-item"><a href="#" className="call-item-menu__transfer"><i
                        className="fas fa-phone-slash"> </i></a></li>
                </React.Fragment>
                : ''
            }
        </ul>
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