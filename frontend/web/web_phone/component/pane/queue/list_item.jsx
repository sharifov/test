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
        return function (event) {
            //only for minify version
            window.phoneWidget.emptyFunc('ListItem.callUpdateHandler');

            self.setState({
                call: event.call
            });
        }
    }

    render() {
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
                                ? <li className="call-info-list__item"><span className="call-info-list__company">{call.data.contact.company}</span></li>
                                : ''
                            }
                            {call.data.typeId === 2 || call.data.typeId === 1 || call.data.typeId === 4
                                ? <li className="call-info-list__item"><span className="call-info-list__number">{call.data.contact.phone}</span> </li>
                                : ''
                            }
                        </ul>
                        <div className="call-list-item__info-action call-info-action">
                            <span className="call-info-action__timer">
                                <PhoneWidgetTimer duration={call.getDuration()} timeStart={Date.now()}/>
                            </span>
                            {
                                (
                                    (call.data.queue === 'inProgress' && call.data.typeId !== 3)
                                    || (call.data.typeId === 3 && !call.data.isListen && !call.data.isHold)
                                )
                                ? <a href="#" className="call-info-action__more"><i className="fa fa-ellipsis-h"> </i></a>
                                : ''
                            }
                        </div>
                        <ListItemMenu call={call}/>
                    </div>
                    <div className="call-list-item__main-action">
                        <a href="#" className="call-list-item__main-action-trigger btn-item-call-queue"
                           data-type-action={getItemActionName(call)}
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

function getItemActionName(call) {
    if (call.data.queue === 'inProgress') {
       return 'hangup';
    }
    if (call.data.queue === 'hold') {
        return 'return';
    }
    if (call.data.isInternal) {
        return 'acceptInternal';
    }
    if (call.data.isWarmTransfer) {
        return 'acceptWarmTransfer';
    }
    return 'accept';
}

function ListItemMenu(props) {
    let call = props.call;
    if (call.data.queue !== 'inProgress') {
        return null;
    }

    if (call.data.typeId === 3) {
        return <ListItemMenuJoinCall call={call}/>
    }

    if (call.data.isInternal) {
        if (call.data.isConferenceCreator) {
            return (
                <ul className="call-list-item__menu call-item-menu">
                    <li className="call-item-menu__list-item">
                        <a href="#" className="call-item-menu__close"><i className="fa fa-chevron-right"> </i></a>
                    </li>
                    <React.Fragment>
                        <ListItemBtnHold call={call}/>
                        <ListItemBtnMute call={call}/>
                    </React.Fragment>
                </ul>
            );
        }
        return (
            <ul className="call-list-item__menu call-item-menu">
                <li className="call-item-menu__list-item">
                    <a href="#" className="call-item-menu__close"><i className="fa fa-chevron-right"> </i></a>
                </li>
                <ListItemBtnMute call={call}/>
            </ul>
        );
    }

    return (
        <ul className="call-list-item__menu call-item-menu">
            <li className="call-item-menu__list-item">
                <a href="#" className="call-item-menu__close"><i className="fa fa-chevron-right"> </i></a>
            </li>
            {!call.data.isHold
             ? <ListItemBtnTransfer call={call}/>
             : ''
            }
            {conferenceBase
                ?
                <React.Fragment>
                    <ListItemBtnHold call={call}/>
                    <ListItemBtnMute call={call}/>
                </React.Fragment>
                : ''
            }
        </ul>
    );
}

function ListItemBtnTransfer(props) {
    return (
        <li className="call-item-menu__list-item wg-transfer-call" data-call-sid={props.call.data.callSid}>
            <a href="#" className="call-item-menu__transfer"><i className="fa fa-random"> </i></a>
        </li>
    );
}

function ListItemBtnHold(props) {
    let call = props.call;
    return (
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
    );
}

function ListItemBtnMute(props) {
    let call = props.call;
    if (call.data.isHold) {
        return null;
    }
    return (
        <li className="call-item-menu__list-item list_item_mute"
            data-call-sid={call.data.callSid} data-is-muted={call.data.isMute}>
            <a href="#" className="call-item-menu__transfer">
                {call.isSentMuteUnMuteRequestState()
                    ? <i className="fa fa-spinner fa-spin"> </i>
                    : call.data.isMute
                        ? <i className="fas fa-microphone-alt-slash"> </i>
                        : <i className="fas fa-microphone"> </i>
                }
            </a>
        </li>
    );
}

function ListItemMenuJoinCall(props) {
    let call = props.call;

    if (call.data.isListen) {
        return null;
    }

    return (
        <ul className="call-list-item__menu call-item-menu">
            <li className="call-item-menu__list-item">
                <a href="#" className="call-item-menu__close"><i className="fa fa-chevron-right"> </i></a>
            </li>
            <ListItemBtnMute call={call}/>
        </ul>
    );
}