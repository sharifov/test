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
        return function(event) {
            //incoming
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
                <div className="incall-group">
                    <div className="contact-info-card">
                        <div className="contact-info-card__details">
                            <div className="contact-info-card__line history-details">
                                <span className="contact-info-card__label">{call.data.type}</span>
                                <div className="contact-info-card__name">
                                    <button className="call-pane__info">
                                        <i className="user-icon fa fa-user"> </i>
                                        <i className="info-icon fa fa-info"> </i>
                                    </button>
                                    <strong>{call.data.contact.name}</strong>
                                </div>
                            </div>
                            <div className="contact-info-card__line history-details">
                                <span className="contact-info-card__call-type">{call.data.contact.phone}</span>
                            </div>
                        </div>
                    </div>
                    <div className="call-pane__call-btns ">
                        <button className="call-pane__start-call calling-state-block" id="btn-accept-call"
                                data-from-internal={call.data.fromInternal} data-call-sid={call.data.callSid} disabled={call.isSentAcceptCallRequestState()}>
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
            </React.Fragment>
        );
    }
}
