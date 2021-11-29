class IncomingPane extends React.Component {
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
            window.phoneWidget.emptyFunc('IncomingPane.callUpdateHandler');

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
                <div className="contact-info-card">
                    <div className="contact-info-card__details">
                        {/*<AntiSpamElement call={call}/>*/}
                        <div className="contact-info-card__line history-details">
                            <span className="contact-info-card__label">{call.data.type}</span>
                            <div className="credential">
                                <div className="contact-info-card__name">
                                    <button className="call-pane__info" data-call-id={call.data.id}>
                                        <i className="user-icon fa fa-user"> </i>
                                        <i className="info-icon fa fa-info"> </i>
                                    </button>
                                    <strong>{call.data.contact.name}</strong>
                                </div>
                                <div className="contact-info-card__phone">
                                    <span> </span>
                                </div>
                            </div>
                        </div>
                        <div className="contact-info-card__line history-details">
                            <span className="contact-info-card__call-type">{call.data.contact.phone}</span>
                        </div>
                    </div>
                </div>
                <IncomingActions call={call}/>
            </React.Fragment>
        );
    }
}

function IncomingActions(props) {
    let call = props.call;

    if (call.data.isInternal) {
        return (
            <div className="actions-container">
                <div className="call-pane__call-btns">
                    <button className="call-pane__end-call end-internal" id="hide-incoming-call"
                            data-call-sid={call.data.callSid}>
                        <i className="fa fa-angle-double-right"> </i>
                    </button>
                    <button className="call-pane__start-call calling-state-block"
                            data-call-sid={call.data.callSid} onClick={() => PhoneWidget.acceptInternalCall(call)}>
                        {call.isSentAcceptCallRequestState()
                            ? <i className="fa fa-spinner fa-spin"> </i>
                            : <i className="fas fa-phone"> </i>
                        }
                    </button>
                    <button className="call-pane__end-call"
                            data-call-sid={call.data.callSid} onClick={() => PhoneWidget.rejectInternalCall(call)}>
                        {call.isSentRejectInternalRequest()
                            ? <i className="fa fa-spinner fa-spin"> </i>
                            : <i className="fa fa-phone-slash"> </i>
                        }
                    </button>
                </div>
            </div>
        );
    }

    return (
        <div className="actions-container">
            <div className="call-pane__call-btns">
                <button className="call-pane__start-call calling-state-block" id="btn-accept-call"
                        data-type-action={call.data.isWarmTransfer ? 'acceptWarmTransfer' : 'accept'}
                        data-from-internal={call.data.fromInternal} data-call-sid={call.data.callSid}
                        disabled={call.isSentAcceptCallRequestState()}>
                    {call.isSentAcceptCallRequestState()
                        ? <i className="fa fa-spinner fa-spin"> </i>
                        : <i className="fas fa-phone"> </i>
                    }
                </button>
                <button className="call-pane__end-call" id="hide-incoming-call"
                        data-call-sid={call.data.callSid}>
                    <i className="fa fa-angle-double-right"> </i>
                </button>
            </div>
        </div>
    );
}

function AntiSpamElement(props) {
    let call = props.call;

    if ('callAntiSpamData' in call.data && 'type' in call.data.callAntiSpamData) {
        let typeTitle = 'Spam';
        let style = {
            color: '#ffffff',
            border: '1px solid #ff0000',
            padding: '5px',
            position: 'relative',
            top: '-20px'
        };
        if (call.data.callAntiSpamData.type === 0) {
            typeTitle = 'Trust';
            style.border = '1px solid #37ff00'
        }

        return (
            <div className="d-flex justify-content-center align-items-center">
                <span style={style}>{typeTitle} Rate: {parseInt(call.data.callAntiSpamData.rate * 100)} %</span>
            </div>
        );
    }

    return ('');
}
