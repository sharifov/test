class OutgoingPane extends React.Component {
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
            window.phoneWidget.emptyFunc('OutgoingPane.callUpdateHandler');

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
                        <div className="contact-info-card__line history-details">
                            <span className="contact-info-card__label">{call.data.type}</span>
                            <div className="credential">
                                <div className="contact-info-card__name">
                                    <button className="call-pane__info">
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
                <div className="actions-container">
                <div className="call-pane__call-btns is-pending">
                    <button className="call-pane__start-call calling-state-block">
                        <div className="call-in-action">
                            <span className="call-in-action__text">{call.data.status}</span>
                            <span className="call-in-action__time"><PhoneWidgetTimer duration={call.getDuration()}
                                                                                     timeStart={Date.now()}/></span>
                        </div>
                        <i className="fas fa-phone"> </i>
                    </button>
                    <button className="call-pane__end-call" id="cancel-outgoing-call"
                            data-call-sid={call.data.callSid} disabled={call.isSentHangupRequestState()}>
                        {call.isSentHangupRequestState()
                            ? <i className="fa fa-spinner fa-spin"> </i>
                            : <i className="fa fa-phone-slash"> </i>
                        }
                    </button>
                </div>
                </div>

            </React.Fragment>
        );
    }
}
